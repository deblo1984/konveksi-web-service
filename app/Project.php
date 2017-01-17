<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Project extends Model
{
    use notifiable;

    protected $table = 'projects';

    protected $fillable = [
        'code', 'name', 'user_id', 'description','image_path',
        'qty', 'cost', 'total', 'order_date', 'due_date',
        'finish_date',
    ];

    public function users()
    {
        return $this->belongsTo('App\User');
    }

    public function projectDetails()
    {
        return $this->hasMany('App\ProjectDetails');
    }

    public function projectProgresses()
    {
        return $this->hasMany('App\ProjectProgresses');
    }
}
