<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','role_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function teacher(){
        return $this->hasOne(Teacher::class,'user_id');
    }

    public function student(){
        return $this->hasOne(Student::class,'user_id');
    }

    public function teacher_leaves(){
        return $this->hasMany(TeacherLeave::class,'user_id','id');
    }

    public function student_leaves(){
        return $this->hasMany(StudentLeave::class,'user_id','id');
    }

    public function sandwich_leave($start,$end){
        if ($start->isSaturday()){
            if ($end->isSaturday()){
                $days = $end->diffInDays($start);
            }elseif ($end->isSunday()){
                $days = $end->diffInDays($start) - 1;
            }elseif ($end->isMonday()){
                $days = $end->diffInDays($start) + 1;
            }else{
                $days = $end->diffInDays($start) + 1;
            }
        }elseif ($start->isSunday()){
            if ($end->isSaturday()){
                $days = $end->diffInDays($start) - 1;
            }elseif ($end->isSunday()){
                $days = $end->diffInDays($start) - 2;
            }else{
                $days = $end->diffInDays($start);
            }
        }elseif ($end->isSaturday()){
            $days = $end->diffInDays($start);
        }elseif ($end->isSunday()){
            $days = $end->diffInDays($start) - 1;
        }else{
            $days = $end->diffInDays($start) + 1;
        }
        return $days;
    }
}
