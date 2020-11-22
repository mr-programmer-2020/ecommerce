<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Models\Admin; 
class LoginController extends Controller
{
    public function getLogin(){
        return view('admin.Auth.login');   
    }

  

    public function login(LoginRequest $request){

        $remember_me=$request->has('remember_me') ? true : false;
        if(auth()->guard('admin')->attempt( ['email' => $request->input("email"), 'password' => $request->input('password')] ,$remember_me )){
           
            return redirect() -> route('admin.dashboard');  
        }
        
        return redirect()->back()->with(['error' => 'هناك خطا بالبيانات']);
    }

}