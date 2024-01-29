<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\CategoryRepositoryInterface;
use Illuminate\Support\Collection;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }


    public function inRandomOrder($count =1):Collection
    {
        return $this->model->inRandomOrder()->limit($count)->get();
    }
}
