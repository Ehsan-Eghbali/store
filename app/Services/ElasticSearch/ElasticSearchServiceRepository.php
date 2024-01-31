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

    public function indexDocument ($type, $id, $document): \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise
    {
        return $this->elasticSearchRepository->indexDocument($type,$id,$document);
    }

    public function updateDocument ($id, $document,$newDocument)
    {
        return $this->elasticSearchRepository->updateDocument($id, $document,$newDocument);
    }

    public function searchDocument ($query,$page=1,$perPage=12)
    {
        return $this->elasticSearchRepository->searchDocument($query,$page,$perPage);
    }
}
