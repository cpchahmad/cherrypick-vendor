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
use App\Models\ProductLog;
use App\Models\ProductType;
use App\Models\Setting;
use App\Models\Store;
use App\Models\ThirdPartyAPICategory;
use App\Models\ThirdPartyAPIProductAttribute;
use App\Models\ThirdPartyAPIProductAttributeOptions;
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

    public $timeout = 1000000;
    protected $vendor_id;
    protected $log_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public function __construct($vendor_id,$log_id)
    {
        $this->vendor_id = $vendor_id;
        $this->log_id = $log_id;
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
                $flag = 1;
                $currentTime = now();
                $log = Log::where('id', $this->log_id)->first();
                $log->status = 'In-Progress';
                $log->save();

                $total_products=0;
                $vid = $vendor->id;
                Product::where('vendor', $vid)->update(['is_available' => 0]);
                $json_url = \Illuminate\Support\Facades\DB::table('cron_json_url')->where('type', 'fetch_from_url')->where('vendor_id', $vendor->id)->first();

                if ($json_url) {
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
                    $total_products = 0;

                    for ($i = 1; $i <= 100; $i++) {
                        $str = file_get_contents("https://" . $json_url->url . "/collections/all/products.json?page=" . $i . "&limit=250", false, $context);

                        $arr = json_decode($str, true);

                        $total_products = $total_products + count($arr['products']);

                        if (count($arr['products']) < 250) {

                            $superAdminController = new SuperadminController();
                            $superAdminController->saveStoreFetchProductsFromJson($arr['products'], $vid, '', $log->id);

                        } else {
                            $superAdminController = new SuperadminController();
                            $superAdminController->saveStoreFetchProductsFromJson($arr['products'], $vid, '', $log->id);

                        }
                        //echo "<pre>"; print_r($arr['products']); die();
                    }
                } else {

                    $api_data = \Illuminate\Support\Facades\DB::table('cron_json_url')->where('type', 'fetch_from_api')->where('vendor_id', $vendor->id)->first();
                    if ($api_data) {
                        $curl = curl_init();

                        curl_setopt_array($curl, array(
                            CURLOPT_URL => $api_data->api_link,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'GET',
                            CURLOPT_HTTPHEADER => array(
                                'Authorization: Bearer ' . $api_data->authorization_token,
                                'Cookie: PHPSESSID=ub0mpqgtmvauj6qjf90s74u6e9'
                            ),
                        ));

                        $response = curl_exec($curl);

                        curl_close($curl);
                        $data = json_decode($response, true);



                        $total_products = count($data['items']);



                        foreach ($data['items'] as $index => $row) {

                            $description = null;
                            $stock = 0;
                            $qty = 0;
                            if (isset($row['extension_attributes']) && isset($row['extension_attributes']) && isset($row['extension_attributes']['stock_item'])) {
                                $stock = $row['extension_attributes']['stock_item']['is_in_stock'];
                                $qty = $row['extension_attributes']['stock_item']['qty'];
                            }
                            $attribute_array = array();
                            if (isset($row['custom_attributes']) && count($row['custom_attributes']) > 0) {
                                foreach ($row['custom_attributes'] as $attribute) {

                                    if ($attribute['attribute_code'] === 'description') {

                                        $description = $attribute['value'] . '<br>';
                                    }

                                    if ($attribute['attribute_code'] === 'fabric') {

                                        array_push($attribute_array, $attribute);
                                    }

                                    if ($attribute['attribute_code'] === 'print_pattern') {

                                        array_push($attribute_array, $attribute);
                                    }

                                    if ($attribute['attribute_code'] === 'border_type') {

                                        array_push($attribute_array, $attribute);
                                    }

                                    if ($attribute['attribute_code'] === 'border_size') {

                                        array_push($attribute_array, $attribute);
                                    }

                                    if ($attribute['attribute_code'] === 'color') {

                                        array_push($attribute_array, $attribute);
                                    }

                                    if ($attribute['attribute_code'] === 'weave') {

                                        array_push($attribute_array, $attribute);
                                    }

                                    if ($attribute['attribute_code'] === 'blouse_included') {

                                        array_push($attribute_array, $attribute);
                                    }

                                    if ($attribute['attribute_code'] === 'blouse_fabric') {

                                        array_push($attribute_array, $attribute);
                                    }

                                    if ($attribute['attribute_code'] === 'blouse_type') {

                                        array_push($attribute_array, $attribute);
                                    }

                                    if ($attribute['attribute_code'] === 'size') {

                                        array_push($attribute_array, $attribute);
                                    }
                                }
                            }

                            $description .= "<b>SPECIFICATIONS</b>";
                            $description .= "<ul>";

                            foreach ($attribute_array as $get_attribute) {

                                $product_attribute_option = ThirdPartyAPIProductAttributeOptions::where('value', $get_attribute['value'])->first();

                                if ($product_attribute_option) {

                                    $product_attribute_title = ThirdPartyAPIProductAttribute::where('attribute_id', $product_attribute_option->product_attribute_id)->first();
                                    if ($product_attribute_title) {
                                        $description .= "<li><b>" . $product_attribute_title->default_frontend_label . ":</b> $product_attribute_option->label</li>";
                                    } else {

                                        $title = ucwords(str_replace('_', ' ', $get_attribute['attribute_code']));
                                        $description .= "<li><b>" . $title . ":</b> $product_attribute_option->label</li>";
                                    }
                                } else {

                                    $title = ucwords(str_replace('_', ' ', $get_attribute['attribute_code']));
                                    $description .= "<li><b>" . $title . ":</b> " . $get_attribute['value'] . "</li>";

                                }
                            }


                            $description .= "</ul>";

                            $title = $row['name'];
                            $id = $row['id'];
                            $store_id = $vid;
                            $description = $description;

                            $tags = '';
                            if (isset($row['extension_attributes']) && count($row['extension_attributes']) > 0) {
                                if (isset($row['extension_attributes']['category_links']) && count($row['extension_attributes']['category_links']) > 0) {

                                    foreach ($row['extension_attributes']['category_links'] as $category_link) {
                                        $get_tag = ThirdPartyAPICategory::where('category_id', $category_link['category_id'])->where('vendor_id', $vid)->first();
                                        if ($get_tag) {
                                            $superAdminController = new SuperadminController();
                                            $tags = $superAdminController->UpdateTags($get_tag, $vid, $tags);
                                        }
                                    }
                                }

                            }


                            $category_id = isset($row['extension_attributes']['category_links'])?$row['extension_attributes']['category_links'][0]['category_id']:null;

                            $superAdminController=new SuperadminController();
                            $product_type = $superAdminController->GetProductType($category_id, $vid);
                            $product_type_id=null;
                            if($product_type){
                                $product_type_id=$product_type->id;
                            }


                            $product_check = Product::where('reference_shopify_id', $row['id'])->where('vendor', $vid)->first();


                            if ($product_check == null)  ////////New Product
                            {


                                $product = new Product;
                                $product->title = $title;
                                $product->reference_shopify_id = $id;
                                $product->body_html = $description;
                                $product->vendor = $store_id;
                                $product->is_updated_by_url = 1;
                                $product->tags = $tags;
                                $product->is_available = 1;
                                $product->is_update_price_inventory = 1;
                                $product->product_type_id = $product_type_id;
                                $product->save();
                                $product_id = $product->id;


                                $product_logs=new ProductLog();
                                $product_logs->title='Product Created';
                                $product_logs->date_time=now()->format('F j, Y H:i:s');
                                $product_logs->product_id=$product_id;
                                $product_logs->log_id = $log->id;
                                $product_logs->save();


                                $store = Store::find($vid);
                                $grams = $row['weight'];
                                if ($grams == 0) {
                                    if ($store && $store->base_weight) {
                                        $grams = $store->base_weight;
                                    }
                                    if ($product_type && $product_type->base_weight) {
                                        $grams = $product_type->base_weight;
                                    }

                                }

                                $pricing_weight = $grams;
                                if ($product_type && $product_type->base_weight) {
                                    $pricing_weight = max($grams, $product_type->base_weight);
                                }


                                $prices = Helpers::calc_price_fetched_products_by_vendor($vid, $row['price'], $pricing_weight);
                                $product_info = new ProductInfo;
                                $product_info->product_id = $product_id;
                                $product_info->sku = $row['sku'];
                                $product_info->price = $prices['inr'];
                                $product_info->price_usd = $prices['usd'];
                                $product_info->price_nld = $prices['nld'];
                                $product_info->price_gbp = $prices['gbp'];
                                $product_info->price_cad = $prices['cad'];
                                $product_info->price_aud = $prices['aud'];
                                $product_info->price_irl = $prices['nld'];
                                $product_info->price_ger = $prices['nld'];
                                $product_info->base_price = $prices['base_price'];
                                $product_info->grams = $grams;
                                $product_info->pricing_weight = $pricing_weight;
                                $product_info->stock = $stock;
                                $product_info->qty = $qty;
                                $product_info->vendor_id = $store_id;
                                $product_info->dimensions = '0-0-0';
                                $product_info->save();

                            } else {



                                $product_check->title = $title;
                                $product_check->body_html = $description;
                                $product_check->is_updated_by_url = 1;
                                $product_check->product_type_id = $product_type_id;
                                $product_check->is_available = 1;
                                $product_check->tags = $tags;
                                $product_check->save();

                                $product_logs=new ProductLog();
                                $product_logs->title='Product Update';
                                $product_logs->date_time=now()->format('F j, Y H:i:s');
                                $product_logs->product_id=$product_check->id;
                                $product_logs->log_id = $log->id;
                                $product_logs->save();



                                $check_info_v = ProductInfo::where('product_id', $product_check->id)->get();
                                foreach ($check_info_v as $v_get) {

                                    if ($v_get->inventory_id == null) {
                                        if ($v_get->manual_weight == 0) {
                                            $v_get->delete();
                                        }
                                    }
                                }

                                $store = Store::find($vid);
                                $grams = $row['weight'];
                                if ($grams == 0) {
                                    if ($store && $store->base_weight) {
                                        $grams = $store->base_weight;
                                    }
                                    if ($product_type && $product_type->base_weight) {
                                        $grams = $product_type->base_weight;
                                    }
                                }

                                $pricing_weight = $grams;
                                if ($product_type && $product_type->base_weight) {
                                    $pricing_weight = max($grams, $product_type->base_weight);
                                }

                                $is_updated_price_inventory=0;
                                $product_info = ProductInfo::where('product_id', $product_check->id)->where('sku', $row['sku'])->first();
                                if ($product_info == null) {
                                    $is_updated_price_inventory=1;
                                    $product_info = new ProductInfo;
                                }else{
                                    if($stock!=$product_info->stock || $row['price']!=$product_info->base_price){
                                        $is_updated_price_inventory=1;
                                    }
                                }
                                $prices = Helpers::calc_price_fetched_products_by_vendor($vid, $row['price'], $pricing_weight);
                                $product_info->product_id = $product_check->id;
                                $product_info->sku = $row['sku'];
                                $product_info->price = $prices['inr'];
                                $product_info->price_usd = $prices['usd'];
                                $product_info->price_nld = $prices['nld'];
                                $product_info->price_gbp = $prices['gbp'];
                                $product_info->price_cad = $prices['cad'];
                                $product_info->price_aud = $prices['aud'];
                                $product_info->price_irl = $prices['nld'];
                                $product_info->price_ger = $prices['nld'];
                                $product_info->base_price = $prices['base_price'];
                                $product_info->grams = $grams;
                                $product_info->pricing_weight = $pricing_weight;
                                $product_info->stock = $stock;
                                $product_info->qty = $qty;
                                $product_info->vendor_id = $store_id;
                                $product_info->dimensions = '0-0-0';
                                $product_info->save();

                                $product_check->is_update_price_inventory=$is_updated_price_inventory;
                                $product_check->save();

                            }
                            if (count($row['media_gallery_entries']) > 0) {
                                foreach ($row['media_gallery_entries'] as $img_val) {
                                    if ($img_val['id'] && $product_check) {
                                    $imgCheck = ProductImages::where('image_id', $img_val['id'])->where('product_id', $product_check->id)->exists();
                                    if (!$imgCheck) {
                                        $url = 'https://kalamandir.com/media/catalog/product' . $img_val['file'];
                                        $img_name = $url;
                                        $product_img = new ProductImages;
                                        $product_img->image = $img_name;
                                        $product_img->image_id = $img_val['id'];
                                        $product_img->product_id = $product_check->id;
                                        $product_img->save();
                                    }
                                    }
                                }
                            }
                        }
                    } else {
                        $flag = 0;
                    }
                }

                if ($flag == 1) {
                    $delete_products = Product::where('vendor', $vid)->whereNull('shopify_id')->where('is_available',0)->pluck('id')->toArray();
                    foreach ($delete_products as $delete_product_id) {
                        ProductInfo::where('product_id', $delete_product_id)->delete();
                        Product::where('id', $delete_product_id)->delete();
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

                    $draft_products = Product::where('vendor', $vid)->whereNotNull('shopify_id')->where('is_updated_by_url', 0)->pluck('shopify_id')->toArray();
                    $update_products = Product::where('vendor', $vid)
                        ->whereNotNull('shopify_id')
                        ->where('is_update_price_inventory', 1)
                        ->select('id', 'shopify_id')
                        ->get();

                    $data['product'] = array(
                        "status" => 'draft',
                    );

                    foreach ($draft_products as $draft_product_id) {


                        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/products/$draft_product_id.json";
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
                            if($product_variant->qty) {
                                $variants[] = array(
                                    "option1" => $product_variant->varient_value,
                                    "option2" => $product_variant->varient1_value,
                                    "option3" => $product_variant->varient2_value,
                                    "sku" => $product_variant->sku,
                                    "price" => $product_variant->price_usd,
                                    "grams" => $product_variant->pricing_weight,
                                    "taxable" => false,
                                    "inventory_management" => "shopify",
                                    "inventory_quantity" => $product_variant->qty,
                                );
                            }
                            else{
                                $variants[] = array(
                                    "option1" => $product_variant->varient_value,
                                    "option2" => $product_variant->varient1_value,
                                    "option3" => $product_variant->varient2_value,
                                    "sku" => $product_variant->sku,
                                    "price" => $product_variant->price_usd,
                                    "grams" => $product_variant->pricing_weight,
                                    "taxable" => false,
                                    "inventory_management" => ($product_variant->stock ? null : "shopify"),
                                );
                            }
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


                    Product::where('vendor', $vid)->update(['is_updated_by_url' => 0,'is_update_price_inventory'=>0]);


                    $product_log_ids = ProductLog::where('log_id', $log->id)->pluck('product_id')->toArray();
                    $product_log_ids = array_unique($product_log_ids);
                    $product_log_ids = implode(',', $product_log_ids);

                    $currentTime = now();
                    $log->date = $currentTime->format('F j, Y');
                    $log->end_time = $currentTime;
                    $log->total_product = $total_products;
                    $log->product_ids = $product_log_ids;
                    $log->status = 'Complete';
                    $log->save();
                }
            }


    }


}
