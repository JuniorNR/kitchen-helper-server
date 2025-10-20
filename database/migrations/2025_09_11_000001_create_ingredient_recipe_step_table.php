<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Ingredient;
use App\Models\RecipeStep;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ingredient_recipe_step', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(RecipeStep::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Ingredient::class)->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2)->nullable();
            $table->unique(['recipe_step_id', 'ingredient_id'], 'ingredient_recipe_step_unique');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredient_recipe_step');
    }
};





