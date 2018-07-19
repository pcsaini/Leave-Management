<?php

namespace App\Http\Controllers;

use App\TeacherLeave;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

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

    public function addLeave(Request $request){
        $user = Auth::user();
        $validator = Validator::make($request->all(),[
            'leave_reason' => 'required|max:20',
            'leave_start' => 'required|date|after:today',
            'leave_end' => 'required|date|after:leave_start'
        ]);
        if ($validator->fails()){
            return redirect()->back()->withErrors($validator->errors());
        }

        $leave = new TeacherLeave();
        $leave->user_id = $user->id;
        $leave->leave_reason = $request->input('leave_reason');
        $leave->leave_start = Carbon::createFromFormat('m/d/Y',$request->input('leave_start'))->toDateString();
        $leave->leave_end = Carbon::createFromFormat('m/d/Y',$request->input('leave_end'))->toDateString();
        $leave->leave_description = $request->input('leave_description');

        $result = $leave->save();
        if (!$result){
            $errors = array(['add_leave' => 'Problem to Create Leave']);
            return redirect()->back()->withErrors($errors);
        }
        return redirect()->route('teacher.get_leave_management')->with('success','Leave Created Successfully');
    }

    public function getEditLeave(){
        return view('teacher.edit_leave');
    }
}
