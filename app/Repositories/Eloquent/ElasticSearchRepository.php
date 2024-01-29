<?php

	namespace App\Repositories\Eloquent;

	use App\Repositories\ElasticSearchRepositoryInterface;
    use Elastic\Elasticsearch\Client;
    use Elastic\Elasticsearch\ClientBuilder;

    class ElasticSearchRepository implements ElasticSearchRepositoryInterface
	{
        protected Client $clientBuilder;
        public function __construct()
        {

            $this->clientBuilder = ClientBuilder::create()
                ->setHosts([config('database.connections.elasticsearch.hosts')[0]['host'].":".config('database.connections.elasticsearch.hosts')[0]['port']])
                ->build();
        }

        public function indexDocument($index, $type, $id, $document)
        {
            $params = [
                'index' => $index,
                'type' => $type,
                'id' => $id,
                'body' => $document,
            ];

            return $this->clientBuilder->index($params);
        }
    }
