<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\Orderitem;
use App\Models\Store;
use Auth;
use App\Models\OrdersOtp;

class fetchOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'fetch store orders';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
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
        $data=array();
        $arr_key=0;
        foreach($result['orders'] as $k=>$v)
        {
            $order_number=$v['order_number'];
            $order_date=date('Y-m-d',strtotime($v['created_at']));
            $total_amount=$v['current_total_price'];
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
                    $store=Store::where('name',$item_val['vendor'])->first();
                    if($store)
                    {
                        $store_id=$store->id;
                    if( !in_array( $store->id ,$store_arr ) )
                        $store_arr[]=$store->id;
                    }
                    $vendor=$item_val['vendor'];
                    $shopify_orders_id=$order_number;
                    $shopify_items_id=$item_val['id'];
                    $product_name=$item_val['name'];
                    $price=$item_val['price'];
                    $quantity=$item_val['quantity'];
                    $sku=$item_val['sku'];
                    //$discount=$item_val['total_discount'];
                    $discount_arr=$item_val['discount_allocations'];
                    if(!empty($discount_arr))
                        $discount=$discount_arr[0]['amount'];
                    else
                        $discount=0;

                    $items=new Orderitem;
                    $items->vendor=$vendor;
                    $items->vendor_id=$store_id;
                    $items->shopify_orders_id=$shopify_orders_id;
                    $items->shopify_items_id=$shopify_items_id;
                    $items->product_name=$product_name;
                    $items->price=$price;
                    $items->quantity=$quantity;
                    $items->sku=$sku;
                    $items->discount=$discount;
                    $items->save();

                }
                foreach($store_arr as $store_val)
                {
                    $otp=rand(100000,999999);
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
                    $res->save();
                }

        }
        //return Command::SUCCESS;
    }
}
