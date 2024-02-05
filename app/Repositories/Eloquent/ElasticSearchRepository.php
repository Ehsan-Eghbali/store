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

        public function indexDocument($type, $id, $document): \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise
        {
            $params = [
                'index' => INDEX,
                'type' => $type,
                'id' => $id,
                'body' => $document,
            ];

            return $this->clientBuilder->index($params);
        }

        public function updateDocument($id, $document, $newDocument): \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise
        {
            $params = [
                'index' => INDEX,
                'id' => $id,
                'body' => [
                    'script' => [
                        'source' => 'ctx._source.' . $document . ' = params.new_value',
                        'params' => [
                            'new_value' => $newDocument,
                        ],
                    ],
                ],
            ];
            return $this->clientBuilder->update($params);
        }

        public function searchDocument($query, int $page = 1, int $perPage = 12, ?array $filter = null, ?array $source = null): array
        {
            // Validate parameters
            $page = max(1, $page);
            $perPage = max(1, $perPage);

            // Reusable query array
            $queryArray = [
                'range' => ['count' => ['gt' => 0]],
            ];

            $multiMatchQuery = $this->buildMultiMatchQuery($query);

            $aggregation = [
                'category_agg' => [
                    'terms' => [
                        'field' => 'categories.name.keyword', // Assuming it's a keyword field
                    ],
                ],
                'brand_agg' => [
                    'terms' => [
                        'field' => 'brand.name.keyword', // Assuming it's a keyword field
                    ],
                ],
            ];

            $params = [
                'index' => INDEX,
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => [$queryArray, $multiMatchQuery],
                        ],
                    ],
                    'aggs' => $aggregation, // Add the aggregation here
                    'from' => ($page - 1) * $perPage,
                    'size' => $perPage,
                ],
            ];


            $this->addFilterConditions($params, $filter);
            $this->addSourceFilter($params, $source);

            // Perform the search
            $response = $this->clientBuilder->search($params);

            // Extract aggregation results
            $aggregationResults = $response['aggregations'] ?? [];

            // Calculate total count
            $countParams = ['index' => INDEX, 'body' => ['query' => ['bool' => ['must' => [$queryArray, $multiMatchQuery]]]]];
            $this->addFilterConditions($countParams, $filter);
            $totalCount = $this->getTotalCount($countParams);

            // Calculate the last page number
            $lastPage = max(1, ceil($totalCount / $perPage));

            return [
                'data' => $response['hits']['hits'],
                'aggregations' => $aggregationResults, // Include aggregation results in the response
                'total' => (int) $totalCount,
                'last_page' => (int) $lastPage,
            ];
        }


        private function buildMultiMatchQuery(string $query): array
        {
            if ($query !== "") {
                return [
                    'multi_match' => [
                        'query' => $query,
                        'operator' => 'OR',
                        'analyzer' => 'standard',
                        'fields' => ['name'],
                        'type' => 'best_fields',
                    ],
                ];
            } else {
                return ['match_all' => (object) []];
            }
        }

        private function addFilterConditions(array &$params, ?array $filter): void
        {
            if ($filter !== null) {
                $params['body']['query']['bool']['filter'] = array_map(function ($key, $value) {
                    return ['terms' => ["$key.name.keyword" => [$value]]];
                }, array_keys($filter), $filter);
            }
        }

        private function addSourceFilter(array &$params, ?array $source): void
        {
            if ($source !== null) {
                $params['body']['_source'] = $source;
            }
        }

        private function getTotalCount(array $countParams): int
        {
            $countResponse = $this->clientBuilder->count($countParams);
            return $countResponse['count'];
        }
    }
