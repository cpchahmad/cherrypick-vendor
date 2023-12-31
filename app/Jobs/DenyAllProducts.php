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

class DenyAllProducts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 100000;
    protected $products;
    protected $log_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public function __construct($products,$log_id)
    {
        $this->products = $products;
        $this->log_id = $log_id;

    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {


        try {
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

            if(count($this->products) > 0){

                $currentTime = now();
                $log=Log::where('id',$this->log_id)->first();
                if($log) {
                    $log->total_product = count($this->products);
                    $log->product_ids = implode(',', $this->products);
                    $log->status = 'In-Progress';
                    $log->save();
                }

                foreach ($this->products as $product_id){

                    $product=Product::find($product_id);
                    if($product){
                        if($product->shopify_id){
                            $data['product'] = array(
                                "status" => 'draft',
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
                       Product::where('id',$product->id)->update(['status'=>3,'approve_date' => Carbon::now()]);
                    }
                }


                $log=Log::where('id',$this->log_id)->first();
                if($log) {
                    $currentTime = now();
                    $log->end_time = $currentTime;
                    $log->status = 'Complete';
                    $log->save();
                }
            }
            else{

                $currentTime = now();
                $log=Log::where('id',$this->log_id)->first();
                if($log) {
                    $log->end_time = $currentTime;
                    $log->status = 'Complete';
                    $log->save();
                }
            }

        }catch (\Exception $exception){

            $currentTime = now();
            $log=Log::where('id',$this->log_id)->first();
            if($log) {
                $log->date = $currentTime->format('F j, Y');
                $log->status = 'Failed';
                $log->end_time = $currentTime;
                $log->message = json_encode($exception->getMessage());
                $log->save();
            }
        }

    }


}
