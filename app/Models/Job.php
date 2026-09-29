<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'jobs';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id','created_at'];

    // protected $appends = ['formated_payload'];

    public function getFormatedPayloadAttribute()
    {
        $payload = json_decode($this->payload,true);
        if (!isset($payload['data']['command']) || !is_string($payload['data']['command'])) {
            return $payload;
        }

        $command = unserialize($payload['data']['command'], ['allowed_classes' => false]);
        $payload['data']['command'] = is_object($command)
            ? get_object_vars($command)
            : (array) $command;

        return $payload;
    }
}
