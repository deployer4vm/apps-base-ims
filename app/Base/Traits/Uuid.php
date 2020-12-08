<?php

namespace App\Base\Traits;

use Exception;
use Ramsey\Uuid\Uuid as Generator;
// use Illuminate\Support\Facades\Log;

trait Uuid
{
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // try {
                if(empty($model->uuid))
                    $model->uuid = Generator::uuid4()->toString();
            // } catch (Exception $e) {
            //     abort(500, $e->getMessage());
            // }
        });
    }
}