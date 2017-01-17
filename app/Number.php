<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Number extends Model
{
    //

    public static function getNumber($type,$year) {
        $q=DB::table('numbers')
            ->where('type',$type)
            ->where('year',$year)
            ->select('number')
            ->first();
        return $q;
    }
}
