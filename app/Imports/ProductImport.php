<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use App\Models\Test;
use App\Models\Product;
use App\Models\ProductInfo;
use App\Models\ProductImages;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Validator;
use Auth;
use App\Helpers\Helpers;
use Maatwebsite\Excel\Concerns\WithValidation;
class ProductImport implements ToModel, WithStartRow, WithValidation
{
    /**
    * @param Collection $collection
    */
 public function startRow(): int
    {
        return 2;
    }

    public function model(array $row)
    {
        //echo "<pre>"; print_r($row); die();
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

                $url1 = $row[16];
                if($url1!='')
                {
                    $handle1 = @fopen($url1, 'r');
                    if($handle1){
                        $img1 = "uploads/profile/".$product_id."-".time().".jpg";
                        file_put_contents($img1, file_get_contents($url1));
                        $img_name1=url($img1);
                        $product_img = new ProductImages;
                        $product_img->image = $img_name1;
                        $product_img->product_id = $product_id;
                        $product_img->save();
                    }
                }

                $url2 = $row[17];
                if($url2!='')
                {
                    $handle2 = @fopen($url2, 'r');
                    if($handle2){
                        $img2 = "uploads/profile/".$product_id."-".time().".jpg";
                        file_put_contents($img2, file_get_contents($url2));
                        $img_name2=url($img2);
                        $product_img = new ProductImages;
                        $product_img->image = $img_name2;
                        $product_img->product_id = $product_id;
                        $product_img->save();
                    }
                }

                $url3 = $row[18];
                if($url3!='')
                {
                    $handle3 = @fopen($url3, 'r');
                    if($handle3){
                        $img3 = "uploads/profile/".$product_id."-".time().".jpg";
                        file_put_contents($img3, file_get_contents($url3));
                        $img_name3=url($img3);
                        $product_img = new ProductImages;
                        $product_img->image = $img_name3;
                        $product_img->product_id = $product_id;
                        $product_img->save();
                    }
                }

                $url4 = $row[19];
                if($url4!='')
                {
                    $handle4 = @fopen($url4, 'r');
                    if($handle4){
                        $img4 = "uploads/profile/".$product_id."-".time().".jpg";
                        file_put_contents($img4, file_get_contents($url4));
                        $img_name4=url($img4);
                        $product_img = new ProductImages;
                        $product_img->image = $img_name4;
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
				$product_info->price_irl = $prices['irl'];
				$product_info->price_ger = $prices['ger'];
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
					$product_info->price_irl = $prices['irl'];
					$product_info->price_ger = $prices['ger'];
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
					$data['price_irl'] = $prices['irl'];
					$data['price_ger'] = $prices['ger'];
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
    }
	public function rules(): array
    {
        return [
			'0' => 'required',
			'1' => 'required',
			'2' => 'required',
			'3' => 'required',
			'7' => 'required',
            '6' => 'required|integer',
			'8' => 'required|integer',
			'9' => 'required|integer'
        ];
    }
	public function customValidationMessages()
	{
    return [
	'0' => 'Title is required',
	'1' => 'Description is required',
	'2' => 'Tags is required',
	'3' => 'Category is required',
	'6' => 'Price must be in interger number',
	'7' => 'SKU is required',
        '8' => 'Weight must be in interger number',
		'9' => 'Stock must be in interger number',
    ];
	}
}
