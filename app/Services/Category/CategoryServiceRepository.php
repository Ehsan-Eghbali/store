<?php

namespace App\Services\Category;



use App\Repositories\CategoryRepositoryInterface;
use App\Repositories\Eloquent\CategoryRepository;
use Illuminate\Support\Collection;

class CategoryServiceRepository  implements CategoryRepositoryInterface
{
    public function __construct(private readonly CategoryRepository $categoryRepository){}

    public function all(): Collection
    {
        return $this->categoryRepository->all();
    }

    public function where (string $column, mixed $operator = '=', mixed $value =null ,array $with = []): array|\Illuminate\Database\Eloquent\Collection
    {
        return $this->categoryRepository->where($column,$operator,$value,$with);
    }
    public function inRandomOrder($count=1): Collection
    {
        return $this->categoryRepository->inRandomOrder($count);
    }
}
