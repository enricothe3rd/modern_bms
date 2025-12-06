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

    public function index()
    {
        $obligationRequests = $this->service->getAllObligationRequests();
        $departments = $this->departmentRepo->all();
        $claimantPayees = $this->claimantPayeeRepo->all();
        $users = $this->userRepo->all();
        $fundTypes = $this->fundTypeRepo->all();

        return view('obligation-requests.index', compact(
            'obligationRequests',
            'departments',
            'claimantPayees',
            'users',
            'fundTypes'
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

        return view('obligation-requests.show', compact('obr'));
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
                'budget_year' => date('Y') // Default to current year, can be enhanced later
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
}
