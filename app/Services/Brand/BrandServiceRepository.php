<?php

namespace App\Services\Brand;

use App\Repositories\BrandRepositoryInterface;
use App\Repositories\Eloquent\BrandRepository;
use Illuminate\Database\Eloquent\Collection;

class BrandServiceRepository  implements BrandRepositoryInterface
{
    public function __construct(private BrandRepository $brandRepository){}

    public function inRandomOrder() :Collection
    {
        return $this->brandRepository->inRandomOrder();
    }
}
