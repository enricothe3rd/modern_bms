<?php
namespace App\Http\Controllers;

use App\Services\ReviewStatusService;
use App\Http\Requests\ReviewStatusRequest;
use Illuminate\Http\Request;

class ReviewStatusController extends Controller
{
    protected $service;

    public function __construct(ReviewStatusService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $reviewStatuses = $this->service->getAllStatuses();
        return view('review-statuses.index', compact('reviewStatuses'));
    }

    public function store(ReviewStatusRequest $request)
    {
        $this->service->createStatus($request->validated());
        return redirect()->route('review-statuses.index')
            ->with('success', 'Review status created successfully!');
    }

    public function edit($id)
    {
        $reviewStatus = $this->service->findStatus($id);
        $reviewStatuses = $this->service->getAllStatuses();
        return view('review-statuses.index', compact('reviewStatus', 'reviewStatuses'));
    }

    public function update(ReviewStatusRequest $request, $id)
    {
        $this->service->updateStatus($id, $request->validated());
        return redirect()->route('review-statuses.index')
            ->with('success', 'Review status updated successfully!');
    }

    public function destroy($id)
    {
        $this->service->deleteStatus($id);
        return redirect()->route('review-statuses.index')
            ->with('success', 'Review status deleted successfully!');
    }
}