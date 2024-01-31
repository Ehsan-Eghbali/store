<?php

	namespace App\Repositories;

	use Elastic\Elasticsearch\Client;

    const INDEX = 'products';
    interface ElasticSearchRepositoryInterface
	{

        public function indexDocument ( $type, $id, $document);
        public function updateDocument ($id, $document,$newDocument);
        public function searchDocument ($query,int $page=1, int $perPage=12);

	}
