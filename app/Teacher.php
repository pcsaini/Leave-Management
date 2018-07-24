<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    //
    protected $table = 'teacher_details';

    public function user(){
        return $this->belongsTo(User::class);
    }

}
