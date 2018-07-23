<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('login');
});

Route::get('login','AuthController@getLogin')->name('get_login');
Route::post('login','AuthController@login')->name('login');
Route::get('logout','AuthController@logout')->name('logout');

Route::prefix('admin')->name('admin.')->middleware('role:1')->group(function (){
    Route::get('/','AdminController@getDashboard')->name('dashboard');
    Route::get('teacher_leave_management','AdminController@getTeacherLeaveManagement')->name('get_teacher_leave_management');
    Route::post('get_teacher_leave','AdminController@getTeachersAllLeave')->name('teacher_all_leave');
    Route::get('approve_teacher_leave/{id}','AdminController@approveTeacherLeave')->name('approve_teacher_leave');
    Route::get('student_leave_management','AdminController@getStudentLeaveManagement')->name('get_student_leave_management');
    Route::post('get_student_leave','AdminController@getStudentsAllLeave')->name('student_all_leave');
    Route::get('approve_student_leave/{id}','AdminController@approveStudentLeave')->name('approve_student_leave');
    Route::get('teacher_management','AdminController@getTeacherManagement')->name('get_teacher_management');
    Route::post('get_teacher','AdminController@getAllTeacher')->name('get_all_teacher');
    Route::get('teacher','AdminController@getAddTeacher')->name('get_add_teacher');
    Route::post('teacher','AdminController@addTeacher')->name('add_teacher');
    Route::get('teacher/{id}','AdminController@getEditTeacher')->name('get_edit_teacher');
    Route::post('teacher/{id}','AdminController@editTeacher')->name('edit_teacher');
    Route::get('delete_teacher/{id}','AdminController@deleteTeacher')->name('delete_teacher');
    Route::get('student_management','AdminController@getStudentManagement')->name('get_student_management');
    Route::post('get_student','AdminController@getAllStudent')->name('get_all_student');
    Route::get('student','AdminController@getAddStudent')->name('get_add_student');
    Route::post('student','AdminController@addStudent')->name('add_student');
    Route::get('student/{id}','AdminController@getEditStudent')->name('get_edit_student');
    Route::post('student/{id}','AdminController@editStudent')->name('edit_student');
    Route::get('delete_student/{id}','AdminController@deleteStudent')->name('delete_student');
});

Route::prefix('teacher')->name('teacher.')->middleware('role:2')->group(function (){
    Route::get('/','TeacherController@getDashboard')->name('dashboard');
    Route::get('leave_management','TeacherController@getLeaveManagement')->name('get_leave_management');
    Route::post('get_all_leave','TeacherController@getAllLeave')->name('get_all_leave');
    Route::get('student_leave_management','TeacherController@getStudentLeaveManagement')->name('get_student_leave_management');
    Route::post('student_leave','TeacherController@getAllStudentLeave')->name('get_all_student_leave');
    Route::get('approve_student_leave/{id}','TeacherController@approveStudentLeave')->name('approve_student_leave');
    Route::get('leave','TeacherController@getAddLeave')->name('get_add_leave');
    Route::post('leave','TeacherController@addLeave')->name('add_leave');
    Route::get('leave/{id}','TeacherController@getEditLeave')->name('get_edit_leave');
    Route::post('leave/{id}','TeacherController@editLeave')->name('edit_leave');
    Route::get('delete_leave/{id}','TeacherController@deleteLeave')->name('delete_leave');

});

Route::prefix('student')->name('student.')->middleware('role:3')->group(function (){
    Route::get('/','StudentController@getDashboard')->name('dashboard');
    Route::get('leave_management','StudentController@getLeaveManagement')->name('get_leave_management');
    Route::post('all_leave','StudentController@getAllLeave')->name('get_all_leave');
    Route::get('leave','StudentController@getAddLeave')->name('get_add_leave');
    Route::post('leave','StudentController@addLeave')->name('add_leave');
    Route::get('leave/{id}','StudentController@getEditLeave')->name('get_edit_leave');
    Route::post('leave/{id}','StudentController@editLeave')->name('edit_leave');
    Route::get('delete_leave/{id}','StudentController@deleteLeave')->name('delete_leave');
});

Route::get('ip','AdminController@ip');
