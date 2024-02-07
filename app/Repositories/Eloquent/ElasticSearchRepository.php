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
                'categories' => [
                    'terms' => [
                        'field' => 'categories.id', // Assuming it's a keyword field
                    ],
                    'aggs' => [
                        'top_hits' => [
                            'top_hits' => [
                                "size"=> 100,
                                '_source' => ['includes' => ['categories.name']],
                            ],
                        ],
                    ],
                ],
                'brands' => [
                    'terms' => [
                        'field' => 'brand.id', // Assuming it's a keyword field
                    ],
                    'aggs' => [
                        'top_hits' => [
                            'top_hits' => [
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
            $response = $this->clientBuilder->search($params);
            // Extract aggregation results
            $aggregationResults = $this->formatAggregations($response['aggregations'] )?? [];
            return $aggregationResults;
//            dd($aggregationResults);
            // Calculate total count
            $countParams = ['index' => INDEX, 'body' => ['query' => ['bool' => ['must' => [$queryArray, $multiMatchQuery]]]]];
            $this->addFilterConditions($countParams, $filter);
            $totalCount = $this->getTotalCount($countParams);

            // Calculate the last page number
            $lastPage = max(1, ceil($totalCount / $perPage));

            return [
                'data' => $response['hits']['hits'],
                'filters' => $aggregationResults,
//                'paginate_data' => [
//                    'total' => (int) $totalCount,
//                    'last_page' => (int) $lastPage,
//                ],
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
                            }
                        }
                    }
                }

                // Remove duplicates from formattedBuckets and reset array keys
                $formattedAggregations[$aggKey] = array_values(array_unique($formattedBuckets, SORT_REGULAR));
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

        private function getTotalCount(array $countParams): int
        {
            $countResponse = $this->clientBuilder->count($countParams);
            return $countResponse['count'];
        }
    }

//    $queryArray = [
//        'bool' => [
//            'must' => [
//                [
//                    'query_string' => [
//                        'query' => '(total_qty:>0)',
//                    ],
//                ],
//                [
//                    'multi_match' => [
//                        'query' => $query,
//                        'fields' => ['product_name^10', 'product_description_short^4'],
//                        'type' => 'best_fields',
//                        'analyzer' => 'standard',
//                        'operator' => 'or',
//                        'minimum_should_match' => '3<80%',
//                    ],
//                ],
//                [
//                    'terms' => [
//                        'visibility' => ['search', 'both'],
//                    ],
//                ],
//                [
//                    'range' => [
//                        'active' => ['gt' => 0],
//                    ],
//                ],
//                [
//                    'range' => [
//                        'show_price' => ['gt' => 0],
//                    ],
//                ],
//                [
//                    'range' => [
//                        'active_only_one_barcode' => ['gt' => 0],
//                    ],
//                ],
//            ],
//        ],
//    ];
//
//    $aggregation = [
//        'colors' => [
//            'terms' => [
//                'field' => 'color_name.keyword',
//                'size' => 1000,
//            ],
//            'aggregations' => [
//                'top_color_hits' => [
//                    'top_hits' => [
//                        'size' => 1,
//                        '_source' => [
//                            'includes' => ['color_value', 'id_color'],
//                        ],
//                    ],
//                ],
//            ],
//        ],
//        'color_groups' => [
//            'terms' => [
//                'field' => 'id_color_group',
//                'size' => 1000,
//            ],
//            'aggregations' => [
//                'top_color_group_hits' => [
//                    'top_hits' => [
//                        'size' => 1,
//                        '_source' => [
//                            'includes' => ['color_groups_color', 'color_groups_title', 'color_groups_icon'],
//                        ],
//                    ],
//                ],
//            ],
//        ],
//        'sizes' => [
//            'terms' => [
//                'field' => 'size.name.keyword',
//                'size' => 1000,
//            ],
//            'aggregations' => [
//                'top_size_hits' => [
//                    'top_hits' => [
//                        'size' => 1,
//                        '_source' => [
//                            'includes' => ['size'],
//                        ],
//                    ],
//                ],
//            ],
//        ],
//        'manufacturers' => [
//            'terms' => [
//                'field' => 'product_manufacturer_name.keyword',
//                'size' => 1000,
//            ],
//            'aggregations' => [
//                'top_brand_hits' => [
//                    'top_hits' => [
//                        'size' => 1,
//                        '_source' => [
//                            'includes' => ['product_manufacturer_en_name', 'product_manufacturer_id'],
//                        ],
//                    ],
//                ],
//            ],
//        ],
//        'categories' => [
//            'nested' => [
//                'path' => 'product_categories_nested',
//            ],
//            'aggregations' => [
//                'cat_ids' => [
//                    'terms' => [
//                        'field' => 'product_categories_nested.id',
//                        'size' => 1000,
//                    ],
//                    'aggregations' => [
//                        'cat_level' => [
//                            'terms' => [
//                                'field' => 'product_categories_nested.level',
//                            ],
//                        ],
//                        'cat_parent' => [
//                            'terms' => [
//                                'field' => 'product_categories_nested.parent_id',
//                            ],
//                        ],
//                    ],
//                ],
//            ],
//        ],
//        'features' => [
//            'terms' => [
//                'field' => 'product_features.filter.keyword',
//                'size' => 1000,
//            ],
//        ],
//        'max_price' => [
//            'max' => [
//                'field' => 'product_final_price',
//            ],
//        ],
//    ];
//
//    $sort = [
//        [
//            'has_qty' => [
//                'order' => 'desc',
//            ],
//        ],
//        [
//            '_score' => [],
//        ],
//        [
//            'product_categories_sort.position_sort_double_new' => [
//                'mode' => 'min',
//                'order' => 'asc',
//                'nested' => [
//                    'path' => 'product_categories_sort',
//                ],
//            ],
//        ],
//    ];
//
//    $params = [
//        'index' => INDEX,
//        'body' => [
//            'query' => $queryArray,
//            'aggs' => $aggregation,
//            'sort' => $sort,
//            'from' => ($page - 1) * $perPage,
//            'size' => $perPage,
//        ],
//    ];
