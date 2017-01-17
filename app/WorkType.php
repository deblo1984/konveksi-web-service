<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkType extends Model
{
    protected $table = 'work_types';

    protected $fillable = [
      'code', 'work_name', 'description',
    ];

    public function projectProgresses()
    {
        return $this->hasMany('App\ProjectProgress');
    }
}
