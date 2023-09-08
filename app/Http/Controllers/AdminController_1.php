<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class AdminController extends Controller
{

    public function loginview(Request $request){
    	return view('subadmin.pages-login');

    }
    public function login(Request $request){
       $input = $request->all();
    	 $request->validate([
            'username'=>'required|min:6',
            'password'=>'required|min:8',
        ]);
      if(auth::attempt(['username'=>$input['username'],'password'=>$input['password']])){
        $store = Store::where('username',$request->username)->first();
        session()->put('data', $store->id);
        if($store->role == "Vendor"){
          return redirect()->route('home');
        }
        if($store->role == "SuperAdmin"){
          //return redirect()->route('admin.dashboard');
          dd('hi');
        }
        if($store->role == "Other"){
          //return redirect()->route('admin.dashboard');
          dd('cccc');
        }

      }
      else{
         return redirect()->route('login')->with('error','login details are invalid');

      }
   }


      public function logout()
      {
      	Session::flush();
        Auth::logout();
        return redirect()->route('login');

      }
      public function registerview(){
         return view('subadmin.pages-register');
      }
      public function register(Request $request){
          $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:stores',
            'username'=>'required|min:6',
            'password'=>'required|min:8',
        ]);
        $store = new Store;
        $store->name = $request->name;
        $store->email = $request->email;
        $store->role = 'Vendor';
        $store->username = $request->username;
        $store->password = Hash::make($request->password);
        $store->save();
        return redirect()->route('login');
     }
      public function dashboard(){
        return view('subadmin.index');
      }
      public function generalconfig(){
        return view('subadmin.general-configuration');
      }
      public function submitgeneralconfig(Request $request){
            //return $request->all();
            $request->validate([
            'emailid'=>'required|email',
            'mobile'=>'required|min:10',
          ]);
          $id = session()->get('data'); 
          Store::where('id',$id)->update(['email'=>$request->emailid,'mobile'=>$request->mobile]);
          return redirect()->route('admin.generalconfig')->with('success','data stored successfully');

       }
       public function storefront(){
         return view('subadmin.store-front');
       }
       public function submitstorefront(Request $request){
            $request->validate([
            'logo'=>'required|image',
            'about_store'=>'required',
            'store_carry'=>'required',
          ]);
          if($request->hasfile('logo')){
            $file = $request->file('logo');
            $extension = $file->getClientOriginalExtension();
            $filename = time().'.'.$extension;
            $file->move('uploads/logo/',$filename);
          }
           $id = session()->get('data'); 
           Store::where('id',$id)->update(['logo'=>$filename,'about_store'=>$request->about_store,'store_carry'=>$request->store_carry]);
           return redirect()->route('admin.storefront')->with('success','data stored successfully');
       }
      public function editprofile(){
         $id = session()->get('data');
         $data = Store::where('id',$id)->first();
         return view('subadmin.users-profile',compact('data'));
      }
      public function saveprofile(Request $request){
            $request->validate([
            'fullname'=>'required',
            'phone'=>'required',
            'about'=>'required',
            'company'=>'required',
            'job'=>'required',
            'country'=>'required',
            'address'=>'required',
          ]);
           $id = session()->get('data'); 
           Store::where('id',$id)->update(['name'=>$request->fullname,'mobile'=>$request->phone,'about'=>$request->about,'company'=>$request->company,'job'=>$request->job,'country'=>$request->country,'address'=>$request->address]);
            return redirect()->route('admin.editprofile')->with('success','profile updated');
      }
      public function changepassword(Request $request){
            $request->validate([
             'password'=>'required',
             'newpassword'=>'required|min:8|max:8',
             'renewpassword'=>'required|same:newpassword',
          ]);
          $id = session()->get('data'); 
          $data = Store::where('id',$id)->first();
          if(Hash::check($request->password,$data->password)){
            Store::where('id',$id)->update(['password'=>Hash::make($request->newpassword)]);
            return redirect()->route('admin.editprofile')->with('success','password changed');

         }

          else{
            return redirect()->route('admin.editprofile')->with('error','Current Password does not match');
          }


      }
     public function profileimage(Request $request){
       $id =  Auth::id();
        if ($id && $request->profile) {
        $store = Store::where('id', $id)->first();

      if (!$store) {
        $response['success'] = false;
        $response['message'] = "Error! user not found.";
      } else {
        if($request->hasfile('profile')){
            $file = $request->file('profile');
            $extension = $file->getClientOriginalExtension();
            $filename = time().'.'.$extension;
            $file->move('uploads/profile/',$filename);
          }
        
        //Banner::update([
         Store::where('id',$id)->update(['profile_picture'=>$filename]);
        //]);
        $response['success'] = true;
        $response['message'] = "Success! user image  updated successfully.";
      }
    } else {
      $response['success'] = false;
      $response['message'] = "Error! Please enter all the required fields.";
    }

    return json_encode($response);

     }

} 
