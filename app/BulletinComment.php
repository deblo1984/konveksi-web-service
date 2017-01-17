<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BulletinComment extends Model
{
    protected $table = 'bulletin_comments';

    public function users()
    {
        $this->belongsTo('App\User');
    }

    public function bulletins()
    {
        $this->belongsTo('App\Bulletin');
    }

}
