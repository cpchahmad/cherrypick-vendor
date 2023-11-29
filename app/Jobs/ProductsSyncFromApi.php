<?php

namespace App\Jobs;

use App\Helpers\Helpers;
use App\Http\Controllers\superadmin\SuperadminController;
use App\Imports\BluckProductImport;
use App\Models\Category;
use App\Models\Extra;
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

class ProductsSyncFromApi implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 10000;
    protected $vendor_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public function __construct($vendor_id)
    {
        $this->vendor_id = $vendor_id;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        set_time_limit(0);
        $vendor = Store::find($this->vendor_id);
        if ($vendor) {

            $currentTime = now();
            $log = new Log();
            $log->name = 'Fetch Product From Json ('.$vendor->name.')';
            $log->date = $currentTime->format('F j, Y');
            $log->start_time = $currentTime->toTimeString();
            $log->status = 'In-Progress';
            $log->save();

            $vid=$vendor->id;
            $json_url = \Illuminate\Support\Facades\DB::table('cron_json_url')->where('vendor_id', $vendor->id)->first();
           $extra=new Extra();
           $extra->log=json_encode($json_url);
           $extra->save();
            if($json_url){
                $context = stream_context_create(
                    array(
                        "http" => array(
                            "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36"
                        )
                    )
                );


                $headers = @get_headers("https://" . $json_url->url . "/collections/all/products.json");
                if (!$headers || strpos($headers[0], '404')) {

                }
                //$vid=0;
                for ($i = 1; $i <= 100; $i++) {
                    $str = file_get_contents("https://" . $json_url->url . "/collections/all/products.json?page=" . $i . "&limit=250", false, $context);
                    $extra=new Extra();
                    $extra->log=json_encode($str);
                    $extra->save();
                    $arr = json_decode($str, true);


                    if (count($arr['products']) < 250) {
                        $extra=new Extra();
                        $extra->log=json_encode($arr);
                        $extra->save();
                        $superAdminController=new SuperadminController();
                        $superAdminController->saveStoreFetchProductsFromJson($arr['products'], $vid, '');

                    } else {
                        $superAdminController=new SuperadminController();
                        $superAdminController->saveStoreFetchProductsFromJson($arr['products'], $vid, '');

                    }
                    //echo "<pre>"; print_r($arr['products']); die();
                }


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

                $draft_products = Product::where('vendor', $vid)->whereNotNull('shopify_id')->where('is_updated_by_url', 0)->get();
                $update_products = Product::where('vendor', $vid)->whereNotNull('shopify_id')->where('is_updated_by_url', 1)->get();


                $data['product'] = array(
                    "status" => 'draft',
                );

                foreach ($draft_products as $draft_product) {


                    $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/products/$draft_product->shopify_id.json";
                    $curl = curl_init();
                    curl_setopt($curl, CURLOPT_URL, $SHOPIFY_API);
                    $headers = array(
                        "Authorization: Basic " . base64_encode("$API_KEY:$PASSWORD"),
                        "Content-Type: application/json",
                        "charset: utf-8"
                    );
                    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_VERBOSE, 0);
                    //curl_setopt($curl, CURLOPT_HEADER, 1);
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                    //curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

                    $response = curl_exec($curl);
                    curl_close($curl);
                }

                foreach ($update_products as $update_product) {
                    $upload_product = 0;
                    $product_variants = ProductInfo::where('product_id', $update_product->id)->get();
                    $variants = [];
                    foreach ($product_variants as $product_variant) {
                        if ($product_variant->stock) {
                            $upload_product = 1;
                        }
                        $variants[] = array(
                            "option1" => $product_variant->varient_value,
                            "option2" => $product_variant->varient1_value,
                            "sku" => $product_variant->sku,
                            "price" => $product_variant->price_usd,
                            "grams" => $product_variant->pricing_weight,
                            "taxable" => false,
                            "inventory_management" => ($product_variant->stock ? null : "shopify"),
                        );
                    }

                    $products_array = array(
                        "product" => array(
                            "status" => 'active',
                            "variants" => $variants,
                        )
                    );
                    if ($upload_product) {

                        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/products/$update_product->shopify_id.json";
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
                        //curl_setopt($curl, CURLOPT_HEADER, 1);
                        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
//                    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
                        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($products_array));
                        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

                        $response = curl_exec($curl);
                        curl_close($curl);

                    } else {

                        $products_array = array(
                            "product" => array(
                                "status" => 'draft',
                                "variants" => $variants,

                            )
                        );
                        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/products/$update_product->shopify_id.json";
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
                        //curl_setopt($curl, CURLOPT_HEADER, 1);
                        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
//                    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
                        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($products_array));
                        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

                        $response = curl_exec($curl);
                        curl_close($curl);
                    }


                }


                Product::where('vendor', $vid)->update(['is_updated_by_url' => 0]);

                $currentTime = now();
                $log->date = $currentTime->format('F j, Y');
                $log->end_time = $currentTime->toTimeString();
                $log->status = 'Complete';
                $log->save();
            }
        }

    }


}
