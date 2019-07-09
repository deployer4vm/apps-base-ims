<?php

namespace App\Base;

use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as LaravelBaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BaseController extends LaravelBaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use RepoCacheTrait;
    
    //default data parameter untuk responseable
    protected $output = [
            'status'=>200,
            'message'=>'',
                'message_type'=>'info',//khusus warning view (bukan api)
            'data'=>null,
                'viewdata'=>null,//data yang hanya disertakan di web request
                // 'listdata'=>null,//untuk data berbentuk list array, jadi di view akan jadi output->data['data'] dan di api akan jadi output->data
            'errors'=>null,
        ];
    
    //nama variable list data di view (web request)
    protected $listDataVarName = 'data';


    //default response paramter untuk
    protected $response = '';
    
    //nama class responseable nya
    protected $responsableName = '\App\Base\DefaultResponse';
    
    //force output menjadi api atau web
    private $forceOutput = 0;//0 auto, 1 WEB, 2 API
    
    /**
     * 
     * @param string $message
     * @param type $error
     * @param type $code
     * @param type $response
     */
    protected function setError($message,$error=false,$code=400,$response=null)
    {        
        $this->output['status'] = $code;
        $this->output['message'] = $message;
        $this->output['errors'] = $error===true||$error===1||$error===false?[true]:$error;
        if(!is_null($response)){
            $this->response = 
                $response===true||$response===1||$response===false?
                redirect(url()->previous())->withInput():
                $response;
        }
    }
    
    /**
     * set alert view
     * 
     * @param string $message
     * @param string $type 'warning','info','warning','danger'
     */
    protected function setAlert($message,$type='info')
    {        
        $this->output['message'] = $message;
        $this->output['message_type'] = $type;
        
        \Session::put('alert', [
                'type' => $type,
                'message' => $message
            ]);
    }
    
    /**
     * cek apakah request dari ifframe atau bukan
     * @return boolean
     */
    protected function hasReferer()
    {
        return isset($_SERVER['HTTP_REFERER'])?true:false;
    }
    
    /**
     * cek apakah request API atau WEB
     * @return boolean
     */
    protected function isApiCall()
    {
        return request()->wantsJson()?true:false;
    }
    
    /**
     * cek apakah request API atau WEB
     * @return boolean
     */
    protected function isAjaxCall()
    {
        return request()->ajax()?true:false;
    }
    
    /**
     * bypass output responsable menjadi API (JSON) menghiraukan request yg masuk
     */
    protected function forceApiOutput()
    {
        $this->forceOutput = 2;
    }    
    
    /**
     * bypass output responsable menjadi WEB menghiraukan request yg masuk
     */
    protected function forceWebOutput()
    {
        $this->forceOutput = 1;
    }
    
    /**
     * 
     * @param type $response
     * @return \App\Base\responsableName
     */
    protected function done($response=false)
    {
        if($response)$this->response=$response;
        return new $this->responsableName(
            $this->output, 
            $this->response, 
            $this->forceOutput, 
            $this->listDataVarName);
    }
    
    /*
     * controller level cache
     * -------------------------------------------------------------------------
     */
}
