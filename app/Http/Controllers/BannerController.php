<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banner;
use Auth;
use App\Models\Store;
use App\Helpers\Helpers;
//use App\Models\Banner;

class BannerController extends Controller
{
    public function bannerview(){
        $id =  Helpers::VendorID();
        $data = Banner::where('vendor_id',$id)->get();
    	 return view('subadmin.manage-banner',compact('data'));
    }
    public function savebanner(Request $request)
    {
        $vendor_id=Helpers::VendorID();
        $store_name=strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', Helpers::StoreName())));
        $row=Banner::where('vendor_id',$vendor_id)->first();
        if($row)
        {
            $home_desktop_banner=$row->home_desktop_banner;
            $home_mobile_banner=$row->home_mobile_banner;
            $store_desktop_banner=$row->store_desktop_banner;
            $store_mobile_banner=$row->store_mobile_banner;
        }
        else
        {
            $home_desktop_banner='';
            $home_mobile_banner='';
            $store_desktop_banner='';
            $store_mobile_banner='';
        }
        if($request->hasfile('home_desktop_banner')){
            $file = $request->file('home_desktop_banner');
            $extension = $file->getClientOriginalExtension();
            $home_desktop_banner = 'home_desktop_banner_'.time().'.'.$extension;
            $file->move('uploads/banner/',$home_desktop_banner);
          }
          if($request->hasfile('home_mobile_banner')){
            $file = $request->file('home_mobile_banner');
            $extension = $file->getClientOriginalExtension();
            $home_mobile_banner = 'home_mobile_banner_'.time().'.'.$extension;
            $file->move('uploads/banner/',$home_mobile_banner);
          }
          if($request->hasfile('store_desktop_banner')){
            $file = $request->file('store_desktop_banner');
            $extension = $file->getClientOriginalExtension();
            $store_desktop_banner = 'store_desktop_banner_'.time().'.'.$extension;
            $file->move('uploads/banner/',$store_desktop_banner);
          }
          if($request->hasfile('store_mobile_banner')){
            $file = $request->file('store_mobile_banner');
            $extension = $file->getClientOriginalExtension();
            $store_mobile_banner = 'store_mobile_banner_'.time().'.'.$extension;
            $file->move('uploads/banner/',$store_mobile_banner);
          }
          Banner::updateOrCreate(
            ['vendor_id' => $vendor_id],
            [
                'home_desktop_banner'=>$home_desktop_banner,
                'home_mobile_banner'=>$home_mobile_banner,
                'store_desktop_banner'=>$store_desktop_banner,
                'store_mobile_banner'=>$store_mobile_banner,
                'store_slug'=>$store_name,
                'vendor_id' => $vendor_id,
				'approve_status' => 'Disable'
            ]
          );
          return redirect()->route('admin.banner');
    }
    public function banner(Request $request)
    {
            $id=$request->id;
            $data=Banner::where('store_slug', $id)->first();
            if($data)
                $data = ['src' => url($data->store_desktop_banner)];
            else
                $data = ['src' => url('uploads/banner/banner.jpg')];
            return json_encode($data);
    }
}
