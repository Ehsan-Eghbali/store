<?php

namespace App\Services\Product;

use App\Repositories\Eloquent\ProductRepository;
use App\Repositories\ProductRepositoryInterface;

class ProductServiceRepository implements ProductRepositoryInterface
{
    public function __construct(private ProductRepository $productRepository){}
}
