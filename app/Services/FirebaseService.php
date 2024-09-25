<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Database as FirebaseDatabase;

class FirebaseService
{
    protected $database;

    public function __construct()
    {
        // Configurez la connexion à Firebase
        $factory = (new Factory)
            ->withServiceAccount('/home/ba/hadiyatou/storage/app/firebase/apprenant-1699e-firebase-adminsdk-rvx47-db4d75de36.json')
            ->withDatabaseUri('https://apprenant-1699e-default-rtdb.firebaseio.com/');

        $this->database = $factory->createDatabase();
    }

    public function createPromotion(array $promotionData)
    {
        // Récupérer toutes les promotions existantes pour déterminer le dernier ID
        $promotions = $this->database->getReference('promotions')->getValue();

        if ($promotions) {
            // Récupérer le plus grand ID actuel
            $existingIds = array_map('intval', array_keys($promotions)); // Convertir les clés en entiers
            $newId = max($existingIds) + 1; // Incrémenter pour obtenir un nouvel ID
        } else {
            // Si aucune promotion n'existe encore, démarrer avec l'ID 1
            $newId = 1;
        }

        // Ajouter l'ID à la nouvelle promotion
        $promotionData['id'] = $newId;

        // Enregistrer la nouvelle promotion avec l'ID dans Firebase
        $this->database->getReference('promotions/' . $newId)->set($promotionData);

        return $promotionData;
    }


    public function updatePromotion($promotionId, array $promotionData)
    {
        // Mettez à jour la promotion existante dans Firebase
        $this->database->getReference('promotions/' . $promotionId)->update($promotionData);
        return $promotionData + ['id' => $promotionId];
    }

    public function getPromotion($promotionId)
    {
        // Récupérez une promotion par son ID
        $promotion = $this->database->getReference('promotions/' . $promotionId)->getValue();
        if ($promotion) {
            $promotion['id'] = $promotionId; // Ajoutez l'ID à la promotion
        }
        return $promotion;
    }

    public function getAllPromotions()
    {
        // Récupérez toutes les promotions
        $promotions = $this->database->getReference('promotions')->getValue();
        if ($promotions) {
            foreach ($promotions as $key => $promotion) {
                $promotion['id'] = $key; // Ajoutez l'ID à chaque promotion
                $result[] = $promotion; // Construisez un tableau de promotions
            }
            return $result;
        }
        return []; // Retournez un tableau vide si aucune promotion n'est trouvée
    }

    public function deletePromotion($promotionId)
    {
        // Supprimez une promotion par son ID
        $this->database->getReference('promotions/' . $promotionId)->remove();
        return ['success' => true, 'id' => $promotionId]; // Retournez l'ID de la promotion supprimée
    }
}
