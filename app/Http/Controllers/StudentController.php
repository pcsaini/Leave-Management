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

    public function getAllLeave(Request $request){
        $columns  = array(
            0 => 'id',
            1 => 'leave_reason',
            2 => 'leave_to',
            3 => 'leave_description',
            4 => 'leave_start',
            5 => 'leave_end',
            6 => 'status',
            7 => 'id'
        );

        $totalData = StudentLeave::count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if(empty($request->input('search.value'))){
            $leaves = StudentLeave::offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();
        } else{
            $search = $request->input('search.value');
            $leaves = StudentLeave::where('leave_reason','LIKE','%'.$search.'%')
                ->orWhere('leave_to','LIKE','%'.$search.'%')
                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();
            $totalFiltered = StudentLeave::where('leave_reason','LIKE','%'.$search.'%')
                ->orWhere('leave_to','LIKE','%'.$search.'%')->count();
        }

        $data = array();

        if(!empty($leaves))
        {
            foreach ($leaves as $leave)
            {
                $edit =  route('student.get_edit_leave',$leave->id);

                $nestedData['id'] = $leave->id;
                $nestedData['leave_reason'] = $leave->leave_reason;
                $nestedData['leave_to'] = $leave->leave_to;
                $nestedData['leave_description'] = $leave->leave_description;
                $nestedData['leave_start'] =date('j M Y h:i a',strtotime($leave->leave_start));
                $nestedData['leave_end'] = date('j M Y h:i a',strtotime($leave->leave_end));
                $nestedData['leave_to'] = $leave->leave_to;
                $nestedData['status'] = $leave->status;
                $nestedData['options'] = "&emsp;<a href='{$edit}' title='SHOW' ><span class='glyphicon glyphicon-list'></span></a> ";
                $data[] = $nestedData;

            }
        }

        $json_data = array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        );

        echo json_encode($json_data);
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
