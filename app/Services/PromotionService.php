<?php

namespace App\Services;

use App\Models\Promotion;
use App\Models\Referential;
use App\Notifications\PromotionClosedNotification;
use App\Repositories\PromotionRepository;
use App\Repositories\ReferentialRepository;
use App\Repositories\StudentRepository;

class PromotionService
{
    private $promotionRepository;
    private $firebaseService;
    private $database;
    private $referentielRepository;

    public function __construct(FirebaseService $firebaseService, PromotionRepository $promotionRepository)
    {
        $this->firebaseService = $firebaseService;
        $this->promotionRepository = $promotionRepository; // Ajout de cette ligne
    }


    public function getAllPromotions()
    {
        // Récupérer toutes les promotions depuis le repository
        return $this->promotionRepository->getAllPromotions();
    }


    public function createPromotion(array $data)
    {
        // Créer la promotion dans Firebase
        $promotion = $this->firebaseService->createPromotion($data);
        return $promotion;
    }

    public function updatePromotion($promotionId, array $data)
    {
        // Mettre à jour la promotion dans Firebase
        $promotion = $this->firebaseService->updatePromotion($promotionId, $data);
        return $promotion;
    }


    public function addReferentialToPromotion($promotionId, $referentialId)
    {
        $promotionRef = $this->database->getReference('promotions/' . $promotionId . '/referentials');
        $promotionRef->push($referentialId);
    }

    public function removeReferentialFromPromotion($promotionId, $referentialId)
    {
        $promotionRef = $this->database->getReference('promotions/' . $promotionId . '/referentials');
        $promotionRef->orderByValue()->equalTo($referentialId)->remove();
    }

    public function changePromotionStatus($promotionId, $status)
    {
        $promotionRef = $this->database->getReference('promotions/' . $promotionId);
        $promotionRef->update(['status' => $status]);
    }

    public function closePromotion($promotionId)
    {
        $promotionRef = $this->database->getReference('promotions/' . $promotionId);
        $promotionRef->update(['status' => 'closed']);
    }
}
