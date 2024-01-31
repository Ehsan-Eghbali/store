<?php

namespace App\Repositories;


interface ProductRepositoryInterface
{
    public function transferDataToElastic(int $batchSize,int $lastId);
    public function search($request);
}
