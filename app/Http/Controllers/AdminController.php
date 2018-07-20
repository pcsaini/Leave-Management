<?php

namespace App\Http\Controllers;

use App\StudentLeave;
use App\TeacherLeave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    //
    public function getDashboard(){
        $user = Auth::user();
        return view('admin.dashboard',['user' => $user]);
    }

    public function getTeacherLeaveManagement(){
        return view('admin.teacher_leave');
    }

    public function getTeachersAllLeave(Request $request){
        $columns  = array(
            0 => 'id',
            1 => 'leave_reason',
            2 => 'leave_description',
            3 => 'leave_start',
            4 => 'leave_end',
            5 => 'status',
        );

        $totalData = TeacherLeave::count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if(empty($request->input('search.value'))){
            $leaves = DB::table('teacher_leave')
                ->leftJoin('users','users.id','=','teacher_leave.user_id')
                ->select('teacher_leave.*','users.name')
                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();
        } else{
            $search = $request->input('search.value');
            $leaves = DB::table('teacher_leave')
                ->leftJoin('users','users.id','=','teacher_leave.user_id')
                ->select('teacher_leave.*','users.name')
                ->where('leave_reason','LIKE','%'.$search.'%')
                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();
            $totalFiltered = TeacherLeave::where('leave_reason','LIKE','%'.$search.'%')->count();
        }

        $data = array();

        if(!empty($leaves))
        {
            foreach ($leaves as $leave)
            {
                $approve =  route('admin.approve_teacher_leave',$leave->id);
                $nestedData['id'] = $leave->id;
                $nestedData['name'] = $leave->name;
                $nestedData['leave_reason'] = $leave->leave_reason;
                $nestedData['leave_description'] = str_limit($leave->leave_description,20);
                $nestedData['leave_start'] =date('j M Y',strtotime($leave->leave_start));
                $nestedData['leave_end'] = date('j M Y',strtotime($leave->leave_end));
                $nestedData['status'] = $leave->status == 0 ? "<a href='{$approve}' class='btn btn-primary'>Approve</a>" : "<span class='text-green'><b>Approved</b></span>";
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

    public function approveTeacherLeave($id){
        $leave = TeacherLeave::find($id);

        if (!$leave){
            return redirect()->back()->with('error','Leave Not Found');
        }
        if ($leave->status == 1){
            return redirect()->back()->with('error','Leave Already Approved');
        }
        $leave->status = 1;
        $result = $leave->save();
        if (!$result){
            return redirect()->back()->with('error','Problem to Delete Leave');
        }

        return redirect()->back()->with('success','Leave Approved Successfully');
    }

    public function getStudentLeaveManagement(){
        return view('admin.student_leave');
    }

    public function getStudentsAllLeave(Request $request){
        $columns  = array(
            0 => 'id',
            1 => 'name',
            2 => 'leave_reason',
            3 => 'leave_to',
            4 => 'leave_description',
            5 => 'leave_start',
            6 => 'leave_end',
            7 => 'status'
        );

        $totalData = StudentLeave::count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if(empty($request->input('search.value'))){
            $leaves = DB::table('student_leave')
                ->leftJoin('users as student','student.id','=','student_leave.user_id')
                ->leftJoin('users as teacher','teacher.id','=','student_leave.leave_to')
                ->select('student_leave.*','student.name as name','teacher.name as leave_to_name')
                ->offset($start)
                ->limit($limit)
                ->orderBy('student_leave.'.$order,$dir)
                ->get();
        } else{
            $search = $request->input('search.value');
            $leaves = DB::table('student_leave')
                ->leftJoin('users as student','student.id','=','student_leave.user_id')
                ->leftJoin('users as teacher','teacher.id','=','student_leave.leave_to')
                ->select('student_leave.*','student.name as name','teacher.name as leave_to_name')
                ->where('leave_reason','LIKE','%'.$search.'%')
                ->offset($start)
                ->limit($limit)
                ->orderBy('student_leave.'.$order,$dir)
                ->get();
            $totalFiltered = StudentLeave::where('leave_reason','LIKE','%'.$search.'%')
                ->count();
        }

        $data = array();

        if(!empty($leaves))
        {
            foreach ($leaves as $leave)
            {
                $approve =  route('admin.approve_student_leave',$leave->id);
                $nestedData['id'] = $leave->id;
                $nestedData['name'] = $leave->name;
                $nestedData['leave_reason'] = $leave->leave_reason;
                $nestedData['leave_to'] = $leave->leave_to_name;
                $nestedData['leave_description'] = str_limit($leave->leave_description,20);
                $nestedData['leave_start'] =date('j M Y',strtotime($leave->leave_start));
                $nestedData['leave_end'] = date('j M Y',strtotime($leave->leave_end));
                $nestedData['status'] = $leave->status == 0 ? "<a href='{$approve}' class='btn btn-primary'>Approve</a>" : "<span class='text-green'><b>Approved</b></span>";
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

    public function approveStudentLeave($id){
        $leave = StudentLeave::find($id);

        if (!$leave){
            return redirect()->back()->with('error','Leave Not Found');
        }
        if ($leave->status == 1){
            return redirect()->back()->with('error','Leave Already Approved');
        }
        $leave->status = 1;
        $result = $leave->save();
        if (!$result){
            return redirect()->back()->with('error','Problem to Delete Leave');
        }

        return redirect()->back()->with('success','Leave Approved Successfully');
    }

    public function getTeacherManagement(){
        return view('admin.teacher_mang');
    }

    public function getStudentManagement(){
        return view('admin.student_mang');
    }
}
