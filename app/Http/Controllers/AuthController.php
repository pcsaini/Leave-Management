<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getLogin(){
        return view('login');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request){
        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()){
            return redirect()->route('get_login')->withErrors($validator->errors())->withInput($request->all());
        }

        $email = $request->input('email');
        $password = $request->input('password');

        if (Auth::attempt(array('email' => $email,'password' => $password))){
            $user = Auth::user();
            if ($user->role_id == 1){
                return redirect()->route('admin.dashboard');
            }elseif (($user->role_id ==2)){
                return redirect()->route('teacher.dashboard');
            }else{
                return redirect()->route('student.dashboard');
            }
        }

        return redirect()->route('get_login')->withErrors(array('login_error' => 'Wrong Email or Password'))->withInput($request->all());
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(){
        Auth::logout();
        return redirect()->route('get_login');
    }
}
