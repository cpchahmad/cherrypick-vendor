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

class ActiveDenyProducts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 100000;
    protected $log;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public function __construct($log)
    {
        $this->log = $log;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $log = $this->log;
        if ($log){

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
        $currentTime = now();

            $deny_product_ids = explode(',', $log->deny_product_ids);

            if (count($deny_product_ids) > 0) {

                foreach ($deny_product_ids as $product_id) {

                    $product = Product::find($product_id);
                    if ($product) {
                        if ($product->shopify_id) {
                            $data['product'] = array(
                                "status" => 'active',
                            );

                            $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/products/$product->shopify_id.json";
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
                            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

                            $response = curl_exec($curl);
                            curl_close($curl);
                            $result = json_decode($response, true);

                            if (isset($result['product'])) {
                                $shopify_product_status = $result['product']['status'];
                                Product::where('id', $product_id)->update(['product_status' => $shopify_product_status]);
                            }
                        }
                        Product::where('id', $product->id)->update(['status' => 1, 'approve_date' => Carbon::now()]);
                    }
                }


            } else {


            }


    }

    }


}
