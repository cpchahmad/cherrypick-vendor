<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\Role;
use App\Models\ProductInfo;
use App\Models\Product;
use App\Models\Order;
use App\Models\Orderitem;
use App\Models\StoreTiming;
use Illuminate\Support\Facades\Auth;
use Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Helpers\Helpers;
use Carbon\Carbon;

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
		$user = Store::where('username',$input['username'])->first();
		if($user)
		{
		if($user->status=='Active')
		{
         $remember_me = $request->has('remember_me') ? true : false;
      if(auth::attempt(['username'=>$input['username'],'password'=>$input['password']],$remember_me)){
        if(Auth::user()->role == "Vendor"){
          return redirect()->route('home');
        }
        if(Auth::user()->role == "SuperAdmin"){
            return redirect('superadmin/dashboard');
        }
        if(Auth::user()->role == "Other"){
            $permission = Role::where('id',Auth::user()->store_role_id)->first();
            session()->put('store_configuration', $permission->store_configuration);
            session()->put('products', $permission->products);
            session()->put('orders',$permission->orders);
            session()->put('marketing',$permission->marketing);
          return redirect()->route('home');
          ///dd('cccc');
        }

      }
      else{
         return redirect()->route('login')->with('error','login details are invalid');

      }
		}
		else{
			return redirect()->route('login')->with('error','This user is not acitve');
		}
		}
		else
			 return redirect()->route('login')->with('error','login details are invalid');
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
            'name' => 'required|unique:stores',
            'email' => 'required|email|unique:stores',
            'username'=>'required|min:6',
            'password'=>'required|min:8',
        ]);
        $store = new Store;
        $store->name = $request->name;
        $store->email = $request->email;
        //$store->collections_ids = $request->collections_ids;
        $store->role = 'Vendor';
        $store->username = $request->username;
        $store->password = Hash::make($request->password);
        $store->save();
		$id=$store->id;
		$collection_id=$this->createCollection($request->name);
		Store::where('id', $id)->update(['collections_ids' => $collection_id]);
        return redirect()->route('login');
     }
	 public function createCollection($title)
	{
        $setting=Setting::first();
        if($setting){
            $API_KEY =$setting->api_key;
            $PASSWORD = $setting->password;
            $SHOP_URL =$setting->shop_url;

        }else{
            $API_KEY = '6bf56fc7a35e4dc3879b8a6b0ff3be8e';
            $PASSWORD = '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';
            $SHOP_URL = 'cityshop-company-store.myshopify.com';
        }



//        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2020-04/custom_collections.json";
        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2020-04/smart_collections.json";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $SHOPIFY_API);
        $headers = array(
            "Authorization: Basic ".base64_encode("$API_KEY:$PASSWORD"),
            "Content-Type: application/json",
            "X-Shopify-Api-Features: include-presentment-prices",
            "charset: utf-8"
        );


             $values = [
                 'column' => 'vendor',
                 'relation'=>'equals',
                 'condition'=>$title
             ];

         $data=
             [
                 'smart_collection' => [
                     'title' => $title,
                     "disjunctive" => false,
                     "body_html" => null,
                     'rules' => [$values]
                 ]
             ];


        curl_setopt($curl, CURLOPT_HTTPHEADER,$headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_VERBOSE, 0);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
//        curl_setopt($curl, CURLOPT_POSTFIELDS, '{"custom_collection":{"title":"'.$title.'"}}');
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec ($curl);
        curl_close ($curl);
        $result=json_decode($response, true);
//		return $result['custom_collection']['id'];
		return $result['smart_collection']['id'];
	}
      public function dashboard(){
		if(Auth::user()->id==7)
		{
			return redirect()->route('admin.logout');
			die();
		}
         $vendor=Helpers::VendorID();
         $sql=Order::where('vendor', $vendor)->get()->toArray();
         $total_order=count($sql);
         $sql_week=Order::where('vendor', $vendor)->whereBetween('order_date',[Carbon::now()->startOfWeek()->toDateString(), Carbon::now()->endOfWeek()->toDateString()])->get()->toArray();
         $total_order_week=count($sql_week);
         $sql_week=Order::where('vendor', $vendor)->whereBetween('order_date',[Carbon::now()->startOfWeek()->toDateString(), Carbon::now()->endOfWeek()->toDateString()])->get()->toArray();
         $total_order_week=count($sql_week);
         $sql_month=Order::where('vendor', $vendor)->whereBetween('order_date',[Carbon::now()->startOfMonth()->toDateString(), Carbon::now()->lastOfMonth()->toDateString()])->get()->toArray();
         $total_order_month=count($sql_month);
         $sql_month=Order::where('vendor', $vendor)->whereBetween('order_date',[Carbon::now()->startOfMonth()->toDateString(), Carbon::now()->lastOfMonth()->toDateString()])->get()->toArray();
         $total_order_month=count($sql_month);
         $sql_today=Order::where('vendor', $vendor)->where('order_date',Carbon::now()->toDateString())->get()->toArray();
         $total_order_today=count($sql_today);

         $sql_items=Orderitem::where('vendor_id', $vendor)->get()->toArray();
         $total_items_sale=count($sql_items);
         $total_price=0;
         foreach($sql_items as $v)
         {
             //$total_price=$total_price+($v['price']-$v['discount']);
			 $total_price=$total_price+($v['price']);
         }

         $out_of_stock=ProductInfo::join('product_master','product_master.id','products_variants.product_id')
                            ->select('product_master.id as pid','product_master.title','products_variants.*')
                            ->where('products_variants.vendor_id', $vendor)->where('products_variants.stock', '<', 1)->limit(20)->get();

        $sql=Order::where('vendor', $vendor);
        $new_orders=$sql->where('status', 0)->paginate(30);

        $month_arr=['01','02','03','04','05','06','07','08','09','10','11','12'];
        $data_month_arr=array();
        foreach($month_arr as $currentMonth)
        {
            $order_items_sql = Orderitem::select(\DB::raw('SUM(price) as price, SUM(discount) as discount'))->whereRaw('MONTH(created_at) = ?',[$currentMonth])->where('vendor_id', $vendor)->get()->toArray();
            $data_month_arr[]=ceil($order_items_sql[0]['price']-$order_items_sql[0]['discount']);
        }
//
//         echo "<pre>"; print_r($data_month_arr);
//         die();
        return view('subadmin.index',compact('total_order','total_order_week','total_order_month','total_items_sale','total_price','total_order_today','out_of_stock','new_orders','data_month_arr'));
      }
      public function generalconfig(){
		$id=Helpers::VendorID();
        $data=Store::find($id);
		$storeTiming=StoreTiming::where('store_id', $id)->get()->toArray();
		$open=array();
		$close=array();
		$status=array();
		foreach($storeTiming as $v)
		{
			$open[$v['day']]=$v['opening_time'];
			$close[$v['day']]=$v['closing_time'];
			$status[$v['day']]=$v['status'];
		}
        return view('subadmin.general-configuration',compact('data','open', 'close', 'status'));
      }
      public function submitgeneralconfig(Request $request){
		  //echo "<pre>"; print_r($request->all()); die();
            //return $request->all();
            $request->validate([
            'emailid'=>'required|email|email:rfc,dns',
            'mobile'=>'required|min:10',
            //'collections_ids'=>'required',
          ]);
          $id=Helpers::VendorID();
          Store::where('id',$id)->update(['email'=>$request->emailid,'mobile'=>$request->mobile,'collections_ids'=>$request->collections_ids]);
		  for($i=1; $i<=7; $i++)
		  {
			  StoreTiming::updateOrCreate(
										['store_id' => $id, 'day' => $i],
										['opening_time' => $request->open[$i], 'closing_time' => $request->close[$i], 'status' => $request->status[$i]]
										);
		  }

          return redirect()->route('admin.generalconfig')->with('success','data stored successfully');

       }
       public function storefront(){
         $data=Store::where('id',Auth::user()->id)->first();
         return view('subadmin.store-front',compact('data'));
       }
       public function submitstorefront(Request $request){

           $setting=Setting::first();
           if($setting){
               $API_KEY =$setting->api_key;
               $PASSWORD = $setting->password;
               $SHOP_URL =$setting->shop_url;

           }else{
               $API_KEY = '6bf56fc7a35e4dc3879b8a6b0ff3be8e';
               $PASSWORD = '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';
               $SHOP_URL = 'cityshop-company-store.myshopify.com';
           }
            $request->validate([
            'logo'=>'image|mimes:jpg,jpeg,png,gif',
            'about_store'=>'required',
            'store_carry'=>'required',
          ]);
          if($request->hasfile('logo')){
            $file = $request->file('logo');
            $extension = $file->getClientOriginalExtension();
            $filename = time().'.'.$extension;
            $file->move('uploads/logo/',$filename);
            $data['logo']=$filename;
          }
           $data['about_store']=$request->about_store;
           $data['store_carry']=$request->store_carry;
       $store_data=Store::where('id',Auth::user()->id)->update($data);
$store=Store::where('id',Auth::user()->id)->latest()->first();

           $values = [
               'store_logo' => 'http://phpstack-1103991-3868726.cloudwaysapps.com/uploads/logo/'.$store->logo,
               'about_store' => $store->about_store,
               'store_carry' => $store->store_carry
           ];
       $metafield_data=[
               "metafield" =>
                [
                               "key" => 'store_front',
                               "value" => json_encode($values),
                               "type" => "json_string",
                               "namespace" => "configuration",

               ]
       ];




           $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/smart_collections/$store->collections_ids/metafields.json";

           $curl = curl_init();
           curl_setopt($curl, CURLOPT_URL, $SHOPIFY_API);
           $headers = array(
               "Authorization: Basic ".base64_encode("$API_KEY:$PASSWORD"),
               "Content-Type: application/json",
               "charset: utf-8"
           );
           curl_setopt($curl, CURLOPT_HTTPHEADER,$headers);
           curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
           curl_setopt($curl, CURLOPT_VERBOSE, 0);
           //curl_setopt($curl, CURLOPT_HEADER, 1);
           curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
           //curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
           curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($metafield_data));
           curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
           curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

           $response = curl_exec ($curl);

           curl_close ($curl);


           return redirect()->route('admin.storefront')->with('success','data stored successfully');
       }
      public function editprofile(){
         $data = Store::where('id',Auth::user()->id)->first();
         return view('subadmin.users-profile',compact('data'));
      }
      public function saveprofile(Request $request){
            $request->validate([
            'fullname'=>'required',
            // 'about'=>'required',
            // 'company'=>'required',
            // 'job'=>'required',
            // 'country'=>'required',
            // 'address'=>'required',
          ]);
          if($request->hasfile('file')){
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $filename = time().'.'.$extension;
            $file->move('uploads/profile',$filename);
            $data['profile_picture']=$filename;
          }
            $data['name']=$request->fullname;
            $data['about']=$request->about;
            $data['company']=$request->company;
            $data['job']=$request->job;
            $data['country']=$request->country;
            $data['address']=$request->address;
            $id = session()->get('data');
            Store::where('id',Auth::user()->id)->update($data);
            return redirect()->route('admin.editprofile')->with('success','profile updated');
      }
      public function changepassword(Request $request){
            $request->validate([
             'password'=>'required',
             'newpassword'=>'required|min:8|max:8',
             'renewpassword'=>'required|same:newpassword',
          ]);
          $id = session()->get('data');
          $data = Store::where('id',Auth::user()->id)->first();
          if(Hash::check($request->password,$data->password)){
            Store::where('id',$id)->update(['password'=>Hash::make($request->newpassword)]);
            return redirect()->route('admin.editprofile')->with('success','password changed');

         }

          else{
            return redirect()->route('admin.editprofile')->with('error','Current Password does not match');
          }


      }
      public function removeprofile($id){
        $id = Store::findOrFail($id);
        $path = 'uploads/profile/'.$id->profile_picture;
        if(File::exists($path)){
           File::delete($path);
         }
        return redirect()->route('admin.editprofile');

      }


}
