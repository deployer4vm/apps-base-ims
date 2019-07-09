<?php

namespace App\Base;

use Illuminate\Support\Facades\Cache;

abstract class BaseRepository {

    use RepoCacheTrait;

    //default model
    protected $model;

    //list field yg dimasukan untuk search
    protected $searchField = ['name'];

    /*
     * cache var
     * -------------------------------------------------------------------------
     */
    protected $error = '';//error strinng
    protected $errorCode = 0;//error code

    /**
     * DONE
     * get error string
     * 
     * @return string       error string
     */
    public function error() {
        return $this->error;
    }

    /**
     * DONE
     * get error code
     * 
     * @return integer       error code
     */
    public function errorCode() {
        return $this->errorCode;
    }

    /**
     * DONE
     * get deafault model
     * 
     * @return eloquent model
     */
    public function getModel() {
        return $this->model;
    }

    /**
     * DONE
     * set default model
     * 
     * @param type $model
     */
    public function setModel($model) {
        $this->model = $model;
    }

    /*
     * MAIN FUNCTION
     * -------------------------------------------------------------------------
     */

    /**
     * SEMENTARA TIDAK DIGUNAKAN
     * 
     * deteksi parameter where, apakah memiliki param $field (default "id") atau tidak,
     * jika memiliki maka value dari field tersebut akan di retur
     * 
     * @param array|string      $key
     * @param string            $value
     * @param string            $field
     * 
     * @return false|mix    false jika bukan $field, atau value $field nya jika berdasarkan $field
     */
    // protected function _isField($key, $field = 'id') {
    //     if (isset($key[$field]))
    //         return $key[$field];
    //     if ($field == 'id' && !is_array($key))
    //         return $key;
    //     return false;
    // }

    /**
     * TIDAK DIGUNAKAN, DOUBLE SAMA DENGAN _filterField()
     * filter result data
     * menghapus semua data berdasarkan key yang tidak ada di $availableColumn
     * 
     * @param array         $data data record array yang akan difilter
     * @param array         $availableColumn array list nama field yang boleh ada
     * @return array        array record hasil filternya
     */
    // protected function _filterData($data, $availableColumn = []) {
    //     $userData = [];
    //     foreach ($availableColumn as $value) {
    //         if (isset($data[$value]))
    //             $userData[$value] = $data[$value];
    //     }
    //     if (isset($userData))
    //         return $userData;
    //     return [];
    // }


    /**
     * DONE
     * 
     * cek apakah seluruh data yang akan digunakan untuk input / update telah sesuai
     * dengan available list field
     * 
     * @param array        $inputData   array input datanya (untuk update atau craate)
     * @param array        $fields      list available field nya
     * @return bolean                   true jika sesuai, false jika tidak sesuai
     */
    protected function _checkField($inputData, $fields = null) {
        $collection = collect($inputData);

        return $collection->every(function ($value, $key) use ($fields) {
            return in_array($key, $fields);
        });
    }

    /**
     * DONE
     * 
     * filter data yang akan di input / update, jika ada field yang tidak sesuai dengan
     * list available field maka akan dihapus
     * 
     * @param array        $fields      list available field nya
     * @param array        $inputData   input datanya
     * @return array                    hasil filter data
     */
    protected function _filterField($inputData, $fields = null) {
        $collection = collect($inputData);

        return $collection->filter(function ($value, $key) use ($fields) {
            return in_array($key, $fields);
        });
    }

    /**
     * DONE
     * generate basic where function
     * 
     * @param eloquent instance $model
     * @param array $filter
     * @return eloquent instance 
     */
    protected function _setFilterWhere($model, $filter) {
        //jika filter diisi selain array maka asumsikan isinya adalah id table
        if (!is_array($filter)) {
            $filter = [['id', $filter]];
        }

        foreach ($filter as $value) {
            //jika sudah tidak nested maka langsung proses
            if (!is_array($value[1])) {
                $model = $this->__generateWhere($model, $value);                
            } else {
                $varWhere = 'where';
                //detek apakah or
                if(!is_array($value[0]) && $value[0] == 'or'){
                    unset($value[0]);
                    $varWhere = 'orWhere';
                }
                $model = $model->$varWhere(function($model) use ($value){
                    $model = $this->_setFilterWhere($model, $value);
                });
            }
        }

        return $model;
    }

    /**
     * DONE
     * helper untuk _setFilterWhere()
     */
    private function __generateWhere($model, $value){
        $op = '=';
        $field = $value[0];
        $isOr = false;
        if(stripos($value[0],'or ')===0){
            $field = str_ireplace('or ','', $value[0]);
            $isOr = true;
        }
        //jika ada 3 item berarti menyertakan operator nya juga
        if(count($value)==3){
            $op = $value[1];
            $dVal = $value[2];
        }else{
            $dVal = $value[1];
        }

        if(is_array($dVal)){
            if($isOr){
                $model = $model->orWhereIn($field,$op, $dVal);
            }else{
                $model = $model->whereIn($field,$op, $dVal);
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
     * DONE
     * 
     * Default string search
     * 
     * @param eloquent instance $model
     * @param string $q query string
     * @param array $searchField list field yg di search nya
     * @return eloquent instance
     */
    protected function _setFilterSearch($model, $q, $searchField = false) {
        $model = $model->where(function($query) use ($q, $searchField) {
            foreach ($searchField as $value) {
                $query = $query->orWhere($value, 'LIKE', '%' . $q . '%');
            }
        });
        return $model;
    }

    /**
     * 
     * DONE
     * 
     * list data
     * 
     * @param eloquen instance $model model data yang digunakan
     * @param array $filter filter data jika ada
     *      filter array filter basic
     *          q string jika menyertakan ini maka akan dilakuan string filter berdasarkan field $searchField
     *          ADDITIONAL_PARAM array where untuk default filter
     *      function function($model) filter tambahan jika diperlukan
     *      searchField array list field/column yg termasuk kedalam filter search
     *      hiddeColumn array list field/column yg di hidde *      
     *      
     * @param array $orderBy
     * @param int $offset
     * @param int $limit
     * 
     * @return array
     */
    protected function _list($model, $filter, $orderBy = false, $offset = 0, $limit = 0) {
        if ($orderBy) {
            $model = $model->orderBy($orderBy[0], $orderBy[1]);
        }

        if ($filter) {
            $qSearch = false;
            if (isset($filter['filter']['q'])) {
                $qSearch = $filter['filter']['q'];
                unset($filter['filter']['q']);
            }
            if (isset($filter['function'])) {
                $model = $filter['function']($model);
            }

            if (isset($filter['filter'])) {
                $model = $this->_setFilterWhere($model, $filter['filter']);
            }

            if ($qSearch) {
                $searchField = isset($filter['searchField']) ? $filter['searchField'] : $this->searchField;
                $model = $this->_setFilterSearch($model, $qSearch, $searchField);
            }
        }

        $this->pagination['count'] = $model->count();
        $this->pagination['offset'] = $offset;
        $this->pagination['limit'] = $limit;
        $this->pagination['curPage'] = 1;
        $this->pagination['pageCount'] = 1;

        if ($limit){

            $model = $model->limit($limit)->offset($offset);
            
            $this->pagination['curPage'] = floor(($offset+1)/$limit);
            $this->pagination['pageCount'] = ceil($this->pagination['count']/$limit);
        }

        if ($model) {
            $this->pagination['data'] = $model->get()->toArray();
            //jika menyertakan hiddeColumn berarti ada column yg di hide
            if (isset($filter['hiddeColumn'])) {
                $hideColumn = $filter['hiddeColumn'];
                $collection = collect($this->pagination['data']);
                $collection->transform(function($i) use ($hideColumn) {
                    foreach ($hideColumn as $value) {
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
    protected function _getPagination($path = '', $pagination = false) {
        if (!$path)
            $path = request()->url();
        if (!$pagination)
            $pagination = $this->pagination;
        return pagination_generate($pagination, $path);
    }

    /**
     * DONE
     * 
     * fungsi utama untuk get 1 record data
     * 
     * @param eloquen instance $model
     * @param array $filter where array filter
     * @return boolean|array            false jika gagal, array record jika ada
     */
    protected function _getOne($model, $filter) {
        $data = $this->_getOneModel($model, $filter);
        return $data ? $data->toArray() : false;
    }

    /**
     * DONE
     * 
     * fungsi utama untuk get 1 record data
     * 
     * @param eloquen       $model      model eloquent
     * @param array         $filter     array where filter
     * @return eloquen                  false jika gagal, aloquent collection jika berhasil
     */
    protected function _getOneModel($model, $filter) {
        //jika array berarti berisi filter
        if (!is_array($filter)) {
            $filter = ['id',$filter];
        }

        $data = $this->_setFilterWhere($model, $filter);
        $data = $data->first();
        if (!$data)
            return false;
        return $data;
    }

    /**
     * DONE
     * detect
     * 
     * @param array $filter array where filter
     */
    protected function _exists($model, $where) {
        //jika array berarti berisi filter
        if (!is_array($where)) {
            $where = ['id',$where];
        }

        $data = $this->_setFilterWhere($model, $where);

        return $data->exists();
    }

    /**
     * DONE
     * 
     * insert new record
     * 
     * @param eloquent $model
     * @param array $data
     * @return boolean|array    false jika gagal, atau array record databasenya jika berhasil
     */
    protected function _create($model, $data) {
        if ($data = $model->create($data)) {
            return $data->toArray();
        }
        return false;
    }

    /**
     * DONE
     * 
     * Update data
     * 
     * @param eloquent          $model  instance eloquent model yang akan diupdate
     * @param string|array      $where  array where filter atau string/integer id data
     * @param array             $data   array data yang akan update
     * @return false|array              record table yang diupdatenya atau false jika gagal
     */
    protected function _update($model, $where, $data = null) {
        if (!is_array($where)) {
            $where = ['id', $where];
        }
        $model = $this->_getOneModel($model, $where);
        if ($model)
            return $model->update($data);
        return false;
    }

    /**
     * DONE
     * 
     * Delete data
     * 
     * @param eloquent          $model  instance eloquent model yang akan diupdate
     * @param string|array      $where  array where filter atau string/integer id data
     * @return boolean
     */
    protected function _delete($model, $where) {
        $model = $this->_getOneModel($model, $where);
        if ($model != false) {
            if ($model->delete()) {
                return true;
            }
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
     * @param type $filter
     * @param type $orderBy
     * @param type $offset
     * @param type $limit
     * @return type
     */
    public function getList($filter = false, $orderBy = false, $offset = 0, $limit = 0) {
        $filter = [
            'filter' => $filter,
            'searchField' => $this->searchField
        ];

        return $this->_list($this->model, $filter, $orderBy, $offset, $limit);
    }

    /**
     * Default pagination function
     * 
     * @param string $path
     * @param array $pagination data pagination
     * @return pagination laravel object
     */
    public function getPagination($path = '', $pagination = false) {
        return $this->_getPagination($path, $pagination);
    }

    /**
     * method default untuk get 1 record data model utama
     * 
     * @param array/string $key
     * @param string/null $value
     * @return array / false
     */
    public function getOne($key, $value = null) {
        return $this->_getOne($this->model, $key, $value);
    }

    /**
     * cek apakah data yg dimaksud ada
     * 
     * @param array/string $key
     * @param string/null $value
     * @return booleadn
     */
    public function exist($key, $value = null) {
        return $this->_exists($this->model, $key, $value);
    }

    /**
     * default create new data function
     * 
     * @param array $data
     * @return array/false
     */
    public function create($data) {
        return $this->_create($this->model, $data);
    }

    /**
     * 
     * @param array/string $key
     * @param array/string $value jika $data null berarti $value berisi $data
     * @param array/null $data
     * @return array/false
     */
    public function update($key, $value, $data = null) {
        return $this->_update($this->model, $key, $value, $data);
    }

    public function delete($key, $value = null) {
        return $this->_delete($this->model, $key, $value);
    }

}
