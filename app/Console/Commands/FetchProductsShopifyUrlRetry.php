<?php

namespace App\Console\Commands;

use App\Http\Controllers\superadmin\SuperadminController;
use App\Models\Log;
use App\Models\ProductLog;
use App\Models\ProductType;
use App\Models\Setting;
use App\Models\ThirdPartyAPICategory;
use App\Models\ThirdPartyAPIProductAttribute;
use App\Models\ThirdPartyAPIProductAttributeOptions;
use App\Models\VendorUrl;
use Illuminate\Console\Command;
use App\Models\Store;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductInfo;
use App\Models\ProductImages;
use App\Models\ProductInventoryLocation;
use Auth;
use App\Models\OrdersOtp;
use App\Helpers\Helpers;
use DB;
use Collection;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Mockery\Exception;

class FetchProductsShopifyUrlRetry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:product-shopifyurl-retry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Vendor Products if Failed';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        set_time_limit(0);
        $context = stream_context_create(
            array(
                "http" => array(
                    "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36"
                )
            )
        );

        $vendor_data=VendorUrl::where('status','In-Progress')->first();
        if ($vendor_data && $vendor_data->updated_at) {
            $thirtyMinutesAgo = now()->subMinutes(60); // Get the current time and subtract 30 minutes
            if ($vendor_data->updated_at->lt($thirtyMinutesAgo)) {

                        $vid = $vendor_data->vendor_id;
                        $cron_url = DB::table('cron_json_url')->where('vendor_id', $vid)->first();

                        if ($cron_url) {
                            $is_error = 0;
                            try {

                                $vendor_data->status = 'In-Progress';
                                $vendor_data->start_time = now();
                                $vendor_data->save();

                                if($vendor_data->fetch_from_api==0) {
                                    $vendor_data->total_products=0;
                                    $vendor_data->save();
                                    Product::where('vendor', $vid)->update(['is_available' => 0]);
                                    if ($cron_url->type == 'fetch_from_url') {
                                        try {


                                            $url = $cron_url->url;

                                            $total_products = 0;
                                            try {
                                                for ($i = 1; $i <= 100; $i++) {

                                                    $str = file_get_contents("https://" . $url . "/collections/all/products.json?page=" . $i . "&limit=250", false, $context);
                                                    $arr = json_decode($str, true);

                                                    $data = collect($arr['products']);


                                                    $date = date('Y-m-d');
                                                    //$date='2023-06-24';

                                                    $product = $data->filter(function ($q) use ($date) {
                                                        return Str::startsWith($q['updated_at'], $date);
                                                    });

                                                    if (sizeof($product) > 0) {
                                                        $this->saveStoreFetchProductsFromJson($product, $vid, '', $vendor_data->log_id);

                                                        $total_products = $total_products + count($product);
                                                        $vendor_data->total_products = $total_products;
                                                        $vendor_data->save();
                                                    }else{
                                                        break;
                                                    }

                                                }


                                            } catch (\Exception $exception) {
                                                $is_error = 1;
                                                $currentTime = now();
                                                $vendor_data->end_time = $currentTime;
                                                $vendor_data->failed_reason = json_encode($exception->getMessage());
                                                $vendor_data->status = 'Failed';
                                                $vendor_data->save();

                                            }
                                        } catch (\Exception $exception) {
                                            $is_error = 1;
                                            $currentTime = now();
                                            $vendor_data->end_time = $currentTime;
                                            $vendor_data->failed_reason = json_encode($exception->getMessage());
                                            $vendor_data->status = 'Failed';
                                            $vendor_data->save();
                                        }
                                    }
                                    elseif ($cron_url->type == 'fetch_from_api') {

                                        if ($cron_url->api_link) {

                                            try {
                                                $curl = curl_init();

                                                curl_setopt_array($curl, array(
                                                    CURLOPT_URL => $cron_url->api_link,
                                                    CURLOPT_RETURNTRANSFER => true,
                                                    CURLOPT_ENCODING => '',
                                                    CURLOPT_MAXREDIRS => 10,
                                                    CURLOPT_TIMEOUT => 0,
                                                    CURLOPT_FOLLOWLOCATION => true,
                                                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                                    CURLOPT_CUSTOMREQUEST => 'GET',
                                                    CURLOPT_HTTPHEADER => array(
                                                        'Authorization: Bearer ' . $cron_url->authorization_token,
                                                        'Cookie: PHPSESSID=ub0mpqgtmvauj6qjf90s74u6e9'
                                                    ),
                                                ));

                                                $response = curl_exec($curl);

                                                curl_close($curl);
                                                $data = json_decode($response, true);


                                                $total_products = count($data['items']);
                                                $vendor_data->total_products = $total_products;
                                                $vendor_data->save();
                                                try {
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
                                                        $store_id = $vendor_data->vendor_id;
                                                        $vid = $vendor_data->vendor_id;
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


                                                        $category_id = isset($row['extension_attributes']['category_links']) ? $row['extension_attributes']['category_links'][0]['category_id'] : null;

                                                        $superAdminController = new SuperadminController();
                                                        $product_type = $superAdminController->GetProductType($category_id, $vid);
                                                        $product_type_id = null;
                                                        if ($product_type) {
                                                            $product_type_id = $product_type->id;
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
                                                            $product->product_type_id = $product_type_id;
                                                            $product->is_update_price_inventory = 1;
                                                            $product->save();
                                                            $product_id = $product->id;


                                                            $product_logs = new ProductLog();
                                                            $product_logs->title = 'Product Created';
                                                            $product_logs->date_time = now()->format('F j, Y H:i:s');
                                                            $product_logs->product_id = $product_id;
                                                            $product_logs->log_id = $vendor_data->log_id;
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


                                                            $product_logs = new ProductLog();
                                                            $product_logs->title = 'Product Update';
                                                            $product_logs->date_time = now()->format('F j, Y H:i:s');
                                                            $product_logs->product_id = $product_check->id;
                                                            $product_logs->log_id = $vendor_data->log_id;
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

                                                            $is_updated_price_inventory = 0;
                                                            $product_info = ProductInfo::where('product_id', $product_check->id)->where('sku', $row['sku'])->first();
                                                            if ($product_info == null) {
                                                                $is_updated_price_inventory = 1;
                                                                $product_info = new ProductInfo;
                                                            } else {
                                                                if ($stock != $product_info->stock || $row['price'] != $product_info->base_price) {
                                                                    $is_updated_price_inventory = 1;
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


                                                            $product_check->is_update_price_inventory = $is_updated_price_inventory;
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
                                                } catch (\Exception $exception) {
                                                    $is_error = 1;
                                                    $currentTime = now();
                                                    $vendor_data->end_time = $currentTime;
                                                    $vendor_data->failed_reason = json_encode($exception->getMessage());
                                                    $vendor_data->status = 'Failed';
                                                    $vendor_data->save();
                                                }

                                            } catch (\Exception $exception) {
                                                $is_error = 1;
                                                $currentTime = now();
                                                $vendor_data->end_time = $currentTime;
                                                $vendor_data->failed_reason = json_encode($exception->getMessage());
                                                $vendor_data->status = 'Failed';
                                                $vendor_data->save();
                                            }
                                        }

                                    }
                                }


                                $delete_products = Product::where('vendor', $vid)->whereNull('shopify_id')->where('is_available', 0)->pluck('id')->toArray();
                                foreach ($delete_products as $delete_product_id) {

                                    ProductInfo::where('product_id', $delete_product_id)->delete();
                                    Product::where('id', $delete_product_id)->delete();
                                }

                                $vendor_data->total_draft_products=0;
                                $vendor_data->total_update_products=0;
                                $vendor_data->save();


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
                                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                                    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

                                    $response = curl_exec($curl);
                                    curl_close($curl);
                                    $vendor_data->total_draft_products = $vendor_data->total_draft_products + 1;
                                    $vendor_data->save();
                                }

                                foreach ($update_products as $update_product) {
                                    $upload_product = 0;
                                    $product_variants = ProductInfo::where('product_id', $update_product->id)->get();
                                    $variants = [];
                                    foreach ($product_variants as $product_variant) {
                                        if ($product_variant->stock) {
                                            $upload_product = 1;
                                        }
                                        if ($product_variant->qty) {
                                            $variants[] = array(
                                                "option1" => $product_variant->varient_value,
                                                "option2" => $product_variant->varient1_value,
                                                "option3" => $product_variant->varient2_value,
                                                "sku" => $product_variant->sku,
                                                "price" => $product_variant->price_usd,
                                                "compare_at_price" => '',
                                                "grams" => $product_variant->grams,
                                                "taxable" => false,
                                                "inventory_management" => "shopify",
                                                "inventory_quantity" => $product_variant->qty,
                                            );
                                        } else {
                                            $variants[] = array(
                                                "option1" => $product_variant->varient_value,
                                                "option2" => $product_variant->varient1_value,
                                                "option3" => $product_variant->varient2_value,
                                                "sku" => $product_variant->sku,
                                                "price" => $product_variant->price_usd,
                                                "compare_at_price" => '',
                                                "grams" => $product_variant->grams,
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
                                        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
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

                                    $vendor_data->total_update_products = $vendor_data->total_update_products + 1;
                                    $vendor_data->save();
                                }


                                Product::where('vendor', $vid)->update(['is_updated_by_url' => 0,'is_update_price_inventory'=>0]);

                                if ($is_error == 0) {
                                    $product_log_ids = ProductLog::where('log_id', $vendor_data->log_id)->pluck('product_id')->toArray();
                                    $product_log_ids = array_unique($product_log_ids);
                                    $product_log_ids = implode(',', $product_log_ids);


                                    $currentTime = now();
                                    $vendor_data->end_time = $currentTime;
                                    $vendor_data->status = 'Complete';
                                    $vendor_data->product_ids = $product_log_ids;
                                    $vendor_data->save();

                                }
                                $count_total_url = VendorUrl::where('log_id', $vendor_data->log_id)->count();
                                $count_total_complete_url = VendorUrl::where('log_id', $vendor_data->log_id)->whereIn('status', ['Complete', 'Failed'])->count();

                                if ($count_total_url == $count_total_complete_url) {
                                    Log::where('id', $vendor_data->log_id)->update(['status' => 'Complete','end_time'=>now()]);
                                }
                            } catch (\Exception $exception) {

                                $currentTime = now();
                                $vendor_data->end_time = $currentTime;
                                $vendor_data->failed_reason = json_encode($exception->getMessage());
                                $vendor_data->status = 'Failed';
                                $vendor_data->save();

                            }
                        }
            }
        }


    }


    function saveStoreFetchProductsFromJson($products,$vid,$tag_url=null,$log_id=null)
    {


        //echo "<pre>"; print_r($products); die;
        foreach($products as $index=> $row)
        {
            $html=$row['body_html'];
            $html = preg_replace('/<img[^>]*>/', '', $html);

            // Extract the first two options
            $selectedOptions=null;
            if(count($row['options']) > 0){
//                $selectedOptions = array_slice($row['options'], 0, 2);
                $selectedOptions = $row['options'];
            }


            $weight_available=0;
            foreach ($row['options'] as $option) {
                foreach ($option['values'] as $value) {
                    if (strpos($value, 'Gms') !== false) {
                        $weight_available = 1;
                    }
                    if (strpos($value, 'gms') !== false) {
                        $weight_available = 1;
                    }
                    if (strpos($value, 'kg') !== false) {
                        $weight_available = 1;
                    }
                    if (strpos($value, 'Grams') !== false) {
                        $weight_available = 1;
                    }
                    if (strpos($value, 'Gram') !== false) {
                        $weight_available = 1;
                    }
                    if (strpos($value, 'Kgs') !== false) {
                        $weight_available = 1;
                    }
                    if (strpos($value, 'G') !== false) {
                        $weight_available = 1;
                    }
                    if (strpos($value, 'KG') !== false) {
                        $weight_available = 1;
                    }
                }

            }


            $linkToRemove = 'https://www.violetandpurple.com/index.php/faq';
// Find the position of the link
            $position = strpos($html, $linkToRemove);

// If the link is found, find the parent <p> tag and remove it
            if ($position !== false) {
                $startTagPosition = strrpos(substr($html, 0, $position), '<p');
                $endTagPosition = strpos($html, '</p>', $position) + 4;

                // Remove the parent <p> tag
                $modifiedHtml = substr_replace($html, '', $startTagPosition, $endTagPosition - $startTagPosition);
            } else {
                // If the link is not found, keep the original HTML
                $modifiedHtml = $html;
            }

// Output the modified HTML
            $html=$modifiedHtml;



            $product_check=Product::where('reference_shopify_id',$row['id'])->where('vendor',$vid)->first();

            $product_type=ProductType::where('product_type',$row['product_type'])->where('vendor_id',$vid)->first();
            if($product_type==null){
                $product_type=new ProductType();
            }
            $product_type->product_type=$row['product_type'];
            $product_type->vendor_id=$vid;
            $product_type->save();

            //echo $pid; die;
            if($product_check==null)  ////////New Product
            {
                $cat=Category::where('category',$row['product_type'])->first();
                if($cat)
                    $category_id=$cat->id;
                else
                {
                    $cate_que = new Category;
                    $cate_que->category = $row['product_type'];
                    $cate_que->save();
                    $category_id=$cate_que->id;
                }



                $shopify_id=$row['id'];
                $title=$row['title'];
                $description=$html;
                $vendor=$row['vendor'];
                $tags=implode(",",$row['tags']);
                $handle=$row['handle'];
                $store_id=$vid;

                $product = new Product;
                $product->title = $title;
                $product->reference_shopify_id = $shopify_id;
                $product->body_html = $description;
                $product->vendor = $store_id;
                $product->tags = $tags;
                $product->orignal_vendor = $vendor;
                $product->category = $category_id;
                $product->options = json_encode($selectedOptions);
                $product->product_type_id=$product_type->id;
                $product->is_updated_by_url=1;
                $product->is_available=1;
                $product->is_update_price_inventory=1;
                $product->save();
                $product_id=$product->id;


                $product_logs=new ProductLog();
                $product_logs->title='Product Created';
                $product_logs->date_time=now()->format('F j, Y H:i:s');
                $product_logs->product_id=$product_id;
                $product_logs->log_id = $log_id;
                $product_logs->save();


                $i=0;

                $grams=0;
                $store=Store::find($vid);
                if($store->base_weight){
                    $grams=$store->base_weight;
                }
                if($product_type && $product_type->base_weight){
                    $grams=$product_type->base_weight;
                }
                $grams_selected=0;
                if($row['variants'][0]['grams'] > 0){
                    $grams_selected=1;
                    $grams=$row['variants'][0]['grams'];
                }else {
                    foreach ($row['variants'] as $var) {
                        if ($var['grams'] > 0 && $grams_selected==0) {
                            $grams_selected=1;
                            $grams = $var['grams'];
                        }
                    }
                }


                foreach($row['variants'] as $var)
                {

                    $i++;
                    $variant_grams=($var['grams'] > 0) ? $var['grams'] :$grams;

                    if ($variant_grams == 0 && $weight_available == 1) {
                        if (preg_match('/(\d+)\s*Gms/', $var['option1'], $matches)) {

                            $variant_grams = $matches[1]; // This will give you '250'

                        }
                        elseif (preg_match('/(\d+)\s*gms/', $var['option1'], $matches)) {

                            $variant_grams = $matches[1]; // This will give you '250'

                        }

                        elseif (preg_match('/(\d+)\s*kg/i', $var['option1'], $matches)) {
                            $variant_grams = $matches[1] * 1000; // This will give you '1' if the string contains '1kg'
                            // Outputs: 1
                        } elseif (preg_match('/(\d+)\s*KG/i', $var['option1'], $matches)) {
                            $variant_grams = $matches[1] * 1000; // This will give you '1' if the string contains '1kg'
                            // Outputs: 1
                        } elseif (preg_match('/(\d+)\s*G/', $var['option1'], $matches)) {
                            $variant_grams = $matches[1]; // This will give you '250'
                        } elseif (preg_match('/(\d+)\s*Grams/', $var['option1'], $matches)) {
                            $variant_grams = $matches[1]; // This will give you '250'
                        } elseif (preg_match('/(\d+)\s*Gram/', $var['option1'], $matches)) {
                            $variant_grams = $matches[1]; // This will give you '250'
                        } elseif (preg_match('/(\d+)\s*kgs/i', $var['option1'], $matches)) {
                            $variant_grams = $matches[1] * 1000; // This will give you '1' if the string contains '1kg'
                            // Outputs: 1
                        }

                    }

                    $pricing_weight=$variant_grams;

                    if($product_type && $product_type->base_weight){
                        $pricing_weight=max($variant_grams, $product_type->base_weight);
                    }
                    $check=ProductInfo::where('reference_shopify_id',$var['id'])->where('product_id',$product_id)->first();


                    if ($check==null)
                    {
                        if($var['sku']){
                            $sku=$var['sku'];
                        }
                        else{
                            if($store->sku_count < 10){
                                $count=$store->sku_count+1;
                                if($product_type && $product_type->product_type) {
                                    $sku = $store->name.'-'.$product_type->product_type.'-0'.$count;
                                }else{
                                    $sku = $store->name.'-0'.$count;
                                }
                            }else{
                                $count=$store->sku_count+1;
                                if($product_type && $product_type->product_type) {
                                    $sku = $store->name.'-'.$product_type->product_type.'-'.$count;
                                }else{
                                    $sku = $store->name.'-'.$count;
                                }
                            }
                            $store->sku_count=$store->sku_count+1;
                            $store->save();
                        }

                        $prices=Helpers::calc_price_fetched_products_by_vendor($vid,$var['price'],$pricing_weight);
                        $product_info = new ProductInfo;
                        $product_info->product_id = $product_id;
                        $product_info->sku = $sku;
                        $product_info->price = $prices['inr'];
                        $product_info->price_usd = $prices['usd'];
                        $product_info->price_nld = $prices['nld'];
                        $product_info->price_gbp = $prices['gbp'];
                        $product_info->price_cad = $prices['cad'];
                        $product_info->price_aud = $prices['aud'];
                        $product_info->price_irl = $prices['nld'];
                        $product_info->price_ger = $prices['nld'];
                        $product_info->base_price = $prices['base_price'];
                        $product_info->grams = $variant_grams;
                        $product_info->pricing_weight = $pricing_weight;
                        $product_info->stock = $var['available'];
                        $product_info->reference_shopify_id=$var['id'];
                        $product_info->vendor_id = $store_id;
                        $product_info->dimensions = '0-0-0';
                        if(isset($row['options'])){
                            $product_info->varient_name =$row['options'][0]['name'];
                        }

                        if(isset($row['options']) && isset($row['options'][1])){
                            $product_info->varient1_name =$row['options'][1]['name'];
                        }
                        if(isset($row['options']) && isset($row['options'][2])){
                            $product_info->varient2_name =$row['options'][2]['name'];
                        }

                        $product_info->varient_value = $var['option1'];
                        $product_info->varient1_value= $var['option2'];
                        $product_info->varient2_value= $var['option3'];
                        $product_info->save();
                    }
                }
                if($i>1)
                {
                    Product::where('id', $product_id)->update(['is_variants' => 1]);
                }
                foreach($row['images'] as $img_val)
                {
                    $imgCheck=ProductImages::where('image_id',$img_val['id'])->where('product_id',$product_id)->exists();
                    if (!$imgCheck)
                    {
                        $url = $img_val['src'];
//								$img = "uploads/shopifyimages/".$img_val['id'].".jpg";
//								file_put_contents($img, file_get_contents($url));
//								$img_name=url($img);
                        $img_name=$url;
                        $product_img = new ProductImages;
                        $product_img->image = $img_name;
                        $product_img->image_id = $img_val['id'];
                        $product_img->product_id = $product_id;
//                                $product_img->image_id = $img_val['id'];
//                                $product_img->width = $img_val['width'];
//                                $product_img->height = $img_val['height'];
                        $product_img->save();
                    }
                }
            }
            else  //Existing Product
            {
                $vendor=$row['vendor'];
                $data['title']=$row['title'];
                $data['body_html']=$html;
                $data['tags']=implode(",",$row['tags']);
                $data['product_type_id']=$product_type->id;
                $data['orignal_vendor'] = $vendor;
                $data['is_updated_by_url'] = 1;
                $data['is_available'] = 1;
                $data['options'] = json_encode($selectedOptions);
                Product::where('id',$product_check->id)->update($data);
                $product_id=$product_check->id;


                $product_logs=new ProductLog();
                $product_logs->title='Product Update';
                $product_logs->date_time=now()->format('F j, Y H:i:s');
                $product_logs->product_id=$product_id;
                $product_logs->log_id = $log_id;
                $product_logs->save();

                $i=0;

                $grams=0;
                $store=Store::find($vid);
                if($store->base_weight){
                    $grams=$store->base_weight;
                }
                if($product_type && $product_type->base_weight){
                    $grams=$product_type->base_weight;
                }
                $grams_selected=0;
                if($row['variants'][0]['grams'] > 0){
                    $grams_selected=1;
                    $grams=$row['variants'][0]['grams'];
                }else {
                    foreach ($row['variants'] as $var) {
                        if ($var['grams'] > 0 && $grams_selected==0) {
                            $grams_selected=1;
                            $grams = $var['grams'];
                        }
                    }
                }


                $check_info_v=ProductInfo::where('product_id',$product_check->id)->get();
                foreach ($check_info_v as $v_get){

                    if($v_get->inventory_id==null){
                        if($v_get->manual_weight==0) {
                            $v_get->delete();
                        }
                    }
                }

                $is_updated_price_inventory=0;
                foreach($row['variants'] as $var)
                {
                    $i++;
                    $variant_grams=($var['grams'] > 0) ? $var['grams'] :$grams;
                    if ($variant_grams == 0 && $weight_available == 1) {
                        if (preg_match('/(\d+)\s*Gms/', $var['option1'], $matches)) {

                            $variant_grams = $matches[1]; // This will give you '250'

                        }
                        elseif (preg_match('/(\d+)\s*gms/', $var['option1'], $matches)) {

                            $variant_grams = $matches[1]; // This will give you '250'

                        }

                        elseif (preg_match('/(\d+)\s*kg/i', $var['option1'], $matches)) {
                            $variant_grams = $matches[1] * 1000; // This will give you '1' if the string contains '1kg'
                            // Outputs: 1
                        } elseif (preg_match('/(\d+)\s*KG/i', $var['option1'], $matches)) {
                            $variant_grams = $matches[1] * 1000; // This will give you '1' if the string contains '1kg'
                            // Outputs: 1
                        } elseif (preg_match('/(\d+)\s*G/', $var['option1'], $matches)) {
                            $variant_grams = $matches[1]; // This will give you '250'
                        } elseif (preg_match('/(\d+)\s*Grams/', $var['option1'], $matches)) {
                            $variant_grams = $matches[1]; // This will give you '250'
                        } elseif (preg_match('/(\d+)\s*Gram/', $var['option1'], $matches)) {
                            $variant_grams = $matches[1]; // This will give you '250'
                        } elseif (preg_match('/(\d+)\s*kgs/i', $var['option1'], $matches)) {
                            $variant_grams = $matches[1] * 1000; // This will give you '1' if the string contains '1kg'
                            // Outputs: 1
                        }

                    }

                    $pricing_weight=$variant_grams;

                    if($product_type && $product_type->base_weight){
                        $pricing_weight=max($variant_grams, $product_type->base_weight);
                    }



                    $check_info=ProductInfo::where('reference_shopify_id',$var['id'])->first();


                    if($var['sku']){
                        $sku=$var['sku'];
                    }
                    else{
                        if($store->sku_count < 10){
                            $count=$store->sku_count+1;
                            if($product_type && $product_type->product_type) {
                                $sku = $store->name.'-'.$product_type->product_type.'-0'.$count;
                            }else{
                                $sku = $store->name.'-0'.$count;
                            }
                        }else{
                            $count=$store->sku_count+1;
                            if($product_type && $product_type->product_type) {
                                $sku = $store->name.'-'.$product_type->product_type.'-'.$count;
                            }else{
                                $sku = $store->name.'-'.$count;
                            }
                        }
                        $store->sku_count=$store->sku_count+1;
                        $store->save();
                    }


                    if (!$check_info)
                    {
                        $is_updated_price_inventory=1;

                        $prices=Helpers::calc_price_fetched_products_by_vendor($vid,$var['price'],$pricing_weight);
                        $product_info = new ProductInfo;
                        $product_info->product_id = $product_id;
                        $product_info->sku = $sku;
                        $product_info->price = $prices['inr'];
                        $product_info->price_usd = $prices['usd'];
                        $product_info->price_nld = $prices['nld'];
                        $product_info->price_gbp = $prices['gbp'];
                        $product_info->price_cad = $prices['cad'];
                        $product_info->price_aud = $prices['aud'];
                        $product_info->price_irl = $prices['nld'];
                        $product_info->price_ger = $prices['nld'];
                        $product_info->base_price =$var['price'];
                        $product_info->grams = $variant_grams;
                        $product_info->pricing_weight = $pricing_weight;
                        $product_info->stock = $var['available'];
                        $product_info->reference_shopify_id = $var['id'];
                        $product_info->vendor_id = $vid;
                        $product_info->dimensions = '0-0-0';
                        if(isset($row['options'])){
                            $product_info->varient_name =$row['options'][0]['name'];
                        }

                        if(isset($row['options']) && isset($row['options'][1])){
                            $product_info->varient1_name =$row['options'][1]['name'];
                        }

                        if(isset($row['options']) && isset($row['options'][2])){
                            $product_info->varient2_name =$row['options'][2]['name'];
                        }
                        $product_info->varient_value = $var['option1'];
                        $product_info->varient1_value= $var['option2'];
                        $product_info->varient2_value= $var['option3'];

                        $product_info->save();
                    }
                    else   //update variants
                    {
                        if($check_info->manual_weight==1){
                            $pricing_weight=$check_info->pricing_weight;
                        }
                        if($var['available']==true){
                            $check_available=1;
                        }else{
                            $check_available=0;
                        }
                        if($check_available!=$check_info->stock || $var['price']!=$check_info->base_price){
                            $is_updated_price_inventory=1;
                        }

                        $prices=Helpers::calc_price_fetched_products_by_vendor($vid,$var['price'],$pricing_weight);
                        $info_id=$check_info->id;
                        $info['price']=$prices['inr'];
                        $info['price_usd']=$prices['usd'];
                        $info['price_nld']=$prices['nld'];
                        $info['price_gbp']=$prices['gbp'];
                        $info['price_cad']=$prices['cad'];
                        $info['price_aud']=$prices['aud'];
                        $info['price_irl']=$prices['nld'];
                        $info['price_ger']=$prices['nld'];
                        $info['base_price']=$prices['base_price'];
                        $info['grams']=$variant_grams;
                        $info['sku']=$sku;
                        $info['pricing_weight'] = $pricing_weight;

                        $info['stock']=$var['available'];


                        if(isset($row['options'])) {
                            $info['varient_name'] = $row['options'][0]['name'];
                        }
                        if(isset($row['options']) && isset($row['options'][1])){
                            $info['varient1_name'] =$row['options'][1]['name'];
                        }

                        if(isset($row['options']) && isset($row['options'][2])){
                            $info['varient2_name'] =$row['options'][2]['name'];
                        }
                        $info['varient_value']=$var['option1'];
                        $info['varient1_value']=$var['option2'];
                        $info['varient2_value']=$var['option3'];
                        ProductInfo::where('id', $info_id)->update($info);
                    }

                }
                $update_data['is_update_price_inventory']=$is_updated_price_inventory;
                Product::where('id',$product_check->id)->update($update_data);
                if($i>1)
                {
                    Product::where('id', $product_id)->update(['is_variants' => 1]);
                }

                foreach($row['images'] as $img_val)
                {
                    $imgCheck=ProductImages::where('image_id',$img_val['id'])->where('product_id',$product_id)->exists();
                    if (!$imgCheck)
                    {
                        $url = $img_val['src'];
//								$img = "uploads/shopifyimages/".$img_val['id'].".jpg";
//								file_put_contents($img, file_get_contents($url));
//								$img_name=url($img);
                        $img_name=$url;
                        $product_img = new ProductImages;
                        $product_img->image = $img_name;
                        $product_img->image_id = $img_val['id'];
                        $product_img->product_id = $product_id;
//								$product_img->image_id = $img_val['id'];
//								$product_img->width = $img_val['width'];
//								$product_img->height = $img_val['height'];
                        $product_img->save();
                    }
                }
            }

        }



    }

}
