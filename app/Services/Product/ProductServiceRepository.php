<?php

namespace App\Services\Product;

use App\Repositories\Eloquent\ProductRepository;
use App\Repositories\ProductRepositoryInterface;

class ProductServiceRepository implements ProductRepositoryInterface
{
    public function __construct(private ProductRepository $productRepository){}

    public function all(): \Illuminate\Support\Collection
    {
        return $this->productRepository->all();
    }
    public function transferData()
    {
        return $this->productRepository->transferData();
    }
}
