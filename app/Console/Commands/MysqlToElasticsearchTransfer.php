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
            $batchSize = 500; // Adjust the batch size based on your needs
            $lastId = 600000;
            do {
                $products = $this->productServiceRepository->transferDataToElastic($batchSize,$lastId);
                if ($products->count() > 0) {
                    foreach ($products as $product) {
                        $body = $product->toArray();
                        $this->elasticSearchServiceRepository->indexDocument(
                            '_doc',
                            $product->id,
                            $body,
                        );
                        $lastId = $product->id;

                        $this->info('Transferring data product id =>'.$product->id);
                    }
                }
            } while ($products->count() > 0);
            $this->info('Data transfer completed.');
        }
    }
