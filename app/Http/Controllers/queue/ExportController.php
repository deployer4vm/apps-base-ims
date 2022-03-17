<?php

namespace App\Http\Controllers\queue;

use Illuminate\Http\Request;

use App\Models\Job;

use App\Base\BaseController;
use App\Facades\Web;
use App\Facades\Trans;


use App\Facades\Export;
use hpsynapse\moduser\Models\User;
use Illuminate\Support\Facades\DB;

class ExportController extends BaseController
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
        $tmpUser = [];
        // -----------------------
        $this->output['data']['jobs'] = Job::where('payload','LIKE','%\\\\Export\\"%')->orWhere('payload','LIKE','%\\\\ExportSpout\\"%')->orderBy('attempts','DESC')->orderBy('queue','ASC')->get()->append(['formated_payload']);
        $newJobs = [];
        $i=0;
        foreach($this->output['data']['jobs'] as $v){
            $newJobs[$i] = $v->toArray();
            $userId = explode('.',$v->formated_payload['data']['command']['cacheKey']);            
            $userId = $userId[count($userId)-1];
            if(!isset($tmpUser[$userId]))
                $tmpUser[$userId] = User::where('id',$userId)->first();

            $newJobs[$i]['user'] = $tmpUser[$userId];
            $newJobs[$i]['export'] = Export::getExport($v->formated_payload['data']['command']['cacheKey']);
            $i++;
        }
        $this->output['data']['jobs'] = $newJobs;

        //-------------------------------

        // set data2 khusus jika bukan API
        if($this->isWebCall()){
            $this->response = 'system.queue.list';
        }
        return $this->done();
    }

    public function cancelQueue(Request $request)
    {
        if($export = Export::getExport($request->route('cacheKey'))){
            Export::cancelExport($export['cacheKey']);
            $this->output['message'] = 'Cancel Success';
        }else{
            $this->setError('Detail Export <b>'.$request->route('cacheKey').'</b> tidak ditemukan !');
        }
        $this->response = redirect()->route('system.queue.export.list');
        return $this->done();
    }

    public function historyQueue(Request $request)
    {        
        $tmpUser = [];

        $this->output['data']['list'] = Export::listExport();
        foreach($this->output['data']['list'] as $v){
            $this->output['data']['list'][$v] = ['cacheKey'=>$v];
            $userId = explode('.',$v);
            $userId = $userId[count($userId)-1];
            if(!isset($tmpUser[$userId]))
                $tmpUser[$userId] = User::where('id',$userId)->first();
            $this->output['data']['list'][$v]['user'] = $tmpUser[$userId];
        }

        // set data2 khusus jika bukan API
        if($this->isWebCall()){
            $this->response = 'system.queue.history';
        }
        return $this->done();

    }
    
    public function deleteQueue(Request $request)
    {
        if($export = Export::getExport($request->route('cacheKey'))){
            Export::deleteExport($export['cacheKey']);
            $this->output['message'] = 'Delete Success';
        }else{
            $this->setError('Detail Export <b>'.$request->route('cacheKey').'</b> tidak ditemukan !');
        }
        $this->response = redirect()->route('system.queue.export.history');
        return $this->done();
    }
    
    public function detailQueue(Request $request)
    {
        $this->output['data']['isHistory'] = $request->input('isHistory',false);
        $this->output['data']['data'] = Export::getExport($request->route('cacheKey'));
        if($this->output['data']['data']){
            $userId = explode('.',$this->output['data']['data']['cacheKey']);
            $this->output['data']['data']['user'] = User::where('id',$userId[count($userId)-1])->first();
            $this->output['data']['data']['jobs'] = Job::where('payload','LIKE','%\"'.$this->output['data']['data']['cacheKey'].'\\\\\"%')->get()->append(['formated_payload'])->toArray();
        }

        // set data2 khusus jika bukan API
        if($this->isWebCall()){
            
            if(!$this->output['data']['data']){       
                $this->setError('Detail Export <b>'.$request->route('cacheKey').'</b> tidak ditemukan !');
                $this->response = redirect()->route('system.queue.export.list');
                return $this->done();
            }
            
            $this->response = 'system.queue.detail';
        }
        return $this->done();
    }
}