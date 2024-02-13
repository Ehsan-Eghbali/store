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

        public function indexDocuments($type, $documents): \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise
        {

            foreach ($documents as $product) {
                $params['body'][] = [
                    'index' => [
                        '_index' => INDEX,
                        '_id' => $product->id,
                    ]
                ];
                $productData = $product->toArray();
                $productData['price'] = (float) $productData['price'];

                $params['body'][] = $productData;
            }
            return $this->clientBuilder->bulk($params);
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

        public function searchDocument($query, int $page = 1, int $perPage = 12, ?array $filter = null, ?array $source = null,?array $sort = null): array
        {
            // Validate parameters
            $page = max(1, $page);
            $perPage = max(12, $perPage);

            // Reusable query array
            $queryArray = [
                'range' => ['count' => ['gt' => 0]],
                'range' => ['price'=>   ['gt'=>0]],
            ];

            $multiMatchQuery = $this->buildMultiMatchQuery($query);

            $aggregation = [
                'categories' => [
                    'terms' => [
                        'field' => 'categories.id', // Assuming it's a keyword field
                        'size' => 100, // Increase size to make sure all buckets are considered
                    ],
                    'aggs' => [
                        'top_hits' => [
                            'top_hits' => [
                                "size" => 100,
                                '_source' => ['includes' => ['categories.id', 'categories.name']],
                            ],
                        ],
                        'merge_buckets' => [
                            'bucket_selector' => [
                                'buckets_path' => [
                                    'categories_count' => '_count',
                                ],
                                'script' => 'params.categories_count > 0', // Merge all buckets with count > 0 into one bucket
                            ],
                        ],
                    ],
                ],
                'brands' => [
                    'terms' => [
                        'field' => 'brand.id', // Assuming it's a keyword field
                        "size" => 100,
                    ],
                    'aggs' => [
                        'top_hits' => [
                            'top_hits' => [
                                "size" => 100,
                                '_source' => ['includes' => ['brand.name','brand.id']],
                            ],
                        ],
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
            if ($sort){
                $this->addSort($params,$sort);

            }
            $response = $this->clientBuilder->search($params);

            // Extract aggregation results
            $aggregationResults = $this->formatAggregations($response['aggregations'] )?? [];
            $totalCount  = $response['hits']['total']['value'];
            // Calculate the last page number
            $lastPage = max(1, ceil($totalCount / $perPage));

            return [

                'data' =>$response['hits']['hits'],
                'filters' => $aggregationResults,
                'paginate_data' => [
                    'total' => (int) $totalCount,
                    'last_page' => (int) $lastPage,
                ],

            ];
        }
        public function formatAggregations(array $aggregationResults): array
        {
            $formattedAggregations = [];

            foreach ($aggregationResults as $aggKey => $aggValue) {
                $formattedBuckets = [];

                foreach ($aggValue['buckets'] ?? [] as $bucket) {
                    // Ensure 'top_hits' key and its sub-keys are set before accessing
                    if (isset($bucket['top_hits']['hits']['hits'])) {
                        foreach ($bucket['top_hits']['hits']['hits'] as $hit) {
                            if (isset($hit['_source'])) {
                                // Extract the first value from _source and add to formattedBuckets
                                $formattedBuckets[] = reset($hit['_source']);
//                                dd($formattedBuckets);
                            }
                        }
                    }
                }
                // Remove duplicates from formattedBuckets and reset array keys
                $formattedAggregations[$aggKey] = array_merge(...array_values(array_unique($formattedBuckets,SORT_REGULAR)));
            }

            return $formattedAggregations;
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
                $formattedFilter = array_map(function ($key, $value) {
                    return ['terms' => ["$key.id" => explode(',', $value)]];
                }, array_keys($filter), $filter);
                $params['body']['query']['bool']['filter'] = array_merge($params['body']['query']['bool']['filter'] ?? [], $formattedFilter);
            }
        }


        private function addSourceFilter(array &$params, ?array $source): void
        {
            if ($source !== null) {
                $params['body']['_source'] = $source;
            }
        }

        private function addSort (array &$params, ?array $sortData)
        {
            if ($sortData!==null){
                $sort = [];
                foreach ($sortData['sort'] as $order => $field) {
                    $sort[] = [
                        $field => [
                            'order' => $order
                        ]
                    ];
                }
                $params['body']['sort'] = $sort;
            }
        }

    }

//
