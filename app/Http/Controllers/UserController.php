<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\VendorUser;
use App\Models\Store;
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
        $id = Auth::id();
        $user = Store::join('roles','stores.store_role_id','roles.id')->select('stores.*','roles.name as role_name')->where('vendor_id',$id)->get();
        return view('subadmin.view-user',compact('user'));
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $role = Role::where('vendor',Auth::id())->get();
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
         $data = new Store;
         $data->name = $request->first_name;
         $data->last_name = $request->last_name;
         $data->mobile = $request->phone;
         $data->role = 'Other';
         $data->store_role_id = $request->role;
         $data->email = $request->email;
         $data->password = Hash::make($request->password);
         $data->username = $request->email;
         if($request->hasfile('profile')){
            $file = $request->file('profile');
            $extension = $file->getClientOriginalExtension();
            $filename = time().'.'.$extension;
            $file->move('uploads/userprofile/',$filename);
            $data->profile_picture = $filename;
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
        $role = Role::where('vendor',Auth::id())->get();
        $user = Store::findOrFail($id);
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
            'password'=>'required',
            'profile'=>'nullable|image',
          ]);
        $VendorUser = Store::where('id',$id)->first();
        $VendorUser->name = $request->first_name;
        $VendorUser->last_name = $request->last_name;
        $VendorUser->mobile = $request->phone;
        $VendorUser->store_role_id = $request->role;
        $VendorUser->email = $request->email;
        $VendorUser->username = $request->email;
        $VendorUser->password = Hash::make($request->password);
        if($request->hasfile('profile')){
            $file = $request->file('profile');
            $extension = $file->getClientOriginalExtension();
            $filename = time().'.'.$extension;
            $file->move('uploads/userprofile/',$filename);
            $VendorUser->profile_picture = $filename;
        }
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
        $id = Store::findOrFail($id);
        $id->delete();
        return redirect()->route('users.index')->with('success','User deleted.');


    }
}
