<?php

namespace App\Services\Brand;

use App\Repositories\BrandRepositoryInterface;
use App\Repositories\Eloquent\BrandRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class BrandServiceRepository implements BrandRepositoryInterface
{
    public function __construct(private BrandRepository $brandRepository){}

    public function all(): Collection
    {
        return $this->brandRepository->all();
    }
    public function inRandomOrder($count=1):Collection
    {
        return $this->brandRepository->inRandomOrder($count);
    }
}
