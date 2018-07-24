<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TeacherLeave extends Model
{
    //
    protected $table = 'teacher_leave';

    public function user(){
        return $this->belongsTo(User::class);
    }
}
