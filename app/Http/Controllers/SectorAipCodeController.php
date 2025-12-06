<?php

namespace App\Http\Controllers;

use App\Services\SectorAipCodeService;
use App\Http\Requests\SectorAipCodeRequest;
use Illuminate\Http\Request;

class SectorAipCodeController extends Controller
{
    protected $service;

    public function __construct(SectorAipCodeService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of AIP codes for a sector
     */
    public function index($sectorId)
    {
        $sector = $this->service->getSector($sectorId);
        $aipCodes = $this->service->getAipCodesBySector($sectorId);

        return view('sector-aip-codes.index', compact('sector', 'aipCodes'));
    }

    /**
     * Store a newly created AIP code
     */
    public function store(SectorAipCodeRequest $request, $sectorId)
    {
        $this->service->getSector($sectorId); // Verify sector exists
        
        $this->service->createAipCode($request->validated(), $sectorId);

        return redirect()->route('sector-aip-codes.index', $sectorId)
            ->with('success', 'AIP code created successfully!');
    }

    /**
     * Update the specified AIP code
     */
    public function update(SectorAipCodeRequest $request, $sectorId, $aipCodeId)
    {
        $this->service->getSector($sectorId); // Verify sector exists

        $this->service->updateAipCode($aipCodeId, $request->validated(), $sectorId);

        return redirect()->route('sector-aip-codes.index', $sectorId)
            ->with('success', 'AIP code updated successfully!');
    }

    /**
     * Toggle the active status of an AIP code
     */
    public function toggle($sectorId, $aipCodeId)
    {
        $this->service->getSector($sectorId); // Verify sector exists
        $this->service->toggleAipCodeStatus($aipCodeId, $sectorId);

        return redirect()->route('sector-aip-codes.index', $sectorId)
            ->with('success', 'AIP code status updated successfully!');
    }

    /**
     * Remove the specified AIP code
     */
    public function destroy($sectorId, $aipCodeId)
    {
        $this->service->getSector($sectorId); // Verify sector exists
        $this->service->deleteAipCode($aipCodeId, $sectorId);

        return redirect()->route('sector-aip-codes.index', $sectorId)
            ->with('success', 'AIP code deleted successfully!');
    }
}
