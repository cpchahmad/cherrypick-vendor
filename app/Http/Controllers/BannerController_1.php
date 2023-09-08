<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banner;
use Auth;
use App\Models\Store;
//use App\Models\Banner;

class BannerController extends Controller
{
    public function bannerview(){
        $id =  Auth::id();
        $data = Banner::where('vendor_id',$id)->get();
    	return view('subadmin.manage-banner',compact('data'));
    }
    public function submithomedesktopbanner(Request $request){
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
            $file->move('uploads/banner/',$filename);
          }
          $banner = Banner::updateOrCreate(
            ['vendor_id' => $id],
            [
                'home_desktop_banner'=>$filename,
                'vendor_id' => $id
            ]
       );
        $response['success'] = true;
        $response['message'] = "Success! user image  updated successfully.";
      }
    } else {
      $response['success'] = false;
      $response['message'] = "Error! Please enter all the required fields.";
    }

    return json_encode($response);
}
 public function submithomemobilebanner(Request $request){
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
            $file->move('uploads/banner/',$filename);
          }
        
        //Banner::update([
          /*$banner = new Banner;
          $banner->home_mobile_banner = $filename;
          $banner->vendor_id = $id;
          $banner->save();*/
        //]);
        Banner::where('vendor_id',$id)->update(['home_mobile_banner'=>$filename]);
        $response['success'] = true;
        $response['message'] = "Success! user image  updated successfully.";
      }
    } else {
      $response['success'] = false;
      $response['message'] = "Error! Please enter all the required fields.";
    }

    return json_encode($response);
  }
  public function submitstoredesktopbanner(Request $request){
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
            $file->move('uploads/banner/',$filename);
          }
        
        //Banner::update([
        Banner::where('vendor_id',$id)->update(['store_desktop_banner'=>$filename]);
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
  public function submitstoremobilebanner(Request $request){
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
            $file->move('uploads/banner/',$filename);
          }
        
        //Banner::update([
         Banner::where('vendor_id',$id)->update(['store_mobile_banner'=>$filename]);
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
