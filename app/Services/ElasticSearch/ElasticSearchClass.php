<?php

namespace App\Services\ElasticSearch;
use Elastic\Elasticsearch\Client;


class ElasticSearchClass
{
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function indexDocument($index, $type, $id, $document): \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise
    {
        $params = [
            'index' => $index,
            'type' => $type,
            'id' => $id,
            'body' => $document,
        ];

        return $this->client->index($params);
    }
}
