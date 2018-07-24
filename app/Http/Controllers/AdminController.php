<?php

namespace App\Http\Controllers;

use App\Student;
use App\StudentLeave;
use App\Teacher;
use App\TeacherLeave;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Stevebauman\Location\Facades\Location;

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
            5 => 'leave_days',
            6 => 'status',
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
            7 => 'leave_days',
            8 => 'status'
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

    public function getAllTeacher(Request $request){
        $columns  = array(
            0 => 'id',
            1 => 'name',
            2 => 'email',
            3 => 'contact_no',
            4 => 'address',
            5 => 'id'
        );

        $totalData = User::where('role_id',2)->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if(empty($request->input('search.value'))){
            $users = DB::table('users')
                ->leftJoin('teacher_details','users.id','=','teacher_details.user_id')
                ->where('users.role_id',2)
                ->offset($start)
                ->limit($limit)
                ->orderBy('users.'.$order,$dir)
                ->get();
        } else{
            $search = $request->input('search.value');
            $users = DB::table('users')
                ->leftJoin('teacher_details','users.id','=','teacher_details.user_id')
                ->where('users.role_id',2)
                ->where('users.name','LIKE','%'.$search.'%')
                ->offset($start)
                ->limit($limit)
                ->orderBy('users.'.$order,$dir)
                ->get();
            $totalFiltered = User::where('name','LIKE','%'.$search.'%')
                ->where('role_id',2)
                ->count();
        }

        $data = array();

        if(!empty($users))
        {
            foreach ($users as $user)
            {
                $edit = route('admin.get_edit_teacher',$user->user_id);
                $delete = route('admin.delete_teacher',$user->user_id);
                $nestedData['id'] = $user->id;
                $nestedData['name'] = $user->name;
                $nestedData['email'] = $user->email;
                $nestedData['contact_no'] = $user->contact_no;
                $nestedData['address'] = $user->address;
                $nestedData['options'] = "<a href='{$edit}' title='Edit' ><span class='glyphicon glyphicon-edit text-primary'></span></a> &nbsp; <a href='{$delete}' title='Delete' ><span class='glyphicon glyphicon-trash text-red'></span></a>";
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

    public function getAddTeacher(){
        return view('admin.add_teacher');
    }

    public function addTeacher(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required|max:20',
            'email' => 'email|unique:users,email',
            'password' => 'min:6|max:16',
            'contact_no' => 'min:8|max:13',
        ]);
        if ($validator->fails()){
            return redirect()->back()->withErrors($validator->errors())->withInput($request->all());
        }

        $user = new User();
        $user->email = $request->input('email');
        $user->name = $request->input('name');
        $user->password = bcrypt($request->input('password'));
        $user->role_id = 2;
        $user->save();

        $teacher = new Teacher();
        $teacher->contact_no = $request->input('contact_no');
        $teacher->address = $request->input('address');
        $teacher = $user->teacher()->save($teacher);

        if (!$teacher){
            return redirect()->back()->with('error','Problem to Create Teacher');
        }

        return redirect()->route('admin.get_teacher_management')->with('success' , 'Teacher Add Successfully');


    }

    public function getEditTeacher($id){
       /* $user = User::find($id);
        $teacher = $user->teacher;

        return response()->json($teacher);*/
        $teacher = DB::table('users')
            ->leftJoin('teacher_details','teacher_details.user_id','=','users.id')
            ->where('users.id',$id)
            ->first();

        return view('admin.edit_teacher',['teacher' => $teacher]);
    }

    public function editTeacher(Request $request, $id){
        dd($request->all());
    }

    public function deleteTeacher($id){
        $user = User::find($id);
        if (!$user){
            return redirect()->back()->with('error','User Not Found');
        }
        $teacherDetails = $user->teacher;
        if (!$teacherDetails){
            return redirect()->back()->with('error','Student Details Not Found');
        }
        $teacherDetails->delete();
        $user->delete();

        return redirect()->back()->with('success', 'user Delete Successfully');
    }

    public function getStudentManagement(){
        return view('admin.student_mang');
    }

    public function getAllStudent(Request $request){
        $columns  = array(
            0 => 'id',
            1 => 'name',
            2 => 'father_name',
            3 => 'email',
            4 => 'contact_no',
            5 => 'class',
            6 => 'address',
            7 => 'id'
        );

        $totalData = User::where('role_id',3)->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if(empty($request->input('search.value'))){
            $users = DB::table('users')
                ->leftJoin('student_details','users.id','=','student_details.user_id')
                ->where('users.role_id',3)
                ->offset($start)
                ->limit($limit)
                ->orderBy('users.'.$order,$dir)
                ->get();
        } else{
            $search = $request->input('search.value');
            $users = DB::table('users')
                ->leftJoin('student_details','users.id','=','student_details.user_id')
                ->where('users.role_id',3)
                ->where('users.name','LIKE','%'.$search.'%')
                ->orWhere('student_details.father_name','LIKE','%'.$search.'%')
                ->offset($start)
                ->limit($limit)
                ->orderBy('users.'.$order,$dir)
                ->get();
            $totalFiltered = DB::table('users')
                ->leftJoin('student_details','users.id','=','student_details.user_id')
                ->where('users.role_id',3)
                ->where('users.name','LIKE','%'.$search.'%')
                ->orWhere('student_details.father_name','LIKE','%'.$search.'%')
                ->count();
        }

        $data = array();

        if(!empty($users))
        {
            foreach ($users as $user)
            {
                $edit = route('admin.get_edit_student',$user->user_id);
                $delete = route('admin.delete_student',$user->user_id);
                $nestedData['id'] = $user->id;
                $nestedData['name'] = $user->name;
                $nestedData['father_name'] = $user->father_name;
                $nestedData['email'] = $user->email;
                $nestedData['contact_no'] = $user->contact_no;
                $nestedData['class'] =  $user->class;
                $nestedData['address'] = $user->address;
                $nestedData['options'] = "<a href='{$edit}' title='Edit' ><span class='glyphicon glyphicon-edit text-primary'></span></a> &nbsp; <a href='{$delete}' title='Delete' ><span class='glyphicon glyphicon-trash text-red'></span></a>";
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

    public function getAddStudent(){
        return view('admin.add_student');
    }

    public function addStudent(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required|max:20',
            'father_name' => 'required|max:20',
            'email' => 'email|unique:users,email',
            'password' => 'min:6|max:16',
            'contact_no' => 'min:8|max:13',
        ]);
        if ($validator->fails()){
            return redirect()->back()->withErrors($validator->errors())->withInput($request->all());
        }

        $user = new User();
        $user->email = $request->input('email');
        $user->name = $request->input('name');
        $user->password = bcrypt($request->input('password'));
        $user->role_id = 3;
        $user->save();

        $student = new Student();
        $student->class = $request->input('class');
        $student->father_name = $request->input('father_name');
        $student->contact_no = $request->input('contact_no');
        $student->address = $request->input('address');
        $student = $user->student()->save($student);

        if (!$student){
            return redirect()->back()->with('error','Problem to Create Student');
        }

        return redirect()->route('admin.get_student_management')->with('success' , 'Student Add Successfully');

    }

    public function getEditStudent($id){
        $student = DB::table('users')
            ->leftJoin('student_details','student_details.user_id','=','users.id')
            ->where('users.id',$id)
            ->first();

        return view('admin.edit_student',['student' => $student]);
    }

    public function editStudent(Request $request, $id){


    }

    public function deleteStudent($id){
        $user = User::find($id);
        if (!$user){
            return redirect()->back()->with('error','User Not Found');
        }
        $studentDetails = $user->student;
        if (!$studentDetails){
            return redirect()->back()->with('error','Student Details Not Found');
        }
        $studentDetails->delete();
        $user->delete();

        return redirect()->back()->with('success', 'user Delete Successfully');
    }
}
