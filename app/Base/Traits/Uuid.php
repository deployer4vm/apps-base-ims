<?php

namespace App\Base\Traits;

use Exception;
use Ramsey\Uuid\Uuid as Generator;

trait Uuid
{
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // try {
                $model->uuid = empty($model->uuid)?Generator::uuid4()->toString():$model->uuid;
            // } catch (Exception $e) {
            //     abort(500, $e->getMessage());
            // }
        });
    }
}