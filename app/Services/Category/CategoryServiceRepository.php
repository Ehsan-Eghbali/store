<?php

namespace App\Services\Category;



use App\Repositories\CategoryRepositoryInterface;
use App\Repositories\Eloquent\CategoryRepository;
use Illuminate\Support\Collection;

class CategoryServiceRepository  implements CategoryRepositoryInterface
{
    public function __construct(private CategoryRepository $categoryRepository){}

    public function all(): Collection
    {
        return $this->categoryRepository->all();
    }
    public function inRandomOrder(): Collection
    {
        return $this->categoryRepository->inRandomOrder();
    }
}
