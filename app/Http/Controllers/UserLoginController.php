<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VendorUser;
use Illuminate\Support\Facades\Auth;
use Hash;
use Session;
use App\Models\Role;

class UserLoginController extends Controller
{
    public function userloginview(){
    	return view('subadmin.user-login');
    }
    public function userlogin(Request $request)
    {
    	$request->validate([
    	'email'=>'required',
    	'password'=>'required|min:8|max:8',
      ]);
     $user = VendorUser::where('email',$request->email)->first();
     if($user){
     	if(Hash::check($request->password,$user->password)){
     	    $get_data= Role::where('name',$user->role)->first();
     		session()->put('role',$get_data);
     		session()->put('loginuser',$user->id);
                echo "<pre>"; print_r(Auth::user()); die();
            return redirect()->route('home');
        }
     	else{
     	  return redirect()->route('user-login')->with('error','Password does not match');
     	}

     }
     else{
     	return redirect()->route('user-login')->with('error','Email does not match');
     }
 }
 public function userlogout(){
 	Session::flush();
    return redirect()->route('user-login');
  }


}
