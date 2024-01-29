<?php

	namespace App\Repositories;

	use Elastic\Elasticsearch\Client;

    interface ElasticSearchRepositoryInterface
	{

        public function indexDocument ($index, $type, $id, $document);
	}
