<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\RecipeStep;

class Ingredient extends Model
{
    /** @use HasFactory<\Database\Factories\IngredientFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'price',
        'currency',
        'unit',
        'category',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function steps(): BelongsToMany
    {
        return $this->belongsToMany(RecipeStep::class, 'ingredient_recipe_step')
            ->as('usage')
            ->withPivot(['amount'])
            ->withTimestamps();
    }
}
