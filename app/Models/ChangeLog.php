<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class ChangeLog extends Model
    {
        use HasFactory;

        protected $fillable =
            [
                'user_id',
                'model',
                'model_id',
                'field',
                'old_value',
                'new_value',
                'loggable_id',
                'loggable_type',
            ];
    }
