<?php

namespace App\Base;

use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as LaravelBaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Base\Traits\ResCacheTrait;

class BaseController extends LaravelBaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use ResCacheTrait;
    
    //default data parameter untuk responseable
    protected $output = [
            'status'=>200,
            'message'=>'',
            'message_type'=>'info',//khusus warning view (bukan api)
            'data'=>null,
            'viewdata'=>null,//data yang hanya disertakan di web request
            // 'listdata'=>null,//untuk data berbentuk list array, jadi di view akan jadi output->output['data'][VAR_NAME] dan di api akan jadi output->output['data']
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
     * @param string $type 'warning','info','warning','danger'
     * @param integer $code http response code
     * @param mix $error
     * @param mix $response
     */
    protected function setWarning($message,$type='warning',$code=400,$error=false,$response=null)
    {        
        $this->output['status'] = $code;
        $this->output['message'] = $message;
        $this->output['message_type'] = $type;
        $this->output['errors'] = $error===true||$error===1||$error===false?[true]:$error;

        if(!is_null($response)){
            $this->response = 
                $response===true||$response===1||$response===false?
                redirect(url()->previous())->withInput():
                $response;
        }

        if($this->isWebCall() && $this->forceOutput != 2)
            \Session::put('alert', [
                    'type' => $type,
                    'message' => $message
                ]);
    }
    /**
     * 
     * @param string $message
     * @param mix $error
     * @param integer $code
     * @param type $response
     */
    protected function setError($message,$error=false,$code=400,$response=null)
    {        
        $this->setWarning($message,'danger',$code,$error,$response);

        // $this->output['status'] = $code;
        // $this->output['message'] = $message;
        // $this->output['message_type'] = 'danger';
        // $this->output['errors'] = $error===true||$error===1||$error===false?[true]:$error;
        // if(!is_null($response)){
        //     $this->response = 
        //         $response===true||$response===1||$response===false?
        //         redirect(url()->previous())->withInput():
        //         $response;
        // }
    }
    
    /**
     * set alert view
     * 
     * @param string $message
     * @param string $type 'warning','info','warning','danger'
     */
    protected function setAlert($message,$type='info')
    {        
        $this->setWarning($message,$type,'200');

        // $this->output['message'] = $message;
        // $this->output['message_type'] = $type;
        // if($this->isWebCall() && $this->forceOutput != 2)
        //     \Session::put('alert', [
        //             'type' => $type,
        //             'message' => $message
        //         ]);
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
     * cek apakah request WEB
     * @return boolean
     */
    protected function isWebCall()
    {
        return !(request()->ajax()||request()->wantsJson())?true:false;
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
