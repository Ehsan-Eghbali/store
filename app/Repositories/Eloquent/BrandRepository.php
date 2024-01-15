<?php

namespace App\Repositories\Eloquent;

use App\Repositories\BrandRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository;

class BrandRepository extends BaseRepository implements BrandRepositoryInterface
{
    public function __construct(private BrandRepositoryInterface $brandRepository)
    {

    }

    public function store($request)
    {
        // TODO: Implement store() method.
    }
}
