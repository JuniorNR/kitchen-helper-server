<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Recipe>
 */
class RecipeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $recipesCatalog = [
            ['title' => 'Паста Карбонара', 'type' => 'main'],
            ['title' => 'Куриное карри', 'type' => 'main'],
            ['title' => 'Салат Цезарь', 'type' => 'salad'],
            ['title' => 'Борщ классический', 'type' => 'soup'],
            ['title' => 'Ризотто с грибами', 'type' => 'main'],
            ['title' => 'Оладьи банановые', 'type' => 'dessert'],
            ['title' => 'Стейк с овощами', 'type' => 'main'],
            ['title' => 'Суп том-ям', 'type' => 'soup'],
            ['title' => 'Лазанья классическая', 'type' => 'main'],
            ['title' => 'Шакшука', 'type' => 'main'],
        ];

        $entry = $this->faker->randomElement($recipesCatalog);
        $descStarts = [
            'Пошаговый рецепт: ',
            'Подробная инструкция приготовления: ',
            'Как приготовить блюдо: ',
        ];

        return [
            'title' => $entry['title'],
            'description' => $this->faker->randomElement($descStarts) . $entry['title'] . '.',
            'price_of_dish' => $this->faker->randomFloat(2, 100, 2500),
            'price_to_buy' => $this->faker->randomFloat(2, 80, 2000),
            'calories' => $this->faker->numberBetween(150, 1200),
            'fats' => $this->faker->randomFloat(2, 5, 80),
            'proteins' => $this->faker->randomFloat(2, 5, 90),
            'carbohydrates' => $this->faker->randomFloat(2, 5, 140),
            'ration' => $this->faker->randomElement(['breakfast', 'lunch', 'dinner']),
            'type' => $entry['type'],
        ];
    }
}
