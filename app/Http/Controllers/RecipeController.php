<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;
use App\Models\RecipeStep;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreRecipeRequest;
use App\Http\Requests\UpdateRecipeRequest;
use App\Enums\ApiCode;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;


class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
			$query = Auth::user()
				->recipes()
				->with(['steps.ingredients', 'images', 'user'])
				->orderBy('created_at', 'desc');

			// Фильтры
			if ($request->has('ration')) {
				$request->merge(['ration' => $this->normalizeStringList($request->input('ration'))]);
			}
			if ($request->has('type')) {
				$request->merge(['type' => $this->normalizeStringList($request->input('type'))]);
			}

			$validated = $request->validate([
				'price_of_dish_from' => 'nullable|numeric',
				'price_of_dish_to' => 'nullable|numeric',
				'price_to_buy_from' => 'nullable|numeric',
				'price_to_buy_to' => 'nullable|numeric',
				'calories_from' => 'nullable|integer',
				'calories_to' => 'nullable|integer',
				'fats_from' => 'nullable|numeric',
				'fats_to' => 'nullable|numeric',
				'proteins_from' => 'nullable|numeric',
				'proteins_to' => 'nullable|numeric',
				'carbohydrates_from' => 'nullable|numeric',
				'carbohydrates_to' => 'nullable|numeric',
				'ration' => 'nullable|array',
				'ration.*' => 'string',
				'type' => 'nullable|array',
				'type.*' => 'string',
				'created_from' => 'nullable|date',
				'created_to' => 'nullable|date',
			]);

			if (isset($validated['price_of_dish_from'])) {
				$query->where('price_of_dish', '>=', $validated['price_of_dish_from']);
			}
			if (isset($validated['price_of_dish_to'])) {
				$query->where('price_of_dish', '<=', $validated['price_of_dish_to']);
			}
			if (isset($validated['price_to_buy_from'])) {
				$query->where('price_to_buy', '>=', $validated['price_to_buy_from']);
			}
			if (isset($validated['price_to_buy_to'])) {
				$query->where('price_to_buy', '<=', $validated['price_to_buy_to']);
			}
			if (isset($validated['calories_from'])) {
				$query->where('calories', '>=', $validated['calories_from']);
			}
			if (isset($validated['calories_to'])) {
				$query->where('calories', '<=', $validated['calories_to']);
			}
			if (isset($validated['fats_from'])) {
				$query->where('fats', '>=', $validated['fats_from']);
			}
			if (isset($validated['fats_to'])) {
				$query->where('fats', '<=', $validated['fats_to']);
			}
			if (isset($validated['proteins_from'])) {
				$query->where('proteins', '>=', $validated['proteins_from']);
			}
			if (isset($validated['proteins_to'])) {
				$query->where('proteins', '<=', $validated['proteins_to']);
			}
			if (isset($validated['carbohydrates_from'])) {
				$query->where('carbohydrates', '>=', $validated['carbohydrates_from']);
			}
			if (isset($validated['carbohydrates_to'])) {
				$query->where('carbohydrates', '<=', $validated['carbohydrates_to']);
			}
			if (!empty($validated['ration'])) {
				$query->whereIn('ration', $validated['ration']);
			}
			if (!empty($validated['type'])) {
				$query->whereIn('type', $validated['type']);
			}
			if (!empty($validated['created_from'])) {
				$query->where('created_at', '>=', Carbon::parse($validated['created_from'])->startOfDay());
			}
			if (!empty($validated['created_to'])) {
				$query->where('created_at', '<=', Carbon::parse($validated['created_to'])->endOfDay());
			}

			// Если параметр page не передан — вернуть все без пагинации
			if (!$request->has('page')) {
				$recipes = $query->get();

				return response()->json([
					'data' => $recipes,
					'code' => ApiCode::RECIPES_RETURNED->value
				], 200);
			}

			$recipes = $query->paginate(6);

			return response()->json([
				'data' => $recipes->items(),
				'pagination' => [
					'next' => $recipes->nextPageUrl(),
					'prev' => $recipes->previousPageUrl(),
					'current_page' => $recipes->currentPage(),
					'last_page' => $recipes->lastPage(),
					'per_page' => $recipes->perPage(),
					'total' => $recipes->total(),
				],
				'code' => ApiCode::RECIPES_RETURNED->value
			], 200);
    }

    public function all(Request $request)
    {
        $query = Recipe::with(['steps.ingredients', 'images', 'user'])
            ->orderBy('created_at', 'desc');

        // Фильтры
        if ($request->has('ration')) {
            $request->merge(['ration' => $this->normalizeStringList($request->input('ration'))]);
        }
        if ($request->has('type')) {
            $request->merge(['type' => $this->normalizeStringList($request->input('type'))]);
        }

        $validated = $request->validate([
            'price_of_dish_from' => 'nullable|numeric',
            'price_of_dish_to' => 'nullable|numeric',
            'price_to_buy_from' => 'nullable|numeric',
            'price_to_buy_to' => 'nullable|numeric',
            'calories_from' => 'nullable|integer',
            'calories_to' => 'nullable|integer',
            'fats_from' => 'nullable|numeric',
            'fats_to' => 'nullable|numeric',
            'proteins_from' => 'nullable|numeric',
            'proteins_to' => 'nullable|numeric',
            'carbohydrates_from' => 'nullable|numeric',
            'carbohydrates_to' => 'nullable|numeric',
            'ration' => 'nullable|array',
            'ration.*' => 'string',
            'type' => 'nullable|array',
            'type.*' => 'string',
            'created_from' => 'nullable|date',
            'created_to' => 'nullable|date',
        ]);

        if (isset($validated['price_of_dish_from'])) {
            $query->where('price_of_dish', '>=', $validated['price_of_dish_from']);
        }
        if (isset($validated['price_of_dish_to'])) {
            $query->where('price_of_dish', '<=', $validated['price_of_dish_to']);
        }
        if (isset($validated['price_to_buy_from'])) {
            $query->where('price_to_buy', '>=', $validated['price_to_buy_from']);
        }
        if (isset($validated['price_to_buy_to'])) {
            $query->where('price_to_buy', '<=', $validated['price_to_buy_to']);
        }
        if (isset($validated['calories_from'])) {
            $query->where('calories', '>=', $validated['calories_from']);
        }
        if (isset($validated['calories_to'])) {
            $query->where('calories', '<=', $validated['calories_to']);
        }
        if (isset($validated['fats_from'])) {
            $query->where('fats', '>=', $validated['fats_from']);
        }
        if (isset($validated['fats_to'])) {
            $query->where('fats', '<=', $validated['fats_to']);
        }
        if (isset($validated['proteins_from'])) {
            $query->where('proteins', '>=', $validated['proteins_from']);
        }
        if (isset($validated['proteins_to'])) {
            $query->where('proteins', '<=', $validated['proteins_to']);
        }
        if (isset($validated['carbohydrates_from'])) {
            $query->where('carbohydrates', '>=', $validated['carbohydrates_from']);
        }
        if (isset($validated['carbohydrates_to'])) {
            $query->where('carbohydrates', '<=', $validated['carbohydrates_to']);
        }
        if (!empty($validated['ration'])) {
            $query->whereIn('ration', $validated['ration']);
        }
        if (!empty($validated['type'])) {
            $query->whereIn('type', $validated['type']);	
        }
        if (!empty($validated['created_from'])) {
            $query->where('created_at', '>=', Carbon::parse($validated['created_from'])->startOfDay());
        }
        if (!empty($validated['created_to'])) {
            $query->where('created_at', '<=', Carbon::parse($validated['created_to'])->endOfDay());
        }

        if (!$request->has('page')) {
            $recipes = $query->get();

            return response()->json([
                'data' => $recipes,
                'code' => ApiCode::RECIPES_RETURNED->value
            ], 200);
        }

        $recipes = $query->paginate(6);

        return response()->json([
            'data' => $recipes->items(),
            'pagination' => [
                'next' => $recipes->nextPageUrl(),
                'prev' => $recipes->previousPageUrl(),
                'current_page' => $recipes->currentPage(),
                'last_page' => $recipes->lastPage(),
                'per_page' => $recipes->perPage(),
                'total' => $recipes->total(),
            ],
            'code' => ApiCode::RECIPES_RETURNED->value
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
	public function store(StoreRecipeRequest $request)
	{
		$data = $request->validated();
		$steps = $data['steps'] ?? [];
		unset($data['steps'], $data['images']);

		// Сначала создаём рецепт, затем прикрепляем изображения и шаги
		$recipe = Auth::user()->recipes()->create($data);

		$imagesInput = $request->input('images', []);
		$imagesFiles = $request->file('images', []);
		$imagesToCreate = [];
		
		foreach ($imagesFiles as $index => $img) {
			// Поддержка как структуры [ 'file' => UploadedFile ], так и UploadedFile напрямую
			$file = is_array($img) ? ($img['file'] ?? null) : $img;
			if (!$file) {
				continue;
			}

			$path = $file->store('recipes', 'public');

			$imagesToCreate[] = [
				'path' => $path,
				'is_main' => filter_var($imagesInput[$index]['is_main'] ?? false, FILTER_VALIDATE_BOOLEAN),
				'position' => isset($imagesInput[$index]['position']) ? (int) $imagesInput[$index]['position'] : 0,
			];
		}

		if (!empty($imagesToCreate)) {
			$recipe->images()->createMany($imagesToCreate);
		}

		if (!empty($steps)) {
			foreach ($steps as $step) {
				$stepModel = $recipe->steps()->create([
					'title' => $step['title'] ?? '',
					'description' => $step['description'] ?? null,
					'order' => $step['order'] ?? 0,
					'duration' => $step['duration'] ?? null,
				]);

				$ingredients = $step['ingredients'] ?? [];
				if (!empty($ingredients)) {
					$syncData = collect($ingredients)->mapWithKeys(function ($item) {
						return [
							$item['id'] => [
								'amount' => $item['amount'] ?? null,
							],
						];
					})->toArray();

					$stepModel->ingredients()->sync($syncData);
				}
			}
		}

		return response()->json([
			'data' => $recipe->load(['steps.ingredients', 'images', 'user']),
			'code' => ApiCode::RECIPE_CREATED->value
		], 201);
	}

	public function show(Recipe $recipe)
	{
		if ($recipe->user_id !== Auth::id()) {
			abort(404);
		}

		$recipe->load(['steps.ingredients', 'images', 'user']);
		return response()->json([
			'data' => $recipe,
			'code' => ApiCode::RECIPE_RETURNED->value
		], 200);
	}

    /**
     * Update the specified resource in storage.
     */
	public function update(UpdateRecipeRequest $request, Recipe $recipe)
	{
		if ($recipe->user_id !== Auth::id()) {
			abort(404);
		}

		$data = $request->validated();
		$steps = $data['steps'] ?? null;
		$images = $data['images'] ?? null;
		unset($data['steps'], $data['images']);

		$recipe->update($data);

		if (is_array($steps)) {
			$recipe->steps()->delete();
			foreach ($steps as $step) {
				$stepModel = $recipe->steps()->create([
					'title' => $step['title'] ?? '',
					'description' => $step['description'] ?? null,
					'order' => $step['order'] ?? 0,
					'duration' => $step['duration'] ?? null,
				]);

				$ingredients = $step['ingredients'] ?? [];
				if (!empty($ingredients)) {
					$syncData = collect($ingredients)->mapWithKeys(function ($item) {
						return [
							$item['id'] => [
								'amount' => $item['amount'] ?? null,
							],
						];
					})->toArray();

					$stepModel->ingredients()->sync($syncData);
				}
			}
		}

		if (is_array($images)) {
			$recipe->images()->delete();
			$recipe->images()->createMany($images);
		}

		return response()->json([
			'data' => $recipe->load(['steps.ingredients', 'images', 'user']),
			'code' => ApiCode::RECIPE_UPDATED->value
		], 200);
	}

    /**
     * Remove the specified resource from storage.
     */
	public function destroy(Request $request)
	{
        $recipe = Auth::user()->recipes()->with('images')->findOrFail($request->id);

        $paths = $recipe->images->pluck('path')->filter()->values()->all();
        if (!empty($paths)) {
            Storage::disk('public')->delete($paths);
        }

        $recipe->delete();

		return response()->json([
			'code' => ApiCode::RECIPE_DELETED->value
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
}
