<?php

namespace App\Base;

use DateTimeInterface;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
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
    
    public function createdby()
    {
        return $this->belongsTo('hpsynapse\moduser\Models\User', 'created_by', 'id');
    }
        
    public function updatedby()
    {
        return $this->belongsTo('hpsynapse\moduser\Models\User', 'updated_by', 'id');
    }
}
