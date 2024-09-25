<?php

namespace App\Services;
use App\Repositories\ReferentielRepository;


class ReferentielService
{
    protected $referentielRepository;

    public function __construct(ReferentielRepository $referentielRepository)
    {
        $this->referentielRepository = $referentielRepository;
    }

    public function getAllReferentiels()
    {
        return $this->referentielRepository->getAllReferentiels();
    }

    public function createReferentiel(array $data)
    {
        return $this->referentielRepository->createReferentiel($data);
    }

    public function getReferentielById($id)
    {
        return $this->referentielRepository->getReferentielById($id);
    }

    public function updateReferentiel($id, array $data)
    {
        return $this->referentielRepository->updateReferentiel($id, $data);
    }

    public function deleteReferentiel($id)
    {
        $this->referentielRepository->deleteReferentiel($id);
    }

    public function getArchivedReferentiels()
    {
        return $this->referentielRepository->getArchivedReferentiels();
    }
}