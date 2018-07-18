<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudentController extends Controller
{
    //
    public function getDashboard(){
        return view('student.dashboard');
    }

    public function getLeaveManagement(){
        return view('student.leave');
    }

    public function getAddLeave(){
        return view('student.add_leave');
    }

    public function getEditLeave(){
        return view('student.edit_leave');
    }
}
