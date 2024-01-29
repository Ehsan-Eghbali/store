<?php

namespace App\Repositories\Eloquent;

use App\Models\Brand;
use App\Repositories\BrandRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class BrandRepository extends BaseRepository implements BrandRepositoryInterface
{
    public function __construct(Brand $model)
    {
        parent::__construct($model);
    }


    public function inRandomOrder($count=1):Collection
    {
        return $this->model->inRandomOrder()->limit($count)->get();
    }
}
