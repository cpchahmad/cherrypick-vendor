<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\Product;
use Auth;
use App\Models\Category;
use App\Models\ProductInfo;
use App\Models\Order;
use App\Models\Orderitem;
use App\Models\Store;
use App\Models\OrdersOtp;
use DB;
use Session;
use App\Helpers\Helpers;
use Carbon\Carbon;
use Mail;

class OrdersController extends Controller
{
    public function createOrderWebhook(Request $request)
    {
        $webhookContent = "";
        $webhook = fopen('php://input' , 'rb');
        while (!feof($webhook)) {
            $webhookContent .= fread($webhook, 4096);
        }
        fclose($webhook);
        $v = json_decode($webhookContent, true);
            $order_number=$v['order_number'];
            $order_date=date('Y-m-d',strtotime($v['created_at']));
            $total_amount=$v['total_price'];
            $currency=$v['currency'];
            $payment_status=$v['financial_status'];
            $fulfillment_status=$v['fulfillment_status'];
            $total_items=count($v['line_items']);
            $billing_address=json_encode($v['billing_address']);
            $customer=json_encode($v['customer']);
                $store_arr=array();
                foreach($v['line_items'] as $item_val)
                {
                    $store_id=0;
					$check_count=Orderitem::where('shopify_variant_id', $item_val['variant_id'])->where('shopify_orders_id', $order_number)->count();
					if($check_count==0)
					{
					$st_discount=0;
                    $store=Store::where('name',$item_val['vendor'])->first();
                    if($store)
                    {
                        $store_id=$store->id;
						$st_discount=$store->vendor_discount;
                    if( !in_array( $store->id ,$store_arr ) )
                        $store_arr[]=$store->id;
                    }
                    $vendor=$item_val['vendor'];
                    $shopify_orders_id=$order_number;
                    $shopify_items_id=$item_val['id'];
                    $product_name=$item_val['name'];
                    //$price=$item_val['price'];
                    $quantity=$item_val['quantity'];
                    $sku=$item_val['sku'];
                    $shopify_variant_id=$item_val['variant_id'];
                    //$discount=$item_val['total_discount'];
                    $discount_arr=$item_val['discount_allocations'];
                    // if(!empty($discount_arr))
                        // $discount=$discount_arr[0]['amount'];
                    // else
                        $discount=0;

                    $info_price=ProductInfo::where('inventory_id', $shopify_variant_id)->first();
					if($info_price) {
						if($info_price->product_discount > 0)
							$price=$info_price->discounted_base_price*$quantity;
						else
							$price=$info_price->base_price*$quantity;
						//check discount only in inr
						if($v['presentment_currency']=='INR' && $item_val['total_discount'] > 0)
						{
							$price=$price-$item_val['total_discount'];
						}
						if($st_discount > 0)
						{
							$discount=$price*($st_discount/100);
							$price=$price-$discount;
						}
					}
					else
						$price=0;
                    $items=new Orderitem;
                    $items->vendor=$vendor;
                    $items->vendor_id=$store_id;
                    $items->shopify_orders_id=$shopify_orders_id;
                    $items->shopify_items_id=$shopify_items_id;
                    $items->shopify_variant_id=$shopify_variant_id;
                    $items->product_name=$product_name;
                    $items->price=$price;

                    $items->quantity=$quantity;
                    $items->sku=$sku;
                    $items->discount=$discount;
                    $items->save();
					}
                }
                foreach($store_arr as $store_val)
                {
                    $otp=rand(100000,999999);
                    $store_dis=Store::where('id',$store_val)->first();
                    $res=new Order;
                    $res->shopify_order_id=$order_number;
                    $res->order_date=$order_date;
                    $res->payment_status=$payment_status;
                    $res->total_amount=$total_amount;
                    $res->currency=$currency;
                    $res->fulfillment_status=$fulfillment_status;
                    $res->no_of_items=$total_items;
                    $res->billing_address=$billing_address;
                    $res->customer=$customer;
                    $res->vendor=$store_val;
                    $res->otp=$otp;
                    $res->vendor_discount=$store_dis->vendor_discount;
                    $res->save();
					$email=$store_dis->email;
					// Mail::send('subadmin.mail.new-order', array('order' => $order_number), function($message) use ($email)
					// {
						// $message->from('cherrypick@example.com', 'Cherrypick');
						// $message->to($email);
						// $message->subject('New Order Recived');
					// });
                }
                DB::table('tests')->insert(
                    array(
                        'name'   =>   $order_number
                    )
                 );
    }
    public function fetchShopifyOrders()
    {
		//die();
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


        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2020-04/orders.json?order=created_at+desc";
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
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec ($curl);
        curl_close ($curl);
        $result=json_decode($response,true);
        echo "<pre>";
        print_r($result); die();
        $data=array();
        $arr_key=0;
        // foreach($result['orders'] as $k=>$v)
        // {

			// if($v['order_number'] > 2612)
			// {
			// $order_number=$v['order_number'];
            // $order_date=date('Y-m-d',strtotime($v['created_at']));
            // $total_amount=$v['total_price'];
            // $currency=$v['currency'];
            // $payment_status=$v['financial_status'];
            // $fulfillment_status=$v['fulfillment_status'];
            // $total_items=count($v['line_items']);
            // $billing_address=json_encode($v['billing_address']);
            // $customer=json_encode($v['customer']);
                // $store_arr=array();
                // foreach($v['line_items'] as $item_val)
                // {
                    // $store_id=0;
                    // $store=Store::where('name',$item_val['vendor'])->first();
                    // if($store)
                    // {
                        // $store_id=$store->id;
                    // if( !in_array( $store->id ,$store_arr ) )
                        // $store_arr[]=$store->id;
                    // }
                    // $vendor=$item_val['vendor'];
                    // $shopify_orders_id=$order_number;
                    // $shopify_items_id=$item_val['id'];
                    // $product_name=$item_val['name'];
                    // $price=$item_val['price'];
                    // $quantity=$item_val['quantity'];
                    // $sku=$item_val['sku'];
                    // $shopify_variant_id=$item_val['variant_id'];
                    // //$discount=$item_val['total_discount'];
                    // $discount_arr=$item_val['discount_allocations'];
                    // if(!empty($discount_arr))
                        // $discount=$discount_arr[0]['amount'];
                    // else
                        // $discount=0;

                    // $items=new Orderitem;
                    // $items->vendor=$vendor;
                    // $items->vendor_id=$store_id;
                    // $items->shopify_orders_id=$shopify_orders_id;
                    // $items->shopify_items_id=$shopify_items_id;
                    // $items->shopify_variant_id=$shopify_variant_id;
                    // $items->product_name=$product_name;
                    // $items->price=$price;
                    // $items->quantity=$quantity;
                    // $items->sku=$sku;
                    // $items->discount=$discount;
                    // $items->save();
                // }
                // foreach($store_arr as $store_val)
                // {
                    // $otp=rand(100000,999999);
                    // $store_dis=Store::where('id',$store_val)->first();
                    // $res=new Order;
                    // $res->shopify_order_id=$order_number;
                    // $res->order_date=$order_date;
                    // $res->payment_status=$payment_status;
                    // $res->total_amount=$total_amount;
                    // $res->currency=$currency;
                    // $res->fulfillment_status=$fulfillment_status;
                    // $res->no_of_items=$total_items;
                    // $res->billing_address=$billing_address;
                    // $res->customer=$customer;
                    // $res->vendor=$store_val;
                    // $res->otp=$otp;
                    // $res->vendor_discount=$store_dis->vendor_discount;
                    // $res->save();
                // }

			// }



        // }
                echo "good"; die();
    }
    public function allOrders(Request $request)
    {

        $vendor_id=Helpers::VendorID();
        $sql=Order::where('vendor',$vendor_id);
        if($request->query('order') != ""){
          $sql->where('shopify_order_id' , $request->query('order'));
        }
        if($request->query('sdate') != "" && $request->query('edate') != ""){
          $sql->where('order_date' , '>=', $request->query('sdate'));
		  $sql->where('order_date' , '<=', $request->query('edate'));
        }
		if($request->query('flag') != "" && $request->query('flag') == "week"){
          $sql->whereBetween('order_date',[Carbon::now()->startOfWeek()->toDateString(), Carbon::now()->endOfWeek()->toDateString()]);
        }
		if($request->query('flag') != "" && $request->query('flag') == "month"){
          $sql->whereBetween('order_date',[Carbon::now()->startOfMonth()->toDateString(), Carbon::now()->lastOfMonth()->toDateString()]);
        }

        if($request->query('status') !=""){
            $sql->where('status',$request->query('status'));
        }
        $data=$sql->orderBy('shopify_order_id', 'desc')->paginate(30);
        return view('subadmin.orders',compact('data'));
    }
    public function newOrders(Request $request)
    {
        $vendor_id=Helpers::VendorID();
        $sql=Order::where('vendor',$vendor_id);
        if($request->query('order') != ""){
          $sql->where('shopify_order_id' , $request->query('order'));
        }
        if($request->query('date') != ""){
          $sql->where('order_date' , $request->query('date'));
        }
        $data=$sql->where('status', 0)->orderBy('shopify_order_id', 'desc')->paginate(30);
        return view('subadmin.new-orders',compact('data'));
    }
    public function pickupOrders(Request $request)
    {
        $vendor_id=Helpers::VendorID();
        $sql=Order::where('vendor',$vendor_id);
        if($request->query('order') != ""){
          $sql->where('shopify_order_id' , $request->query('order'));
        }
        if($request->query('date') != ""){
          $sql->where('order_date' , $request->query('date'));
        }
        $data=$sql->where('status', 1)->paginate(30);
        return view('subadmin.pickup-orders',compact('data'));
    }
    public function completeOrders(Request $request)
    {
        $vendor_id=Helpers::VendorID();
        $sql=Order::where('vendor',$vendor_id);
        if($request->query('order') != ""){
          $sql->where('shopify_order_id' , $request->query('order'));
        }
        if($request->query('date') != ""){
          $sql->where('order_date' , $request->query('date'));
        }
        $data=$sql->where('status', 2)->paginate(30);
        return view('subadmin.complete-orders',compact('data'));
    }
    public function detailsOrders($id)
    {
        $vendor_id=Helpers::VendorID();
        $data=Order::find($id);
        $items_data=Orderitem::where('vendor_id',$vendor_id)->where('shopify_orders_id',$data->shopify_order_id)->get();
        return view('subadmin.orders-details',compact('data','items_data'));
    }
    public function changeOrderStatus($oid,$status)
    {
        $vendor_id=Helpers::VendorID();
        Order::where(['shopify_order_id'=>$oid,'vendor'=>$vendor_id])->update(['status'=>$status]);
		//Update Status on shoperapp
			$store=Store::find($vendor_id);
			$vendor_name=$store->name;
            $status=$status;
            $order_id=$oid;
            $ch = curl_init();
			$postdata=array('vendor_name' => $vendor_name,'order_id' => $order_id,'status' => $status);
			curl_setopt($ch, CURLOPT_URL,"https://phpstack-711164-2355937.cloudwaysapps.com/update-order-status");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,$postdata);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			echo $server_output = curl_exec($ch);
			curl_close($ch);
			$result=json_decode($server_output, true);
		/////////////////////////////
		$email=Auth::user()->email;
		if($status==1)
		{
			// Mail::send('subadmin.mail.order-status-change', array('order' => $oid), function($message) use ($email)
			// {
				// $message->from('cherrypick@example.com', 'Cherrypick');
				// $message->to($email);
				// $message->subject('Order Ready For Pickup');
			// });
            return redirect()->route('pickup-orders')->with('success','Order Status Updated Successfully');
		}
        else
		{
			//update inventory status
			$items_data=Orderitem::where('vendor_id',$vendor_id)->where('shopify_orders_id',$oid)->get();
			foreach($items_data as $item_row)
			{
				$inventory_id=$item_row->shopify_variant_id;
				$quantity=$item_row->quantity;
				$productInfo=ProductInfo::where('inventory_id',$inventory_id)->first();
				if($productInfo)
				{
					$stock=$productInfo->stock - $quantity;
					ProductInfo::where('id',$productInfo->id)->update(['inventory_status'=>1,'stock'=>$stock]);
				}

			}

			// Mail::send('subadmin.mail.order-complete', array('order' => $oid), function($message) use ($email)
			// {
				// $message->from('cherrypick@example.com', 'Cherrypick');
				// $message->to($email);
				// $message->subject('Order Completed');
			// });
            return redirect()->route('pickup-orders')->with('success','Order Status Updated Successfully');
		}
    }
}
