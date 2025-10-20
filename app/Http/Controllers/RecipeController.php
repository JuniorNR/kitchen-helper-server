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


class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
			$recipes = Auth::user()
			->recipes()
			->with(['steps.ingredients', 'images', 'user'])->get();

			return response()->json([
				'data' => $recipes,
				'code' => ApiCode::RECIPES_RETURNED->value
			], 200);
    }

    public function all()
    {
        $recipes = Recipe::with(['steps.ingredients', 'images', 'user'])->get();
        return response()->json([
            'data' => $recipes,
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
}
