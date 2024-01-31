<?php

    namespace App\Repositories\Eloquent;

    use App\Repositories\ElasticSearchRepositoryInterface;
    use Elastic\Elasticsearch\Client;
    use Elastic\Elasticsearch\ClientBuilder;
    use const App\Repositories\INDEX;

    class ElasticSearchRepository implements ElasticSearchRepositoryInterface
    {
        protected Client $clientBuilder;

        public function __construct ()
        {
            $this->clientBuilder = ClientBuilder::create()
                ->setHosts([config('database.connections.elasticsearch.hosts')[0]['host'] . ":" . config('database.connections.elasticsearch.hosts')[0]['port']])
                ->build();
        }

        public function createIndex ($name)
        {

        }

        public function indexDocument ($type, $id, $document)
        {
            $params = [
                'index' => INDEX,
                'type' => $type,
                'id' => $id,
                'body' => $document,
            ];

            return $this->clientBuilder->index($params);
        }

        public function updateDocument ($id, $document, $newDocument)
        {
            $params = [
                'index' => INDEX,
                'id' => $id,
                'body' => [
                    'doc' => [
                        "'$document'" => $newDocument,
                    ],
                ],
            ];

            return $this->clientBuilder->update($params);
        }

        public function searchDocument ($query,int $page=1, int $perPage=12)
        {
            $params = [
                'index' => INDEX,
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => [
                                ['range' => ['count' => ['gt' => 0]]],
                                [
                                    'multi_match' => [
                                        'query' => $query,
                                        'operator' => 'OR',
                                        'analyzer' => 'standard',
                                        'fields' => ['name', 'brand.name'],
                                        "type"=> "best_fields",
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'from' => ($page - 1) * $perPage, // Calculate the starting index based on the current page
                    'size' => $perPage, // Number of results to return per page
                ],
            ];

            $response = $this->clientBuilder->search($params);
            return $response['hits']['hits'];
        }
    }
