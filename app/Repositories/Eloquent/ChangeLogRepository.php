<?php

	namespace App\Repositories\Eloquent;

	use App\Models\ChangeLog;
    use App\Repositories\ChangeLogRepositoryInterface;
    use Illuminate\Http\Request;

    class ChangeLogRepository extends BaseRepository implements ChangeLogRepositoryInterface
	{
        public function __construct(ChangeLog $model)
        {
            parent::__construct($model);
        }


    }
