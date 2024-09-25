<?php

namespace App\Http\Controllers;

use App\Services\ReferentielService;
use Illuminate\Http\Request;
use App\Http\Requests\StoreReferentielRequest;
use App\Http\Requests\UpdateReferentielRequest;

class ReferentielController extends Controller
{
    protected $referentielService;

    public function __construct(ReferentielService $referentielService)
    {
        $this->referentielService = $referentielService;
    }

    public function index()
    {
        $referentiels = $this->referentielService->getAllReferentiels();
        return response()->json($referentiels);
    }

    public function store(StoreReferentielRequest $request)
    {
        $referentiel = $this->referentielService->createReferentiel($request->validated());
        return response()->json($referentiel, 201);
    }

    public function update(UpdateReferentielRequest $request, $id)
    {
        $referentiel = $this->referentielService->updateReferentiel($id, $request->validated());
        return response()->json($referentiel);
    }

    public function show($id)
    {
        $referentiel = $this->referentielService->getReferentielById($id);
        return response()->json($referentiel);
    }

    public function destroy($id)
    {
        $this->referentielService->deleteReferentiel($id);
        return response()->json(null, 204);
    }

    public function getArchived()
    {
        $referentiels = $this->referentielService->getArchivedReferentiels();
        return response()->json($referentiels);
    }
}