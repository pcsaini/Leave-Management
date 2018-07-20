<?php

namespace App\Http\Controllers;

use App\StudentLeave;
use App\TeacherLeave;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TeacherController extends Controller
{
    //
    public function getDashboard(){
        $user_id = Auth::id();
        $user = DB::table('users')
            ->leftJoin('teacher_details','users.id','=','teacher_details.user_id')
            ->where('users.id',$user_id)
            ->first();
        return view('teacher.dashboard',['user' => $user]);
    }

    public function getLeaveManagement(){
        return view('teacher.leave');
    }

    public function getAllLeave(Request $request){
        $user_id = Auth::id();
        $columns  = array(
            0 => 'id',
            1 => 'leave_reason',
            2 => 'leave_description',
            3 => 'leave_start',
            4 => 'leave_end',
            5 => 'status',
            6 => 'id'
        );

        $totalData = TeacherLeave::where('user_id',$user_id)->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if(empty($request->input('search.value'))){
            $leaves = DB::table('teacher_leave')
                ->where('user_id',$user_id)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();
        } else{
            $search = $request->input('search.value');
            $leaves = DB::table('teacher_leave')
                ->where('user_id',$user_id)
                ->where('leave_reason','LIKE','%'.$search.'%')
                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();
            $totalFiltered = TeacherLeave::where('user_id',$user_id)
                                ->where('leave_reason','LIKE','%'.$search.'%')->count();
        }

        $data = array();

        if(!empty($leaves))
        {
            foreach ($leaves as $leave)
            {
                $edit =  route('teacher.get_edit_leave',$leave->id);
                $delete = route('teacher.delete_leave',$leave->id);
                $nestedData['id'] = $leave->id;
                $nestedData['leave_reason'] = $leave->leave_reason;
                $nestedData['leave_description'] = str_limit($leave->leave_description,20);
                $nestedData['leave_start'] =date('j M Y',strtotime($leave->leave_start));
                $nestedData['leave_end'] = date('j M Y',strtotime($leave->leave_end));
                $nestedData['status'] = $leave->status == 0 ? "<span class='text-red'><b>Pending</b></span>" : "<span class='text-green'><b>Approved</b></span>";
                $nestedData['options'] = $leave->status == 0 ? "<a href='{$edit}' title='Edit' ><span class='glyphicon glyphicon-edit text-primary'></span></a> &nbsp; <a href='{$delete}' title='Delete' ><span class='glyphicon glyphicon-trash text-red'></span></a>" : " ";
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

    public function getStudentLeaveManagement(){
        return view('teacher.student_leave');
    }

    public function getAllStudentLeave(Request $request){
        $user = Auth::user();
        $columns  = array(
            0 => 'id',
            1 => 'name',
            2 => 'leave_reason',
            3 => 'leave_description',
            4 => 'leave_start',
            5 => 'leave_end',
            6 => 'status',
            7 => 'id'
        );

        $totalData = StudentLeave::where('leave_to',$user->id)
                        ->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if(empty($request->input('search.value'))){
            $leaves = DB::table('student_leave')
                ->leftJoin('users','users.id','=','student_leave.user_id')
                ->select('student_leave.*','users.name')
                ->where('leave_to',$user->id)
                ->offset($start)
                ->limit($limit)
                ->orderBy('student_leave.'.$order,$dir)
                ->get();
        } else{
            $search = $request->input('search.value');
            $leaves = DB::table('student_leave')
                ->leftJoin('users','users.id','=','student_leave.user_id')
                ->select('student_leave.*','users.name')
                ->where('leave_to',$user->id)
                ->where('leave_reason','LIKE','%'.$search.'%')
                ->offset($start)
                ->limit($limit)
                ->orderBy('student_leave.'.$order,$dir)
                ->get();
            $totalFiltered = StudentLeave::where('leave_reason','LIKE','%'.$search.'%')
                ->where('leave_to',$user->id)
                ->count();
        }

        $data = array();

        if(!empty($leaves))
        {
            foreach ($leaves as $leave)
            {
                $approve =  route('teacher.approve_student_leave',$leave->id);
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

    public function approveStudentLeave($id){
        $user_id = Auth::id();
        $leave = StudentLeave::find($id);

        if (!$leave){
            return redirect()->back()->with('error','Leave Not Found');
        }
        if ($user_id !== $leave->leave_to){
            return redirect()->back()->with('error','Leave Not Assign You');
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

    public function getAddLeave(){
        return view('teacher.add_leave');
    }

    public function addLeave(Request $request){
        $user = Auth::user();
        $leave_date = explode('-',$request->input('leave_range'));
        $request->request->add(['leave_start' => $leave_date[0]]);
        $request->request->add(['leave_end' => $leave_date[1]]);
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
        $leave->leave_start = Carbon::createFromFormat('m/d/Y',trim($request->input('leave_start')))->toDateString();
        $leave->leave_end = Carbon::createFromFormat('m/d/Y',trim($request->input('leave_end')))->toDateString();
        $leave->leave_description = $request->input('leave_description');

        $result = $leave->save();
        if (!$result){
            $errors = array(['add_leave' => 'Problem to Create Leave']);
            return redirect()->back()->withErrors($errors);
        }
        return redirect()->route('teacher.get_leave_management')->with('success','Leave Created Successfully');
    }

    public function getEditLeave($id){
        $leave = TeacherLeave::find($id);
        return view('teacher.edit_leave',['leave' => $leave]);
    }

    public function editLeave(Request $request, $id){
        $user = Auth::user();
        $leave_date = explode('-',$request->input('leave_range'));
        $request->request->add(['leave_start' => $leave_date[0]]);
        $request->request->add(['leave_end' => $leave_date[1]]);
        $validator = Validator::make($request->all(),[
            'leave_reason' => 'required|max:20',
            'leave_start' => 'required|date|after:today',
            'leave_end' => 'required|date|after:leave_start'
        ]);
        if ($validator->fails()){
            return redirect()->back()->withErrors($validator->errors());
        }

        $leave = TeacherLeave::find($id);
        $leave->user_id = $user->id;
        $leave->leave_reason = $request->input('leave_reason');
        $leave->leave_start = Carbon::createFromFormat('m/d/Y',trim($request->input('leave_start')))->toDateString();
        $leave->leave_end = Carbon::createFromFormat('m/d/Y',trim($request->input('leave_end')))->toDateString();
        $leave->leave_description = $request->input('leave_description');

        $result = $leave->save();
        if (!$result){
            $errors = array(['add_leave' => 'Problem to Create Leave']);
            return redirect()->back()->withErrors($errors);
        }
        return redirect()->route('teacher.get_leave_management')->with('success','Leave Edit Successfully');
    }

    public function deleteLeave($id){
        $leave = TeacherLeave::find($id);
        if (!$leave){
            return redirect()->back()->with('error','Leave Not Found');
        }
        if ($leave->status == 1){
            return redirect()->back()->with('error','Leave Can\'t Delete');
        }
        $result = $leave->delete();
        if (!$result){
            return redirect()->back()->with('error','Problem to Delete Leave');
        }

        return redirect()->back()->with('success','Leave Delete Successfully');
    }
}
