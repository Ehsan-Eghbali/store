<?php

namespace App\Services\ElasticSearch;
use App\Repositories\ElasticSearchRepositoryInterface;
use App\Repositories\Eloquent\ElasticSearchRepository;
use Elastic\Elasticsearch\Client;


class ElasticSearchServiceRepository implements ElasticSearchRepositoryInterface
{


    public function __construct (private readonly ElasticSearchRepository $elasticSearchRepository)
    {
    }

    public function indexDocument ($index, $type, $id, $document): \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise
    {
        return $this->elasticSearchRepository->indexDocument($index,$type,$id,$document);
    }
}
