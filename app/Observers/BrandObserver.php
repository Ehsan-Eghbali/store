<?php

namespace App\Observers;

use App\Models\Brand;
use App\Services\ElasticSearch\ElasticSearchServiceRepository;

class BrandObserver
{
    public function __construct (private ElasticSearchServiceRepository $elasticSearchServiceRepository)
    {

    }
    /**
     * Handle the Brand "created" event.
     */
    public function created(Brand $brand): void
    {
        //
    }

    /**
     * Handle the Brand "updated" event.
     */
    public function updated(Brand $brand): void
    {
        $brand->products->each(function ($product) use ($brand) {
            dd($product->pluck('id'));
        });
    }

    /**
     * Handle the Brand "deleted" event.
     */
    public function deleted(Brand $brand): void
    {
        //
    }

    /**
     * Handle the Brand "restored" event.
     */
    public function restored(Brand $brand): void
    {
        //
    }

    /**
     * Handle the Brand "force deleted" event.
     */
    public function forceDeleted(Brand $brand): void
    {
        //
    }
}
