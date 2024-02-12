<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\ProductRepositoryInterface;
use App\Services\ElasticSearch\ElasticSearchServiceRepository;


class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $model,protected ElasticSearchServiceRepository $elasticSearchServiceRepository)
    {
        parent::__construct($model);
    }

    public function transferDataToElastic(int $batchSize,int $lastId)
    {
        return $this->model::with('categories:id,name,parent_id','categories.categoryParent:id,name,parent_id','brand:id,name')
            ->select(['id','name','price','count','brand_id'])
            ->orderBy('id')
            ->offset($lastId)
            ->take($batchSize)
            ->get();
    }

    public function search ($request,$filter = null)
    {
        $query = $request->get('q') ??"";
        $page = $request->get('page') ?? 1;
        $perPage = $request->get('perPage') ?? 12;
        return $this->elasticSearchServiceRepository->searchDocument($query,(int) $page, (int) $perPage,$filter);
    }
}
