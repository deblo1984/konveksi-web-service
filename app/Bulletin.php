<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bulletin extends Model
{
    protected $table = 'bulletins';

    protected $fillable = [
      'subject','content','image_path', 'user_id',
    ];

    public function users()
    {
        $this->belongsTo('App\User');
    }
}
