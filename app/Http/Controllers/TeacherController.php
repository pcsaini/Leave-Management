<?php

namespace App\Http\Controllers;

use App\StudentLeave;
use App\TeacherLeave;
use App\User;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TeacherController extends Controller
{
    //
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getDashboard(){
        $user_id = Auth::id();
        $user = DB::table('users')
            ->leftJoin('teacher_details','users.id','=','teacher_details.user_id')
            ->where('users.id',$user_id)
            ->first();
        return view('teacher.dashboard',['user' => $user]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getEditProfile(){
        $user_id = Auth::id();
        $teacher = DB::table('users')
            ->leftJoin('teacher_details','teacher_details.user_id','=','users.id')
            ->where('users.id',$user_id)
            ->first();
        return view('teacher.edit_profile',['teacher' => $teacher]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function editProfile(Request $request){
        $user_id = Auth::id();
        $validator = Validator::make($request->all(),[
            'name' => 'required|max:20',
            'contact_no' => 'min:8|max:13',
        ]);

        if ($validator->fails()){
            return redirect()->back()->withErrors($validator->errors())->withInput($request->all());
        }

        $user = User::find($user_id);
        $user->name = $request->input('name');
        $teacher = $user->teacher;
        $teacher->contact_no = $request->input('contact_no');
        $teacher->address = $request->input('address');
        $teacher->update();
        $user->update();

        return redirect()->route('teacher.dashboard')->with('success' , 'Teacher Edit Successfully');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getLeaveManagement(){
        return view('teacher.leave');
    }

    /**
     * @param Request $request
     */
    public function getAllLeave(Request $request){
        $user_id = Auth::id();
        $columns  = array(
            0 => 'id',
            1 => 'leave_reason',
            2 => 'leave_description',
            3 => 'leave_start',
            4 => 'leave_end',
            5 => 'leave_days',
            6 => 'status',
            7 => 'id'
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
                $nestedData['leave_days'] = $leave->leave_days;
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

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getStudentLeaveManagement(){
        return view('teacher.student_leave');
    }

    /**
     * @param Request $request
     */
    public function getAllStudentLeave(Request $request){
        $user = Auth::user();
        $columns  = array(
            0 => 'id',
            1 => 'name',
            2 => 'leave_reason',
            3 => 'leave_description',
            4 => 'leave_start',
            5 => 'leave_end',
            6 => 'leave_days',
            7 => 'status',
            8 => 'id'
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
                $nestedData['leave_days'] = $leave->leave_days;
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

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getAddLeave(){
        return view('teacher.add_leave');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addLeave(Request $request){
        $user = Auth::user();
        $leave_date = explode('-',$request->input('leave_range'));
        $request->request->add(['leave_start' => $leave_date[0]]);
        $request->request->add(['leave_end' => $leave_date[1]]);
        $validator = Validator::make($request->all(),[
            'leave_reason' => 'required|max:20',
            'leave_start' => 'required|date',
            'leave_end' => 'required|date'
        ]);
        if ($validator->fails()){
            return redirect()->back()->withErrors($validator->errors())->withInput($request->all());
        }

        $leave_start = Carbon::createFromFormat('m/d/Y',trim($request->input('leave_start')));
        $leave_end = Carbon::createFromFormat('m/d/Y',trim($request->input('leave_end')));

        $userModel = new User();
        $days = $userModel->sandwich_leave($leave_start,$leave_end);

        $leave = new TeacherLeave();
        $leave->leave_reason = $request->input('leave_reason');
        $leave->leave_start = $leave_start->toDateString();
        $leave->leave_end = $leave_end->toDateString();
        $leave->leave_days = $days;
        $leave->leave_description = $request->input('leave_description');

        $user = User::find($user->id);
        $result = $user->teacher_leaves()->save($leave);

        if (!$result){
            $errors = array(['add_leave' => 'Problem to Create Leave']);
            return redirect()->back()->withErrors($errors)->withInput($request->all());
        }
        return redirect()->route('teacher.get_leave_management')->with('success','Leave Created Successfully');
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getEditLeave($id){
        $leave = TeacherLeave::find($id);
        return view('teacher.edit_leave',['leave' => $leave]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function editLeave(Request $request, $id){
        $user = Auth::user();
        $leave_date = explode('-',$request->input('leave_range'));
        $request->request->add(['leave_start' => $leave_date[0]]);
        $request->request->add(['leave_end' => $leave_date[1]]);
        $validator = Validator::make($request->all(),[
            'leave_reason' => 'required|max:20',
            'leave_start' => 'required|date',
            'leave_end' => 'required|date'
        ]);
        if ($validator->fails()){
            return redirect()->back()->withErrors($validator->errors())->withInput($request->all());
        }

        $leave_start = Carbon::createFromFormat('m/d/Y',trim($request->input('leave_start')));
        $leave_end = Carbon::createFromFormat('m/d/Y',trim($request->input('leave_end')));

        $userModel = new User();
        $days = $userModel->sandwich_leave($leave_start,$leave_end);

        $leave = TeacherLeave::find($id);
        $leave->leave_reason = $request->input('leave_reason');
        $leave->leave_start = $leave_start->toDateString();
        $leave->leave_end = $leave_end->toDateString();
        $leave->leave_days = $days;
        $leave->leave_description = $request->input('leave_description');

        $user = User::find($user->id);
        $result = $user->teacher_leaves()->save($leave);
        if (!$result){
            $errors = array(['add_leave' => 'Problem to Create Leave']);
            return redirect()->back()->withErrors($errors)->withInput($request->all());
        }
        return redirect()->route('teacher.get_leave_management')->with('success','Leave Edit Successfully');
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
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
