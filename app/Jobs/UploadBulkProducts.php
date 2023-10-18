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
use App\Models\Store;
use App\Models\VariantChange;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\SimpleExcel\SimpleExcelReader;

class UploadBulkProducts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 10000;
    protected $vid;
    protected $hashname;
    /**
     * Create a new job instance.
     *
     * @return void
     */

    public function __construct($hashname,$vid)
    {
        $this->vid = $vid;
        $this->hashname = $hashname;

    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $vendor_id=$this->vid;
        $rows=    SimpleExcelReader::create($this->hashname)->noHeaderRow()->getRows();

        try {
            $log = new Log();
            $log->name = 'Import Bulk Product From Excel File';
            $log->date = date("F j, Y g:i a");
            $log->status = 'In-Progress';
            $log->save();
            foreach ($rows as $index => $row) {
                if ($index > 0) {


                    $check_category = Category::where('category', $row[3])->first();

                    if ($check_category == null) {
                        $category_que = new Category;
                        $category_que->category = $row[3];
                        $category_que->save();
                        $category_id = $category_que->id;
                    } else {
                        $category_id = $check_category->id;
                    }


                    $product_type=ProductType::where('product_type',$row[3])->where('vendor_id',$vendor_id)->first();
                    if($product_type==null){
                        $product_type=new ProductType();
                    }
                    $product_type->product_type=$row[3];
                    $product_type->vendor_id=$vendor_id;
                    $product_type->save();

                    $check = Product::where('title', $row[0])->where('vendor', $vendor_id)->first();


                    if (!$check) {
                        $product = new Product;
                        $product->title = $row[0];
                        $product->body_html = $row[1];
                        $product->vendor = $vendor_id;
                        $product->tags = $row[2];
                        $product->category = $category_id;

                        //zain
                        $product->additional_key_ingredients = $row[21];

                        $product->additional_how_to_use = $row[22];
                        $product->additional_who_can_use = $row[23];
                        $product->additional_why_mama_earth = $row[24];
                        $product->additional_different_shades = $row[25];
                        $product->product_type_id=$product_type->id;
                        $product->additional_faqs = $row[26];


                        $product->save();
                        $product_id = $product->id;
                        $Tags = explode(",", $row[2]);
                        if (in_array("Saree", $Tags))
                            $is_saree = 1;
                        else
                            $is_saree = 0;
                        if ($row[18] > 0 && $row[19] > 0 && $row[20] > 0) {
                            $is_furniture = 1;
                            $volumetric_Weight = ($row[18] * $row[19] * $row[20]) / 5000;
                        } else {
                            $is_furniture = 0;
                            $volumetric_Weight = 0;
                        }

                        $grams=$row[14];
                        if($grams==0){
                            $store=Store::find($vendor_id);
                            if($store && $store->base_weight){
                                $grams=$store->base_weight;
                            }
                            if($product_type && $product_type->base_weight){
                                $grams=$product_type->base_weight;
                            }
                        }

                        ///Product variants


                        $product_info = new ProductInfo;
                        $product_info->product_id = $product_id;
                        $product_info->vendor_id = $vendor_id;
                        $product_info->sku = $row[11];
                        $prices = Helpers::calc_price_new($row[10], $grams, $row[2], $volumetric_Weight, $vendor_id);
                        $product_info->price = $prices['inr'];
                        $product_info->price_usd = $prices['usd'];
                        $product_info->price_aud = $prices['aud'];
                        $product_info->price_cad = $prices['cad'];
                        $product_info->price_gbp = $prices['gbp'];
                        $product_info->price_nld = $prices['nld'];
                        $product_info->price_irl = $prices['irl'];
                        $product_info->price_ger = $prices['ger'];
                        $product_info->base_price = $row[10];
                        $product_info->grams = $grams;
                        $product_info->hex_code = $row[8];
                        $product_info->swatch_image = $row[9];
                        $product_info->stock = $row[15];
                        //zain
                        $product_info->dimensions_text = $row[12];
                        $product_info->volume = $row[13];

                        $product_info->dimensions = $row[18] . "-" . $row[19] . "-" . $row[20];
                        $product_info->varient_name = $row[4];
                        $product_info->varient_value = $row[5];


                        //zain
                        $product_info->varient1_name = $row[6];
                        $product_info->varient1_value = $row[7];


                        $product_info->shelf_life = $row[16];
                        $product_info->temp_require = $row[17];
                        $product_info->save();

                    } else {
                        $product = Product::find($check->id);
                        $product_id = $product->id;
                        $product->title = $row[0];
                        $product->body_html = $row[1];
                        $product->vendor = $vendor_id;
                        $product->tags = $row[2];
                        $product->product_type_id=$product_type->id;
                        $product->category = $category_id;
                        //zain


                        $product->additional_key_ingredients = $row[21];
                        $product->additional_how_to_use = $row[22];
                        $product->additional_who_can_use = $row[23];
                        $product->additional_why_mama_earth = $row[24];
                        $product->additional_different_shades = $row[25];
                        $product->additional_faqs = $row[26];
                        $product->save();


                        $Tags = explode(",", $row[2]);
                        if (in_array("Saree", $Tags))
                            $is_saree = 1;
                        else
                            $is_saree = 0;
                        if ($row[18] > 0 && $row[19] > 0 && $row[20] > 0) {
                            $is_furniture = 1;
                            $volumetric_Weight = ($row[18] * $row[19] * $row[20]) / 5000;
                        } else {
                            $is_furniture = 0;
                            $volumetric_Weight = 0;
                        }


                        $grams=$row[14];
                        if($grams==0){
                            $store=Store::find($vendor_id);
                            if($store && $store->base_weight){
                                $grams=$store->base_weight;
                            }
                            if($product_type && $product_type->base_weight){
                                $grams=$product_type->base_weight;
                            }
                        }

                        $check_info = ProductInfo::where('sku', $row[11])->first();
                        if (!$check_info) {
                            $prices = Helpers::calc_price_new($row[10], $grams, $row[2], $volumetric_Weight, $vendor_id);
                            $product_info = new ProductInfo;
                            $product_info->product_id = $check->id;
                            $product_info->vendor_id = $vendor_id;
                            $product_info->sku = $row[11];
                            $product_info->base_price = $row[10];
                            $product_info->price = $prices['inr'];
                            $product_info->hex_code = $row[8];
                            $product_info->swatch_image = $row[9];

                            $product_info->price_usd = $prices['usd'];
                            $product_info->price_aud = $prices['aud'];
                            $product_info->price_cad = $prices['cad'];
                            $product_info->price_gbp = $prices['gbp'];
                            $product_info->price_nld = $prices['nld'];
                            $product_info->price_irl = $prices['irl'];
                            $product_info->price_ger = $prices['ger'];
                            $product_info->grams = $grams;
                            $product_info->stock = $row[15];
                            $product_info->dimensions = $row[18] . "-" . $row[19] . "-" . $row[20];
                            $product_info->varient_name = $row[4];
                            $product_info->varient_value = $row[5];

                            //zain
                            $product_info->varient1_name = $row[6];
                            $product_info->varient1_value = $row[7];


                            $product_info->shelf_life = $row[16];
                            $product_info->temp_require = $row[17];
                            $product_info->save();
                        } else {
                            $prices = Helpers::calc_price_new($row[10], $grams, $row[2], $volumetric_Weight, $vendor_id);
                            $data['price'] = $prices['inr'];
                            $data['price_usd'] = $prices['usd'];
                            $data['price_aud'] = $prices['aud'];
                            $data['price_cad'] = $prices['cad'];
                            $data['price_gbp'] = $prices['gbp'];
                            $data['price_nld'] = $prices['nld'];
                            $data['price_irl'] = $prices['irl'];
                            $data['price_ger'] = $prices['ger'];
                            $data['base_price'] = $row[10];
                            $data['grams'] = $grams;
                            $data['stock'] = $row[15];
                            $data['hex_code'] = $row[8];
                            $data['swatch_image'] = $row[9];
                            $data['dimensions'] = $row[18] . "-" . $row[19] . "-" . $row[20];
                            $data['varient_name'] = $row[4];
                            $data['varient_value'] = $row[5];

                            //zain
                            $data['varient1_name'] = $row[6];
                            $data['varient1_value'] = $row[7];

                            $data['shelf_life'] = $row[16];
                            $data['temp_require'] = $row[17];
                            $data['price_status'] = '0';

                            ProductInfo::where('id', $check_info->id)->update($data);

                            $product_info = $check_info;
                        }
                        $count_info = ProductInfo::where('product_id', $check->id)->count();
                        if ($count_info > 1) {
                            Product::where('id', $check->id)->update(['is_variants' => 1]);
                        }
                    }


                    ///Image


                    $product_images = ProductImages::where('product_id',$product_id)->where('variant_ids',$product_info->id)->first();

                    if ($product_images == null) {
                        $url = $row[27];

                        if ($url != '') {
                            $handle = @fopen($url, 'r');
                            if ($handle) {
                                $fileName = $product_id . '-image1-' . time() . '.jpg';
                                $img = public_path('uploads/profile/' . $fileName);

                                file_put_contents($img, file_get_contents($url));
                                $img_name = asset('/uploads/profile') . '/' . $fileName;

                            }
                        }

                        $url1 = $row[28];
                        if ($url1 != '') {
                            $handle1 = @fopen($url1, 'r');
                            if ($handle1) {
                                $fileName1 = $product_id . '-image2-' . time() . '.jpg';
                                $img1 = public_path('uploads/profile/' . $fileName1);
                                file_put_contents($img1, file_get_contents($url1));
                                $img_name1 = asset('/uploads/profile') . '/' . $fileName1;
                            }
                        }

                        $url2 = $row[29];
                        if ($url2 != '') {
                            $handle2 = @fopen($url2, 'r');
                            if ($handle2) {
                                $fileName2 = $product_id . '-image3-' . time() . '.jpg';
                                $img2 = public_path('uploads/profile/' . $fileName2);
                                file_put_contents($img2, file_get_contents($url2));
                                $img_name2 = asset('/uploads/profile') . '/' . $fileName2;
                            }
                        }

                        $url3 = $row[30];
                        if ($url3 != '') {
                            $handle3 = @fopen($url3, 'r');
                            if ($handle3) {
                                $fileName3 = $product_id . '-image4-' . time() . '.jpg';
                                $img3 = public_path('uploads/profile/' . $fileName3);
                                file_put_contents($img3, file_get_contents($url3));
                                $img_name3 = asset('/uploads/profile') . '/' . $fileName3;

                            }
                        }
                        $url4 = $row[31];
                        if ($url4 != '') {
                            $handle4 = @fopen($url4, 'r');
                            if ($handle4) {
                                $fileName4 = $product_id . '-image5-' . time() . '.jpg';
                                $img4 = public_path('uploads/profile/' . $fileName4);
                                file_put_contents($img4, file_get_contents($url4));
                                $img_name4 = asset('/uploads/profile') . '/' . $fileName4;
                            }
                        }


                        $product_img = new ProductImages();
                        $product_img->image = isset($img_name) ? $img_name : null;
                        $product_img->image2 = isset($img_name1) ? $img_name1 : null;
                        $product_img->image3 = isset($img_name2) ? $img_name2 : null;
                        $product_img->image4 = isset($img_name3) ? $img_name3 : null;
                        $product_img->image5 = isset($img_name4) ? $img_name4 : null;
                        $product_img->variant_ids = $product_info->id;
                        $product_img->alt_text = $row[11];
                        $product_img->product_id = $product_id;
                        $product_img->save();

                    }
                }
            }
            $log->date = date("F j, Y g:i a");
            $log->status = 'Complete';
            $log->save();
        }catch (\Exception $exception){
            $log->date = date("F j, Y g:i a");
            $log->status = 'Failed';
            $log->message=json_encode($exception->getMessage());
            $log->save();

        }

    }

//    function saveStoreFetchProductsFromJson($products,$vid,$tag_url=null)
//    {
//
//        //echo "<pre>"; print_r($products); die;
//        foreach($products as $index=> $row)
//        {
//
//
//            $product_check=Product::where('title',$row['title'])->where('vendor',$vid)->first();
//
//            //echo $pid; die;
//            if($product_check==null)  ////////New Product
//            {
//                $cat=Category::where('category',$row['product_type'])->first();
//                if($cat)
//                    $category_id=$cat->id;
//                else
//                {
//                    $cate_que = new Category;
//                    $cate_que->category = $row['product_type'];
//                    $cate_que->save();
//                    $category_id=$cate_que->id;
//                }
//                $shopify_id=$row['id'];
//                $title=$row['title'];
//                $description=$row['body_html'];
//                $vendor=$row['vendor'];
//                $tags=implode(",",$row['tags']);
//                $handle=$row['handle'];
//                $store_id=$vid;
//
//                $product = new Product;
//                $product->title = $title;
//                $product->body_html = $description;
//                $product->vendor = $store_id;
//                $product->tags = $tags;
//                $product->category = $category_id;
//                $product->save();
//                $product_id=$product->id;
//
//                $i=0;
//
//                foreach($row['variants'] as $var)
//                {
//
//                    $i++;
//                    $check=ProductInfo::where('sku',$var['sku'])->where('product_id',$product_id)->first();
//
//
//                    if ($check==null)
//                    {
//                        $prices=Helpers::calc_price_fetched_products($var['price'],$var['grams']);
//                        $product_info = new ProductInfo;
//                        $product_info->product_id = $product_id;
//                        $product_info->sku = $var['sku'];
//                        $product_info->price = $prices['inr'];
//                        $product_info->price_usd = $prices['usd'];
//                        $product_info->price_nld = $prices['nld'];
//                        $product_info->price_gbp = $prices['gbp'];
//                        $product_info->price_cad = $prices['cad'];
//                        $product_info->price_aud = $prices['aud'];
//                        $product_info->price_irl = $prices['nld'];
//                        $product_info->price_ger = $prices['nld'];
//                        $product_info->base_price = $prices['base_price'];
//                        $product_info->grams = $var['grams'];
//                        $product_info->stock = $var['available'];
//                        $product_info->vendor_id = $store_id;
//                        $product_info->dimensions = '0-0-0';
//                        $product_info->varient_name = isset($row['options'][1]['name'])?$row['options'][1]['name']:$row['options'][0]['name'];
//                        $product_info->varient_value = isset($var['option2'])?$var['option2']:$var['option1'];
//                        $product_info->save();
//                    }
//                }
//                if($i>1)
//                {
//                    Product::where('id', $product_id)->update(['is_variants' => 1]);
//                }
//                foreach($row['images'] as $img_val)
//                {
//                    $imgCheck=ProductImages::where('image_id',$img_val['id'])->exists();
//                    if (!$imgCheck)
//                    {
//                        $url = $img_val['src'];
////								$img = "uploads/shopifyimages/".$img_val['id'].".jpg";
////								file_put_contents($img, file_get_contents($url));
////								$img_name=url($img);
//                        $img_name=$url;
//                        $product_img = new ProductImages;
//                        $product_img->image = $img_name;
//                        //$product_img->image_id = $img_val['id'];
//                        $product_img->product_id = $product_id;
////                                $product_img->image_id = $img_val['id'];
////                                $product_img->width = $img_val['width'];
////                                $product_img->height = $img_val['height'];
//                        $product_img->save();
//                    }
//                }
//            }
//            else  //Existing Product
//            {
//                $data['title']=$row['title'];
//                $data['body_html']=$row['body_html'];
//                $data['tags']=implode(",",$row['tags']);
//                Product::where('id',$product_check->id)->update($data);
//                $product_id=$product_check->id;
//                $i=0;
//
//                foreach($row['variants'] as $var)
//                {
//                    $i++;
//                    $check_info=ProductInfo::where('sku',$var['sku'])->first();
//                    if (!$check_info)
//                    {
//                        $prices=Helpers::calc_price_fetched_products($var['price'],$var['grams']);
//                        $product_info = new ProductInfo;
//                        $product_info->product_id = $product_id;
//                        $product_info->sku = $var['sku'];
//                        $product_info->price = $prices['inr'];
//                        $product_info->price_usd = $prices['usd'];
//                        $product_info->price_nld = $prices['nld'];
//                        $product_info->price_gbp = $prices['gbp'];
//                        $product_info->price_cad = $prices['cad'];
//                        $product_info->price_aud = $prices['aud'];
//                        $product_info->price_irl = $prices['nld'];
//                        $product_info->price_ger = $prices['nld'];
//                        $product_info->base_price = $prices['base_price'];
//                        $product_info->grams = $var['grams'];
//                        $product_info->stock = $var['available'];
//                        $product_info->vendor_id = $vid;
//                        $product_info->dimensions = '0-0-0';
//                        $product_info->varient_name = isset($row['options'][1]['name'])?$row['options'][1]['name']:$row['options'][0]['name'];
//                        $product_info->varient_value = isset($var['option2'])?$var['option2']:$var['option1'];
//                        $product_info->save();
//                    }
//                    else   //update variants
//                    {
//                        $prices=Helpers::calc_price_fetched_products($var['price'],$var['grams']);
//                        $info_id=$check_info->id;
//                        $info['price']=$prices['inr'];
//                        $info['price_usd']=$prices['usd'];
//                        $info['price_nld']=$prices['nld'];
//                        $info['price_gbp']=$prices['gbp'];
//                        $info['price_cad']=$prices['cad'];
//                        $info['price_aud']=$prices['aud'];
//                        $info['price_irl']=$prices['nld'];
//                        $info['price_ger']=$prices['nld'];
//                        $info['base_price']=$prices['base_price'];
//                        $info['grams']=$var['grams'];
//                        $info['stock']=$var['available'];
//                        $info['varient_name']=$row['title'];
//                        $info['varient_value']=$var['option1'];
//                        ProductInfo::where('id', $info_id)->update($info);
//                    }
//                }
//                if($i>1)
//                {
//                    Product::where('id', $product_id)->update(['is_variants' => 1]);
//                }
//                foreach($row['images'] as $img_val)
//                {
//                    $imgCheck=ProductImages::where('image_id',$img_val['id'])->exists();
//                    if (!$imgCheck)
//                    {
//                        $url = $img_val['src'];
////								$img = "uploads/shopifyimages/".$img_val['id'].".jpg";
////								file_put_contents($img, file_get_contents($url));
////								$img_name=url($img);
//                        $img_name=$url;
//                        $product_img = new ProductImages;
//                        $product_img->image = $img_name;
//                        //$product_img->image_id = $img_val['id'];
//                        $product_img->product_id = $product_id;
////								$product_img->image_id = $img_val['id'];
////								$product_img->width = $img_val['width'];
////								$product_img->height = $img_val['height'];
//                        $product_img->save();
//                    }
//                }
//            }
//
//        }
//
//    }
}
