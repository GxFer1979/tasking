<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailTask extends Model
{
    protected $fillable = [
        'task_id',
        'state_id',
        'date_begin',
        'date_end',
        'user_id',
        'advance',

    ];


    protected $dates = [
        'date_begin',
        'date_end',
        'created_at',
        'updated_at',

    ];

    protected $appends = ['resource_url'];
    protected $with = ['state','task','user'];


    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/detail-tasks/'.$this->getKey());
    }
    public function state()
    {
        return $this->belongsTo('App\Models\State');

    }
    public function task()
    {
        return $this->belongsTo('App\Models\Task');

    }

    public function user()
    {
        return $this->belongsTo('App\Models\AdminUser', 'user_id', 'id');
    }
}
