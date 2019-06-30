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
     * get error string
     * 
     * @return string       error string
     */
    public function error() {
        return $this->error;
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
     * get main model
     * 
     * @return eloquent model
     */
    public function getModel() {
        return $this->model;
    }

    /**
     * set main model
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
     * deteksi parameter where, apakah berdasarkan field id atau bukan
     * 
     * @param array|string      $key
     * @param string            $value
     * @param string            $field
     * 
     * @return false|inteber    false jika bukan id, atau value id nya jika berdasarkan id
     */
    protected function _isField($key, $value = false, $field = 'id') {
        if (isset($key[$field]))
            return $key[$field];
        if ($value === false && $field == 'id' && !is_array($key))
            return $key;
        return false;
    }

    /**
     * menghapus semua data berdasarkan key yang tidak ada di $availableColumn
     * 
     * @param array         $data data array yang akan digunakan
     * @param array         $availableColumn array list nama field yang boleh ada
     * @return type
     */
    protected function _filterData($data, $availableColumn = []) {
        foreach ($availableColumn as $value) {
            if (isset($data[$value]))
                $userData[$value] = $data[$value];
        }
        if (isset($userData))
            return $userData;
        return [];
    }

    /**
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
            if (is_array($value[1])) {
                $model = $model->whereIn($key, $value);
            } else {
                $model = $model->where($key, $value);
            }
        }

        return $model;
    }

    /**
     * 
     * @param eloquent instance $model
     * @param string $q query string
     * @param array $searchFilter list field yg di search nya
     * @return eloquent instance
     */
    protected function _setFilterSearch($model, $q, $searchFilter = false) {
        $model = $model->where(function($query) use ($q, $searchFilter) {
            foreach ($searchFilter as $value) {
                $query = $query->orWhere($value, 'LIKE', '%' . $q . '%');
            }
        });
        return $model;
    }

    /**
     * list data
     * 
     * @param eloquen instance $model model data yang digunakan
     * @param array $filter filter data jika ada
     *      filter array
     *          q string
     *          ADDITIONAL_PARAM untuk filterFucntion
     *      filterFunction function($data, $filter) filter tambahan jika diperlukan
     *      searchField array list field/column yg termasuk kedalam filter search
     *      filterBasic array basic filter
     *      hiddeColumn array list field/column yg di hidde     *      
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
            if (isset($filter['filterFunction'])) {
                $model = $filter['filterFunction']($model, $filter['filter']);
            }
            if (isset($filter['filterBasic'])) {
                $model = $this->_setFilterWhere($model, $filter['filterBasic']);
            }
            if (isset($filter['filter']['q'])) {
                $searchFilter = isset($filter['searchField']) ? $filter['searchField'] : $this->searchField;
                $model = $this->_setFilterSearch($model, $filter['filter']['q'], $searchFilter);
            }
        }

        $this->pagination['count'] = $model->count();
        $this->pagination['offset'] = $offset;
        $this->pagination['limit'] = $limit;

        if ($limit)
            $model = $model->limit($limit)->offset($offset);

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
        return $this->pagination['data'];
    }

    /**
     * 
     * @param string $path path paginationnya
     * @param type $pagination
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
     * fungsi utama untuk get 1 record data
     * 
     * @param eloquen instance $model
     * @param string/array $key 
     *      string field/kolom : jika $key string dan $value terisi
     *      array filter : jika berisi array maka $value tidak akan diprose
     *      string value id table : jika $value = null (tidak diisi)
     * 
     * @param string/false $value filter
     * @return boolean|array
     */
    protected function _getOne($model, $key, $value = null) {
        $data = $this->_getOneModel($model, $key, $value);
        return $data ? $data->toArray() : false;
    }

    protected function _getOneModel($model, $key, $value = null) {
        //jika array berarti berisi filter
        if (is_array($key)) {
            $filter = $key;
        } else {
            if (is_null($value)) {
                $filter = ['id' => $key];
            } else {
                $filter = [$key => $value];
            }
        }

        $data = $this->_setFilterWhere($model, $filter);
        $data = $data->first();
        if (!$data)
            return false;
        return $data;
    }

    protected function _exists($model, $key, $value = null) {
        //jika array berarti berisi filter
        if (is_array($key)) {
            $filter = $key;
        } else {
            if (is_null($value)) {
                $filter = ['id' => $key];
            } else {
                $filter = [$key => $value];
            }
        }

        $data = $this->_setFilterWhere($model, $filter);

        return $data->exists();
    }

    /**
     * 
     * @param type $model
     * @param type $data
     * @return boolean
     */
    protected function _create($model, $data) {
        if ($data = $model->create($data)) {
            return $data->toArray();
        }
        return false;
    }

    /**
     * 
     * @param eloquent instance $model
     * @param string/array $filter filter
     * @param type $data
     * @return false|array record table yang diupdatenya
     */
    protected function _update($model, $key, $value, $data = null) {
        if (is_null($data)) {
            $data = $value;
            $value = null;
        }
        $model = $this->_getOneModel($model, $key, $value);
        if ($model)
            return $model->update($data);
        return false;
    }

    /**
     * 
     * @param eloquent instance $model
     * @param type $id
     * @return boolean
     */
    protected function _delete($model, $key, $value = null) {
        $model = $this->_getOneModel($model, $key, $value);
        if ($model != false) {
            if ($model->delete()) {
                return true;
            }
        }
        return false;
    }

    /**
     * check apakah field sesuai
     * 
     * @param array        $fields list available field nya
     * @param array        $inputData input datanya
     * @return boolean
     */
    protected function _checkField($inputData, $fields = null) {
        $collection = collect($inputData);

        return $collection->every(function ($value, $key) use ($fields) {
                    return in_array($key, $fields);
                });
    }

    /**
     * filter data sesuai kesesuaian field
     * 
     * @param array        $fields list available field nya
     * @param array        $inputData input datanya
     * @return array       hasil filter data
     */
    protected function _filterField($inputData, $fields = null) {
        $collection = collect($inputData);

        return $collection->filter(function ($value, $key) use ($fields) {
                    return in_array($key, $fields);
                });
    }

    /*
     * MAIN MODEL IMPLEMENTATION
     * -------------------------------------------------------------------------
     */

    /**
     * method default untuk listing data model utama
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

        return $this->_getList($this->model, $filter, $orderBy, $offset, $limit);
    }

    /**
     * Default pagination function
     * 
     * @param type $path
     * @param type $pagination
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
