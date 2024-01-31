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
    public function transferDataToElastic(int $batchSize,int $lastId)
    {
        return $this->productRepository->transferDataToElastic($batchSize,$lastId);
    }

    public function search ($request)
    {
        return $this->productRepository->search($request);
    }
}
