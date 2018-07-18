<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TeacherController extends Controller
{
    //
    public function getDashboard(){
        return view('teacher.dashboard');
    }

    public function getLeaveManagement(){
        return view('teacher.leave');
    }

    public function getStudentLeaveManagement(){
        return view('teacher.student_leave');
    }

    public function getAddLeave(){
        return view('teacher.add_leave');
    }

    public function getEditLeave(){
        return view('teacher.edit_leave');
    }
}
