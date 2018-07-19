<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param $role
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {

        if (Auth::check()){
            $user = Auth::user();
            if($user->role_id == $role){
                return $next($request);
            }else{
                return response(view('admin.un_authenticate'));
            }
        }else{
            abort(403, 'Unauthorized action.');
        }


    }
}
