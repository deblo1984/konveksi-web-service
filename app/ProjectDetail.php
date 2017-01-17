<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectDetail extends Model
{
    protected $table = 'project_details';

    protected $fillable = [
        'project_id','item_id','item_name','qty','price','total',
        'image_path','remarks',
    ];

    public function projects()
    {
        return $this->belongsTo('App\Project');
    }
}
