<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\VendorUser;
use Auth;
Use Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      if($request->search != ""){
       $search = $request->search;
       $user = VendorUser::query()
      ->where('first_name' ,'Like', "%{$search}%")
      ->orWhere('email', 'Like', "%{$search}%")
      ->orWhere('phone', 'Like', "%{$search}%")
      ->orWhere('role', 'Like', "%{$search}%")
      ->get();
         return view('subadmin.view-user',compact('user'));
      }
        $id = Auth::id();
        $user = VendorUser::where('vendor_id',$id)->get();
        /*foreach($user as $row){
            $data[] = Role::where('name',$row['role'])->get();
        }*/
        return view('subadmin.view-user',compact('user'));
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $role = Role::all();
        return view('subadmin.add-user',compact('role'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name'=>'required',
            'last_name'=>'required',
            'phone'=>'required',
            'role'=>'required',
            'email'=>'required|email',
            'password'=>'required|min:8|max:8',
            'profile'=>'required',
          ]);
         $data = new VendorUser;
         $data->first_name = $request->first_name;
         $data->last_name = $request->last_name;
         $data->phone = $request->phone;
         $data->role = $request->role;
         $data->email = $request->email;
         $data->password = Hash::make($request->password);
         if($request->hasfile('profile')){
            $file = $request->file('profile');
            $extension = $file->getClientOriginalExtension();
            $filename = time().'.'.$extension;
            $file->move('uploads/userprofile/',$filename);
            $data->profile = $filename;
        }
         $data->vendor_id = Auth::id();
         $data->save();
          return redirect()->route('users.index')->with('success','User Created.');
       }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::all();
        $user = VendorUser::findOrFail($id);
        return view('subadmin.edit-user',compact('role','user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'first_name'=>'required',
            'last_name'=>'required',
            'phone'=>'required',
            'role'=>'required',
            'email'=>'required|email',
            'password'=>'nullable',
            'profile'=>'nullable|image',
          ]);
        $VendorUser = VendorUser::where('id',$id)->first();
        $VendorUser->first_name = $request->first_name;
        $VendorUser->last_name = $request->last_name;
        $VendorUser->phone = $request->phone;
        $VendorUser->role = $request->role;
        $VendorUser->email = $request->email;
        $VendorUser->password = Hash::make($request->password);
        if($request->hasfile('profile')){
            $file = $request->file('profile');
            $extension = $file->getClientOriginalExtension();
            $filename = time().'.'.$extension;
            $file->move('uploads/userprofile/',$filename);
            $VendorUser->profile = $filename;
        }
        $VendorUser->vendor_id = Auth::id();
        $VendorUser->update();
       return redirect()->route('users.index')->with('success','User updated.');

 }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $id = VendorUser::findOrFail($id);
        $id->delete();
        return redirect()->route('users.index')->with('success','User deleted.');


    }
}
