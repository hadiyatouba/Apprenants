<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Storage;
use Exception;

class FirebaseStorageService
{
    protected $storage;

    public function __construct()
    {
        try {
            // Initialise Firebase avec les credentials du fichier JSON
            $firebase = (new Factory)->withServiceAccount(config('firebase.credentials.file'));
            $this->storage = $firebase->createStorage();
        } catch (Exception $e) {
            // Capture les erreurs liées à la configuration de Firebase
            throw new Exception("Impossible de se connecter à Firebase Storage: " . $e->getMessage());
        }
    }

    public function uploadFile($file, $path)
    {
        try {
            // Obtenir le bucket du stockage
            $bucket = $this->storage->getBucket();

            // Upload le fichier vers Firebase Storage
            $object = $bucket->upload(
                file_get_contents($file->getRealPath()),
                ['name' => $path]
            );

            // Générer une URL signée avec une durée de 10 ans
            return $object->signedUrl(new \DateTime('+10 years'));
        } catch (Exception $e) {
            // Capture les erreurs lors de l'upload du fichier
            throw new Exception("Erreur lors de l'upload du fichier vers Firebase: " . $e->getMessage());
        }
    }
}
