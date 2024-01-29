<?php

    namespace App\Console\Commands;

    use App\Services\ElasticSearch\ElasticSearchServiceRepository;
    use App\Services\Product\ProductServiceRepository;
    use Elastic\Elasticsearch\ClientBuilder;
    use Illuminate\Console\Command;

    class MysqlToElasticsearchTransfer extends Command
    {
        /**
         * The name and signature of the console command.
         *
         * @var string
         */
        protected $signature = 'transfer:mysql-to-elasticsearch';

        /**
         * The console command description.
         *
         * @var string
         */
        protected $description = 'Transfer data from MySQL to Elasticsearch';


        public function __construct (protected ProductServiceRepository $productServiceRepository,protected ElasticSearchServiceRepository $elasticSearchServiceRepository)
        {
            parent::__construct();
        }

        /**
         * Execute the console command.
         */
        public function handle ()
        {
            $this->info('Transferring data from MySQL to Elasticsearch...');

            $products = $this->productServiceRepository->all()->first();
            dd($products->categories->pluck('name'));
            foreach ($products as $product) {
                $body = $product->toArray();
                $body['brand_name'] = $product->brand->name;
                $this->elasticSearchServiceRepository->indexDocument(
                    'products',
                    '_doc',
                    $product->id,
                    $body,
                );
            }

            $this->info('Data transfer completed.');
        }
    }
