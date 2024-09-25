<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use App\Models\Referential;
use Illuminate\Http\Request;
use App\Services\PromotionService;
use App\Http\Controllers\Controller;
use App\Http\Requests\PromotionRequest;

class PromotionController extends Controller
{
    private $promotionService;

    public function __construct(PromotionService $promotionService)
    {
        $this->promotionService = $promotionService;
    }

    public function index()
    {
        $promotions = $this->promotionService->getAllPromotions();
        return response()->json($promotions);
    }

    public function show(Promotion $promotion)
    {
        return response()->json($promotion);
    }

    public function store(Request $request)
    {
        $promotion = $this->promotionService->createPromotion($request->all());
        return response()->json($promotion, 201);
    }

    public function update(PromotionRequest $request, Promotion $promotion)
    {
        $updatedPromotion = $this->promotionService->updatePromotion($promotion, $request->validated());
        return response()->json($updatedPromotion);
    }

    public function addReferential(Promotion $promotion, Referential $referential)
    {
        $updatedPromotion = $this->promotionService->addReferentialToPromotion($promotion, $referential);
        return response()->json($updatedPromotion);
    }

    public function removeReferential(Promotion $promotion, Referential $referential)
    {
        $updatedPromotion = $this->promotionService->removeReferentialFromPromotion($promotion, $referential);
        return response()->json($updatedPromotion);
    }

    public function changeStatus(Promotion $promotion, string $status)
    {
        $updatedPromotion = $this->promotionService->changePromotionStatus($promotion, $status);
        return response()->json($updatedPromotion);
    }

    public function close(Promotion $promotion)
    {
        $closedPromotion = $this->promotionService->closePromotion($promotion);
        return response()->json($closedPromotion);
    }
}