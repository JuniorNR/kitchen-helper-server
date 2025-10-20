<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('price_of_dish', 10, 2)->default(0);
            $table->decimal('price_to_buy', 10, 2)->default(0);
            $table->unsignedInteger('calories')->default(0);
            $table->decimal('fats', 6, 2)->default(0);
            $table->decimal('proteins', 6, 2)->default(0);
            $table->decimal('carbohydrates', 6, 2)->default(0);
            $table->string('ration')->default('breakfast');
            $table->string('type')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
