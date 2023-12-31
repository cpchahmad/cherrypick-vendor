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
use App\Models\ProductInventoryLocation;
use App\Models\ProductLog;
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

class ProcessProductLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1000000;
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

        $log=$this->log;

        if($log){
            if($log->is_retry == 1){
                $log->is_retry=0;
                $log->retry=0;
                $log->start_time = now();
                $log->save();
            }
            $product_ids=explode(',',$log->product_ids);

//        Product::whereIn('id',$product_ids)->update(['in_queue' => '1']);
            $product_count=count($product_ids);
            if($product_count > 0) {

                $log_id=$log->id;

                try {
                    $stop_processing=0;


//                $product_data = Product::where('status', 1)->whereNull('shopify_id')->where('shopify_status', 'Pending')->whereIn('id',$product_ids)->chunk(10, function ($products) use ($log_id,$stop_processing) {
//                });

                    Retry:

                    $products=Product::where('status', 1)->whereNull('shopify_id')->where('shopify_status', 'Pending')->whereIn('id',$product_ids)->get();

                    foreach ($products as $p_index=> $product) {
                        try {
                            $check_log = Log::where('id', $log_id)->first();
                            if ($check_log->is_enabled == 1) {

                                if ($check_log->retry < 10) {
                                    $product->shopify_status = 'In-Progress';
                                    $product->save();

                                    $check_log->status = 'In-Progress';
                                    $check_log->save();



                                    $metafield_data =
                                        [
                                            [
                                                "key" => 'key_ingredients',
                                                "value" => $product->additional_key_ingredients,
                                                "type" => "multi_line_text_field",
                                                "namespace" => "additional_data",
                                            ],

                                            [

                                                "key" => 'how_to_use',
                                                "value" => $product->additional_how_to_use,
                                                "type" => "multi_line_text_field",
                                                "namespace" => "additional_data",
                                            ],

                                            [

                                                "key" => 'who_can_use',
                                                "value" => $product->additional_who_can_use,
                                                "type" => "multi_line_text_field",
                                                "namespace" => "additional_data",
                                            ],

                                            [

                                                "key" => 'why_mama_earth',
                                                "value" => $product->additional_why_mama_earth,
                                                "type" => "multi_line_text_field",
                                                "namespace" => "additional_data",
                                            ],

                                            [

                                                "key" => 'different_shades',
                                                "value" => $product->additional_different_shades,
                                                "type" => "multi_line_text_field",
                                                "namespace" => "additional_data",
                                            ],

                                            [
                                                "key" => 'faqs',
                                                "value" => $product->additional_faqs,
                                                "type" => "multi_line_text_field",
                                                "namespace" => "additional_data",
                                            ],


                                        ];

                                    $category = Category::find($product->category);
                                    $vendor = Store::find($product->vendor);
                                    $variants = [];
                                    $opt = [];
                                    $product_info = ProductInfo::where('product_id', $product->id)->get();
                                    $groupedData = [];
                                    $groupedData1 = [];
                                    $groupedData2 = [];
                                    $options_array = [];
                                    $upload_product = 0;
                                    foreach ($product_info as $v) {
                                        if ($v->stock) {
                                            $upload_product = 1;
                                        }

                                        if($v->qty) {
                                            $variants[] = array(
                                                "option1" => $v->varient_value,
                                                "option2" => $v->varient1_value,
                                                "option3" => $v->varient2_value,
                                                "sku" => $v->sku,
                                                "price" => $v->price_usd,
                                                "grams" => $v->pricing_weight,
                                                "taxable" => false,
                                                "inventory_management" => "shopify",
                                                "inventory_quantity" => $v->qty
                                            );
                                        }else{
                                            $variants[] = array(
                                                "option1" => $v->varient_value,
                                                "option2" => $v->varient1_value,
                                                "option3" => $v->varient2_value,
                                                "sku" => $v->sku,
                                                "price" => $v->price_usd,
                                                "grams" => $v->pricing_weight,
                                                "taxable" => false,
                                                "inventory_management" => ($v->stock ? null : "shopify"),
//                            "inventory_quantity" => $v->stock
                                            );
                                        }

                                        $varientName = $v->varient_name;
                                        $varientValue = $v->varient_value;


                                        $varient1Name = $v->varient1_name;
                                        $varient1Value = $v->varient1_value;


                                        $varient2Name = $v->varient2_name;
                                        $varient2Value = $v->varient2_value;

                                        if ($varientName != '' || $varientName != null) {
                                            // Check if the varient_name already exists in the grouped data array
                                            if (array_key_exists($varientName, $groupedData)) {
                                                // If it exists, add the varient_value to the existing array
                                                $groupedData[$varientName]['value'][] = $varientValue;
                                            } else {
                                                // If it doesn't exist, create a new entry with the varient_name and an array containing the varient_value
                                                $groupedData[$varientName] = [
                                                    'name' => $varientName,
                                                    'value' => [$varientValue]
                                                ];
                                            }
                                        }


                                        if ($varient1Name != '' || $varient1Name != null) {
                                            // Check if the varient_name already exists in the grouped data array
                                            if (array_key_exists($varient1Name, $groupedData1)) {
                                                // If it exists, add the varient_value to the existing array
                                                $groupedData1[$varient1Name]['value'][] = $varient1Value;
                                            } else {
                                                // If it doesn't exist, create a new entry with the varient_name and an array containing the varient_value
                                                $groupedData1[$varient1Name] = [
                                                    'name' => $varient1Name,
                                                    'value' => [$varient1Value]
                                                ];
                                            }
                                        }


                                        if ($varient2Name != '' || $varient2Name != null) {
                                            // Check if the varient_name already exists in the grouped data array
                                            if (array_key_exists($varient2Name, $groupedData2)) {
                                                // If it exists, add the varient_value to the existing array
                                                $groupedData2[$varient2Name]['value'][] = $varient2Value;
                                            } else {
                                                // If it doesn't exist, create a new entry with the varient_name and an array containing the varient_value
                                                $groupedData2[$varient2Name] = [
                                                    'name' => $varient2Name,
                                                    'value' => [$varient2Value]
                                                ];
                                            }
                                        }

                                    }


                                    $result_options = array_values($groupedData);
                                    $result1_options = array_values($groupedData1);
                                    $result2_options = array_values($groupedData2);

                                    foreach ($result_options as $index => $result_option) {

                                        array_push($options_array, [
                                            'name' => $result_option['name'],
                                            'position' => $index + 1,
                                            'values' => $result_option['value']
                                        ]);
                                    }
                                    foreach ($result1_options as $index => $result1_option) {
                                        array_push($options_array, [
                                            'name' => $result1_option['name'],
                                            'position' => $index + 1,
                                            'values' => $result1_option['value']
                                        ]);
                                    }

                                    foreach ($result2_options as $index => $result2_option) {
                                        array_push($options_array, [
                                            'name' => $result2_option['name'],
                                            'position' => $index + 1,
                                            'values' => $result2_option['value']
                                        ]);
                                    }

                                    $tags = $product->tags;
                                    if ($product->orignal_vendor) {
                                        $result = strcmp($vendor->name, $product->orignal_vendor);
                                        if ($result != 0) {
                                            $tags = $product->tags . ',' . $product->orignal_vendor;
                                        }

                                    }
                                    $use_store_hsncode = 0;
                                    if ($product->product_type_id) {
                                        $product_type_check = ProductType::find($product->product_type_id);
                                        if ($product_type_check) {
                                            if ($product_type_check->hsn_code) {
                                                $use_store_hsncode = 1;
                                                $tags = $tags . ',HSN:' . $product_type_check->hsn_code;
                                            }
                                            $tags = $tags . ',' . $product_type_check->product_type;
                                        }
                                    }

                                    if ($vendor && $vendor->hsn_code) {
                                        if ($use_store_hsncode == 0) {
                                            $tags = $tags . ',HSN:' . $vendor->hsn_code;
                                        }
                                    }
                                    if($product->options){
                                        $options_array=json_decode($product->options);
                                    }


                                    $products_array = array(
                                        "product" => array(
                                            "title" => $product->title,
                                            "body_html" => $product->body_html,
                                            "vendor" => $vendor->name,
                                            "product_type" => $category->category ?? '',
                                            "published" => true,
                                            "tags" => explode(",", $tags),
                                            "variants" => $variants,
                                            "options" => $options_array,
                                            "metafields" => $metafield_data,
                                            "status" => $product->product_status
                                        )
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

                                    if ($upload_product) {

                                        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2020-04/products.json";
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
                                        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
                                        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($products_array));
                                        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                                        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                                        $response = curl_exec($curl);


                                        curl_close($curl);
                                        $result = json_decode($response, true);


//                                    if($p_index > 1){
//                                        $result=null;
//                                    }

                                        if (isset($result['product'])) {


                                            $shopify_product_id = $result['product']['id'];
                                            $shopify_handle = $result['product']['handle'];
                                            $variant_ids_array = array();
                                            Product::where('id', $product->id)->update(['shopify_id' => $shopify_product_id, 'handle' => $shopify_handle, 'status' => '1', 'approve_date' => Carbon::now()]);
                                            foreach ($result['product']['variants'] as $prd) {
                                                array_push($variant_ids_array, $prd['id']);
                                                ProductInfo::where('sku', $prd['sku'])->update(['inventory_item_id' => $prd['inventory_item_id'], 'inventory_id' => $prd['id']]);
                                                $location_id = Helpers::DiffalultLocation();
                                                ProductInventoryLocation::updateOrCreate(
                                                    ['items_id' => $prd['inventory_item_id'], 'location_id' => $location_id],
                                                    ['items_id' => $prd['inventory_item_id'], 'stock' => $prd['inventory_quantity'], 'location_id' => $location_id]
                                                );
                                            }


                                            $count_variants=count($variant_ids_array);

                                            $values = array();
                                            foreach ($product_info as $index => $v) {

                                                $value = [
                                                    "hex_code" => $v->hex_code,
                                                    "swatch_image" => $v->swatch_image,
                                                    "volume" => $v->volume,
                                                    'dimensions' => $v->dimensions_text,
                                                    'shelf_life' => $v->shelf_life,
                                                    'temp_require' => $v->temp_require,
                                                    'height' => $v->height,
                                                    'width' => $v->width,
                                                    'length' => $v->length,
                                                    'sku' => $v->sku
                                                ];
                                                array_push($values, $value);
                                            }


                                            $metafield_variant_data = [
                                                "metafield" =>
                                                    [
                                                        "key" => 'detail',
                                                        "value" => json_encode($values),
                                                        "type" => "json_string",
                                                        "namespace" => "variants",

                                                    ]
                                            ];


                                            $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/products/$shopify_product_id/metafields.json";

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
                                            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
                                            //curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
                                            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($metafield_variant_data));
                                            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                                            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

                                            $response1 = curl_exec($curl);

                                            curl_close($curl);
                                            $this->shopifyUploadeImage($product->id, $shopify_product_id, $variant_ids_array);
                                            $update_log = Log::where('id', $log_id)->first();
                                            $update_log->product_pushed = $update_log->product_pushed + 1;
                                            $update_log->product_left = $update_log->product_left - 1;
                                            $update_log->variant_pushed = $update_log->variant_pushed + $count_variants;
                                            $update_log->save();
                                            $product->shopify_status = 'Complete';
                                            $product->save();

                                            $product_logs=new ProductLog();
                                            $product_logs->title='Product Pushed to Shopify';
                                            $product_logs->date_time=now()->format('F j, Y H:i:s');
                                            $product_logs->product_id=$product->id;
                                            $product_logs->save();


//                                        $variant_count_log=ProductVariantLog::where('log_id',$log_id)->where('date',now()->toDateString())->first();
//                                        if($variant_count_log==null){
//                                            $variant_count_log=new ProductVariantLog();
//                                            $variant_count_log->log_id=$log_id;
//                                            $variant_count_log->date=now()->toDateString();
//                                            $variant_count_log->save();
//                                        }
//                                        $variant_count_log->variant_count=$variant_count_log->variant_count+$count_variants;
//                                        $variant_count_log->save();

                                        }
                                        else {
                                            $expectedErrorMessage = "Daily variant creation limit reached. Please try again later. See https://help.shopify.com/api/getting-started/api-call-limit for more information about rate limits and how to avoid them.";


                                            if (isset($result['errors']['product'][0]) && $result['errors']['product'][0] == $expectedErrorMessage)
                                            {
                                                $product->shopify_status = 'Pending';
                                                $product->save();

                                                $extra = new Extra();
                                                $extra->log = json_encode($result);
                                                $extra->record = 'Push Product to Shopify'.$product->id;
                                                $extra->save();

                                                $update_log = Log::where('id', $log_id)->first();
                                                $update_log->retry = $update_log->retry + 1;
                                                $update_log->save();
                                                if($update_log->retry==10) {

                                                    $startTime = Carbon::parse($update_log->start_time);
                                                    $currentTime = Carbon::now();


                                                    $get_hold_log=Log::where('name','Approve Product Push')->where('status','On-Hold')->first();
                                                    if($get_hold_log){
                                                        $running_at = Carbon::parse($get_hold_log->running_at);

                                                        // Calculate the difference between current time and start time
                                                        $timeDifference = $running_at->diff($currentTime);
                                                        $days = $timeDifference->days;


                                                        if($days > 0){
                                                            $running_at = $startTime->addHours(24)->toDateTimeString();
                                                        }else{
                                                            $running_at=$running_at->toDateTimeString();
                                                        }

                                                    }else{

                                                        // Calculate the difference between current time and start time
                                                        $timeDifference = $currentTime->diff($startTime);
                                                        $days = $timeDifference->days;
                                                        if($days > 0) {
                                                            $running_at = now()->addHours(24)->toDateTimeString();
                                                        }else{
                                                            $running_at=$startTime->addHours(24)->toDateTimeString();
                                                        }
                                                    }



                                                    $update_log->running_at =$running_at;
                                                    $update_log->status = 'On-Hold';
                                                    $update_log->is_retry=1;
                                                    $update_log->save();
                                                    break;
                                                }

                                            }

                                            else{
                                                $product->shopify_status = 'Failed';
                                                $product->shopify_error = json_encode($result['errors']);
                                                $product->save();
                                            }





                                        }
//                          $this->linkProductToCollection($shopify_product_id, $vendor->collections_ids);


                                    }

                                }

                            }
                            else{
                                $update_log = Log::where('id', $log_id)->first();
                                $update_log->status = 'Paused';
                                $update_log->save();
                                $stop_processing=1;
                                break;
                            }
                        }
                        catch (\Exception $exception) {


                            $product->shopify_status = 'Failed';
                            $product->save();
                        }
                    }


                    $update_log = Log::where('id', $log_id)->first();
                    $currentTime = now();
                    if($update_log->is_enabled && $update_log->is_retry==0) {
                        $l_product_count=Product::where('status', 1)->whereNull('shopify_id')->where('shopify_status', 'Pending')->whereIn('id',$product_ids)->count();

                        if($l_product_count > 0 && $update_log->retry < 10){
                            goto Retry;
                        }

                        $update_log->date = $currentTime->format('F j, Y');
                        $update_log->status = 'Complete';
                        $update_log->is_complete = 1;
                        $update_log->is_running = 0;
                        $update_log->end_time = $currentTime;
                        $update_log->save();

                        $log_update=Log::where('name','Approve Product Push')->where('is_running',0)->where('is_complete',0)->first();
                        if($log_update){

                            $log_update->is_running=1;
                            $log_update->status='In-Progress';
                            $log_update->start_time=now();
                            $log_update->running_at=now();
                            $log_update->save();
                        }
                    }


                }catch (\Exception $exception){

                    $currentTime = now();
                    $update_log = Log::where('id', $log_id)->first();

                    $update_log->date = $currentTime->format('F j, Y');
                    $update_log->status = 'Failed';
                    $update_log->end_time = $currentTime;
                    $update_log->message = json_encode($exception->getMessage());
                    $update_log->save();
                }
            }else{
                $update_log = Log::where('id', $log->id)->first();
                $update_log->date = now()->format('F j, Y');
                $update_log->status = 'Complete';
                $update_log->is_complete = 1;
                $update_log->is_running = 0;
                $update_log->end_time = now();
                $update_log->save();
            }
        }

    }


    public function shopifyUploadeImage($id,$shopify_id,$variant_ids_array)
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

        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2020-04/products/$shopify_id/images.json";
        $product_images = ProductImages::where('product_id',$id)->get();
        foreach($product_images as $index=> $img_val)
        {
            if($img_val->variant_ids && isset($variant_ids_array[$index])) {

                $data['image'] = array(
                    'src' => $img_val->image,
                    'alt' => $img_val->alt_text,
                    'variant_ids' => [$variant_ids_array[$index]]

                );
            }else{
                $data['image'] = array(
                    'src' => $img_val->image,
                    'alt' => $img_val->alt_text,


                );
            }

            //$image='{"image":{"src":"https://sslimages.shoppersstop.com/sys-master/images/h98/hcf/28719001468958/GHM9150K_BLUE.jpg_230Wx334H"}}';
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
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            $response = curl_exec ($curl);
            $img_result=json_decode($response, true);
            if(isset($img_result['image']['id']))
                ProductImages::where('id', $img_val->id)->update(['shopify_image_id' => $img_result['image']['id']]);

            if($img_val->image2) {


                $data['image'] = array(
                    'src' => $img_val->image2,
                    'alt' => $img_val->alt_text,

                );


                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $SHOPIFY_API);
                $headers = array(
                    "Authorization: Basic " . base64_encode("$API_KEY:$PASSWORD"),
                    "Content-Type: application/json",
                    "charset: utf-8"
                );
                curl_setopt($curl, CURLOPT_HTTPHEADER,$headers);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_VERBOSE, 0);
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                $response = curl_exec($curl);

                $img_result = json_decode($response, true);

            }

            if($img_val->image3) {
                $data['image'] = array(
                    'src' => $img_val->image3,
                    'alt' => $img_val->alt_text,


                );
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $SHOPIFY_API);
                $headers = array(
                    "Authorization: Basic " . base64_encode("$API_KEY:$PASSWORD"),
                    "Content-Type: application/json",
                    "charset: utf-8"
                );
                curl_setopt($curl, CURLOPT_HTTPHEADER,$headers);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_VERBOSE, 0);
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                $response = curl_exec($curl);
                $img_result = json_decode($response, true);

            }

            if($img_val->image4) {
                $data['image'] = array(
                    'src' => $img_val->image4,
                    'alt' => $img_val->alt_text,


                );
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $SHOPIFY_API);
                $headers = array(
                    "Authorization: Basic " . base64_encode("$API_KEY:$PASSWORD"),
                    "Content-Type: application/json",
                    "charset: utf-8"
                );
                curl_setopt($curl, CURLOPT_HTTPHEADER,$headers);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_VERBOSE, 0);
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                $response = curl_exec($curl);
                $img_result = json_decode($response, true);
            }

            if($img_val->image5) {
                $data['image'] = array(
                    'src' => $img_val->image5,
                    'alt' => $img_val->alt_text,


                );
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $SHOPIFY_API);
                $headers = array(
                    "Authorization: Basic " . base64_encode("$API_KEY:$PASSWORD"),
                    "Content-Type: application/json",
                    "charset: utf-8"
                );
                curl_setopt($curl, CURLOPT_HTTPHEADER,$headers);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_VERBOSE, 0);
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                $response = curl_exec($curl);
                $img_result = json_decode($response, true);
            }

        }
    }


}
