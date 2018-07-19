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
    Route::get('student_leave_management','AdminController@getStudentLeaveManagement')->name('get_student_leave_management');
    Route::get('teacher_management','AdminController@getTeacherManagement')->name('get_teacher_management');
    Route::get('student_management','AdminController@getStudentManagement')->name('get_student_management');
});

Route::prefix('teacher')->name('teacher.')->middleware('role:2')->group(function (){
    Route::get('/','TeacherController@getDashboard')->name('dashboard');
    Route::get('leave_management','TeacherController@getLeaveManagement')->name('get_leave_management');
    Route::get('student_leave_management','TeacherController@getStudentLeaveManagement')->name('get_student_leave_management');
    Route::get('leave','TeacherController@getAddLeave')->name('get_add_leave');
    Route::POST('leave','TeacherController@addLeave')->name('add_leave');
    Route::get('leave/{id}','TeacherController@getEditLeave')->name('get_edit_leave');
    Route::POST('leave/{id}','TeacherController@editLeave')->name('edit_leave');

});

Route::prefix('student')->name('student.')->middleware('role:3')->group(function (){
    Route::get('/','StudentController@getDashboard')->name('dashboard');
    Route::get('leave_management','StudentController@getLeaveManagement')->name('get_leave_management');
    Route::POST('get_all_leave','StudentController@getAllLeave')->name('get_all_leave');
    Route::get('leave','StudentController@getAddLeave')->name('get_add_leave');
    Route::POST('leave','StudentController@addLeave')->name('add_leave');
    Route::get('leave/{id}','StudentController@getEditLeave')->name('get_edit_leave');
    Route::POST('leave/{id}','StudentController@editLeave')->name('edit_leave');


});
