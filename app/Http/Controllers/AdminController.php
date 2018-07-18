<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    //
    public function getDashboard(){
        $user = Auth::user();
        return view('admin.dashboard');
    }

    public function getTeacherLeaveManagement(){
        return view('admin.teacher_leave');
    }

    public function getStudentLeaveManagement(){
        return view('admin.student_leave');
    }

    public function getTeacherManagement(){
        return view('admin.teacher_mang');
    }

    public function getStudentManagement(){
        return view('admin.student_mang');
    }
}
