<?php

namespace App\Http\Controllers;

use App\Services\PromotionService;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    protected $promotionService;

    public function __construct(PromotionService $promotionService)
    {
        $this->promotionService = $promotionService;
    }

    public function create(Request $request)
    {
        $data = $request->validate([
            'libelle' => 'required|string|unique:promotions',
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date',
            'duree' => 'nullable|integer',
            'referentiels' => 'array|nullable',
            'photo' => 'nullable|string',
        ]);

        $promotionId = $this->promotionService->createPromotion($data);
        return response()->json(['id' => $promotionId], 201);
    }

    public function index()
    {
        $promotions = $this->promotionService->listPromotions();
        return response()->json($promotions);
    }

    public function show($id)
    {
        $promotion = $this->promotionService->getPromotionById($id);
        return response()->json($promotion);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'libelle' => 'nullable|string',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date',
            'duree' => 'nullable|integer',
            'referentiels' => 'array|nullable',
            'photo' => 'nullable|string',
        ]);

        $this->promotionService->updatePromotion($id, $data);
        return response()->json(['message' => 'Promotion updated successfully.']);
    }

    // Ajoutez d'autres méthodes pour les différentes opérations
}
