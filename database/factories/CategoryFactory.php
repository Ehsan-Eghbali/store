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
        $category = [
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
        $gender = ['مردانه','زنانه','بچگانه','مردانه و زنانه'];

        $categoryServiceRepository = app(CategoryServiceRepository::class);
        $parentCategories = $categoryServiceRepository->where('parent_id','=',null)->pluck('id')->toArray();
        $parentCategories[] = null;
        return [
            'name'=>$this->faker->randomElement($category).' '.$this->faker->randomElement($gender),
            'parent_id'=>$this->faker->randomElement($parentCategories),
        ];
    }
}
