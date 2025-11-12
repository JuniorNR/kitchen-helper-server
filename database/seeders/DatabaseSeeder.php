<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\RecipeStep;
// use App\Models\RecipeImage;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Пользователь для авторизации
        $user = User::updateOrCreate(
            ['email' => 'kitchenHelper@mail.ru'],
            [
                'name' => 'Kitchen Helper',
                'password' => 'kitchenHelper',
                'email_verified_at' => now(),
            ]
        );

        // уникальные ингредиенты пользователя (без дублей), категории только из разрешённого списка
        $ingredientPool = [
            ['title' => 'Помидор', 'unit' => 'piece', 'category' => 'veg-tomato'],
            ['title' => 'Огурец', 'unit' => 'piece', 'category' => 'veg-cucumber'],
            ['title' => 'Лук репчатый', 'unit' => 'piece', 'category' => 'veg-allium'],
            ['title' => 'Чеснок', 'unit' => 'clove', 'category' => 'veg-allium'],
            ['title' => 'Куриная грудка', 'unit' => 'g', 'category' => 'chicken'],
            ['title' => 'Говядина', 'unit' => 'g', 'category' => 'beef'],
            ['title' => 'Свинина', 'unit' => 'g', 'category' => 'pork'],
            ['title' => 'Лосось', 'unit' => 'g', 'category' => 'fish-fatty'],
            ['title' => 'Молоко', 'unit' => 'ml', 'category' => 'milk-cream'],
            ['title' => 'Сливочное масло', 'unit' => 'g', 'category' => 'fat-butter'],
            ['title' => 'Оливковое масло', 'unit' => 'ml', 'category' => 'oil-olive'],
            ['title' => 'Соль', 'unit' => 'g', 'category' => 'salt-iodized'],
            ['title' => 'Черный перец', 'unit' => 'g', 'category' => 'spice-ground'],
            ['title' => 'Базилик', 'unit' => 'g', 'category' => 'greens'],
            ['title' => 'Петрушка', 'unit' => 'g', 'category' => 'greens'],
            ['title' => 'Укроп', 'unit' => 'g', 'category' => 'greens'],
            ['title' => 'Сыр пармезан', 'unit' => 'g', 'category' => 'cheese'],
            ['title' => 'Моцарелла', 'unit' => 'g', 'category' => 'cheese'],
            ['title' => 'Макароны', 'unit' => 'g', 'category' => 'pasta-italian'],
            ['title' => 'Рис', 'unit' => 'g', 'category' => 'grain-rice'],
            ['title' => 'Киноа', 'unit' => 'g', 'category' => 'grain-quinoa'],
            ['title' => 'Гречка', 'unit' => 'g', 'category' => 'grain-buckwheat'],
            ['title' => 'Морковь', 'unit' => 'piece', 'category' => 'veg-root'],
            ['title' => 'Болгарский перец', 'unit' => 'piece', 'category' => 'veg-peppers'],
            ['title' => 'Картофель', 'unit' => 'piece', 'category' => 'veg-root'],
            ['title' => 'Шампиньоны', 'unit' => 'g', 'category' => 'mush-cultivated'],
            ['title' => 'Цуккини', 'unit' => 'piece', 'category' => 'veg-gourd'],
            ['title' => 'Баклажан', 'unit' => 'piece', 'category' => 'veg-gourd'],
            ['title' => 'Лимон', 'unit' => 'piece', 'category' => 'fruit-citrus'],
            ['title' => 'Помидоры черри', 'unit' => 'g', 'category' => 'veg-tomato'],
        ];

        $selectedIngredientEntries = collect($ingredientPool)->take(20);
        $ingredients = collect();
        foreach ($selectedIngredientEntries as $entry) {
            $ingredients->push(
                Ingredient::factory()
                    ->for($user)
                    ->state([
                        'title' => $entry['title'],
                        'unit' => $entry['unit'],
                        'category' => $entry['category'],
                    ])
                    ->create()
            );
        }

        // 15 рецептов пользователя с уникальными названиями
        $recipePool = [
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
            ['title' => 'Плов узбекский', 'type' => 'main'],
            ['title' => 'Чили кон карне', 'type' => 'main'],
            ['title' => 'Сырники', 'type' => 'dessert'],
            ['title' => 'Котлеты по-киевски', 'type' => 'main'],
            ['title' => 'Греческий салат', 'type' => 'salad'],
            ['title' => 'Тыквенный крем-суп', 'type' => 'soup'],
            ['title' => 'Фахитас с курицей', 'type' => 'main'],
            ['title' => 'Терияки лосось', 'type' => 'main'],
            ['title' => 'Рамен куриный', 'type' => 'soup'],
            ['title' => 'Овощное рагу с цукини', 'type' => 'main'],
        ];

        $selectedRecipes = collect($recipePool)->take(15);
        $recipes = collect();
        foreach ($selectedRecipes as $rec) {
            $recipes->push(
                Recipe::factory()
                    ->for($user)
                    ->state([
                        'title' => $rec['title'],
                        'type' => $rec['type'],
                    ])
                    ->create()
            );
        }

        // для каждого рецепта генерируем 10–30 подробных шагов с инструкциями по категории основного ингредиента
        foreach ($recipes as $recipe) {
            $stepsCount = rand(10, 30);
            for ($i = 1; $i <= $stepsCount; $i++) {
                $stepIngredients = $ingredients->random(rand(1, 3))->values();
                $ingredientNames = $stepIngredients->pluck('title')->all();
                $ingredientsList = implode(', ', $ingredientNames);
                $primary = $stepIngredients->first();
                $primaryName = $primary->title;
                $category = $primary->category;

                // нормализация категории в группу
                $group = match (true) {
                    str_starts_with($category, 'veg-allium') => 'allium',
                    in_array($category, ['veg-root','veg-peppers','veg-tomato','veg-cucumber','veg-gourd']) => 'vegetable',
                    $category === 'greens' => 'greens',
                    in_array($category, ['grain-rice','grain-quinoa','grain-buckwheat']) => 'grain',
                    $category === 'pasta-italian' => 'pasta',
                    in_array($category, ['oil-olive','fat-butter']) => 'fat',
                    in_array($category, ['salt-iodized','spice-ground']) => 'seasoning',
                    in_array($category, ['cheese','milk-cream']) => 'dairy',
                    in_array($category, ['chicken','beef','pork']) => 'meat',
                    $category === 'fish-fatty' => 'fish',
                    $category === 'mush-cultivated' => 'mushroom',
                    $category === 'fruit-citrus' => 'citrus',
                    default => 'generic',
                };

                // шаблоны по группам
                $templates = [
                    'allium' => [
                        ['title' => 'Мелко нашинковать: :one', 'desc' => 'Очистите :one. Разрежьте пополам, срежьте корешок. Нашинкуйте поперёк тонкими срезами 2–3 мм, затем пройдитесь ножом крест-накрест до мелкой крошки. Соберите скребком, не тяните остриём.'],
                        ['title' => 'Пассировать на слабом огне: :one', 'desc' => 'Разогрейте 1 ст. л. масла на слабом огне. Добавьте :one, щепотку соли. Готовьте, помешивая, до мягкости и сладкого аромата, не допуская подрумянивания (5–7 минут).'],
                    ],
                    'vegetable' => [
                        ['title' => 'Нарезать кубиком: :one', 'desc' => 'Вымойте и обсушите :one. Снимите кожицу при необходимости. Нарежьте пластины, затем брусочки и мелкий кубик 5–7 мм. Старайтесь держать одинаковый размер для равномерной готовности.'],
                        ['title' => 'Нарезать соломкой (жюльен): :one', 'desc' => 'Удалите семена/сердцевину у :one (если есть). Нарежьте ровными пластинами 3–4 мм, затем соломкой. Поддерживайте параллельность для аккуратной текстуры.'],
                    ],
                    'greens' => [
                        ['title' => 'Шифонад (тонкие ленты): :one', 'desc' => 'Сложите листья :one стопкой, сверните рулоном и нашинкуйте тонкими лентами 1–2 мм плавными движениями ножа. Не давите — чтобы зелень не пустила сок.'],
                        ['title' => 'Грубая рубка: :one', 'desc' => 'Соберите пучок :one в кучку и порубите ножом до среднего размера. Работайте быстро, чтобы не измельчить слишком мелко.'],
                    ],
                    'grain' => [
                        ['title' => 'Промыть крупу: :one', 'desc' => 'Пересыпьте :one в сито и промывайте под холодной водой 30–60 секунд, пока стекающая вода не станет прозрачной. Дайте стечь 2–3 минуты.'],
                        ['title' => 'Отварить до готовности: :one', 'desc' => 'Доведите до кипения воду (соотношение 1:2 по объёму). Посолите 8–10 г на литр. Всыпьте :one, убавьте огонь и варите под крышкой до готовности, не перестаивая мешать.'],
                    ],
                    'pasta' => [
                        ['title' => 'Сварить пасту аль денте: :one', 'desc' => 'Кипящая вода, 10 г соли на литр. Опустите :one, варите до состояния аль денте по инструкции минус 1 минуту. Сохраните стакан воды от варки.'],
                    ],
                    'fat' => [
                        ['title' => 'Разогреть жир: :one', 'desc' => 'Поставьте сковороду на средний огонь и прогрейте 1–2 ст. л. :one до лёгкого мерцания. Не перегревайте, чтобы не перегорел вкус.'],
                    ],
                    'seasoning' => [
                        ['title' => 'Приправить по вкусу: :one', 'desc' => 'Добавьте щепотку :one, затем пробуйте. Регулируйте соль/остроту небольшими порциями, чтобы не пересолить. В конце подправьте баланс кислотности/соли/остроты.'],
                    ],
                    'dairy' => [
                        ['title' => 'Натереть/подготовить: :one', 'desc' => 'Если :one — твёрдый сыр, натрите на мелкой тёрке. Если молочный продукт — прогрейте до тёплого состояния, не доводя до кипения, чтобы не свернулся.'],
                    ],
                    'meat' => [
                        ['title' => 'Обсушить и обжарить: :one', 'desc' => 'Обсушите :one бумажными полотенцами. Посолите, поперчите. На хорошо разогретом масле обжарьте до сильной румяной корочки с каждой стороны, затем доведите до готовности на слабом огне.'],
                    ],
                    'fish' => [
                        ['title' => 'Подготовить и обжарить: :one', 'desc' => 'Удалите кости у :one при необходимости. Обсушите, посолите. Жарьте на среднем огне в капле масла 2–3 минуты с каждой стороны до золотистой корочки, не пересушивая.'],
                    ],
                    'mushroom' => [
                        ['title' => 'Очистить и нарезать: :one', 'desc' => 'Счищайте грязь со шляпок :one мягкой щёткой, при необходимости протрите влажным полотенцем. Нарежьте пластинами 3–5 мм.'],
                        ['title' => 'Обжарить до испарения влаги: :one', 'desc' => 'На сухой горячей сковороде прогрейте :one 2–3 минуты, затем добавьте масло и жарьте до золотистого цвета, выпаривая влагу.'],
                    ],
                    'citrus' => [
                        ['title' => 'Снять цедру и выжать сок: :one', 'desc' => 'Снимите тонкую цедру с :one мелкой тёркой, избегая белой кожи. Разрежьте и выжмите сок через сито, удалив косточки.'],
                    ],
                    'generic' => [
                        ['title' => 'Подготовить: :one', 'desc' => 'Аккуратно подготовьте :one к использованию: промойте, обсушите и нарежьте удобным способом.'],
                        ['title' => 'Смешать ингредиенты: :list', 'desc' => 'Соедините :list в миске, добавьте щепотку соли и каплю масла. Перемешайте до равномерного распределения.'],
                    ],
                ];

                $options = $templates[$group] ?? $templates['generic'];
                $choice = $options[array_rand($options)];
                $title = str_replace([':one', ':list'], [$primaryName, $ingredientsList], $choice['title']);

                $baseDesc = str_replace([':one', ':list'], [$primaryName, $ingredientsList], $choice['desc']);

                // рассчитать количества для привязки и добавить подсказки по граммовке
                $attach = [];
                $usageLines = [];
                foreach ($stepIngredients as $ing) {
                    $unit = $ing->unit;
                    if ($unit === 'g') {
                        $amount = fake()->randomFloat(2, 10, 300);
                        $usageLines[] = $ing->title . ': ' . $amount . ' г';
                    } elseif ($unit === 'ml') {
                        $amount = fake()->randomFloat(2, 10, 200);
                        $usageLines[] = $ing->title . ': ' . $amount . ' мл';
                    } elseif ($unit === 'piece') {
                        $amount = fake()->numberBetween(1, 3);
                        $usageLines[] = $ing->title . ': ' . $amount . ' шт';
                    } elseif ($unit === 'clove') {
                        $amount = fake()->numberBetween(1, 3);
                        $usageLines[] = $ing->title . ': ' . $amount . ' зуб.';
                    } else {
                        $amount = fake()->randomFloat(2, 5, 100);
                        $usageLines[] = $ing->title . ': ' . $amount . ' ' . $unit;
                    }
                    $attach[$ing->id] = ['amount' => $amount];
                }

                $desc = $baseDesc . ' Рекомендуемые количества: ' . implode('; ', $usageLines) . '. Работайте острым ножом, держите доску сухой, следите за равномерностью нарезки.';

                $step = RecipeStep::create([
                    'recipe_id' => $recipe->id,
                    'title' => $title,
                    'description' => $desc,
                    'duration' => fake()->randomElement(['5m', '7m', '10m', '12m', '15m']),
                    'order' => $i,
                ]);

                $step->ingredients()->attach($attach);
            }

            // изображения рецепта отключены
        }
    }
}
