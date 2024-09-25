<?php

namespace App\Services;

use App\Models\Promotion;
use Kreait\Firebase\Contract\Database;
use Kreait\Laravel\Firebase\Facades\Firebase;

class FirebasePromotionService
{
    private $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function getAllPromotions()
    {
        $reference = $this->database->getReference('promotions');
        $snapshot = $reference->getValue();
        $promotions = [];

        if ($snapshot) {
            foreach ($snapshot as $id => $promotionData) {
                $promotions[] = new Promotion($promotionData + ['id' => $id]);
            }
        }

        return $promotions;
    }

    public function getPromotionById($id)
    {
        $reference = $this->database->getReference('promotions/' . $id);
        $snapshot = $reference->getValue();

        if ($snapshot) {
            return new Promotion($snapshot + ['id' => $id]);
        }

        return null; // Promotion non trouvée
    }

    public function createPromotion(array $data)
    {
        $newPromotionRef = $this->database->getReference('promotions')->push($data);
        $newPromotionData = $data + ['id' => $newPromotionRef->getKey()];

        return new Promotion($newPromotionData);
    }

    public function updatePromotion(Promotion $promotion, array $data)
    {
        $this->database->getReference('promotions/' . $promotion->id)->update($data);
        return new Promotion($data + ['id' => $promotion->id]);
    }

    public function deletePromotion($id)
    {
        $this->database->getReference('promotions/' . $id)->remove();
    }
}
