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

    public function create($data,$maxLimit=10) {
        $this->_create($this->model,$data);
        if($this->model->count() > $maxLimit){
            $this->deleteFirst();
        }

    }

    //delete backup yang terlama
    public function deleteFirst()
    {
        $backup = BModel::orderBy('id','ASC')->first();

        //delete file
        exec("rm -rf '".$backup->path."'");

        $backup->delete();
    }
}