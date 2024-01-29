<?php

namespace Database\Factories;

use App\Services\Brand\BrandServiceRepository;
use App\Models\Product;
use App\Services\Category\CategoryServiceRepository;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $brandServiceRepository = app(BrandServiceRepository::class);
        $brand = $brandServiceRepository->inRandomOrder()->first();

        return [
            'name' => $this->faker->word,
            'price' => $this->faker->randomFloat(2),
            'brand_id' => $brand->id,
            'count'=>rand(1,50),
        ];
    }

    /**
     * After creating the product, attach random categories.
     *
     *
     * @return ProductFactory
     */
    public function configure(): ProductFactory
    {
        return $this->afterCreating(function (Product $product) {
            $categoryServiceRepository = app(CategoryServiceRepository::class);
            $categories = $categoryServiceRepository->inRandomOrder(rand(1, 5));
            $product->categories()->attach($categories);
        });
    }
}
