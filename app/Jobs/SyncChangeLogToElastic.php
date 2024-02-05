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
        $this->elasticSearchServiceRepository = resolve(ElasticSearchRepositoryInterface::class);
        $t = [
            'brand.name'=>"test"
        ];
        $data = $this->elasticSearchServiceRepository->searchDocument("",1,12,$t,['id']);
        foreach ($data['data'] as $list){
            $this->elasticSearchServiceRepository->updateDocument($list['_id'],"brand.name",$this->changeLog->new_value);
        }

    }
}
