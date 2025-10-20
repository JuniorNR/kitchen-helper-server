<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Ingredient;
use App\Models\RecipeImage;
use App\Models\User;
use App\Models\RecipeStep;

class Recipe extends Model
{
    /** @use HasFactory<\Database\Factories\RecipeFactory> */
    use HasFactory;

    protected $fillable = [
		'title',
		'description',
		'price_of_dish',
		'price_to_buy',
		'calories',
		'fats',
		'proteins',
		'carbohydrates',
		'ration',
		'type',
	];

	protected $casts = [
		'price_of_dish' => 'decimal:2',
		'price_to_buy' => 'decimal:2',
		'calories' => 'integer',
		'fats' => 'decimal:2',
		'proteins' => 'decimal:2',
		'carbohydrates' => 'decimal:2',
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}

    public function steps()
	{
		return $this->hasMany(RecipeStep::class)->orderBy('order');
	}

	public function images()
	{
		return $this->hasMany(RecipeImage::class);
	}
}
