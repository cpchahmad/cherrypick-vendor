<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Helpers\Helpers;
use Auth;

class RoleController extends Controller
{
    public function userrole(){
        $vendor=Helpers::VendorID();
        $data=Role::where('vendor',$vendor)->get();
       return view('subadmin.user-role',compact('data'));
    }
    public function roleCreate()
    {
        return view('subadmin.user-add-role');
    }
    public function edit($id)
    {
        $data=Role::find($id);
        return view('subadmin.user-edit-role',compact('data'));
    }
    public function updaterole(Request $request)
    {
        $input = $request->all();
    	$request->validate([
    		'name'=>'required',
         ]);
    	$role = Role::find($request->id);
    	$role->name = $request->name;
    	if($request->module1){
    		$role->store_configuration = 1;
    	}
        else
            $role->store_configuration = '0';
    	if($request->module2){
    		$role->products = 1;
    	}
        else
            $role->products = '0';
    	if($request->module3){
    		$role->orders = 1;
    	}
        else
            $role->orders = '0';
    	if($request->module4){
    		$role->marketing = 1;
    	}
        else
            $role->marketing = '0';

    	$role->save();
       return redirect()->route('user-role')->with('success','Role saved.');
    }
    public function saverole(Request $request){
    	//dd($request->module1);
    	$input = $request->all();
    	$request->validate([
    		'name'=>'required',
         ]);
    	$role = new Role;
    	$role->name = $request->name;
    	if($request->module1){
    		$role->store_configuration = 1;
    	}
    	if($request->module2){
    		$role->products = 1;
    	}
    	if($request->module3){
    		$role->orders = 1;
    	}
    	if($request->module4){
    		$role->marketing = 1;
    	}
        $vendor=Helpers::VendorID();
        $role->vendor=$vendor;
    	$role->save();
       return redirect()->route('user-role')->with('success','Role saved.');


    }
}
