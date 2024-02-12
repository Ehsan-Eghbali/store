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
        protected $signature = 'transfer:mysql-to-elasticsearch {--chunk-size=500 : The size of chunks to transfer}';

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

            $batchSize = $this->option('chunk-size');
            $lastId = 0;
            do {
                $startTime = microtime(true);
                $products = $this->productServiceRepository->transferDataToElastic($batchSize,$lastId);
//                dd($products->first()->created_at);
                if ($products->count() > 0) {
                    $this->elasticSearchServiceRepository->indexDocuments(
                        '_doc',
                        $products
                    );
                    $lastId = $products->last()->id;
                    $endTime = microtime(true); // End time tracking
                    $totalTime = $endTime - $startTime; // Calculate total time
                    $this->info('Data transfer ID '.$lastId-$batchSize.'-'.$lastId.' completed in ' . $totalTime . ' seconds.');
                }
            } while ($products->count() > 0);

            $this->info('Data transfer completed');
        }
    }
