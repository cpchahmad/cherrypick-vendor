<?php

namespace App\Console\Commands;

use App\Models\Log;
use App\Models\Product;
use App\Models\ProductLog;
use App\Models\Setting;
use Illuminate\Console\Command;
use App\Models\ProductInfo;
use Auth;
use DB;
use App\Helpers\Helpers;

class updatePrice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:price';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update product price';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

//		$data=ProductInfo::whereIn('vendor_id', [32,42])->where('price_status', 0)->whereNotNull('inventory_id')->orderBy('id', 'DESC')->get();


		//zain



        $data_count=ProductInfo::where('price_status',0)->whereNotNull('inventory_id')->orderBy('id', 'DESC')->count();


//        $data=ProductInfo::where('price_status',0)->whereNotNull('inventory_id')->orderBy('id', 'DESC')->get();

        if($data_count > 0) {
            $currentTime = now();
                $log = new Log();
                $log->name = 'Update Price in Shopify';
            $log->date = $currentTime->format('F j, Y');
            $log->start_time = $currentTime;
                $log->status = 'In-Progress';
                $log->save();
                $log_id=$log->id;

//            $variant_data = ProductInfo::where('price_status',0)->whereNotNull('inventory_id')->chunk(20, function ($data) use ($log_id) {
            $data = ProductInfo::where('price_status',0)->whereNotNull('inventory_id')->get();

                try {

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

                    $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2020-04/graphql.json";

                    $product_ids_array=array();


                    foreach ($data as $row) {
                        array_push($product_ids_array,$row->product_id);
                        //DB::table('tests')->insert(['name' => 'start']);
                        $INR = $row->price;
                        $CAD = $row->price_cad;
                        $GBP = $row->price_gbp;
                        $AUD = $row->price_aud;
                        $EUR = $row->price_nld;
                        $USD = $row->price_usd;
                        if ($row->product_discount > 0) {
                            $INR_com = $row->discounted_inr;
                            $CAD_com = $row->discounted_cad;
                            $GBP_com = $row->discounted_gbp;
                            $AUD_com = $row->discounted_aud;
                            $EUR_com = $row->discounted_nld;
                            $USD_com = $row->discounted_usd;
                        } else {
                            $INR_com = $row->price;
                            $CAD_com = $row->price_cad;
                            $GBP_com = $row->price_gbp;
                            $AUD_com = $row->price_aud;
                            $EUR_com = $row->price_nld;
                            $USD_com = $row->price_usd;
                        }
                        $arr['23571431599'] = array(
                            'compare_at_price' => '',
                            'price' => $INR_com,
                            'currecy' => 'INR'
                        );
                        $arr['23366041775'] = array(
                            'compare_at_price' => '',
                            'price' => $CAD_com,
                            'currecy' => 'CAD'
                        );
                        $arr['23550656687'] = array(
                            'compare_at_price' => '',
                            'price' => $GBP_com,
                            'currecy' => 'GBP'
                        );
                        $arr['23593582767'] = array(
                            'compare_at_price' => '',
                            'price' => $AUD_com,
                            'currecy' => 'AUD'
                        );
                        $arr['23550689455'] = array(
                            'compare_at_price' => '',
                            'price' => $EUR_com,
                            'currecy' => 'EUR'
                        );
                        $variant_id = $row->inventory_id;

                        $data['variant'] = array(
                            "id" => $variant_id,
                            "price" => $USD_com,
                            "compare_at_price" => '',
                            "grams" => $row->pricing_weight,
                        );

                        $setting = Setting::first();
                        if ($setting) {
                            $API_KEY = $setting->api_key;
                            $PASSWORD = $setting->password;
                            $SHOP_URL = $setting->shop_url;

                        } else {
                            $API_KEY = '6bf56fc7a35e4dc3879b8a6b0ff3be8e';
                            $PASSWORD = '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';
                            $SHOP_URL = 'cityshop-company-store.myshopify.com';
                        }

                        $SHOPIFY_API_primary = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/variants/$variant_id.json";
                        $curl = curl_init();
                        curl_setopt($curl, CURLOPT_URL, $SHOPIFY_API_primary);
                        $headers = array(
                            "Authorization: Basic " . base64_encode("$API_KEY:$PASSWORD"),
                            "Content-Type: application/json",
                            "charset: utf-8"
                        );
                        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($curl, CURLOPT_VERBOSE, 0);
                        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                        $response = curl_exec($curl);

                        curl_close($curl);

                        $res = json_decode($response, true);

                        foreach ($arr as $k => $v) {
                            $curl = curl_init();
                            curl_setopt($curl, CURLOPT_URL, $SHOPIFY_API);
                            $headers = array(
                                "Authorization: Basic " . base64_encode("$API_KEY:$PASSWORD"),
                                "Content-Type: application/json",
                                "X-Shopify-Api-Features: include-presentment-prices",
                                "charset: utf-8"
                            );
                            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($curl, CURLOPT_VERBOSE, 0);
                            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
                            //curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
                            curl_setopt($curl, CURLOPT_POSTFIELDS, '{
		"query": "mutation priceListFixedPricesAdd($priceListId: ID!, $prices: [PriceListPriceInput!]!) { priceListFixedPricesAdd(priceListId: $priceListId, prices: $prices) { prices { compareAtPrice { amount currencyCode } price { amount currencyCode } } userErrors { field code message } } }",
		"variables": {
		"priceListId": "gid://shopify/PriceList/' . $k . '",
		"prices": [
		{

			"price": {
				"amount": ' . $v['price'] . ',
				"currencyCode": "' . $v['currecy'] . '"
		},
        "variantId": "gid://shopify/ProductVariant/' . $variant_id . '"
		}
		]
		}
		}');
                            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

                            $response = curl_exec($curl);

                            curl_close($curl);

                        }
                        //This is for diffalut currency
                        // if($row->product_discount > 0) {
                        // $data['variant']=array(
                        // "id" => $variant_id,
                        // "price"   => $USD_com,
                        // "compare_at_price" => $USD
                        // );
                        // $API_KEY = '6bf56fc7a35e4dc3879b8a6b0ff3be8e';
                        // $PASSWORD = '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';
                        // $SHOP_URL = 'cityshop-company-store.myshopify.com';
                        // $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/variants/$variant_id.json";
                        // $curl = curl_init();
                        // curl_setopt($curl, CURLOPT_URL, $SHOPIFY_API);
                        // $headers = array(
                        // "Authorization: Basic ".base64_encode("$API_KEY:$PASSWORD"),
                        // "Content-Type: application/json",
                        // "charset: utf-8"
                        // );
                        // curl_setopt($curl, CURLOPT_HTTPHEADER,$headers);
                        // curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        // curl_setopt($curl, CURLOPT_VERBOSE, 0);
                        // curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                        // curl_setopt($curl, CURLOPT_POSTFIELDS,json_encode($data));
                        // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                        // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                        // $response = curl_exec ($curl);
                        // curl_close ($curl);
                        // $res=json_decode($response,true);
                        // }

                        ProductInfo::where('id', $row->id)->update(['price_status' => 1]);
                        //DB::table('tests')->insert(['name' => 'okk']);
                    }
                    $product_ids=array_unique($product_ids_array);
                    foreach ($product_ids as $product_id){
                        $product_log=new ProductLog();
                        $product_log->title='Update Price in Shopify';
                        $product_log->date_time=now()->format('F j, Y H:i:s');
                        $product_log->product_id=$product_id;
                        $product_log->save();
                    }

                    $currentTime = now();
                    $update_log = Log::where('id', $log_id)->first();
                    $update_log->date = $currentTime->format('F j, Y');
                    $update_log->status = 'Complete';
                    $update_log->end_time = $currentTime;
                    $update_log->product_ids=implode(',',$product_ids);
                    $update_log->save();

                }catch (\Exception $exception){

                    $update_log = Log::where('id', $log_id)->first();
                    $currentTime = now();
                    $update_log->date = $currentTime->format('F j, Y');
                    $update_log->end_time = $currentTime;
                    $update_log->status = 'Failed';
                    $update_log->message=json_encode($exception->getMessage());
                    $update_log->save();
                }

//            });







            }
        //return Command::SUCCESS;
    }
	// public function priceCalculate($id)
	// {
		// $prices=Helpers::calc_price($request->price,$request->grams,$is_saree,$is_furniture,$volumetric_Weight);
	// }
}
