<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use App\Models\Test;
use App\Models\Product;
use App\Models\Store;
use App\Models\ProductInfo;
use App\Models\ProductImages;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Validator;
use Auth;
use App\Helpers\Helpers;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
class BluckProductImport implements ToCollection, WithHeadingRow,WithValidation
{
    /**
    * @param Collection $collection
    */
 public function startRow(): int
    {
        return 2;
    }
    
    
      public function collection(Collection $collection)
    {
    
     $result= collect($collection)
            ->groupBy('handle')
            ->map(function ($group) {
                return [
                     'product_id'=> $group[0]['product_id'],
                    'handle' => $group[0]['handle'],
                    'title' => $group[0]['title'],
                    'body_html' => $group[0]['body_html'],
                    'vendor' => $group[0]['vendor'],
                   'product_category' => $group[0]['product_category'],
                    'tags'=>$group[0]['tags'],
                   'variant_inventory_item_id'=>$group[0]['variant_inventory_item_id'],
                    'variant_id'=>$group[0]['variant_id'],
                   'image_src' => $group[0]['image_src'],
                  
                   /// 'summary'=>$group[0]['summary'],
                   // 'photo' => $group[0]['photo'],
                    // 'is_featured'=>$group[0]['is_featured'],
                    'variant_res' => $group
                ];
            })
            ->values()
            ->all();
            
         // dd($result);
            
            
             foreach($result as $value){
              if(!empty($value['product_id']) && !empty($value['product_category']) ){
                $variant_data=$value['variant_res'];
                if(sizeof($variant_data)> 0){ $is_variant=1;}else{$is_variant=0; }
                
		$check_category=Category::where('category',$value['product_category'])->first();
		if($check_category == null)
		{
		$category_que = new Category;
		$category_que->category = $value['product_category'];
		$category_que->save();
		$category_id=$category_que->id;
		}
		else
		{
		$category_id=$check_category->id;
		}
		
		$get_vendor_id=Store::where('name',$value['vendor'])->first();
                
                $product = Product::updateOrCreate(['shopify_id'=>$value['product_id']],[
                                'handle' => $value['handle'],
                                 'vendor' =>32,
                                'approve_date'=>date('Y-m-d'),
                                 'status'=>1,
                                 'vendor' => $get_vendor_id->id,
                                'category' => $category_id,
                                'title' => $value['title'],
                                'tags' => $value['tags'],
                                'is_variants'=>$is_variant,
                                'body_html'=>$value['body_html'],
                                
                            ]);

                            foreach($variant_data as $variant_val){
                            
                            
                        $url = $variant_val['image_src'];
			if($url!='')
			{
				$handle = @fopen($url, 'r');
				if($handle){
					$img = "uploads/profile/".$product->id."-".time().".jpg";
					file_put_contents($img, file_get_contents($url));
					$img_name=url($img);
					
				}				
			}
                            
                             
                            
			if(!empty($variant_val['inventory_id'])){
			$product_details=ProductInfo::updateOrCreate(
			['inventory_id' => $variant_val['variant_id'],'product_id'=>$product->id],
			['price_status'=>1,'grams'=>$variant_val['variant_grams'],'stock'=>$variant_val['variant_inventory_qty'],'sku'=>$variant_val['variant_sku'],'price' => $variant_val['price_inr'],'base_price'=>$variant_val['base_price_in_inr'],'discounted_inr'=>$variant_val['compare_at_price_inr'],'inventory_item_id'=>$variant_val['variant_inventory_item_id'],'inventory_id'=>$variant_val['variant_id'],'vendor_id'=> $get_vendor_id->id,'price_usd'=>$variant_val['variant_price'],'discounted_usd'=>$variant_val['variant_compare_at_price'],'price_gbp'=>$variant_val['price_gbp'],'discounted_gbp'=>$variant_val['compare_at_price_gbp'],'price_nld'=>$variant_val['price_nld'],'discounted_nld'=>$variant_val['compare_at_price_nld'],'price_cad'=>$variant_val['price_cad'],'discounted_cad'=>$variant_val['compare_at_price_cad'],'price_aud'=>$variant_val['price_aud'],'discounted_aud'=>$variant_val['compare_at_price_aud'],'varient_name'=>$variant_val['option1_name'],'varient_value'=>$variant_val['option1_value']]);


			$product_image=ProductImages::updateOrCreate(
			['product_id' =>$product->id,'variant_ids'=>$product_details->id],
			['image' => $img_name]);

			}
                          
             }
             }     
     
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
