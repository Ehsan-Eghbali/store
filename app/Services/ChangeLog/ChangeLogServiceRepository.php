<?php

namespace App\Services\ChangeLog;

use App\Repositories\ChangeLogRepositoryInterface;
use App\Repositories\Eloquent\ChangeLogRepository;
use App\Repositories\Eloquent\ProductRepository;

class ChangeLogServiceRepository implements ChangeLogRepositoryInterface
{
    public function __construct(private readonly ChangeLogRepository $changeLogRepository){}


    public function create ($attributes): \Illuminate\Database\Eloquent\Model
    {
        return $this->changeLogRepository->create($attributes);
    }
}
