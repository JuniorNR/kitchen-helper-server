<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreIngredientRequest;
use App\Http\Requests\UpdateIngredientRequest;
use App\Enums\ApiCode;
use Illuminate\Support\Facades\Auth;
use App\Models\Ingredient;

class IngredientController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $ingredients = $user->ingredients;
        return response()->json([
            'data' => $ingredients,
            'code' => ApiCode::INGREDIENTS_RETURNED->value
        ], 200);
    }
    public function all()
    {
        $ingredients = Ingredient::all();
        return response()->json([
            'data' => $ingredients,
            'code' => ApiCode::INGREDIENTS_RETURNED->value
        ], 200);
    }
    public function show($id)
    {
        $ingredient = Auth::user()
            ->ingredients()
            ->findOrFail($id);

        return response()->json([
            'data' => $ingredient,
            'code' => ApiCode::INGREDIENT_RETURNED->value
        ], 200);
    }
    public function store(StoreIngredientRequest $request)
    {
        $user = Auth::user();
        $ingredient = $user
            ->ingredients()
            ->create($request->validated());

        return response()->json([
            'data' =>  $ingredient,
            'code' => ApiCode::INGREDIENT_CREATED->value
        ], 201);
    }
    public function update(UpdateIngredientRequest $request, $id)
    {
        $ingredient = Auth::user()
            ->ingredients()
            ->findOrFail($id);

        $ingredient->update($request->validated());
        return response()->json([
            'data' => $ingredient->fresh(),
            'code' => ApiCode::INGREDIENT_UPDATED->value
        ], 200);
    }
    public function destroy($id)
    {
        $ingredient = Auth::user()
            ->ingredients()
            ->findOrFail($id);

        $ingredient->delete();

        return response()->json([
            'code' => ApiCode::INGREDIENT_DELETED->value,
        ], 200);
    }
}
