<?php

namespace App\Jobs;

use App\Helpers\Helpers;
use App\Http\Controllers\superadmin\SuperadminController;
use App\Imports\BluckProductImport;
use App\Models\Category;
use App\Models\Log;
use App\Models\Product;
use App\Models\ProductImages;
use App\Models\ProductInfo;
use App\Models\ProductType;
use App\Models\Setting;
use App\Models\Store;
use App\Models\VariantChange;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\SimpleExcel\SimpleExcelReader;

class UpdateShopifyPricesByProductType implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 10000;
    protected $id;
    protected $store;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public function __construct($id,$store)
    {
        $this->id = $id;
        $this->store = $store;


    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {


        try {

            $products=Product::where('product_type_id',$this->id)->where('shopify_status','Complete')->get();
            $product_ids=Product::where('product_type_id',$this->id)->where('shopify_status','Complete')->pluck('id')->toArray();
            if(count($products) > 0 ) {
                $product_count=count($products);
                    $currentTime = now();
                    $log=new Log();
                    $log->name='Update Price in Shopify ('.$this->store.')';
                    $log->date = $currentTime->format('F j, Y');
                    $log->total_product = $product_count;
                    $log->start_time = $currentTime->toTimeString();
                $log->product_ids=implode(',',$product_ids);
                    $log->status='In-Progress';
                    $log->save();

                foreach ($products as $product) {

                    $data=ProductInfo::where('product_id', $product->id)->whereNotNull('inventory_id')->get();
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



                    foreach ($data as $row) {

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
                            'compare_at_price' => $INR,
                            'price' => $INR_com,
                            'currecy' => 'INR'
                        );
                        $arr['23366041775'] = array(
                            'compare_at_price' => $CAD,
                            'price' => $CAD_com,
                            'currecy' => 'CAD'
                        );
                        $arr['23550656687'] = array(
                            'compare_at_price' => $GBP,
                            'price' => $GBP_com,
                            'currecy' => 'GBP'
                        );
                        $arr['23593582767'] = array(
                            'compare_at_price' => $AUD,
                            'price' => $AUD_com,
                            'currecy' => 'AUD'
                        );
                        $arr['23550689455'] = array(
                            'compare_at_price' => $EUR,
                            'price' => $EUR_com,
                            'currecy' => 'EUR'
                        );
                        $variant_id = $row->inventory_id;

                        $data['variant'] = array(
                            "id" => $variant_id,
                            "price" => $USD_com,
                            "compare_at_price" => $USD,
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
			"compareAtPrice": {
					"amount": ' . $v['compare_at_price'] . ',
					"currencyCode": "' . $v['currecy'] . '"
			},
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

                    }
                }

                $currentTime = now();
                $log->date = $currentTime->format('F j, Y');
                $log->end_time = $currentTime->toTimeString();
                $log->status='Complete';
                $log->save();

            }

        }catch (\Exception $exception){
            $currentTime = now();
            $log->date = $currentTime->format('F j, Y');
            $log->status = 'Failed';
            $log->end_time = $currentTime->toTimeString();
            $log->message=json_encode($exception->getMessage());
            $log->save();

        }

    }


}
