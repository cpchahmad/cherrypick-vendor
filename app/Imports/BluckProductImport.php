<?php

namespace App\Imports;

use App\Models\ProductImages;
use App\Models\ProductImagesNew;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use App\Models\Test;
use App\Models\Product;
use App\Models\Store;
use App\Models\ProductInfo;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Validator;
use Auth;
use App\Helpers\Helpers;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;

//class BluckProductImport implements ToCollection, WithHeadingRow,WithValidation
class BluckProductImport implements ToModel, WithStartRow
{
    /**
    * @param Collection $collection
    */

    protected $vid;
    protected $filePath;

    public function __construct($vid)
    {
        $this->vid = $vid;

    }


 public function startRow(): int
    {

        return 2;
    }


//      public function collection(Collection $collection)
//    {
//
//     $result= collect($collection)
//            ->groupBy('handle')
//            ->map(function ($group) {
//                return [
//                     'product_id'=> $group[0]['product_id'],
//                    'handle' => $group[0]['handle'],
//                    'title' => $group[0]['title'],
//                    'body_html' => $group[0]['body_html'],
//                    'vendor' => $group[0]['vendor'],
//                   'product_category' => $group[0]['product_category'],
//                    'tags'=>$group[0]['tags'],
//                   'variant_inventory_item_id'=>$group[0]['variant_inventory_item_id'],
//                    'variant_id'=>$group[0]['variant_id'],
//                   'image_src' => $group[0]['image_src'],
//
//                   /// 'summary'=>$group[0]['summary'],
//                   // 'photo' => $group[0]['photo'],
//                    // 'is_featured'=>$group[0]['is_featured'],
//                    'variant_res' => $group
//                ];
//            })
//            ->values()
//            ->all();
//
//
//
//
//             foreach($result as $value){
//              if(!empty($value['product_id']) && !empty($value['product_category']) ){
//                $variant_data=$value['variant_res'];
//                if(sizeof($variant_data)> 0){ $is_variant=1;}else{$is_variant=0; }
//
//		$check_category=Category::where('category',$value['product_category'])->first();
//		if($check_category == null)
//		{
//		$category_que = new Category;
//		$category_que->category = $value['product_category'];
//		$category_que->save();
//		$category_id=$category_que->id;
//		}
//		else
//		{
//		$category_id=$check_category->id;
//		}
//
//		$get_vendor_id=Store::where('name',$value['vendor'])->first();
//
//                $product = Product::updateOrCreate(['shopify_id'=>$value['product_id']],[
//                                'handle' => $value['handle'],
//                                 'vendor' =>32,
//                                'approve_date'=>date('Y-m-d'),
//                                 'status'=>1,
//                                 'vendor' => $get_vendor_id->id,
//                                'category' => $category_id,
//                                'title' => $value['title'],
//                                'tags' => $value['tags'],
//                                'is_variants'=>$is_variant,
//                                'body_html'=>$value['body_html'],
//
//                            ]);
//
//                            foreach($variant_data as $variant_val){
//
//
//                        $url = $variant_val['image_src'];
//			if($url!='')
//			{
//				$handle = @fopen($url, 'r');
//				if($handle){
//					$img = "uploads/profile/".$product->id."-".time().".jpg";
//					file_put_contents($img, file_get_contents($url));
//					$img_name=url($img);
//
//				}
//			}
//
//
//
//			if(!empty($variant_val['inventory_id'])){
//			$product_details=ProductInfo::updateOrCreate(
//			['inventory_id' => $variant_val['variant_id'],'product_id'=>$product->id],
//			['price_status'=>1,'grams'=>$variant_val['variant_grams'],'stock'=>$variant_val['variant_inventory_qty'],'sku'=>$variant_val['variant_sku'],'price' => $variant_val['price_inr'],'base_price'=>$variant_val['base_price_in_inr'],'discounted_inr'=>$variant_val['compare_at_price_inr'],'inventory_item_id'=>$variant_val['variant_inventory_item_id'],'inventory_id'=>$variant_val['variant_id'],'vendor_id'=> $get_vendor_id->id,'price_usd'=>$variant_val['variant_price'],'discounted_usd'=>$variant_val['variant_compare_at_price'],'price_gbp'=>$variant_val['price_gbp'],'discounted_gbp'=>$variant_val['compare_at_price_gbp'],'price_nld'=>$variant_val['price_nld'],'discounted_nld'=>$variant_val['compare_at_price_nld'],'price_cad'=>$variant_val['price_cad'],'discounted_cad'=>$variant_val['compare_at_price_cad'],'price_aud'=>$variant_val['price_aud'],'discounted_aud'=>$variant_val['compare_at_price_aud'],'varient_name'=>$variant_val['option1_name'],'varient_value'=>$variant_val['option1_value']]);
//
//
//			$product_image=ProductImages::updateOrCreate(
//			['product_id' =>$product->id,'variant_ids'=>$product_details->id],
//			['image' => $img_name]);
//
//			}
//
//             }
//             }
//
//        }
//
//
//
//
//
//    }
      public function model(array $row)
    {






/*      $d=  DB::table('products_images')->insert([
            'image' => 'image1',
            'image2' => 'image1',
            'image3' => 'image1',
            'image4' => 'image1',
            'image5' => 'image1',
            'variant_ids' => 2,
            'alt_text' => 3,
            'product_id' => 4,
        ]);*/



        $vendor_id=$this->vid;

        $check_category=Category::where('category',$row[3])->first();

        if($check_category == null)
        {
            $category_que = new Category;
            $category_que->category = $row[3];
            $category_que->save();
            $category_id=$category_que->id;
        }
        else
        {
            $category_id=$check_category->id;
        }

        $check=Product::where('title',$row[0])->where('vendor',$vendor_id)->first();


          if (!$check)
          {
              $product = new Product;
              $product->title = $row[0];
              $product->body_html = $row[1];
              $product->vendor = $vendor_id;
              $product->tags = $row[2];
              $product->category = $category_id;

              //zain
              $product->additional_key_ingredients =$row[21];

              $product->additional_how_to_use =$row[22];
              $product->additional_who_can_use=$row[23];
              $product->additional_why_mama_earth=$row[24];
              $product->additional_different_shades =$row[25];
              $product->additional_faqs =$row[26];


              $product->save();
              $product_id=$product->id;
              $Tags=explode(",",$row[2]);
              if(in_array("Saree",$Tags))
                  $is_saree = 1;
              else
                  $is_saree = 0;
              if($row[18] > 0 && $row[19] > 0 && $row[20] > 0)
              {
                  $is_furniture = 1;
                  $volumetric_Weight = ($row[18]*$row[19]*$row[20])/5000;
              }
              else
              {
                  $is_furniture = 0;
                  $volumetric_Weight = 0;
              }


              ///Product variants




              $product_info = new ProductInfo;
              $product_info->product_id = $product_id;
              $product_info->vendor_id = $vendor_id;
              $product_info->sku = $row[11];
              $prices=Helpers::calc_price_new($row[10],$row[14],$row[2],$volumetric_Weight,$vendor_id);
              $product_info->price = $prices['inr'];
              $product_info->price_usd = $prices['usd'];
              $product_info->price_aud = $prices['aud'];
              $product_info->price_cad = $prices['cad'];
              $product_info->price_gbp = $prices['gbp'];
              $product_info->price_nld = $prices['nld'];
              $product_info->price_irl = $prices['irl'];
              $product_info->price_ger = $prices['ger'];
              $product_info->base_price = $row[10];
              $product_info->grams = $row[14];
              $product_info->hex_code =$row[8];
              $product_info->swatch_image =$row[9];
              $product_info->stock = $row[15];
              //zain
              $product_info->dimensions_text = $row[12];
              $product_info->volume = $row[13];

              $product_info->dimensions = $row[18]."-".$row[19]."-".$row[20];
              $product_info->varient_name = $row[4];
              $product_info->varient_value = $row[5];


              //zain
              $product_info->varient1_name = $row[6];
              $product_info->varient1_value = $row[7];


              $product_info->shelf_life = $row[16];
              $product_info->temp_require = $row[17];
              $product_info->save();

          }
          else{
              $product = Product::find($check->id);
              $product_id=$product->id;
              $product->title = $row[0];
              $product->body_html = $row[1];
              $product->vendor = $vendor_id;
              $product->tags = $row[2];
              $product->category = $category_id;
              //zain


              $product->additional_key_ingredients =$row[21];
              $product->additional_how_to_use =$row[22];
              $product->additional_who_can_use=$row[23];
              $product->additional_why_mama_earth=$row[24];
              $product->additional_different_shades=$row[25];
              $product->additional_faqs =$row[26];
              $product->save();


              $Tags=explode(",",$row[2]);
              if(in_array("Saree",$Tags))
                  $is_saree = 1;
              else
                  $is_saree = 0;
              if($row[18] > 0 && $row[19] > 0 && $row[20] > 0)
              {
                  $is_furniture = 1;
                  $volumetric_Weight = ($row[18]*$row[19]*$row[20])/5000;
              }
              else
              {
                  $is_furniture = 0;
                  $volumetric_Weight = 0;
              }
              $check_info=ProductInfo::where('sku',$row[11])->first();
              if (!$check_info)
              {
                  $prices=Helpers::calc_price_new($row[10],$row[14],$row[2],$volumetric_Weight,$vendor_id);
                  $product_info = new ProductInfo;
                  $product_info->product_id = $check->id;
                  $product_info->vendor_id = $vendor_id;
                  $product_info->sku = $row[11];
                  $product_info->base_price = $row[10];
                  $product_info->price = $prices['inr'];
                  $product_info->hex_code =$row[8];
                  $product_info->swatch_image =$row[9];

                  $product_info->price_usd = $prices['usd'];
                  $product_info->price_aud = $prices['aud'];
                  $product_info->price_cad = $prices['cad'];
                  $product_info->price_gbp = $prices['gbp'];
                  $product_info->price_nld = $prices['nld'];
                  $product_info->price_irl = $prices['irl'];
                  $product_info->price_ger = $prices['ger'];
                  $product_info->grams = $row[14];
                  $product_info->stock = $row[15];
                  $product_info->dimensions = $row[18]."-".$row[19]."-".$row[20];
                  $product_info->varient_name = $row[4];
                  $product_info->varient_value = $row[5];

                  //zain
                  $product_info->varient1_name=$row[6];
                  $product_info->varient1_value= $row[7];




                  $product_info->shelf_life = $row[16];
                  $product_info->temp_require = $row[17];
                  $product_info->save();
              }
              else
              {
                  $prices=Helpers::calc_price_new($row[10],$row[14],$row[2],$volumetric_Weight,$vendor_id);
                  $data['price'] = $prices['inr'];
                  $data['price_usd'] = $prices['usd'];
                  $data['price_aud'] = $prices['aud'];
                  $data['price_cad'] = $prices['cad'];
                  $data['price_gbp'] = $prices['gbp'];
                  $data['price_nld'] = $prices['nld'];
                  $data['price_irl'] = $prices['irl'];
                  $data['price_ger'] = $prices['ger'];
                  $data['base_price'] = $row[10];
                  $data['grams'] = $row[14];
                  $data['stock'] = $row[15];
                    $data['hex_code']=$row[8];
                    $data['swatch_image']=$row[9];
                  $data['dimensions'] = $row[18]."-".$row[19]."-".$row[20];
                  $data['varient_name'] = $row[4];
                  $data['varient_value'] = $row[5];

                  //zain
                  $data['varient1_name'] = $row[6];
                  $data['varient1_value'] = $row[7];

                  $data['shelf_life'] = $row[16];
                  $data['temp_require'] = $row[17];
                  $data['price_status'] = '0';

                  ProductInfo::where('id', $check_info->id)->update($data);

                  $product_info=$check_info;
              }
              $count_info=ProductInfo::where('product_id',$check->id)->count();
              if($count_info > 1)
              {
                  Product::where('id', $check->id)->update(['is_variants' => 1]);
              }
          }



        ///Image


       $product_images=ProductImages::where('product_id',$product_id)->where('variant_ids',$product_info->id)->first();

          if($product_images==null) {
              $url = $row[27];

              if ($url != '') {
                  $handle = @fopen($url, 'r');
                  if ($handle) {
                      $img = "uploads/profile/" . $product_id . "-image1-" . time() . ".jpg";
                      file_put_contents($img, file_get_contents($url));
                      $img_name = url($img);

                  }
              }

              $url1 = $row[28];
              if ($url1 != '') {
                  $handle1 = @fopen($url1, 'r');
                  if ($handle1) {
                      $img1 = "uploads/profile/" . $product_id . "-image2-" . time() . ".jpg";
                      file_put_contents($img1, file_get_contents($url1));
                      $img_name1 = url($img1);
                  }
              }

              $url2 = $row[29];
              if ($url2 != '') {
                  $handle2 = @fopen($url2, 'r');
                  if ($handle2) {
                      $img2 = "uploads/profile/" . $product_id . "-image3-" . time() . ".jpg";
                      file_put_contents($img2, file_get_contents($url2));
                      $img_name2 = url($img2);
                  }
              }

              $url3 = $row[30];
              if ($url3 != '') {
                  $handle3 = @fopen($url3, 'r');
                  if ($handle3) {
                      $img3 = "uploads/profile/" . $product_id . "-image4-" . time() . ".jpg";
                      file_put_contents($img3, file_get_contents($url3));
                      $img_name3 = url($img3);

                  }
              }
              $url4 = $row[31];
              if ($url4 != '') {
                  $handle4 = @fopen($url4, 'r');
                  if ($handle4) {
                      $img4 = "uploads/profile/" . $product_id . "-image5-" . time() . ".jpg";
                      file_put_contents($img4, file_get_contents($url4));
                      $img_name4 = url($img4);
                  }
              }


                $product_img=new ProductImages();
              $product_img->image = isset($img_name) ? $img_name:null;
              $product_img->image2 = isset($img_name1) ? $img_name1 :null;
              $product_img->image3 = isset($img_name2) ? $img_name2 :null;
              $product_img->image4 = isset($img_name3) ? $img_name3 :null;
              $product_img->image5 = isset($img_name4) ? $img_name4 :null;
              $product_img->variant_ids=$product_info->id;
              $product_img->alt_text = $row[11];
              $product_img->product_id = $product_id;
              $product_img->save();

          }

    }

    /*public function model(array $row)
    {
        echo "<pre>"; print_r($row); die();
        //for category
        if(Auth::user()->role=='Vendor')
            $vendor_id=Auth::user()->id;
       else
           $vendor_id=Auth::user()->vendor_id;
        $check_category=Category::where('category',$row[3])->first();
        if($check_category == null)
        {
            $category_que = new Category;
            $category_que->category = $row[3];
            $category_que->save();
            $category_id=$category_que->id;
        }
        else
        {
            $category_id=$check_category->id;
        }
        // $check=Product::where('title',$row[0])->where('category',$category_id)->where('vendor',$vendor_id)->first();
        // if($check == null)   //Single variant product
        // {
			$check=Product::where('title',$row[0])->where('vendor',$vendor_id)->first();
			//$check=ProductInfo::where('sku',$row[7])->first();
            if (!$check)
            {
            $product = new Product;
            $product->title = $row[0];
            $product->body_html = $row[1];
            $product->vendor = $vendor_id;
            $product->tags = $row[2];
            $product->category = $category_id;
            $product->save();
            $product_id=$product->id;
			$Tags=explode(",",$row[2]);
            if(in_array("Saree",$Tags))
                $is_saree = 1;
            else
                $is_saree = 0;
			if($row[12] > 0 && $row[13] > 0 && $row[14] > 0)
            {
                $is_furniture = 1;
                $volumetric_Weight = ($row[12]*$row[13]*$row[14])/5000;
            }
            else
            {
                $is_furniture = 0;
                $volumetric_Weight = 0;
            }
			///Image
            $url = $row[15];
			if($url!='')
			{
				$handle = @fopen($url, 'r');
				if($handle){
					$img = "uploads/profile/".$product_id."-".time().".jpg";
					file_put_contents($img, file_get_contents($url));
					$img_name=url($img);
					$product_img = new ProductImages;
					$product_img->image = $img_name;
					$product_img->product_id = $product_id;
					$product_img->save();
				}
			}
            ///Product variants

                $product_info = new ProductInfo;
                $product_info->product_id = $product_id;
                $product_info->vendor_id = $vendor_id;
                $product_info->sku = $row[7];
				$prices=Helpers::calc_price_new($row[6],$row[8],$row[2],$volumetric_Weight,$vendor_id);
				$product_info->price = $prices['inr'];
				$product_info->price_usd = $prices['usd'];
				$product_info->price_aud = $prices['aud'];
				$product_info->price_cad = $prices['cad'];
				$product_info->price_gbp = $prices['gbp'];
				$product_info->price_nld = $prices['nld'];
				$product_info->base_price = $row[6];
                $product_info->grams = $row[8];
                $product_info->stock = $row[9];
                $product_info->dimensions = $row[12]."-".$row[13]."-".$row[14];
                $product_info->varient_name = $row[4];
                $product_info->varient_value = $row[5];
				$product_info->shelf_life = $row[10];
				$product_info->temp_require = $row[11];
                $product_info->save();
            }
			else{
				$product = Product::find($check->id);
				$product->title = $row[0];
				$product->body_html = $row[1];
				$product->vendor = $vendor_id;
				$product->tags = $row[2];
				$product->category = $category_id;
				$product->save();

				$Tags=explode(",",$row[2]);
				if(in_array("Saree",$Tags))
					$is_saree = 1;
				else
					$is_saree = 0;
				if($row[12] > 0 && $row[13] > 0 && $row[14] > 0)
				{
					$is_furniture = 1;
					$volumetric_Weight = ($row[12]*$row[13]*$row[14])/5000;
				}
				else
				{
					$is_furniture = 0;
					$volumetric_Weight = 0;
				}
				$check_info=ProductInfo::where('sku',$row[7])->first();
                if (!$check_info)
                {
					$prices=Helpers::calc_price_new($row[6],$row[8],$row[2],$volumetric_Weight,$vendor_id);
                    $product_info = new ProductInfo;
                    $product_info->product_id = $check->id;
                    $product_info->vendor_id = $vendor_id;
                    $product_info->sku = $row[7];
                    $product_info->base_price = $row[6];
					$product_info->price = $prices['inr'];
					$product_info->price_usd = $prices['usd'];
					$product_info->price_aud = $prices['aud'];
					$product_info->price_cad = $prices['cad'];
					$product_info->price_gbp = $prices['gbp'];
					$product_info->price_nld = $prices['nld'];
                    $product_info->grams = $row[8];
                    $product_info->stock = $row[9];
                    $product_info->dimensions = $row[12]."-".$row[13]."-".$row[14];
                    $product_info->varient_name = $row[4];
                    $product_info->varient_value = $row[5];
					$product_info->shelf_life = $row[10];
					$product_info->temp_require = $row[11];
                    $product_info->save();
                }
                else
                {
					$prices=Helpers::calc_price_new($row[6],$row[8],$row[2],$volumetric_Weight,$vendor_id);
					$data['price'] = $prices['inr'];
					$data['price_usd'] = $prices['usd'];
					$data['price_aud'] = $prices['aud'];
					$data['price_cad'] = $prices['cad'];
					$data['price_gbp'] = $prices['gbp'];
					$data['price_nld'] = $prices['nld'];
					$data['base_price'] = $row[6];
					$data['grams'] = $row[8];
					$data['stock'] = $row[9];
					$data['dimensions'] = $row[12]."-".$row[13]."-".$row[14];
					$data['varient_name'] = $row[4];
					$data['varient_value'] = $row[5];
					$data['shelf_life'] = $row[10];
					$data['temp_require'] = $row[11];
					$data['price_status'] = '0';
					ProductInfo::where('id', $check_info->id)->update($data);
                }
				$count_info=ProductInfo::where('product_id',$check->id)->count();
				if($count_info > 1)
				{
					Product::where('id', $check->id)->update(['is_variants' => 1]);
				}
			}
    }*/



	public function rules(): array
    {

     return [
            'variant_id'=>'required|integer',
            'variant_inventory_item_id'=>'required|integer',
        ];

    }
	//public function customValidationMessages()
	//{
    //return [
	//'variant_id' => 'Variant id is required',
	//''=>'Variant Inventory Item ID is e'

    //];
	//}
}
