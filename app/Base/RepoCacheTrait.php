<?php
namespace App\Base;

use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

trait RepoCacheTrait {    
    
    /**
     * data cache yang sudah di load di local variable per eksekusi per class (controller, repo, dll), format
     * $cacheData[$cacheMainPrefix.$prefix][$id] = $dataCache;
     */
    protected $cacheData;
    protected $cacheIndex;

    protected $cacheIndexField=[];//belum beres
    
    /*
     * $_cachedMethod array list nama method berdasarkan prefix (groupdata) nya
     * format :
     *      ['prefix'=> ['method'],..]
     */
//    private $_cachedMethod = [];
    //prefix utama
    protected $cacheMainPrefix = '';
    protected $cacheActive = false;
    protected $cacheEngine = 'file';
    protected $skipCache = false;

    /**
     * BELUM SELESAI, TEKNIS INDEXING NYA BELUM DIRAMPUNGKAN
     * 
     * get nama field dan value yang dijadikan index cache
     * 
     * @param string            $prefix prefix cache nya
     * @param mix               $key key cache nya
     * @param mix               $value value filter
     * @return false|array      false jika tidak ada index
     */
    protected function _getCacheIndex($prefix, $key, $value=false)
    {
        if(is_array($key)){
            foreach ($key as $field => $val) {
                if(in_array($field, $this->cacheIndexField[$prefix])){
                    return [
                        'field' => $field,
                        'value' => $val
                    ];
                }
            }
        }else{
            if(in_array($key, $this->cacheIndexField[$prefix])){
                return [
                    'field' => $key,
                    'value' => $value
                ];
            }
        }
        return false;
    }

    /**
     * set engine cache yg akan digunakan di repo ini
     * 
     * @param string            $cacheEngine engine cache nya
     */
    protected function _setCacheEngine($cacheEngine)
    {
        $this->cacheEngine = $cacheEngine;
    }
    
    /**
     * set engine cache yg akan digunakan di repo ini
     * 
     * @param string            $cacheEngine engine cache nya
     */
    protected function _setCacheEngineStatus($cacheActive)
    {
        $this->cacheActive = $cacheActive;
    }
    
    /**
     * save / update cache
     * 
     * @param string            $prefix sub-prefix
     * @param string            $id key
     * @param string            $data 
     * @param string            $expireDate 
     * @param array             $fields 
     */
    protected function _saveCache($prefix,$id,$data,$expireDate=false)
    {        
        if($this->skipCache)return false;   
        if(!$this->cacheActive)return false;//$this->_saveCacheOnEngine($prefix,$key,$data,$expireDate);
        
        $fullPrefix = $this->cacheMainPrefix.'.'.$prefix;
        $fullPrefixKey = $fullPrefix.'.'.$id;
        
        if($expireDate){
            if(is_string($expireDate))$expireDate = Carbon::parse($expireDate);
            Cache::store($this->cacheEngine)->put($fullPrefixKey,$data,$expireDate);
        }else{
            Cache::store($this->cacheEngine)->forever($fullPrefixKey,$data);
        }        
        //save di local var
        $this->cacheData[$fullPrefix][$id] = $data;
        
        return true;
        /**
         * BAGIAN INDEXING NYA MAH BELUM DIGUNAKAN, KARENA BELUM RAMPUNG
         */
        //load index per prefix nya       
        if(isset($this->cacheIndexField[$prefix])){
            $fields = $this->cacheIndexField[$prefix];
        }else{
            $fields = [];
        }  
        
        //tambahkan index repo
        foreach ($fields as $field) {
            if(isset($data[$field])){
                
                //simpan index
                if($expireDate){
                    Cache::store($this->cacheEngine)->put($fullPrefix.'.index.'.$field.'.'.$data[$field],$key,$expireDate);
                }else{
                    Cache::store($this->cacheEngine)->forever($fullPrefix.'.index.'.$field.'.'.$data[$field],$key);
                }
            }
        }
        return true;
    }
    
    /**
     * 
     * @param string        $prefix
     * @param string        $id id / prefix key per data nya
     * @return mix
     */
    protected function _getCache($prefix,$id,$defaultValue=null)
    {
        if($this->skipCache)return null;        
        if(!$this->cacheActive)return false;//$this->_getCacheOnEngine($prefix,$key,$defaultValue);
        
        $fullPrefix = $this->cacheMainPrefix.'.'.$prefix;
        $fullPrefixKey = $fullPrefix.'.'.$id;

        //jika sudah diload sebelumnya maka ambil dari local var
        if(isset($this->cacheData[$fullPrefix]) && isset($this->cacheData[$fullPrefix][$id]))
            return $this->cacheData[$fullPrefix][$id];
        
        if (Cache::store($this->cacheEngine)->has($fullPrefixKey)) {
            $this->cacheData[$fullPrefix][$id] = Cache::store($this->cacheEngine)->get($fullPrefixKey);
            return $this->cacheData[$fullPrefix][$id];
        }
        return $defaultValue;
    }    
    
    /**
     * 
     * @param string        $prefix
     * @param string        $id
     * @return mix
     */
    protected function _hasCache($prefix,$id)
    {
        if($this->skipCache)return false;  
        if(!$this->cacheActive)return false;//$this->_hasCacheOnEngine($prefix,$key);
        
        $fullPrefix = $this->cacheMainPrefix.'.'.$prefix.'.'.$id;
        if(Cache::store($this->cacheEngine)->has($fullPrefix))return true;
        return false;
//        $prefix = $this->cacheMainPrefix.'.'.$prefix;
//        
//        if(isset($this->cacheData[$prefix][$key]))
//            return true;
//        return false;
    }
    
    /**
     * 
     * @param type $prefix
     * @param type $id
     * @return boolean
     */
    protected function _deleteCache($prefix,$id)
    {
        if($this->skipCache)return false;  
        if(!$this->cacheActive)return false;
        
        $fullPrefix = $this->cacheMainPrefix.'.'.$prefix;
        $fullPrefixKey = $fullPrefix.'.'.$id;
        
        Cache::store($this->cacheEngine)->forget($fullPrefixKey);

        if(isset($this->cacheData[$fullPrefix]) && isset($this->cacheData[$fullPrefix][$id]))
            unset($this->cacheData[$fullPrefix][$id]);
        
        /**
         * JIKA FITUR INDEXING SUDAH RAMPUNG MAKA DISINI TAMBAHKAN JUGA 
         * CODING UNTUK HAPUS INDEXING YG BERSANGKUTAN
         */
    }
    
    /**
     * 
     * @param string        $prefix prefix cache nya
     * @param string        $field nama field index nya
     * @param string        $fieldValue value index nya
     * @param string        $defaultValue value index nya
     */
    protected function _getCacheByIndex($prefix,$field,$fieldValue,$defaultValue=null)
    {
        if($this->skipCache)return null;        
        if(!$this->cacheActive)return false;//$this->_getCacheByIndexOnEngine($prefix,$field,$fieldValue,$defaultValue);
                
        $fullPrefix = $this->cacheMainPrefix.'.'.$prefix;        
        $indexPrefix = $fullPrefix.'.index.'.$field.'.'.$fieldValue;
        
        if (Cache::store($this->cacheEngine)->has($indexPrefix)) {
            $id = Cache::store($this->cacheEngine)->get($indexPrefix);
            return Cache::store($this->cacheEngine)->get($fullPrefix.'.'.$id);
        }
        return $defaultValue;
        
//        $prefix = $this->cacheMainPrefix.'.'.$prefix;
//        
//        if(isset($this->cacheIndex[$prefix][$field][$fieldValue])){
//            $id = $this->cacheIndex[$prefix][$field][$fieldValue];
//            if(isset($this->cacheData[$prefix][$id])){
//                return $this->cacheData[$prefix][$id];
//            }
//        }
//        return $defaultValue;
    }    
    /*
     * CACHE ENGINE
     * -------------------------------------------------------------------------
     */
    
    
    /**
     * 
     * @param string $prefix nama group data
     * @param string $key
     * @param type $defaultValue
     */
//    private function _getCacheOnEngine($prefix,$key,$defaultValue=null)
//    {
//        $fullPrefix = $this->cacheMainPrefix.'.'.$prefix;
//        $fullPrefixKey = $fullPrefix.'.'.$key;
//        
//        if (Cache::store($this->cacheEngine)->has($fullPrefixKey)) {
//            return Cache::store($this->cacheEngine)->get($fullPrefixKey);
//        }
//        return $defaultValue;
//    }
    
//    private function _getCacheByIndexOnEngine($prefix,$field,$fieldValue,$defaultValue=null)
//    {
//        $fullPrefix = $this->cacheMainPrefix.'.'.$prefix;        
//        $indexPrefix = $fullPrefix.'.index.'.$field.'.'.$fieldValue;
//        
//        if (Cache::store($this->cacheEngine)->has($indexPrefix)) {
//            $id = Cache::store($this->cacheEngine)->get($indexPrefix);
//            return Cache::store($this->cacheEngine)->get($fullPrefix.'.'.$id);
//        }
//        return $defaultValue;
//    }
    
    
//    private function _saveCacheOnEngine($prefix,$key,$value)
//    {
//        $fullPrefix = $this->cacheMainPrefix.'.'.$prefix;
//        $fullPrefixKey = $fullPrefix.'.'.$key;
//        
//        Cache::store($this->cacheEngine)->forever($fullPrefixKey,$value);
//        
//        //load index per prefix nya       
//        if(isset($this->cacheIndexField[$prefix])){
//            $fields = $this->cacheIndexField[$prefix];
//        }else{
//            $fields = [];
//        }  
//        
//        //tambahkan index repo
//        foreach ($fields as $field) {
//            if(isset($value[$field])){
//                //simpan index
//                Cache::store($this->cacheEngine)->forever($fullPrefix.'.index.'.$field.'.'.$value[$field],$key);
//            }
//        }
//        return true;
//    }
    
//    private function _hasCacheOnEngine($prefix,$key)
//    {
//        $fullPrefix = $this->cacheMainPrefix.'.'.$prefix.'.'.$key;
//        if(Cache::store($this->cacheEngine)->has($fullPrefix))return true;
//        return false;
//    }
    
    /**
     * 
     * @param type $prefix
     * @param string $method
     * @param string $param
     */
//    private function _setDefaultCachaMethod($prefix,$method,$param)
//    {
//        $this->_cachedMethod[$prefix] = $method;
//    }
//    
//    private function _loadCacheOnEngine($prefix)
//    {
//        //---eksekusi method untuk load data nya
//        $this->skipCache = true;//set true agar jika di method untuk load data nya fungsi cache maka di skip
//        call_user_func_array($this->_cachedMethod[$prefix], $param_arr);
//        $this->{$this->_cachedMethod[$prefix]}();
//        $this->_cachedMethod[$prefix];
//        $this->skipCache = false;
//        
//        
//        //save cache
//        $this->_saveCacheOnEngine($prefix,$key,$value);
//        return $value;
//    }
}