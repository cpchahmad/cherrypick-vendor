<?php

namespace App\Console\Commands;

use App\Helpers\Helpers;
use App\Models\Category;
use App\Models\Log;
use App\Models\Product;
use App\Models\ProductInfo;
use App\Models\ProductInventoryLocation;
use App\Models\ProductType;
use App\Models\Setting;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PauseProductsCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pauseproduct:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


            try {


                $stop_processing=0;


                $product_data = Log::where('status', 1)->whereNull('shopify_id')->where('shopify_status', 'Pending')->whereIn('id',$product_ids)->chunk(20, function ($products) use ($log_id,$stop_processing) {

                    $p_ids = $products->pluck('id');


                    foreach ($products as $product) {

                        try {

                            $check_log = Log::where('id', $log_id)->first();
                            if($check_log->is_enabled==1) {

                                $product->shopify_status = 'In-Progress';
                                $product->save();

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
                                $options_array = [];
                                $upload_product = 0;
                                foreach ($product_info as $v) {
                                    if ($v->stock) {
                                        $upload_product = 1;
                                    }
                                    $variants[] = array(
                                        "option1" => $v->varient_value,
                                        "option2" => $v->varient1_value,
                                        "sku" => $v->sku,
                                        "price" => $v->price_usd,
                                        "grams" => $v->grams,
                                        "taxable" => false,
                                        "inventory_management" => ($v->stock ? null : "shopify"),
//                            "inventory_quantity" => $v->stock
                                    );

                                    $varientName = $v->varient_name;
                                    $varientValue = $v->varient_value;


                                    $varient1Name = $v->varient1_name;
                                    $varient1Value = $v->varient1_value;

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
                                            $grouped1Data[$varient1Name]['value'][] = $varient1Value;
                                        } else {
                                            // If it doesn't exist, create a new entry with the varient_name and an array containing the varient_value
                                            $groupedData[$varient1Name] = [
                                                'name' => $varient1Name,
                                                'value' => [$varient1Value]
                                            ];
                                        }
                                    }

                                }


                                $result_options = array_values($groupedData);
                                $result1_options = array_values($groupedData1);

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

                                $tags = $product->tags;
                                if ($product->orignal_vendor) {
                                    $result = strcmp($vendor->name, $product->orignal_vendor);
                                    if ($result != 0) {
                                        $tags = $product->tags . ',' . $product->orignal_vendor;
                                    }

                                }
                                if ($product->product_type_id) {
                                    $product_type_check = ProductType::find($product->product_type_id);
                                    if ($product_type_check) {
                                        if ($product_type_check->hsn_code) {
                                            $tags = $tags . ',HSN:' . $product_type_check->hsn_code;
                                        }
                                    }
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
                                        "metafields" => $metafield_data
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
                                        $update_log->save();
                                        $product->shopify_status = 'Complete';
                                        $product->in_queue = 0;
                                        $product->save();
                                    }
//                          $this->linkProductToCollection($shopify_product_id, $vendor->collections_ids);


                                }
                            }else{
                                $stop_processing=1;
                                return false;
                            }
                        } catch (\Exception $exception) {


                            $product->shopify_status = 'Failed';
                            $product->save();
                        }
                    }



                });





                if($stop_processing) {
                    $update_log = Log::where('id', $log_id)->first();
                    $update_log->status = 'On-Hold';
                    $update_log->save();
                }else{
                    $currentTime = now();
                    $update_log = Log::where('id', $log_id)->first();

                    $update_log->date = $currentTime->format('F j, Y');
                    $update_log->status = 'Complete';
                    $update_log->end_time = $currentTime;
                    $update_log->save();
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

    }
}
