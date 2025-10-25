<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ingredient>
 */
class IngredientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $ingredientsCatalog = [
            ['title' => 'Помидор', 'unit' => 'piece', 'category' => 'veg-tomato'],
            ['title' => 'Огурец', 'unit' => 'piece', 'category' => 'veg-cucumber'],
            ['title' => 'Лук репчатый', 'unit' => 'piece', 'category' => 'veg-allium'],
            ['title' => 'Чеснок', 'unit' => 'clove', 'category' => 'veg-allium'],
            ['title' => 'Куриная грудка', 'unit' => 'g', 'category' => 'chicken'],
            ['title' => 'Говядина', 'unit' => 'g', 'category' => 'beef'],
            ['title' => 'Молоко', 'unit' => 'ml', 'category' => 'milk-cream'],
            ['title' => 'Сливочное масло', 'unit' => 'g', 'category' => 'fat-butter'],
            ['title' => 'Яйцо', 'unit' => 'piece', 'category' => 'eggs'],
            ['title' => 'Макароны', 'unit' => 'g', 'category' => 'pasta-italian'],
            ['title' => 'Рис', 'unit' => 'g', 'category' => 'grain-rice'],
            ['title' => 'Оливковое масло', 'unit' => 'ml', 'category' => 'oil-olive'],
            ['title' => 'Соль', 'unit' => 'g', 'category' => 'salt-iodized'],
            ['title' => 'Черный перец', 'unit' => 'g', 'category' => 'spice-ground'],
            ['title' => 'Базилик', 'unit' => 'g', 'category' => 'greens'],
            ['title' => 'Сыр пармезан', 'unit' => 'g', 'category' => 'cheese'],
            ['title' => 'Морковь', 'unit' => 'piece', 'category' => 'veg-root'],
            ['title' => 'Болгарский перец', 'unit' => 'piece', 'category' => 'veg-peppers'],
            ['title' => 'Картофель', 'unit' => 'piece', 'category' => 'veg-root'],
            ['title' => 'Шампиньоны', 'unit' => 'g', 'category' => 'mush-cultivated'],
        ];

        $adjectives = ['свежий', 'спелый', 'ароматный', 'нежный', 'хрустящий', 'домашний'];
        $entry = $this->faker->randomElement($ingredientsCatalog);

        return [
            'title' => $entry['title'],
            'description' => ucfirst($this->faker->randomElement($adjectives)) . ' ' . mb_strtolower($entry['title']) . ' для ежедневной готовки',
            'price' => $this->faker->randomFloat(2, 10, 1200),
            'currency' => 'RUB',
            'unit' => $entry['unit'],
            'category' => $entry['category'],
        ];
    }
}
