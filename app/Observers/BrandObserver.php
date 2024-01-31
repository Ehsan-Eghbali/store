<?php

    namespace App\Observers;

    use App\Models\Brand;
    use App\Services\ChangeLog\ChangeLogServiceRepository;
    use App\Services\ElasticSearch\ElasticSearchServiceRepository;

    class BrandObserver
    {
        public function __construct (private ElasticSearchServiceRepository $elasticSearchServiceRepository, private ChangeLogServiceRepository $changeLogServiceRepository)
        {

        }

        /**
         * Handle the Brand "created" event.
         */
        public function created (Brand $brand): void
        {
            //
        }

        /**
         * Handle the Brand "updated" event.
         */
        public function updated (Brand $brand): void
        {
            $changes = $brand->getDirty();
            foreach ($changes as $field => $new) {
                $old = $brand->getOriginal($field);
                $t = $this->changeLogServiceRepository->create([
                        'field' => $field,
                        'old_value' => $old,
                        'new_value' => $new,
                        'loggable_id' => $brand->getAttribute('id'),
                        'loggable_type' => get_class($brand),
                    ],
                );
            }
        }

        /**
         * Handle the Brand "deleted" event.
         */
        public function deleted (Brand $brand): void
        {
            //
        }

        /**
         * Handle the Brand "restored" event.
         */
        public function restored (Brand $brand): void
        {
            //
        }

        /**
         * Handle the Brand "force deleted" event.
         */
        public function forceDeleted (Brand $brand): void
        {
            //
        }
    }
