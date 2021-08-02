<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

use App\Base\BaseController;

class UploadController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * get uplaod file di storage/app/upload/*
     * 
     * @param Request $request *semua optional
     *      lang : lang id nya
     *      item : item nya jika diperlukan
     *
     */
    public function index(Request $request)
    {
        $segment = $request->segments();
        array_shift($segment);
        $path = implode('/',$segment);

        if(Storage::exists($path)){
            return Storage::download($path);
        }

        return 'file not found';
    }

}
