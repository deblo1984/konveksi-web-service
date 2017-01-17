<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectProgress extends Model
{
    protected $table = 'project_progresses';

    protected $fillable = [
        'project_id','work_type_id',
        'image_path','remarks',
    ];


    public function projects()
    {
        return $this->belongsTo('App\Project');
    }

    public function workTypes()
    {
        return $this->belongsTo('App\WorkType');
    }
}
