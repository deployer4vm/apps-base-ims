<?php

namespace App\Services;
use App\Models\Backup as BModel;
use Illuminate\Support\Facades\File;

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
        $this->deleteBackupFile($backup->path);

        $backup->delete();
    }

    //delete backup yang terlama
    public function delete($where)
    {
        $backup = $this->_getOne($this->model, $where);
        if($backup){
            $this->_delete($this->model, $where);
            //delete file
            $this->deleteBackupFile($backup['path']);
        }
    }

    private function deleteBackupFile($path)
    {
        $backupRoot = realpath(storage_path('app/backups'));
        $target = realpath($path);
        if ($backupRoot && $target && strpos($target, $backupRoot.DIRECTORY_SEPARATOR) === 0 && is_file($target)) {
            File::delete($target);
        }
    }
}
