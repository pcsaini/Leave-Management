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
        $user_id = Auth::id();
        $user = DB::table('users')
            ->leftJoin('student_details','users.id','=','student_details.user_id')
            ->where('users.id',$user_id)
            ->first();
        return view('student.dashboard',['user' => $user]);
    }

    public function getLeaveManagement(){
        return view('student.leave');
    }

    public function getAllLeave(Request $request){
        $user_id = Auth::id();
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

        $totalData = StudentLeave::where('user_id',$user_id)->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if(empty($request->input('search.value'))){
            $leaves = DB::table('student_leave')
                ->leftJoin('users','student_leave.leave_to', '=', 'users.id')
                ->select('student_leave.*','users.name')
                ->where('user_id',$user_id)
                ->offset($start)
                ->limit($limit)
                ->orderBy('student_leave.'.$order,$dir)
                ->get();
        } else{
            $search = $request->input('search.value');
            $leaves = DB::table('student_leave')
                ->leftJoin('users','student_leave.leave_to', '=', 'users.id')
                ->select('student_leave.*','users.name')
                ->where('user_id',$user_id)
                ->where('leave_reason','LIKE','%'.$search.'%')
                ->orWhere('leave_to','LIKE','%'.$search.'%')
                ->offset($start)
                ->limit($limit)
                ->orderBy('student_leave.'.$order,$dir)
                ->get();
            $totalFiltered = StudentLeave::where('leave_reason','LIKE','%'.$search.'%')
                ->orWhere('leave_to','LIKE','%'.$search.'%')
                ->where('user_id',$user_id)
                ->count();
        }

        $data = array();

        if(!empty($leaves))
        {
            foreach ($leaves as $leave)
            {
                $edit =  route('student.get_edit_leave',$leave->id);
                $delete = route('student.delete_leave',$leave->id);
                $nestedData['id'] = $leave->id;
                $nestedData['leave_reason'] = $leave->leave_reason;
                $nestedData['leave_to'] = $leave->name;
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

    public function getAddLeave(){
        $teachers = User::where('role_id',2)->get();
        return view('student.add_leave',['teachers' => $teachers]);
    }

    public function addLeave(Request $request){
        $user = Auth::user();
        $leave_date = explode('-',$request->input('leave_range'));
        $request->request->add(['leave_start' => $leave_date[0]]);
        $request->request->add(['leave_end' => $leave_date[1]]);
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
        $leave->leave_start = Carbon::createFromFormat('m/d/Y',trim($request->input('leave_start')))->toDateString();
        $leave->leave_end = Carbon::createFromFormat('m/d/Y',trim($request->input('leave_end')))->toDateString();
        $leave->leave_description = $request->input('leave_description');

        $result = $leave->save();
        if (!$result){
            $errors = array(['add_leave' => 'Problem to Create Leave']);
            return redirect()->back()->withErrors($errors);
        }
        return redirect()->route('student.get_leave_management')->with('success','Leave Created Successfully');
    }

    public function getEditLeave($id){
        $leave = StudentLeave::find($id);
        $teachers = User::where('role_id',2)->get();
        return view('student.edit_leave',['leave' => $leave,'teachers' => $teachers]);
    }

    public function editLeave(Request $request,$id){
        $user = Auth::user();
        $leave_date = explode('-',$request->input('leave_range'));
        $request->request->add(['leave_start' => $leave_date[0]]);
        $request->request->add(['leave_end' => $leave_date[1]]);
        $validator = Validator::make($request->all(),[
            'leave_reason' => 'required|max:20',
            'leave_to' => 'required|exists:users,id',
            'leave_start' => 'required|date|after:today',
            'leave_end' => 'required|date|after:leave_start'
        ]);
        if ($validator->fails()){
            return redirect()->back()->withErrors($validator->errors());
        }
        $leave = StudentLeave::find($id);
        $leave->user_id = $user->id;
        $leave->leave_reason = $request->input('leave_reason');
        $leave->leave_to = $request->input('leave_to');
        $leave->leave_start = Carbon::createFromFormat('m/d/Y',trim($request->input('leave_start')))->toDateString();
        $leave->leave_end = Carbon::createFromFormat('m/d/Y',trim($request->input('leave_end')))->toDateString();
        $leave->leave_description = $request->input('leave_description');

        $result = $leave->save();
        if (!$result){
            $errors = array(['add_leave' => 'Problem to Create Leave']);
            return redirect()->back()->withErrors($errors);
        }
        return redirect()->route('student.get_leave_management')->with('success','Leave Edit Successfully');
    }

    public function deleteLeave($id){
        $leave = StudentLeave::find($id);
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
