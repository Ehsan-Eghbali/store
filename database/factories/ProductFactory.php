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
            'price' => $this->faker->randomFloat(),
            'brand_id' => $brand->id,
        ];
    }

    /**
     * After creating the product, attach random categories.
     *
     * @param Product $product
     * @return ProductFactory
     */
    public function configure(): ProductFactory
    {
        return $this->afterCreating(function (Product $product) {
            $categoryServiceRepository = app(CategoryServiceRepository::class);
            $categories = $categoryServiceRepository->inRandomOrder()->limit(rand(1, 3))->get();
            $product->categories()->attach($categories);
        });
    }
}
