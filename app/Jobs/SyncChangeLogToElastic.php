<?php

namespace App\Jobs;

use App\Models\ChangeLog;
use App\Repositories\ElasticSearchRepositoryInterface;
use App\Services\ElasticSearch\ElasticSearchServiceRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncChangeLogToElastic implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $elasticSearchServiceRepository;
    /**
     * Create a new job instance.
     */
    public function __construct(protected ChangeLog $changeLog)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Resolve ElasticSearchRepositoryInterface instance through dependency injection
        $this->elasticSearchServiceRepository = resolve(ElasticSearchRepositoryInterface::class);

        // Extract model name from loggable_type
        $model = strtolower(class_basename($this->changeLog->loggable_type));

        // Define filter
        $filter = [
            $model => $this->changeLog->loggable_id
        ];

        // Perform initial search to get total number of pages
        $data = $this->elasticSearchServiceRepository->searchDocument("", 1, 1000, $filter, ['id']);
        $totalPages = $data['paginate_data']['last_page'];

        // Iterate through each page
        for ($i = 1; $i <= $totalPages; $i++) {
            // Retrieve data for current page
            $data = $this->elasticSearchServiceRepository->searchDocument("", $i, 1000, $filter, ['id']);

            // Update documents on current page
            foreach ($data['data'] as $document) {
                $this->elasticSearchServiceRepository->updateDocument((int)$document['_id'], $model.".".$this->changeLog->field, $this->changeLog->new_value);
            }
        }
        dd($data['data']);
    }
}
