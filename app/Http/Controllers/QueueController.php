<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Base\BaseController;
use App\Facades\Web;
use App\Facades\Trans;


use App\Facades\Export;

class QueueController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }
    
    public function listQueue(Request $request)
    {
        $this->output['data']['list'] = Export::listExport();
        
        // set data2 khusus jika bukan API
        if($this->isWebCall()){
            $this->response = 'system.queue.list';
        }
        return $this->done();
    }
    public function detailQueue(Request $request)
    {
        $this->output['data']['data'] = Export::getExport($request->route('cacheKey'));
        
        // set data2 khusus jika bukan API
        if($this->isWebCall()){
            $this->response = 'system.queue.detail';
        }
        return $this->done();
    }
}