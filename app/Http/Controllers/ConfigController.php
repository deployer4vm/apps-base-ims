<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\MConfig;

use App\Base\BaseController;

class ConfigController extends BaseController
{
    protected $cacheActive = true;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * GET - api list config (config yang disimpan didatabase)
     * 
     * @param Request $request
     *      group *optional
     *      key *optional
     *
     * @return array list data config
     */
    public function readList(Request $request)
    {
        $model = new MConfig;
    
        if($request->input('group',false)){
            $model = $model->where('group',$request->input('group'));
        }
        if($request->input('key',false)){
            $model = $model->where('key',$request->input('key'));
        }
    
        $data = $model->get();           
        
        return response()->json($data);
    }

    /**
     * POST - create dan update
     * 
     * @param Request $request
     *      data array list data config yang akan di create / update
     *
     * @return array list data config
     */
    public function createUpdate(Request $request)
    {
        if($data = $request->input('data',false)){
            foreach($data as $value){
                $updateData = [];
                if(isset($value['name']))
                    $updateData['name'] = $value['name'];
                if(isset($value['value']))
                    $updateData['value'] = $value['value'];
                if($updateData){
                    $model = MConfig::where('group',$value['group'])->where('key',$value['key']);
                    if($model->exists()){
                        $model->update($updateData);
                    }else{
                        $updateData['group'] = $value['group'];
                        $updateData['key'] = $value['key'];
                        $model->create($updateData);
                    }
                }
            }
        }

        return response()->json(MConfig::get());
    }

    /**
     * manage access config
     */

     public function accessConfig(Request $request)
     {
        if(!($config = $this->_getCache('generalconfig','accesss'))){
            $config = [
                'allow_login' => 1,
                'allow_login_exept' => [],
                'allow_login_only' => []
            ];           
            $this->_saveCache('generalconfig','accesss',$config); 
        }

        $this->output['data'] = $config;//UserAuth::getAccessConfig();
         return $this->done();
     }

     /**
      * Reset locking
      */
     public function unlockAccess(Request $request)
     {        
         $this->forceApiOutput();
 
         UserAuth::unlockLogin();
         // $this->output['data'] = ;
         return $this->done();
     }

}
