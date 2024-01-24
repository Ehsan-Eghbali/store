<?php

namespace Database\Factories;

use App\Services\Category\CategoryServiceRepository;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categoryServiceRepository = app(CategoryServiceRepository::class);
        $parentCategories = $categoryServiceRepository->all()->pluck('id')->toArray();
        $parentCategories[] = null;
        return [
            'name'=>$this->faker->word,
            'parent_id'=>$this->faker->randomElement($parentCategories),
        ];
    }
}
