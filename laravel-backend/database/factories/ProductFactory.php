<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'slug' => $this->faker->unique()->slug(),
            'short_description' => $this->faker->sentence(),
            'description' => $this->faker->paragraphs(3, true),
            'is_visible' => $this->faker->boolean(80),
            'is_featured' => $this->faker->boolean(20),
            'sort_order' => $this->faker->numberBetween(0, 100),
            'specs' => [
                'Couleur' => $this->faker->colorName(),
                'Matière' => $this->faker->randomElement(['Cuir', 'Plastique', 'Métal', 'Carbone']),
                'Garantie' => $this->faker->randomElement(['1 an', '2 ans', '3 ans']),
            ],
        ];
    }

    public function visible(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_visible' => true,
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }
}