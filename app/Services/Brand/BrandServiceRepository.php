<?php

namespace App\Services\Brand;

use App\Repositories\BrandRepositoryInterface;

class BrandServiceRepository implements BrandServiceInterface
{
    public function __construct(private BrandRepositoryInterface $brandService)
    {

    }
    public function store($request)
    {
        $this->brandService->store($request);
    }
}
