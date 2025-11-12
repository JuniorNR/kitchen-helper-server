<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreIngredientRequest;
use App\Http\Requests\UpdateIngredientRequest;
use App\Enums\ApiCode;
use Illuminate\Support\Facades\Auth;
use App\Models\Ingredient;
use Carbon\Carbon;

class IngredientController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = $user
            ->ingredients()
            ->orderBy('created_at', 'desc');

        // Фильтры
        if ($request->has('categories')) {
            $request->merge(['categories' => $this->normalizeStringList($request->input('categories'))]);
        }
        if ($request->has('units')) {
            $request->merge(['units' => $this->normalizeStringList($request->input('units'))]);
        }

        $validated = $request->validate([
            'price_from' => 'nullable|numeric',
            'price_to' => 'nullable|numeric',
            'categories' => 'nullable|array',
            'categories.*' => 'string',
            'units' => 'nullable|array',
            'units.*' => 'string',
            'created_from' => 'nullable|date',
            'created_to' => 'nullable|date',
        ]);

        if (isset($validated['price_from'])) {
            $query->where('price', '>=', $validated['price_from']);
        }
        if (isset($validated['price_to'])) {
            $query->where('price', '<=', $validated['price_to']);
        }
        if (!empty($validated['categories'])) {
            $query->whereIn('category', $validated['categories']);
        }
        if (!empty($validated['units'])) {
            $query->whereIn('unit', $validated['units']);
        }
        if (!empty($validated['created_from'])) {
            $query->where('created_at', '>=', Carbon::parse($validated['created_from'])->startOfDay());
        }
        if (!empty($validated['created_to'])) {
            $query->where('created_at', '<=', Carbon::parse($validated['created_to'])->endOfDay());
        }

        // Если параметр page не передан — вернуть все без пагинации
        if (!$request->has('page')) {
            $ingredients = $query->get();

            return response()->json([
                'data' => $ingredients,
                'code' => ApiCode::INGREDIENTS_RETURNED->value
            ], 200);
        }

        // Иначе — пагинация
        $ingredients = $query->paginate(6);

        return response()->json([
            'data' => $ingredients->items(),
            'pagination' => [
                'next' => $ingredients->nextPageUrl(),
                'prev' => $ingredients->previousPageUrl(),
                'current_page' => $ingredients->currentPage(),
                'last_page' => $ingredients->lastPage(),
                'per_page' => $ingredients->perPage(),
                'total' => $ingredients->total(),
            ],
            'code' => ApiCode::INGREDIENTS_RETURNED->value
        ], 200);
    }
    public function all(Request $request)
    {
        $query = Ingredient::orderBy('created_at', 'desc');

        // Фильтры
        if ($request->has('categories')) {
            $request->merge(['categories' => $this->normalizeStringList($request->input('categories'))]);
        }
        if ($request->has('units')) {
            $request->merge(['units' => $this->normalizeStringList($request->input('units'))]);
        }
        $validated = $request->validate([
            'price_from' => 'nullable|numeric',
            'price_to' => 'nullable|numeric',
            'categories' => 'nullable|array',
            'categories.*' => 'string',
            'units' => 'nullable|array',
            'units.*' => 'string',
            'created_from' => 'nullable|date',
            'created_to' => 'nullable|date',
        ]);

        if (isset($validated['price_from'])) {
            $query->where('price', '>=', $validated['price_from']);
        }
        if (isset($validated['price_to'])) {
            $query->where('price', '<=', $validated['price_to']);
        }
        if (!empty($validated['categories'])) {
            $query->whereIn('category', $validated['categories']);
        }
        if (!empty($validated['units'])) {
            $query->whereIn('unit', $validated['units']);
        }
        if (!empty($validated['created_from'])) {
            $query->where('created_at', '>=', Carbon::parse($validated['created_from'])->startOfDay());
        }
        if (!empty($validated['created_to'])) {
            $query->where('created_at', '<=', Carbon::parse($validated['created_to'])->endOfDay());
        }

        if (!$request->has('page')) {
            $ingredients = $query->get();

            return response()->json([
                'data' => $ingredients,
                'code' => ApiCode::INGREDIENTS_RETURNED->value
            ], 200);
        }

        $ingredients = $query->paginate(6);

        return response()->json([
            'data' => $ingredients->items(),
            'pagination' => [
                'next' => $ingredients->nextPageUrl(),
                'prev' => $ingredients->previousPageUrl(),
                'current_page' => $ingredients->currentPage(),
                'last_page' => $ingredients->lastPage(),
                'per_page' => $ingredients->perPage(),
                'total' => $ingredients->total(),
            ],
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

    private function normalizeStringList($input): array
    {
        if ($input === null || $input === '') {
            return [];
        }

        // Если пришла JSON-строка
        if (is_string($input)) {
            $decoded = json_decode($input, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $input = $decoded;
            } else {
                // Поддержка comma-separated формата: "a,b,c"
                $input = array_map('trim', explode(',', $input));
            }
        }

        if (!is_array($input)) {
            $input = [$input];
        }

        $result = [];
        foreach ($input as $item) {
            if (is_string($item)) {
                $val = trim($item);
                if ($val !== '') {
                    $result[] = $val;
                }
                continue;
            }

            if (is_array($item)) {
                foreach (['value', 'id', 'name', 'title', 'label'] as $key) {
                    if (isset($item[$key]) && is_string($item[$key])) {
                        $val = trim($item[$key]);
                        if ($val !== '') {
                            $result[] = $val;
                        }
                        break;
                    }
                }
            }
        }

        // Удаляем дубликаты и пустые
        $result = array_values(array_unique(array_filter($result, fn ($v) => $v !== '')));
        // Приведение к нижнему регистру для сопоставления слуг/префиксов
        $result = array_map(fn ($v) => mb_strtolower($v), $result);
        return $result;
    }

    // Утилиты ниже
}
