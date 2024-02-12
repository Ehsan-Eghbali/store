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
        $product = [

            'مانتو',
            'سارافون',
            'روبان',
            'روسری',
            'چادر',
            'کلاه',
            'کاپشن',
            'پالتو',
            'کیف',
            'کفش',
            'بوت',
            'کت',
            'پیراهن',
            'شلوار',
            'لباس زیر',
            'تی‌شرت',
            'جوراب',
            'دامن',
            'توپ',
            'شال',
            'روسری',
            'شال‌گردن',
            'ساعت',
            'عینک',
            'گوشواره',
            'گردنبند',
            'انگشتر',
            'زیورآلات',
            'کمربند',
            'پیراهن',
            'کراوات',
            'کلاه‌شنی',
            'چرم',
            'کلاه بارانی',
            'کلاه گرمسیری',
            'ساک دستی',
            'کلاه بیسبالی',
            'گردن‌بند',
            'کیف پول',
            'کلاه سوارکاری',
            'کیف',
            'کیف',
            'کیف دوشی',
            'کیف کتانی',
            'کیف دستی چرمی',
            'کلاه بافتنی',
            'کیف چرمی',
            'کلاه پارچه‌ای',
            'کلاه شلواری',
            'کلاه جین',
            'مانتو',
            'سارافون',
            'روبان',
            'روسری',
            'چادر',
            'کلاه',
            'کاپشن',
            'پالتو',
            'کیف',
            'کفش',
            'بوت',
            'کت',
            'پیراهن',
            'شلوار',
            'لباس زیر',
            'تی‌شرت',
            'جوراب',
            'دامن',
            'توپ',
            'شال',
            'روسری',
            'شال‌گردن',
            'ساعت',
            'عینک',
            'گوشواره',
            'گردنبند',
            'انگشتر',
            'زیورآلات',
            'کمربند',
            'پیراهن',
            'کراوات',
            'کلاه‌شنی',
            'چرم',
            'کلاه بارانی',
            'کلاه گرمسیری',
            'ساک دستی',
            'کلاه بیسبالی',
            'گردن‌بند',
            'کیف پول',
            'کلاه سوارکاری',
            'کیف زنانه',
            'کیف مردانه',
            'کیف دوشی',
            'کیف کتانی',
            'کیف دستی چرمی',
            'کلاه بافتنی',
            'کیف چرمی',
            'کلاه پارچه‌ای',
            'کلاه شلواری',
            'کلاه جین',
        ];
        $brandServiceRepository = app(BrandServiceRepository::class);
        $brand = $brandServiceRepository->inRandomOrder()->first();
        $gender = [
            'مردانه',
            'زنانه',
            'بچگانه',
            'مردانه و زنانه',
            'غیره',
            'پسرانه',
            'دخترانه',
            'نوزادانه',
            'جوانانه',
            'نوجوانانه',
            'بزرگسالانه',
            'بچه‌گانه',
            ];
        return [
            'name' => $this->faker->randomElement($product).' '.$this->faker->randomElement($gender).' شماره '.$this->faker->randomDigitNot(0),
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
