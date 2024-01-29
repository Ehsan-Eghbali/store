<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\ProductRepositoryInterface;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function transferData(): void
    {
        $this->model::chunk(200, function ($models) {
            foreach ($models as $model) {
                $model->searchable();
            }
        });
    }
}
