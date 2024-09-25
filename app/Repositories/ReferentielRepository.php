<?php
namespace App\Repositories;

use App\Models\Promotion;
use App\Models\Referential;
class ReferentielRepository
{
    protected $firebase;

    public function __construct()
    {
        $this->firebase = app('firebase.database');
    }

    public function getAllReferentiels()
    {
        return $this->firebase->getReference('referentiels')->orderByChild('status')->equalTo('active')->getValue();
    }

    public function createReferentiel(array $data)
    {
        $newReferentiel = $this->firebase->getReference('referentiels')->push($data);
        return $newReferentiel->getSnapshot()->getValue();
    }

    public function getReferentielById($id)
    {
        return $this->firebase->getReference('referentiels/' . $id)->getValue();
    }

    public function updateReferentiel($id, array $data)
    {
        $this->firebase->getReference('referentiels/' . $id)->update($data);
        return $this->firebase->getReference('referentiels/' . $id)->getValue();
    }

    public function deleteReferentiel($id)
    {
        $this->firebase->getReference('referentiels/' . $id)->set([
            'status' => 'archived'
        ]);
    }

    public function getArchivedReferentiels()
    {
        return $this->firebase->getReference('referentiels')->orderByChild('status')->equalTo('archived')->getValue();
    }
}

