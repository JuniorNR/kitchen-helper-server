<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Recipe;

class RecipeImage extends Model
{
    /** @use HasFactory<\Database\Factories\RecipeImageFactory> */
    use HasFactory;

    protected $fillable = ['recipe_id', 'path', 'is_main', 'position'];

	public function recipe()
	{
		return $this->belongsTo(Recipe::class);
	}
}
