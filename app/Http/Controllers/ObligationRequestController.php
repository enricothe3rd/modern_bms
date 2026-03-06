<?php

namespace App\Http\Controllers;

use App\Services\ObligationRequestService;
use App\Repositories\DepartmentRepository;
use App\Repositories\ClaimantPayeeRepository;
use App\Repositories\UserRepository;
use App\Repositories\FundTypeRepository;
use App\Http\Requests\ObligationRequestRequest;
use Illuminate\Http\Request;

class ObligationRequestController extends Controller
{
    protected $service;
    protected $departmentRepo;
    protected $claimantPayeeRepo;
    protected $userRepo;
    protected $fundTypeRepo;

    public function __construct(
        ObligationRequestService $service,
        DepartmentRepository $departmentRepo,
        ClaimantPayeeRepository $claimantPayeeRepo,
        UserRepository $userRepo,
        FundTypeRepository $fundTypeRepo
    ) {
        $this->service = $service;
        $this->departmentRepo = $departmentRepo;
        $this->claimantPayeeRepo = $claimantPayeeRepo;
        $this->userRepo = $userRepo;
        $this->fundTypeRepo = $fundTypeRepo;
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Get current user's assigned review statuses
        $userAssignedStatusIds = [];
        if ($user && method_exists($user, 'departmentAssignments')) {
            $userAssignedStatusIds = $user->departmentAssignments()
                ->with('reviewStatuses')
                ->get()
                ->pluck('reviewStatuses')
                ->flatten()
                ->pluck('id')
                ->unique()
                ->toArray();
        }
        
        // Filter obligation requests:
        // - Super admin sees all
        // - Users see OBRs with their assigned statuses OR OBRs they created (to track progress)
        if ($user->isSuperAdmin()) {
            $obligationRequests = $this->service->getAllObligationRequests();
        } elseif (empty($userAssignedStatusIds)) {
            // Users with no assignments see only their own OBRs
            $obligationRequests = $this->service->getObligationRequestsByCreator($user->id);
        } else {
            // Users see OBRs with assigned statuses OR OBRs they created
            $obligationRequests = $this->service->getObligationRequestsByStatuses($userAssignedStatusIds, $user->id);
        }
        
        $departments = $this->departmentRepo->all();
        $claimantPayees = $this->claimantPayeeRepo->all();
        $users = $this->userRepo->all();
        $fundTypes = $this->fundTypeRepo->all();
        $fiscalYears = \App\Models\FiscalYear::where('is_active', true)->orderBy('year', 'desc')->get();
        
        // Show all statuses so users can see the full workflow
        // But only assigned statuses will be changeable
        $reviewStatuses = \App\Models\ReviewStatus::active()->ordered()->get();

        return view('obligation-requests.index', compact(
            'obligationRequests',
            'departments',
            'claimantPayees',
            'users',
            'fundTypes',
            'fiscalYears',
            'reviewStatuses',
            'userAssignedStatusIds'
        ));
    }

    public function store(ObligationRequestRequest $request)
    {
        $obr = $this->service->createObligationRequest($request->validated());

        if ($request->wantsJson()) {
            return response()->json($obr, 201);
        }

        return redirect()->route('obligation-requests.index')
            ->with('success', 'Obligation Request created successfully!');
    }

    public function show($id)
    {
        $obr = $this->service->findObligationRequest($id);

        if (request()->wantsJson()) {
            return response()->json($obr);
        }

        // Check if PDF generation is requested
        if (request()->get('format') === 'pdf') {
            return $this->generatePdf($obr);
        }

        return view('obligation-requests.show', compact('obr'));
    }

    /**
     * Generate PDF for obligation request
     */
    public function generatePdf($obr)
    {
        $pdf = \App\Services\PdfTemplateBuilder::create('obligation_request', [
            'obr' => $obr,
            'organization' => env('PDF_ORGANIZATION_NAME', config('app.name')),
            'address' => env('PDF_ORGANIZATION_ADDRESS', 'Your Organization Address'),
            'logo_path' => env('PDF_LOGO_PATH', null)
        ]);

        $filename = 'OBR_' . $obr->obr_number . '.pdf';
        
        // Check if action parameter is provided
        $action = request()->get('action', 'view'); // default to view
        
        switch ($action) {
            case 'download':
                return $pdf->download($filename);
            case 'view':
            case 'inline':
            default:
                return $pdf->inline($filename);
        }
    }

    public function edit($id)
    {
        $obr = $this->service->findObligationRequest($id);
        
        // Return JSON for AJAX requests
        if (request()->wantsJson() || request()->expectsJson()) {
            return response()->json([
                'obr' => $obr,
                'signatories' => $obr->signatories,
                'items' => $obr->items,
                'fiscal_year_id' => $obr->fiscal_year_id
            ]);
        }

        // Return view for regular requests
        $obligationRequests = $this->service->getAllObligationRequests();
        $departments = $this->departmentRepo->all();
        $claimantPayees = $this->claimantPayeeRepo->all();
        $users = $this->userRepo->all();
        $fundTypes = $this->fundTypeRepo->all();

        return view('obligation-requests.index', compact(
            'obr',
            'obligationRequests',
            'departments',
            'claimantPayees',
            'users',
            'fundTypes'
        ));
    }

    public function update(ObligationRequestRequest $request, $id)
    {
        $obr = $this->service->updateObligationRequest($id, $request->validated());

        if ($request->wantsJson()) {
            return response()->json($obr);
        }

        return redirect()->route('obligation-requests.index')
            ->with('success', 'Obligation Request updated successfully!');
    }

    public function destroy(Request $request, $id)
    {
        $this->service->deleteObligationRequest($id);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Obligation Request deleted successfully']);
        }

        return redirect()->route('obligation-requests.index')
            ->with('success', 'Obligation Request deleted successfully!');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'review_status_id' => 'required|exists:review_statuses,id'
        ]);

        $user = auth()->user();
        
        // Get user's assigned status IDs
        $userAssignedStatusIds = [];
        if ($user && method_exists($user, 'departmentAssignments')) {
            $userAssignedStatusIds = $user->departmentAssignments()
                ->with('reviewStatuses')
                ->get()
                ->pluck('reviewStatuses')
                ->flatten()
                ->pluck('id')
                ->unique()
                ->toArray();
        }
        
        // Check if user is allowed to change to this status (unless super admin)
        if (!$user->isSuperAdmin() && !in_array($request->review_status_id, $userAssignedStatusIds)) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to change to this status'
                ], 403);
            }
            return redirect()->back()->with('error', 'You are not authorized to change to this status');
        }

        $obr = $this->service->updateReviewStatus($id, $request->review_status_id);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'obr' => $obr->load('reviewStatus')
            ]);
        }

        return redirect()->back()->with('success', 'Status updated successfully!');
    }
}
