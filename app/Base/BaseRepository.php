<?php

namespace App\Base;

use Exception;
use Illuminate\Support\Facades\Validator;
use App\Base\Traits\ResCacheTrait;

abstract class BaseRepository {

    use ResCacheTrait;

    //default model
    protected $model;

    //list field yg dimasukan untuk search
    protected $searchField = ['name'];

    protected $pagination = [
        'count' => 0,
        'offset' =>0,
        'limit' => 0,
        'currentPage' => 1,
        'pageCount' => 1
    ];//default hasil list disimpan

    private $paginationDefault = [
        'count' => 0,
        'offset' =>0,
        'limit' => 0,
        'currentPage' => 1,
        'pageCount' => 1

    ];//default data untuk $pagination


    /**
     * BLOCK CLASS DEPENDENCY
     * -------------------------------------------------------------------------------------
     * 
     * class dependendency ini digunakan untuk dependency ke class yg menggunakan bindind interface & model nya.
     * dibuat seperti ini karena saat repository di binding dengan interface, maka sulit untuk repository tersebut mendependency ke repository yg juga binding dengna interface.
     */    
    private $dependencyLoaded = [];//list instance dari class dependency yg telah diload
    protected $dependency = [
        // 'jurnal' => 'App\MainApp\Modules\Pembukuan\Contracts\Jurnal' --> list array dependency
    ];

    /**
     * load dependency
     */
    public function __get($name) 
    {
        if(isset($this->dependency[$name])){
            if(!isset($this->dependencyLoaded[$name])){
                $this->dependencyLoaded[$name] = resolve($this->dependency[$name]);
                if(method_exists($this->dependencyLoaded[$name],'setTenantId'))
                    $this->dependencyLoaded[$name]->setTenantId($this->tenantId);
            }
            return $this->dependencyLoaded[$name];
        }
        throw new Exception("Property $name is not defined");
    }  
    /**
     * -------------------------------------------------------------------------------------
     * / BLOCK CLASS DEPENDENCY
     */

    /**
     * BLOCK AUTO RESOURCE
     * -------------------------------------------------------------------------------------
     * 
     * auto fungsion
     */
    protected $autoResource = [
        // 'Workshop' => 'hpsynapse\modworkshop\Models\Ws' // 1 general model
        // 'Workshop' => [
        //     'c' => 'hpsynapse\modworkshop\Models\Ws',
        //     'r' => 'hpsynapse\modworkshop\Models\Ws',
        //     'u' => 'hpsynapse\modworkshop\Models\Ws',
        //     'd' => 'hpsynapse\modworkshop\Models\Ws'
        // ]
    ];
    protected $autoResourceSearchField = [
        // 'Workshop' => ['name'] --> list array workshop
    ];
    //list field validation saat create
    protected $autoResourceCreateValidate = [
        // 'Workshop' => ['nama'=>'required']
    ];
    //list field validation saat update
    protected $autoResourceUpdateValidate = [
        // 'Workshop' => ['nama'=>'required']
    ];

    public function __call($name, $arguments)
    {
        if(strpos($name,'list')===0){
            return $this->_autoResourceList($name,$arguments);
        }else if(strpos($name,'get')===0){
            return $this->_autoResourceGet($name,$arguments);
        }else if(strpos($name,'create')===0){
            return $this->_autoResourceCreate($name,$arguments);
        }else if(strpos($name,'update')===0){
            return $this->_autoResourceUpdate($name,$arguments);
        }else if(strpos($name,'delete')===0){
            return $this->_autoResourceDelete($name,$arguments);
        }else if(strrpos($name,'Exists')){
            return $this->_autoResourceExists($name,$arguments);
        }

        throw new Exception("Method $name is not defined");
    }

    private function _autoResourceGetModel($resource,string $crud='r')
    {
        if(is_array($resource) && isset($resource[$crud])){
            $resource = $resource[$crud];
        }
        return is_callable($resource)?$resource:new $resource;
    }

    /**
     * resource list
     */
    protected function _autoResourceList($name, $arguments)
    {
        $model = substr($name, 4);
        if(isset($this->autoResource[$model])){

            $filter = isset($arguments[0])?$arguments[0]:[];
            //jika tidak menyertakan searchfield maka gunakan default
            if(!isset($filter['searchField']))
                $filter['searchField'] = 
                    isset($this->autoResourceSearchField[$model])?
                    $this->autoResourceSearchField[$model]:
                    $this->searchField;

            return $this->_list(
                $this->_autoResourceGetModel($this->autoResource[$model]), 
                $filter, 
                isset($arguments[1])?$arguments[1]:0, 
                isset($arguments[2])?$arguments[2]:0, 
                isset($arguments[3])?$arguments[3]:[]);
        }
        
        throw new Exception("Method $name is not defined");
    }

    /**
     * resource get 1 record
     */
    protected function _autoResourceGet($name, $arguments)
    {
        $model = substr($name, 3);
        if(isset($this->autoResource[$model])){
            return $this->_getOne(
                $this->_autoResourceGetModel($this->autoResource[$model]), 
                isset($arguments[0])?$arguments[0]:null);
        }
        
        throw new Exception("Method $name is not defined");
    }

    /**
     * resource create
     */
    protected function _autoResourceCreate($name, $arguments)
    {
        $model = substr($name, 6);
        if(isset($this->autoResource[$model])){
            $data = isset($arguments[0])?$arguments[0]:[];
            if(isset($this->autoResourceUpdateValidate[$model])){
                //jika error/tidak valid
                if(!$this->_createValidate($this->autoResourceCreateValidate[$model], $data)){
                    return false;
                }
            }
            return $this->_create(
                $this->_autoResourceGetModel($this->autoResource[$model]), 
                $data);
        }
        
        throw new Exception("Method $name is not defined");
    }
    
    /**
     * resource update
     */
    protected function _autoResourceUpdate($name, $arguments)
    {
        $model = substr($name, 6);
        if(isset($this->autoResource[$model])){
            $data = isset($arguments[1])?$arguments[1]:[];
            if(isset($this->autoResourceUpdateValidate[$model])){
                //jika error/tidak valid
                if(!$this->_updateValidate($this->autoResourceUpdateValidate[$model], $data)){
                    return false;
                }
            }
            return $this->_update(
                $this->_autoResourceGetModel($this->autoResource[$model]), 
                isset($arguments[0])?$arguments[0]:null, 
                $data);
        }
        
        throw new Exception("Method $name is not defined");
    }

    /**
     * resource delete
     */
    protected function _autoResourceDelete($name, $arguments)
    {
        $model = substr($name, 6);
        if(isset($this->autoResource[$model])){
            return $this->_delete(
                $this->_autoResourceGetModel($this->autoResource[$model]), 
                isset($arguments[0])?$arguments[0]:null);
        }
        
        throw new Exception("Method $name is not defined");
    }

    /**
     * resource detect exists
     */
    protected function _autoResourceExists($name, $arguments)
    {
        $model = ucfirst(substr($name,0, -6));
        if(isset($this->autoResource[$model])){
            return $this->_exists(
                $this->_autoResourceGetModel($this->autoResource[$model]), 
                isset($arguments[0])?$arguments[0]:null);
        }
        
        throw new Exception("Method $name is not defined");
    }

    /**
     * -------------------------------------------------------------------------------------
     * / BLOCK AUTO RESOURCE
     */

    protected $tenantId = 0;
    public function setTenantId(int $tenantId=0)
    {
        $this->tenantId = $tenantId;
    } 

    
    protected $error = '';//error message string
    protected $errorValidator = [];//error validator/request
    protected $errorCode = 0;//error code

    public function clearError(){
        $this->error = '';
        $this->errorValidator=[];
        $this->errorCode=0;
    }
    /**
     * get error string
     * 
     * @return string       error string
     */
    public function error() {
        return $this->error;
    }
    /**
     * get error string
     * 
     * @return string       error string
     */
    public function errorValidator() {
        return $this->errorValidator;
    }
    /**
     * get error code
     * 
     * @return integer       error code
     */
    public function errorCode() {
        return $this->errorCode;
    }

    /**
     * get deafault model
     * 
     * @return eloquent model
     */
    public function getModel() {
        return $this->model;
    }

    /**
     * set/re-set default model
     * 
     * @param eloquent $model
     */
    public function setModel($model) {
        $this->model = $model;
    }

    /**
     * get default output untuk listing
     */
    protected function getDefaultListFormat()
    {
        return $this->paginationDefault;
    }

    /*
     * MAIN FUNCTION
     * -------------------------------------------------------------------------
     */

    /**
     * cek apakah key di $inputData ada semua di $availableFields
     * 
     * @param array         $inputData          array data
     * @param array         $availableFields    list available field nya
     * 
     * @return boolean                           true jika sesuai, false jika tidak sesuai
     */
    final protected function _checkField(array $inputData = [], $availableFields = null) 
    {
        $collection = collect($inputData);

        return $collection->every(function ($value, $key) use ($availableFields) {
            return in_array($key, $availableFields);
        });
    }

    /**
     * Hapus semua item array $item yang tidak ada di $allowedFields.
     * 
     * filter array hanya berdasarkan key yg diallow nya saja
     * filter $data yang akan di update / output / field, jika ada field yang tidak sesuai dengan
     * list allowedFields field maka akan dihapus
     * 
     * @param array         $data               input datanya
     * @param array         $allowedFields    daftar field-field yang boleh ada di $data
     * 
     * @return array                            hasil filter data
     */
    protected function _filterAllowField(array $data = [], array $allowedFields = null) 
    {
        $collection = collect($data);

        return $collection->filter(function ($value, $key) use ($allowedFields) {
            return in_array($key, $allowedFields);
        })->toArray();
    }

    /**
     * Hapus semua item array $item yang ada di $rejectedFields.
     * 
     * filter array berdasarkan field yang tidak boleh ada (dihapus)
     * filter data yang akan di update / output / field, jika ada field yang terdaftar
     * di rejectedField maka akan dihapus
     * 
     * @param array        $data                input datanya
     * @param array        $rejectedFields      list field yang akan dihapus
     * @return array                            hasil filter data
     */
    final protected function _filterField($data, $rejectedFields = null) 
    {
        $collection = collect($data);

        return $collection->filter(function ($value, $key) use ($rejectedFields) {
            return !in_array($key, $rejectedFields);
        })->toArray();
    }

    /**
     * hapus semua data yang value nya kosong
     * 
     * @param array     $data       array data yang difilter
     * 
     * @return array                hasil data yang sudah difilter
     */
    final protected function _filterEmptyField(array $data = []) 
    {
        $collection = collect($data);
        return $collection->filter(function ($value, $key) {
            return !empty($value);
        })->toArray();
    }

    /**
     * DONE
     * generate basic where function
     * 
     * @param eloquent      $model
     * @param array         $filter     synapse where format
     * 
     * @return eloquent  
     */
    final protected function _where($model, $where) 
    {
        //jika sudah kosong maka langsung kembalikan model nya
        if(empty($where))return $model;

        //jika where di isi selain array maka asumsikan isinya adalah id table
        if (!is_array($where)) {
            $where = [['id', $where]];
        }

        //berarti sudah tidak nested, harusnya yang sudah tidak nested tidak masuk ke sini
        if (isset($where[0]) && !is_array($where[0]) && $where[0] != 'or') {
            $model = $this->__whereNotNested($model, $where); 
        }else{
            $model = $this->__whereNested($model, $where); 
        }

        return $model;
    }

    /**
     * helper untuk _where(), memproses array where yang masih nested
     * 
     * @param Eloquent $model
     * @param array $where
     * 
     * @return eloquent  
     */
    private function __whereNested($model, $where){

        foreach ($where as $value) {
            //jika value[1] tidak ada kemungkinan ada yang keliru input format, maka langsung tolak
            if(!isset($value[1]))return $model;

            //jika sudah tidak nested maka langsung proses
            if (is_array($value) && !is_array($value[0]) && strtolower($value[0]) != 'or') {
                $model = $this->__whereNotNested($model, $value);  
            //jika masih nested maka process recursive lagi              
            } else {
                $varWhere = 'where';
                //detek apakah or
                if(!is_array($value[0]) && strtolower($value[0]) == 'or'){
                    unset($value[0]);
                    $varWhere = 'orWhere';
                }
                $model = $model->$varWhere(function($model) use ($value){
                    $model = $this->_where($model, $value);
                });
            }
        }
        return $model;
    }

    /**
     * helper untuk _where(), memproses array where yang sudah tidak nested
     * 
     * @param Eloquent $model
     * @param array $where
     * 
     * @return eloquent  
     */
    private function __whereNotNested($model, $where)
    {
        
        $op = '=';
        $field = $where[0];
        $isOr = false;
        if(stripos($where[0],'or ')===0){
            $field = str_ireplace('or ','', $where[0]);
            $isOr = true;
        }
        //jika ada 3 item berarti menyertakan operator nya juga
        if(count($where)==3){
            $op = $where[1];
            $dVal = $where[2];
        }else{
            $dVal = $where[1];
        }
        if(is_array($dVal)){
            if($isOr){
                $model = $model->orWhereIn($field,$dVal);
            }else{
                $model = $model->whereIn($field,$dVal);
            }                    
        }else{
            if($isOr){
                $model = $model->orWhere($field,$op, $dVal);
            }else{
                $model = $model->where($field,$op, $dVal);                        
            }
        }   
        return $model;
    }

    /**
     * pemrosesan default search data berdasarkan $q string yg diinput
     * 
     * @param eloquent instance $model
     * @param string $q query string
     * @param array $searchField list field yg di search nya
     * 
     * @return eloquent instance
     */
    final protected function _searchString($model, $q, $searchField = false) 
    {
        $model = $model->where(function($query) use ($q, $searchField) {
            foreach ($searchField as $value) {
                $query = $query->orWhere($value, 'LIKE', '%' . $q . '%');
            }
        });
        return $model;
    }

    /**
     * Default fungsi list data
     * 
     * @param eloquen instance $model model data yang digunakan
     * @param array $filter filter data jika ada
     *      q string jika menyertakan ini maka akan dilakuan string filter berdasarkan field $searchField     * 
     *      function function($model) filter tambahan jika diperlukan
     *      searchField array list field/column yg termasuk kedalam filter search
     *      hiddenColumn array list field/column yg di hidde * -- HINDARI PENGGUNAAN HIDDEN COLUMN UNTUK DATA BESAR
     *     
     *      ADDITIONAL_PARAM array where untuk default filter
     * 
     * @param array $orderBy [['field','DESC/ASC'],['other_field','ASC/DESC']] atau array 1 level jika memang cuma 1 yg di order by nya
     * @param int $offset
     * @param int $limit jika 0 maka view all
     * 
     * @return array
     */
    final protected function _list($model, array $filter = [], int $offset = 0, int $limit = 0, array $orderBy = []) 
    {

        if (!empty($orderBy)) {

            if(!is_array($orderBy[0]))$orderBy=[$orderBy];

            foreach($orderBy as $oBitem){
                $model = $model->orderBy($oBitem[0], $oBitem[1]);
            }
        }

        $hiddenColumn = null;
        if (!empty($filter)) {
            $model = $this->_filter($model,$filter);            
            if (isset($filter['hiddenColumn'])) {                
                $hiddenColumn = $filter['hiddenColumn'];
                unset($filter['hiddenColumn']);
            }
            unset($filter);
        }

        if(empty($model)){
            $this->pagination = $this->getDefaultListFormat();
            return $this->pagination;
        }
        
        $this->pagination['count'] = $model->count();
        $this->pagination['offset'] = $offset;
        $this->pagination['limit'] = $limit;
        $this->pagination['currentPage'] = 1;
        $this->pagination['pageCount'] = 1;

        if ($limit){
            $model = $model->limit($limit)->offset($offset);
            
            $this->pagination['currentPage'] = (int) ceil(($offset+1)/$limit);
            $this->pagination['pageCount'] = (int) ceil($this->pagination['count']/$limit);
        }
        
        if ($model) {
            // $this->pagination['query'] = $model->toSql();
            // $this->pagination['queryBindings'] = $model->getBindings();
            $this->pagination['data'] = $model->get()->toArray();
            //jika menyertakan hiddeColumn berarti ada column yg di hide
            if ($hiddenColumn) {
                $collection = collect($this->pagination['data']);
                $collection->transform(function($i) use ($hiddenColumn) {
                    foreach ($hiddenColumn as $value) {
                        unset($i[$value]);
                    }
                    return $i;
                });
                $this->pagination['data'] = $collection->toArray();
            }
        } else {
            $this->pagination['data'] = [];
        }        
        return $this->pagination;
    }

    final protected function _filter($model,array $filter=[])
    {
        if(empty($model))return $model;

        $qSearch = null;
        $searchField = null;

        if(empty($filter['q']))
            unset($filter['q']);

        if(empty($filter['hiddenColumn']))
            unset($filter['hiddenColumn']);

        if(empty($filter['searchField']))
            unset($filter['searchField']);
            
        if (isset($filter['q'])) {
            $qSearch = $filter['q'];
            unset($filter['q']);
        }

        if (isset($filter['searchField'])) {
            $searchField = $filter['searchField'];
            unset($filter['searchField']);
        }

        if (isset($filter['function'])) {
            $model = $filter['function']($model);
            unset($filter['function']);
        }            

        //hiddenColumn digunakan di filter saat result
        if (isset($filter['hiddenColumn'])) {
            unset($filter['hiddenColumn']);
        }

        if (isset($filter)) {
            $model = $this->_where($model, $filter);
        }
        
        if ($qSearch) {
            $searchField = $searchField ? $searchField : $this->searchField;
            $model = $this->_searchString($model, $qSearch, $searchField);
        }
        return $model;
    }

    /**
     * DONE
     * Generate pagination untuk di view blade (menggunakan pagination laravel)
     * 
     * @param string $path path paginationnya
     * @param array $pagination
     *      count
     *      offset
     *      limit
     *      data
     * @return pagination instance
     */
    final protected function _getPagination(string $path = '', $pagination = false)
    {
        if (!$path)
            $path = request()->url();
        if (!$pagination)
            $pagination = $this->pagination;
        return pagination_generate($pagination, $path);
    }

    /**
     * fungsi utama untuk get 1 record data
     * 
     * @param eloquen instance  $model
     * @param array|int         $filter     synapse array where filter format, atau id table
     * 
     * @return false|array    false jika gagal, array record jika ada
     */
    final protected function _getOne($model, $where)
    {
        if(empty($model))return $model;

        $data = $this->_getOneModel($model, $where);
        return $data ? $data->toArray() : false;
    }

    /**
     * DONE
     * 
     * fungsi utama untuk get 1 record data
     * 
     * @param eloquen           $model      model eloquent
     * @param array|int         $where     synapse array where filter format, atau id table
     * 
     * @return eloquen                      false jika gagal, aloquent collection jika berhasil
     */
    final protected function _getOneModel($model, $where)
    {
        if(empty($model))return $model;

        //jika array berarti berisi where
        if (!is_array($where)) {
            $where = [['id',$where]];
        }

        $data = $this->_where($model, $where);
        $data = $data->first();
        
        if (!$data)
            return false;

        return $data;
    }
    
    /**
     * DONE
     * detect
     * 
     * @param eloquen           $model      model eloquent
     * @param array|int         $filter     synapse array where filter format, atau id table
     * 
     * @return boolean
     */
    final protected function _exists($model, $where): bool
    {
        if(empty($model))return false;
        //jika array berarti berisi filter
        if (!is_array($where)) {
            $where = ['id',$where];
        }

        $data = $this->_where($model, $where);

        return $data->exists();
    }

    /**
     * insert new record
     * 
     * @param eloquent $model
     * @param array $data
     * @return null|array    null jika gagal, atau array record databasenya jika berhasil
     */
    final protected function _create($model, array $data) 
    {
        $this->clearError();
        //get QueryExeption
        try { 
            if ($data = $model->create($data)) {
                return $data->toArray();
            }
        }catch (\Illuminate\Database\QueryException $ex){
            $this->error = $ex->getMessage();
        }
        return null;
    }

    /**
     * validasi menggunakan laravel Validator saat create
     * 
     * @param array $rules validator rule, key : nama field, value : rule
     * @param array $data data input nya
     * @return boolean valid atau tidak valid
     */
    final protected function _createValidate(array $rules, array $data)
    {
        $validator = Validator::make($data,$rules);
        if ($validator->fails()) {
            $this->error = __('alert.form_must_complete_title');
            $this->errorValidator = $validator->errors()->all();
            return false;
        }
        return true;
    }

    /**
     * 
     * Update data
     * 
     * @param eloquent          $model  instance eloquent model yang akan diupdate
     * @param array|int         $where  array where filter atau string/integer id data
     * @param array             $data   array data yang akan update
     * 
     * @return boolean|integer     effected arrow atau true jika berhasil, false jika gagal
     */
    final protected function _update($model, $where, array $data) 
    {
        $this->clearError();
        //get QueryExeption
        try { 
            if (!is_array($where)) {
                $where = [['id', $where]];
            }
            $model = $this->_where($model, $where);
        
            if ($model){
                $return = $model->update($data);
                return $return==null?true:$return;
            }
        }catch (\Illuminate\Database\QueryException $ex){
            $this->error = $ex->getMessage();
        }
        
        return false;
    }

    /**
     * validasi menggunakan laravel Validator saat update
     * 
     * @param array $rules validator rule, key : nama field, value : rule
     * @param array $data data input nya
     * @return boolean valid atau tidak valid
     */
    final protected function _updateValidate(array $rules, array $data)
    {       
        $validateRule = [];
        foreach($rules as $field => $rule){
            if(isset($data[$field]))$validateRule[$field] = $rule;
        }
        $validator = Validator::make($data,$validateRule);
        if ($validator->fails()) {
            $this->error = __('alert.form_must_complete_title');
            $this->errorValidator = $validator->errors()->all();
            return false;
        }
        return true;
    }

    /**
     * 
     * Delete data
     * 
     * @param eloquent          $model  instance eloquent model yang akan diupdate
     * @param array|int         $where  array synapse where format atau integer id data
     * 
     * @return boolean
     */
    final protected function _delete($model, $where): bool
    {
        if(empty($model))return false;

        $model = $this->_where($model, $where);
        if ($model != false) {
            if ($model->delete()) {
                return true;
            }            
        }else{
            $this->error = __('lang.data_not_found');
        }
        return false;
    }

    /*
     * MAIN MODEL IMPLEMENTATION
     * -------------------------------------------------------------------------
     */

    /**
     * method default untuk listing data model default
     * 
     * @param array         $filter     array synapse filter format
     * @param int           $offset
     * @param int           $limit
     * @param array         $orderBy
     * 
     * @return array|null
     */
    public function getList(array $filter = [], int $offset = 0, int $limit = 0, array $orderBy = []) 
    {
        return $this->_list($this->model, $filter, $offset, $limit, $orderBy);
    }

    /**
     * Default pagination function
     * 
     * @param string $path
     * @param array $pagination data pagination
     * @return pagination laravel object
     */
    public function getPagination($path = '', $pagination = false) 
    {
        return $this->_getPagination($path, $pagination);
    }

    /**
     * method default untuk get 1 record data model utama
     * 
     * @param array|int         $where  array synapse where format atau integer id data
     * 
     * @return array|null
     */
    public function getOne($where) 
    {
        return $this->_getOne($this->model, $where);
    }

    /**
     * cek apakah data yg dimaksud ada
     * 
     * @param array|int         $where  array synapse where format atau integer id data
     * 
     * @return boolean
     */
    public function exists($where):bool
    {
        return $this->_exists($this->model, $where);
    }

    /**
     * default create new data function
     * 
     * @param array $data
     * 
     * @return array|null
     */
    public function create(array $data) 
    {
        return $this->_create($this->model, $data);
    }

    /**
     * 
     * @param array|integer     $key
     * @param array             $data
     * 
     * @return null|integer     effected arrow atau null jika gagal
     */
    public function update($where, array $updatedData) 
    {
        return $this->_update($this->model, $where, $updatedData);
    }

    /**
     * @param array|int         $where  array synapse where format atau integer id data
     */
    public function delete($where) 
    {
        return $this->_delete($this->model, $where);
    }

}