<?php

namespace App\Console\Commands;

use App\Http\Controllers\superadmin\SuperadminController;
use App\Models\Setting;
use Illuminate\Console\Command;
use App\Models\Product;

class updateProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:product';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update products';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
//        $setting=Setting::first();
//        if($setting){
//            $API_KEY =$setting->api_key;
//            $PASSWORD = $setting->password;
//            $SHOP_URL =$setting->shop_url;
//
//        }else{
//            $API_KEY = '6bf56fc7a35e4dc3879b8a6b0ff3be8e';
//            $PASSWORD = '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';
//            $SHOP_URL = 'cityshop-company-store.myshopify.com';
//        }
//
//        $product = Product::where('edit_status', 1)->where('status', 1)->get();
//        foreach($product as $row)
//        {
//            $shopify_id=$row->shopify_id;
//            $data['product']=array(
//                    "id" => $shopify_id,
//                    "title" => $row->title,
//                    "tags"   => $row->tags,
//                );
//            $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/products/$shopify_id.json";
//            $curl = curl_init();
//            curl_setopt($curl, CURLOPT_URL, $SHOPIFY_API);
//            $headers = array(
//                "Authorization: Basic ".base64_encode("$API_KEY:$PASSWORD"),
//                "Content-Type: application/json",
//                "charset: utf-8"
//            );
//            curl_setopt($curl, CURLOPT_HTTPHEADER,$headers);
//            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//            curl_setopt($curl, CURLOPT_VERBOSE, 0);
//            //curl_setopt($curl, CURLOPT_HEADER, 1);
//            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
//            //curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
//            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
//            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
//            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
//
//            $response = curl_exec ($curl);
//            curl_close ($curl);
//            Product::where('id', $row['id'])->update(['edit_status' => 0]);
//        }
        //return Command::SUCCESS;

        $superAdminController=new SuperadminController();
        $superAdminController->DadusUpdate();
    }
}
