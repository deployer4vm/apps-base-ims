<?php

namespace App\Base;

use Illuminate\Contracts\Support\Responsable;

abstract class BaseResponse implements Responsable
{

    protected $data, $response, $forceOutput, $listdataVarName;
    
    /**
     * 
     * 
     * @param array $data
     * @param mixed $response 
     *      string jika nama view
     *      instance recirect() jika redirect
     * @param int $forceOutput 0 auto, 1 force web, 2 force api
     */
    public function __construct($data=false,$response='list',$forceOutput=0, $listdataVarName='data')
    {
        $this->data = $data;
        $this->response = $response;
        $this->forceOutput = $forceOutput;
        $this->listdataVarName = $listdataVarName;
    }
    
    /**
     * prepare all data
     */
    abstract protected function prepare();
    
    public function toResponse($request)
    {        
        return $this->isApiCall($request) ? $this->apiResponse() : $this->viewResponse();
    }
    /**
     * Detect apakah request untuk output ke API atau View
     */
    protected function isApiCall($request)
    {
        if($this->forceOutput){
            return $this->forceOutput==2?true:false;
        }
        
        if($request->wantsJson()){
            return true;
        }
        
        return false;
    }
    
    /**
     * cek apakah request API atau WEB
     * @return boolean
     */
    protected function isAjaxCall($request)
    {
        return $request->ajax()?true:false;
    }
    
    /**
     * API REQUEST
     * =========================================================================
     */
    public function prepareApi()
    {
        $outputParam = [
            'status'=>200,
            'data'=>[],
            'message'=>'',
            'errors'=>null
        ];
        //delete semua data selain data khusus api
        foreach ($outputParam as $key => $value) {
            if(isset($this->data[$key])){
                $data[$key] = $this->data[$key];
            }else{
                $data[$key] = $value;
            }
        }
        
        //jika menyertakan data tambahan untuk view
        if(isset($this->data['listdata'])&&is_array($this->data['listdata'])){
            $data['data'] = $this->data['listdata'];
        }
        
        //jika error maka kosongkan data
        if(!is_null($data['errors']))$data['data'] = null;
        
        $this->data = $data;
    }
    
    /**
     * Response output ajax
     */
    private function apiResponse()
    {
        $this->prepareApi();   
        $this->prepare();
        return response()->json($this->data, $this->data['status']);
    }
    
    /**
     * WEB REQUEST
     * =========================================================================
     */
    public function prepareView()
    {
        $this->alert = false;
        $this->errors = false;
        $this->with = false;
        $this->viewdata = false;
        
        $dataTmp = $this->data['data'];
        
        if(isset($this->data['message']) && $this->data['message']){            
            $this->alert = [
                'type' => $this->data['message_type'],
                'message' => $this->data['message']
            ];
        }
        
        if(isset($this->data['errors']) && $this->data['errors'] != null && $this->data['errors'] != [true]){
            $this->errors = $this->data['errors'];
        }
        
        //jika menyertakan data tambahan untuk view
        if(isset($this->data['listdata'])&&!is_null($this->data['listdata'])){
            $dataTmp[$this->listdataVarName] = $this->data['listdata'];
        }
        
        //jika menyertakan data tambahan untuk view
        if(isset($this->data['viewdata'])&&is_array($this->data['viewdata'])){
            $dataTmp = array_merge($dataTmp,$this->data['viewdata']);
            $this->viewdata = $this->data['viewdata'];
        }
        
        if(!$dataTmp)$dataTmp=[];
        $this->data = $dataTmp;
    }
    
    /**
     * Response output view layer
     */
    private function viewResponse()
    {
        $this->prepareView();
        $this->prepare();
        
        //jika string berarti view
        if(is_string($this->response)){
            $this->response = view($this->response, $this->data);
        }else{
            //tambahkan get parameter jika menyertakan viewdata
            if($this->viewdata){
                $redirectUrl = $this->viewResponseProccParam(
                    $this->response->getTargetUrl(),
                    $this->viewdata
                    );
                $this->response = $this->response->setTargetUrl($redirectUrl);
            }
        }
        if($this->alert)\Session::put('alert', $this->alert);
        
        if($this->errors)$this->response = $this->response->withErrors($this->errors);
        
        return $this->response;
    }
    
    private function viewResponseProccParam($redirectUrl,$addQuery)
    {
        $resultUrl = \parse_url($redirectUrl);
        $addQuery = http_build_query($addQuery);
        if(isset($resultUrl['query'])){
            $resultUrl['query'] = $resultUrl['query'].'&'.$addQuery;
        }else{
            $resultUrl['query'] = $addQuery;
        }
        $resultUrl = $resultUrl['scheme'].'://'.$resultUrl['host'].(isset($resultUrl['path'])?$resultUrl['path']:'').(isset($resultUrl['query'])?'?'.$resultUrl['query']:'');
        return $resultUrl;
    }

}
