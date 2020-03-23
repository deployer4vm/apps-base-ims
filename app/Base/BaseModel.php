<?php

namespace App\Base;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    /**
     * override fillable
     */
    public function getFillable()
    {
        if(empty($this->fillable))$this->setAutoFillable();
        return $this->fillable;
    }

    public function setAutoFillable()
    {
        //set fillable sesuai field didatabasenya
        $fields = Schema::getColumnListing($this->getTable());
        $guarded = $this->getGuarded();
        $this->fillable = array_filter($fields,function($v) use ($guarded) {
            return !in_array($v,$guarded);
        });
    }
}
