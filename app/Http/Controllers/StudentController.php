<?php

namespace App\Http\Controllers;

use App\StudentLeave;
use App\Teacher;
use App\User;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

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
        $teachers = User::where('role_id',2)->get();
        return view('student.add_leave',['teachers' => $teachers]);
    }

    public function addLeave(Request $request){
        $user = Auth::user();
        $validator = Validator::make($request->all(),[
            'leave_reason' => 'required|max:20',
            'leave_to' => 'required|exists:users,id',
            'leave_start' => 'required|date|after:today',
            'leave_end' => 'required|date|after:leave_start'
        ]);
        if ($validator->fails()){
            return redirect()->back()->withErrors($validator->errors());
        }

        $leave = new StudentLeave();
        $leave->user_id = $user->id;
        $leave->leave_reason = $request->input('leave_reason');
        $leave->leave_to = $request->input('leave_to');
        $leave->leave_start = Carbon::createFromFormat('m/d/Y',$request->input('leave_start'))->toDateString();
        $leave->leave_end = Carbon::createFromFormat('m/d/Y',$request->input('leave_end'))->toDateString();
        $leave->leave_description = $request->input('leave_description');

        $result = $leave->save();
        if (!$result){
            $errors = array(['add_leave' => 'Problem to Create Leave']);
            return redirect()->back()->withErrors($errors);
        }
        return redirect()->route('student.get_leave_management')->with('success','Leave Created Successfully');
    }

    public function getEditLeave(){
        return view('student.edit_leave');
    }

    public function editLeave(Request $request){
        dd($request->all());
    }
}
