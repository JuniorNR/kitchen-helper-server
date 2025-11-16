<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use App\Models\User;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\RecipeStep;

class RealisticRecipesSeeder extends Seeder
{
	/**
	 * Seed deterministic ingredients and recipes for 5 specific dishes.
	 */
	public function run(): void
	{
		$user = User::updateOrCreate(
			['email' => 'kitchenHelper@mail.ru'],
			[
				'name' => 'Kitchen Helper',
				'password' => 'kitchenHelper',
				'email_verified_at' => now(),
			]
		);

		$catalog = $this->buildIngredientCatalog();
		$ingredientByTitle = [];

		// Create/update all ingredients once per user
		foreach ($catalog as $title => $data) {
			$ingredientByTitle[$title] = $user->ingredients()->updateOrCreate(
				['title' => $title],
				[
					'description' => $data['description'],
					'price' => $data['price'],
					'currency' => 'RUB',
					'unit' => $data['unit'],
					'category' => $data['category'],
				]
			);
		}

		// Helper to fetch ingredient model by title
		$get = function (string $title) use ($ingredientByTitle): Ingredient {
			return $ingredientByTitle[$title];
		};

		// 1) Пельмени по‑домашнему
		$pelmeni = $user->recipes()->updateOrCreate(
			['title' => 'Пельмени по‑домашнему'],
			[
				'description' => 'Классические пельмени с тонким тестом и сочной начинкой из говядины и свинины.',
				'type' => 'dumplings',
				'ration' => 'dinner',
				'price_of_dish' => 0,
				'price_to_buy' => 0,
				'calories' => 0,
				'fats' => 0,
				'proteins' => 0,
				'carbohydrates' => 0,
			]
		);
		$this->resetSteps($pelmeni);
		$this->makeStep($pelmeni, 1, 'Замесить тесто',
			'Соедините муку, воду, яйцо и соль. Замесите гладкое эластичное тесто, накройте и дайте отдохнуть 20 минут.',
			[
				['Мука пшеничная', 400],
				['Вода', 160],
				['Яйцо куриное', 1],
				['Соль', 5],
			],
			'20m'
		);
		$this->makeStep($pelmeni, 2, 'Подготовить начинку',
			'Смешайте говядину и свинину, мелко рубленый лук, воду, соль и перец. Вымешайте до лёгкой вязкости.',
			[
				['Говядина (фарш)', 250],
				['Свинина (фарш)', 250],
				['Лук репчатый', 1],
				['Вода', 30],
				['Соль', 5],
				['Перец чёрный молотый', 1],
			],
			'10m'
		);
		$this->makeStep($pelmeni, 3, 'Лепка пельменей',
			'Раскатайте тесто в пласт, вырежьте кружки, выложите начинку и защипните края.',
			[],
			'20m'
		);
		$this->makeStep($pelmeni, 4, 'Варка',
			'Закипятите воду, посолите, добавьте лавровый лист. Опустите пельмени, варите после всплытия 4–5 минут.',
			[
				['Вода', 2000],
				['Соль', 20],
				['Лавровый лист', 2],
			],
			'10m'
		);
		$this->makeStep($pelmeni, 5, 'Подача',
			'Подавайте со сливочным маслом. По желанию — сметана.',
			[
				['Масло сливочное', 20],
			],
			'1m'
		);

		// 2) Суп харчо
		$kharcho = $user->recipes()->updateOrCreate(
			['title' => 'Суп харчо'],
			[
				'description' => 'Насыщенный кавказский суп на говяжьем бульоне с рисом, орехами и хмели‑сунели.',
				'type' => 'soup',
				'ration' => 'dinner',
				'price_of_dish' => 0,
				'price_to_buy' => 0,
				'calories' => 0,
				'fats' => 0,
				'proteins' => 0,
				'carbohydrates' => 0,
			]
		);
		$this->resetSteps($kharcho);
		$this->makeStep($kharcho, 1, 'Сварить бульон',
			'Залейте говядину холодной водой, доведите до кипения, снимите пену. Посолите, добавьте лавровый лист, варите до мягкости мяса.',
			[
				['Говядина на кости', 600],
				['Вода', 2500],
				['Соль', 15],
				['Лавровый лист', 2],
			],
			'90m'
		);
		$this->makeStep($kharcho, 2, 'Ароматная зажарка',
			'Обжарьте лук до прозрачности, добавьте чеснок, томатную пасту, аджику, кориандр и хмели‑сунели. Прогрейте до аромата.',
			[
				['Лук репчатый', 2],
				['Чеснок', 3],
				['Томатная паста', 70],
				['Аджика', 10],
				['Кориандр молотый', 3],
				['Хмели‑сунели', 5],
				['Масло оливковое', 15],
				['Перец чёрный молотый', 2],
			],
			'12m'
		);
		$this->makeStep($kharcho, 3, 'Сборка и варка с рисом',
			'Перелейте зажарку в бульон, добавьте рис. Варите до готовности риса.',
			[
				['Рис', 100],
				['Соль', 5],
			],
			'20m'
		);
		$this->makeStep($kharcho, 4, 'Финиш с орехами и зеленью',
			'Добавьте рубленные грецкие орехи и кинзу. Дайте настояться под крышкой 10 минут.',
			[
				['Грецкий орех', 80],
				['Кинза', 20],
			],
			'10m'
		);

		// 3) Салат «Цезарь»
		$caesar = $user->recipes()->updateOrCreate(
			['title' => 'Салат Цезарь'],
			[
				'description' => 'Классический салат с курицей, хрустящими гренками и соусом на основе яйца, лимона и оливкового масла.',
				'type' => 'salad',
				'ration' => 'lunch',
				'price_of_dish' => 0,
				'price_to_buy' => 0,
				'calories' => 0,
				'fats' => 0,
				'proteins' => 0,
				'carbohydrates' => 0,
			]
		);
		$this->resetSteps($caesar);
		$this->makeStep($caesar, 1, 'Курица для салата',
			'Приправьте куриную грудку солью и перцем, обжарьте на оливковом масле до готовности. Остудите и нарежьте.',
			[
				['Куриная грудка', 200],
				['Соль', 3],
				['Перец чёрный молотый', 1],
				['Масло оливковое', 10],
			],
			'12m'
		);
		$this->makeStep($caesar, 2, 'Гренки',
			'Смешайте кубики хлеба с чесноком и оливковым маслом, подсушите до золотистого цвета.',
			[
				['Хлеб пшеничный', 100],
				['Чеснок', 1],
				['Масло оливковое', 15],
				['Соль', 2],
			],
			'10m'
		);
		$this->makeStep($caesar, 3, 'Соус «Цезарь»',
			'Соедините яйцо, сок лимона, горчицу, вустершир, анчоусы и чеснок. Тонкой струёй введите оливковое масло до эмульсии. Приправьте.',
			[
				['Яйцо куриное', 1],
				['Лимон', 0.5],
				['Горчица дижонская', 5],
				['Соус вустерширский', 5],
				['Филе анчоусов', 10],
				['Чеснок', 1],
				['Масло оливковое', 60],
				['Соль', 2],
				['Перец чёрный молотый', 1],
			],
			'8m'
		);
		$this->makeStep($caesar, 4, 'Сборка салата',
			'Смешайте листья романо с соусом, добавьте курицу, гренки и пармезан.',
			[
				['Салат романо', 200],
				['Куриная грудка', 200],
				['Гренки пшеничные', 80],
				['Сыр пармезан', 40],
			],
			'4m'
		);

		// 4) Напиток «Джонни Сильверхенд»
		$silverhand = $user->recipes()->updateOrCreate(
			['title' => 'Джонни Сильверхенд'],
			[
				'description' => 'Освежающий лонг‑дринк на текиле с имбирным пивом, соком лайма и острым краем из соли и чили.',
				'type' => 'cocktail-mocktail',
				'ration' => 'dinner',
				'price_of_dish' => 0,
				'price_to_buy' => 0,
				'calories' => 0,
				'fats' => 0,
				'proteins' => 0,
				'carbohydrates' => 0,
			]
		);
		$this->resetSteps($silverhand);
		$this->makeStep($silverhand, 1, 'Подготовить бокал',
			'Сделайте солоно‑чилийную кромку: пройдитесь долькой лайма по краю и обмакните в смесь соли и молотого чили.',
			[
				['Лайм', 0.25],
				['Соль', 1],
				['Чили молотый', 1],
			],
			'1m'
		);
		$this->makeStep($silverhand, 2, 'Собрать коктейль',
			'В бокал на лёд влейте текилу и сок лайма, дополните имбирным пивом. Аккуратно перемешайте.',
			[
				['Текила', 50],
				['Сок лайма', 15],
				['Имбирное пиво', 120],
				['Лёд', 150],
			],
			'1m'
		);

		// 5) Американский пирог (шарлотка)
		$charlotte = $user->recipes()->updateOrCreate(
			['title' => 'Американский пирог (шарлотка)'],
			[
				'description' => 'Воздушная яблочная шарлотка с корицей: много сочных яблок и нежный ванильный бисквит.',
				'type' => 'pie-sweet',
				'ration' => 'breakfast',
				'price_of_dish' => 0,
				'price_to_buy' => 0,
				'calories' => 0,
				'fats' => 0,
				'proteins' => 0,
				'carbohydrates' => 0,
			]
		);
		$this->resetSteps($charlotte);
		$this->makeStep($charlotte, 1, 'Подготовить яблоки',
			'Очистите и нарежьте яблоки дольками, смешайте с сахаром, корицей и соком лимона.',
			[
				['Яблоки', 600],
				['Сахар', 20],
				['Корица молотая', 4],
				['Лимон', 0.5],
			],
			'8m'
		);
		$this->makeStep($charlotte, 2, 'Тесто',
			'Взбейте яйца с сахаром до пышности, аккуратно вмешайте муку с разрыхлителем и щепоткой соли.',
			[
				['Яйцо куриное', 3],
				['Сахар', 120],
				['Мука пшеничная', 180],
				['Разрыхлитель теста', 8],
				['Соль', 2],
			],
			'10m'
		);
		$this->makeStep($charlotte, 3, 'Форма и выпечка',
			'Смажьте форму маслом, выложите яблоки и залейте тестом. Выпекайте при 180°C до пробы «сухая шпажка».',
			[
				['Масло сливочное', 20],
			],
			'45m'
		);
	}

	private function resetSteps(Recipe $recipe): void
	{
		// Remove existing steps and their pivot links to avoid duplicates on re-seed
		foreach ($recipe->steps as $step) {
			$step->ingredients()->detach();
			$step->delete();
		}
	}

	/**
	 * Create a step with attached ingredients and amounts.
	 *
	 * @param array<int, array{0:string,1:float|int}> $ingredientsWithAmounts
	 */
	private function makeStep(Recipe $recipe, int $order, string $title, string $description, array $ingredientsWithAmounts, ?string $duration = null): RecipeStep
	{
		$step = RecipeStep::create([
			'recipe_id' => $recipe->id,
			'title' => $title,
			'description' => $description,
			'duration' => $duration,
			'order' => $order,
		]);

		$attach = [];
		foreach ($ingredientsWithAmounts as [$title, $amount]) {
			/** @var Ingredient $ingredient */
			$ingredient = Ingredient::where('title', $title)->where('user_id', $recipe->user_id)->firstOrFail();
			$attach[$ingredient->id] = ['amount' => $amount];
		}
		if (!empty($attach)) {
			$step->ingredients()->attach($attach);
		}
		return $step;
	}

	/**
	 * Deterministic ingredient catalog: title => {unit, category, price, description}
	 *
	 * Price is per unit of ingredient's unit (g/ml/piece/clove).
	 *
	 * @return array<string, array{unit:string,category:string,price:float,description:string}>
	 */
	private function buildIngredientCatalog(): array
	{
		return [
			// базовые
			'Соль' => ['unit' => 'g', 'category' => 'salt-iodized', 'price' => 0.03, 'description' => 'Поваренная соль мелкого помола для приправления блюд.'],
			'Перец чёрный молотый' => ['unit' => 'g', 'category' => 'spice-ground', 'price' => 0.80, 'description' => 'Молотый чёрный перец для аромата и острой нотки.'],
			'Лавровый лист' => ['unit' => 'piece', 'category' => 'spice-leaf', 'price' => 1.50, 'description' => 'Сушёный лавровый лист для бульонов и соусов.'],
			'Масло оливковое' => ['unit' => 'ml', 'category' => 'oil-olive', 'price' => 0.50, 'description' => 'Оливковое масло для жарки и соусов.'],
			'Масло сливочное' => ['unit' => 'g', 'category' => 'fat-butter', 'price' => 1.20, 'description' => 'Сливочное масло для подачи и выпечки.'],
			'Вода' => ['unit' => 'ml', 'category' => 'liquid-water', 'price' => 0.00, 'description' => 'Питьевая вода.'],

			// овощи, зелень
			'Лук репчатый' => ['unit' => 'piece', 'category' => 'veg-allium', 'price' => 8.00, 'description' => 'Сочный репчатый лук среднего размера.'],
			'Чеснок' => ['unit' => 'clove', 'category' => 'veg-allium', 'price' => 3.00, 'description' => 'Зубчики чеснока с ярким ароматом.'],
			'Кинза' => ['unit' => 'g', 'category' => 'greens', 'price' => 1.20, 'description' => 'Свежая кинза для финишной ароматизации.'],
			'Салат романо' => ['unit' => 'g', 'category' => 'greens', 'price' => 0.35, 'description' => 'Листья салата романо, хрустящие и сочные.'],
			'Лимон' => ['unit' => 'piece', 'category' => 'fruit-citrus', 'price' => 20.00, 'description' => 'Свежий лимон с яркой кислотностью.'],
			'Лайм' => ['unit' => 'piece', 'category' => 'fruit-citrus', 'price' => 22.00, 'description' => 'Ароматный лайм для напитков.'],

			// мясо
			'Говядина (фарш)' => ['unit' => 'g', 'category' => 'beef', 'price' => 0.85, 'description' => 'Говяжий фарш средней жирности.'],
			'Свинина (фарш)' => ['unit' => 'g', 'category' => 'pork', 'price' => 0.75, 'description' => 'Свиной фарш для сочности начинки.'],
			'Говядина на кости' => ['unit' => 'g', 'category' => 'beef', 'price' => 0.80, 'description' => 'Говядина на кости для наваристого бульона.'],
			'Куриная грудка' => ['unit' => 'g', 'category' => 'chicken', 'price' => 0.60, 'description' => 'Филе куриной грудки без кожи и костей.'],

			// крупы, мука, хлеб
			'Мука пшеничная' => ['unit' => 'g', 'category' => 'grain-flour', 'price' => 0.07, 'description' => 'Пшеничная мука высшего сорта для теста и выпечки.'],
			'Рис' => ['unit' => 'g', 'category' => 'grain-rice', 'price' => 0.12, 'description' => 'Круглый рис для супов и каш.'],
			'Хлеб пшеничный' => ['unit' => 'g', 'category' => 'bakery-bread', 'price' => 0.20, 'description' => 'Пшеничный хлеб для гренок.'],
			'Гренки пшеничные' => ['unit' => 'g', 'category' => 'bakery-croutons', 'price' => 0.30, 'description' => 'Домашние гренки с чесноком.'],

			// молочка/сыры/яйца
			'Сыр пармезан' => ['unit' => 'g', 'category' => 'cheese', 'price' => 1.80, 'description' => 'Выдержанный пармезан для посыпки.'],
			'Яйцо куриное' => ['unit' => 'piece', 'category' => 'eggs', 'price' => 10.00, 'description' => 'Куриные яйца категории С1.'],

			// томаты, орехи, специи
			'Томатная паста' => ['unit' => 'g', 'category' => 'veg-tomato', 'price' => 0.25, 'description' => 'Концентрированная томатная паста.'],
			'Грецкий орех' => ['unit' => 'g', 'category' => 'nut-walnut', 'price' => 1.50, 'description' => 'Очищенные грецкие орехи.'],
			'Аджика' => ['unit' => 'g', 'category' => 'condiment-hot', 'price' => 0.50, 'description' => 'Острая паста аджика.'],
			'Кориандр молотый' => ['unit' => 'g', 'category' => 'spice-ground', 'price' => 0.40, 'description' => 'Молотый кориандр с цитрусовым ароматом.'],
			'Хмели‑сунели' => ['unit' => 'g', 'category' => 'spice-blend', 'price' => 0.60, 'description' => 'Кавказская смесь специй.'],

			// Цезарь — соус/компоненты
			'Горчица дижонская' => ['unit' => 'g', 'category' => 'condiment-mustard', 'price' => 0.50, 'description' => 'Острая дижонская горчица.'],
			'Соус вустерширский' => ['unit' => 'ml', 'category' => 'condiment-worcestershire', 'price' => 0.90, 'description' => 'Вустершир для умами‑акцента.'],
			'Филе анчоусов' => ['unit' => 'g', 'category' => 'fish-preserved', 'price' => 2.50, 'description' => 'Солёные анчоусы в масле.'],

			// Напиток
			'Текила' => ['unit' => 'ml', 'category' => 'spirit-tequila', 'price' => 2.00, 'description' => 'Серебряная текила для коктейлей.'],
			'Сок лайма' => ['unit' => 'ml', 'category' => 'juice-citrus', 'price' => 0.60, 'description' => 'Свежевыжатый сок лайма.'],
			'Имбирное пиво' => ['unit' => 'ml', 'category' => 'soda-ginger-beer', 'price' => 0.20, 'description' => 'Газированный напиток с имбирём.'],
			'Чили молотый' => ['unit' => 'g', 'category' => 'spice-ground', 'price' => 0.70, 'description' => 'Острый молотый чили.'],
			'Лёд' => ['unit' => 'g', 'category' => 'ice', 'price' => 0.00, 'description' => 'Пищевой лёд.'],

			// Десерт — шарлотка
			'Яблоки' => ['unit' => 'g', 'category' => 'fruit-apple', 'price' => 0.20, 'description' => 'Сочные кислосладкие яблоки.'],
			'Сахар' => ['unit' => 'g', 'category' => 'sugar', 'price' => 0.03, 'description' => 'Белый сахар‑песок.'],
			'Разрыхлитель теста' => ['unit' => 'g', 'category' => 'leaven-baking-powder', 'price' => 0.30, 'description' => 'Разрыхлитель для пышности выпечки.'],
			'Корица молотая' => ['unit' => 'g', 'category' => 'spice-ground', 'price' => 0.60, 'description' => 'Ароматная молотая корица.'],
		];
	}
}


