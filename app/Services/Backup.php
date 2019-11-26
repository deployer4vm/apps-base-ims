<?php

namespace App\Services;
use App\Models\Backup as BModel;

use App\Base\BaseRepository;

class Backup extends BaseRepository
{   
    public function __construct(BModel $model)
    {
        $this->model = $model;
    }
}