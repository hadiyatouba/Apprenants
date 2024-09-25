<?php

namespace App\Repositories;

use App\Models\Promotion;
use App\Models\Referential;

class PromotionRepository
{
    public function getAllPromotions()
    {
        return Promotion::all(); // Vérifiez si cela renvoie bien des promotions
    }


    public function getPromotionById(int $id)
    {
        return Promotion::findOrFail($id);
    }

    public function createPromotion(array $data)
    {
        $promotion = Promotion::create($data);
        $promotion->referentials()->attach($data['referential_ids']);
        return $promotion;
    }

    public function updatePromotion(Promotion $promotion, array $data)
    {
        $promotion->update($data);
        return $promotion;
    }

    public function addReferentialToPromotion(Promotion $promotion, Referential $referential)
    {
        $promotion->referentials()->attach($referential->id);
        return $promotion->refresh();
    }

    public function removeReferentialFromPromotion(Promotion $promotion, Referential $referential)
    {
        $promotion->referentials()->detach($referential->id);
        return $promotion->refresh();
    }

    public function changePromotionStatus(Promotion $promotion, string $status)
    {
        $promotion->update(['status' => $status]);
        return $promotion;
    }

    public function closePromotion(Promotion $promotion)
    {
        $promotion->update(['status' => 'closed']);
        return $promotion;
    }
}
