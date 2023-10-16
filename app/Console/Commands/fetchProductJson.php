<?php

namespace App\Console\Commands;

use App\Models\Log;
use App\Models\ProductType;
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

class fetchProductJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:jsonproduct';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'fetch yellow verandha products';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        /*set_time_limit(0);
            $context = stream_context_create(
                array(
                    "http" => array(
                        "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36"
                    )
                )
            );
			$url='yellowverandah.in';
            $str_cnt=file_get_contents("https://".$url."/collections/all/count.json", false, $context);
            $arr_1=json_decode($str_cnt,true);
            $page_total=$arr_1['collection']['products_count'];
			if($page_total > 0)
			{
				if($page_total > 250)
					$page_count=ceil($page_total/250);
				else
					$page_count=1;
            for($i=1;$i<=$page_count;$i++)
            {
				$str=file_get_contents("https://".$url."/collections/all/products.json?page=".$i."&limit=250", false, $context);
                $arr=json_decode($str,true);
				if($i==1)
				{
					$vendor=$arr['products'][0]['vendor'];
					$store_info=Store::where('name',$vendor)->first();
					if($store_info)
					{
						$vid=$store_info->id;
						ProductInfo::where('vendor_id', $vid)->update(['stock' => '0']);
					}
					//DB::table('tests')->insert(array('name' => 'start'));
					//$this->saveStoreFetchProductsFromJson($arr['products'],$vid);
				}
				//$str=$i."==".$page_count."===".$vid;
				//DB::table('tests')->insert(array('name' => $str));
				$this->saveStoreFetchProductsFromJson($arr['products'],$vid);
            }
			}*/
        //return Command::SUCCESS;


            set_time_limit(0);
            $context = stream_context_create(
                array(
                    "http" => array(
                        "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36"
                    )
                )
            );

	   $vendor_data=DB::table('cron_json_url')->get();

        if(count($vendor_data) > 0) {
            $log = new Log();
            $log->name = 'Fetch Product From Json';
            $log->date = date("F j, Y g:i a");
            $log->status = 'In-Progress';
            $log->save();
            try{
            foreach ($vendor_data as $val) {

                $vid = $val->vendor_id;
                $url = $val->url;

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

                        $this->saveStoreFetchProductsFromJson($product, $vid, '');
                        //echo "updated";
                        //return back()->with('success','Product imported successfully');

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

    }



//    function saveStoreFetchProductsFromJson($products,$vid,$tag_url=null)
//	{
//		//echo "<pre>"; print_r($products); die;
//		foreach($products as $row)
//		{
//			$pid=0;
//			foreach($row['variants'] as $var)
//			{
//				$check=ProductInfo::where('sku',$var['sku'])->first();
//				if ($check)
//				{
//					$pid=$check->product_id;
//				}
//			}
//			//echo $pid; die;
//			if($pid==0)  ////////New Product
//			{
//			$cat=Category::where('category',$row['product_type'])->first();
//            if($cat)
//				$category_id=$cat->id;
//            else
//                {
//                    $cate_que = new Category;
//                    $cate_que->category = $row['product_type'];
//                    $cate_que->save();
//                    $category_id=$cate_que->id;
//                }
//			$shopify_id=$row['id'];
//			$title=$row['title'];
//			$description=$row['body_html'];
//			$vendor=$row['vendor'];
//			$tags=implode(",",$row['tags']);
//			$handle=$row['handle'];
//			$store_id=$vid;
//			// $pInfo=Product::where('shopify_id', $shopify_id)->first();
//			// if(!$pInfo)
//			// {
//				$product = new Product;
//				$product->title = $title;
//				$product->body_html = $description;
//				$product->vendor = $store_id;
//				$product->tags = $tags;
//				$product->category = $category_id;
//				$product->save();
//				$product_id=$product->id;
//			// }
//			// else
//			// {
//				// $product_id=$pInfo->id;
//			// }
//			$i=0;
//
//			foreach($row['variants'] as $var)
//			{
//				$i++;
//				$check=ProductInfo::where('sku',$var['sku'])->exists();
//				if (!$check)
//				{
//					$prices=Helpers::calc_price_fetched_products($var['price'],$var['grams']);
//					$product_info = new ProductInfo;
//					$product_info->product_id = $product_id;
//					$product_info->sku = $var['sku'];
//					$product_info->price = $prices['inr'];
//					$product_info->price_usd = $prices['usd'];
//					$product_info->price_nld = $prices['nld'];
//					$product_info->price_gbp = $prices['gbp'];
//					$product_info->price_cad = $prices['cad'];
//					$product_info->price_aud = $prices['aud'];
//					$product_info->price_irl = $prices['nld'];
//					$product_info->price_ger = $prices['nld'];
//					$product_info->base_price = $prices['base_price'];
//					$product_info->grams = $var['grams'];
//					$product_info->stock = $var['available'];
//					$product_info->vendor_id = $store_id;
//					$product_info->dimensions = '0-0-0';
//					$product_info->varient_name = isset($row['options'][1]['name'])?$row['options'][1]['name']:$row['options'][0]['name'];
//					$product_info->varient_value = isset($var['option2'])?$var['option2']:$var['option1'];
//					$product_info->save();
//				}
//			}
//			if($i>1)
//			{
//				Product::where('id', $product_id)->update(['is_variants' => 1]);
//			}
//			foreach($row['images'] as $img_val)
//                        {
//							$imgCheck=ProductImages::where('image_id',$img_val['id'])->exists();
//							if (!$imgCheck)
//							{
//								$url = $img_val['src'];
//								$img = "uploads/shopifyimages/".$img_val['id'].".jpg";
//								file_put_contents($img, file_get_contents($url));
//								$img_name=url($img);
//								$product_img = new ProductImages;
//								$product_img->image = $img_name;
//								//$product_img->image_id = $img_val['id'];
//								$product_img->product_id = $product_id;
//								$product_img->save();
//							}
//                        }
//			}
//			else  //Existing Product
//			{
//				$data['title']=$row['title'];
//				$data['body_html']=$row['body_html'];
//				$data['tags']=implode(",",$row['tags']);
//				Product::where('id', $pid)->update($data);
//				$product_id=$pid;
//			$i=0;
//
//			foreach($row['variants'] as $var)
//			{
//				$i++;
//				$check_info=ProductInfo::where('sku',$var['sku'])->first();
//				if (!$check_info)
//				{
//					$prices=Helpers::calc_price_fetched_products($var['price'],$var['grams']);
//					$product_info = new ProductInfo;
//					$product_info->product_id = $product_id;
//					$product_info->sku = $var['sku'];
//					$product_info->price = $prices['inr'];
//					$product_info->price_usd = $prices['usd'];
//					$product_info->price_nld = $prices['nld'];
//					$product_info->price_gbp = $prices['gbp'];
//					$product_info->price_cad = $prices['cad'];
//					$product_info->price_aud = $prices['aud'];
//					$product_info->price_irl = $prices['nld'];
//					$product_info->price_ger = $prices['nld'];
//					$product_info->base_price = $prices['base_price'];
//					$product_info->grams = $var['grams'];
//					$product_info->stock = $var['available'];
//					$product_info->vendor_id = $vid;
//					$product_info->dimensions = '0-0-0';
//					$product_info->varient_name = isset($row['options'][1]['name'])?$row['options'][1]['name']:$row['options'][0]['name'];
//					$product_info->varient_value = isset($var['option2'])?$var['option2']:$var['option1'];
//					$product_info->save();
//				}
//				else   //update variants
//				{
//					$prices=Helpers::calc_price_fetched_products($var['price'],$var['grams']);
//					$info_id=$check_info->id;
//					$info['price']=$prices['inr'];
//					$info['price_usd']=$prices['usd'];
//					$info['price_nld']=$prices['nld'];
//					$info['price_gbp']=$prices['gbp'];
//					$info['price_cad']=$prices['cad'];
//					$info['price_aud']=$prices['aud'];
//					$info['price_irl']=$prices['nld'];
//					$info['price_ger']=$prices['nld'];
//					$info['base_price']=$prices['base_price'];
//					$info['grams']=$var['grams'];
//					$info['stock']=$var['available'];
//					$info['varient_name']=$row['title'];
//					$info['varient_value']=$var['option1'];
//					ProductInfo::where('id', $info_id)->update($info);
//				}
//			}
//			if($i>1)
//			{
//				Product::where('id', $product_id)->update(['is_variants' => 1]);
//			}
//			foreach($row['images'] as $img_val)
//                        {
//							$imgCheck=ProductImages::where('image_id',$img_val['id'])->exists();
//							if (!$imgCheck)
//							{
//								$url = $img_val['src'];
//								$img = "uploads/shopifyimages/".$img_val['id'].".jpg";
//								file_put_contents($img, file_get_contents($url));
//								$img_name=url($img);
//								$product_img = new ProductImages;
//								$product_img->image = $img_name;
//								//$product_img->image_id = $img_val['id'];
//								$product_img->product_id = $product_id;
//								$product_img->save();
//							}
//                        }
//			}
//		}
//	}


    function saveStoreFetchProductsFromJson($products,$vid,$tag_url=null)
    {


        //echo "<pre>"; print_r($products); die;
        foreach($products as $index=> $row)
        {



            $product_check=Product::where('title',$row['title'])->where('vendor',$vid)->first();

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
                $description=$row['body_html'];
                $vendor=$row['vendor'];
                $tags=implode(",",$row['tags']);
                $handle=$row['handle'];
                $store_id=$vid;

                $product = new Product;
                $product->title = $title;
                $product->body_html = $description;
                $product->vendor = $store_id;
                $product->tags = $tags;
                $product->orignal_vendor = $vendor;
                $product->category = $category_id;
                $product->product_type_id=$product_type->id;
                $product->save();
                $product_id=$product->id;

                $i=0;

                foreach($row['variants'] as $var)
                {

                    $i++;
                    $check=ProductInfo::where('sku',$var['sku'])->where('product_id',$product_id)->first();


                    if ($check==null)
                    {
                        $prices=Helpers::calc_price_fetched_products($var['price'],$var['grams']);
                        $product_info = new ProductInfo;
                        $product_info->product_id = $product_id;
                        $product_info->sku = $var['sku'];
                        $product_info->price = $prices['inr'];
                        $product_info->price_usd = $prices['usd'];
                        $product_info->price_nld = $prices['nld'];
                        $product_info->price_gbp = $prices['gbp'];
                        $product_info->price_cad = $prices['cad'];
                        $product_info->price_aud = $prices['aud'];
                        $product_info->price_irl = $prices['nld'];
                        $product_info->price_ger = $prices['nld'];
                        $product_info->base_price = $prices['base_price'];
                        $product_info->grams = $var['grams'];
                        $product_info->stock = $var['available'];
                        $product_info->vendor_id = $store_id;
                        $product_info->dimensions = '0-0-0';
                        if(isset($row['options'])){
                            $product_info->varient_name =$row['options'][0]['name'];
                        }

                        if(isset($row['options']) && isset($row['options'][1])){
                            $product_info->varient1_name =$row['options'][1]['name'];
                        }
                        $product_info->varient_value = $var['option1'];
                        $product_info->varient1_value= $var['option2'];
                        $product_info->save();
                    }
                }
                if($i>1)
                {
                    Product::where('id', $product_id)->update(['is_variants' => 1]);
                }
                foreach($row['images'] as $img_val)
                {
                    $imgCheck=ProductImages::where('image_id',$img_val['id'])->exists();
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
                $data['body_html']=$row['body_html'];
                $data['tags']=implode(",",$row['tags']);
                $data['product_type_id']=$product_type->id;
                $data['orignal_vendor'] = $vendor;
                Product::where('id',$product_check->id)->update($data);
                $product_id=$product_check->id;
                $i=0;

                foreach($row['variants'] as $var)
                {
                    $i++;
                    $check_info=ProductInfo::where('sku',$var['sku'])->first();
                    if (!$check_info)
                    {
                        $prices=Helpers::calc_price_fetched_products($var['price'],$var['grams']);
                        $product_info = new ProductInfo;
                        $product_info->product_id = $product_id;
                        $product_info->sku = $var['sku'];
                        $product_info->price = $prices['inr'];
                        $product_info->price_usd = $prices['usd'];
                        $product_info->price_nld = $prices['nld'];
                        $product_info->price_gbp = $prices['gbp'];
                        $product_info->price_cad = $prices['cad'];
                        $product_info->price_aud = $prices['aud'];
                        $product_info->price_irl = $prices['nld'];
                        $product_info->price_ger = $prices['nld'];
                        $product_info->base_price = $prices['base_price'];
                        $product_info->grams = $var['grams'];
                        $product_info->stock = $var['available'];
                        $product_info->vendor_id = $vid;
                        $product_info->dimensions = '0-0-0';
                        if(isset($row['options'])){
                            $product_info->varient_name =$row['options'][0]['name'];
                        }

                        if(isset($row['options']) && isset($row['options'][1])){
                            $product_info->varient1_name =$row['options'][1]['name'];
                        }
                        $product_info->varient_value = $var['option1'];
                        $product_info->varient1_value= $var['option2'];

                        $product_info->save();
                    }
                    else   //update variants
                    {
                        $prices=Helpers::calc_price_fetched_products($var['price'],$var['grams']);
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
                        $info['grams']=$var['grams'];
                        $info['stock']=$var['available'];


                        if(isset($row['options'])) {
                            $info['varient_name'] = $row['options'][0]['name'];
                        }
                        if(isset($row['options']) && isset($row['options'][1])){
                            $info['varient1_name'] =$row['options'][1]['name'];
                        }
                        $info['varient_value']=$var['option1'];
                        $info['varient1_value']=$var['option2'];
                        ProductInfo::where('id', $info_id)->update($info);
                    }
                }
                if($i>1)
                {
                    Product::where('id', $product_id)->update(['is_variants' => 1]);
                }
                foreach($row['images'] as $img_val)
                {
                    $imgCheck=ProductImages::where('image_id',$img_val['id'])->exists();
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





	/*function saveStoreFetchProductsFromJson($products,$vid)
	{
		foreach($products as $k=>$row)
		{
			//if($k==0)
			//{
			$pid=0;
			foreach($row['variants'] as $var)
			{
				$check=ProductInfo::where('sku',$var['sku'])->first();
				if ($check)
				{
					$pid=$check->product_id;
				}
				$str=$pid."===".$var['sku'];
				DB::table('tests')->insert(array('name' => $str));
			}

			if($pid==0)  ////////New Product
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
			$description=$row['body_html'];
			$vendor=$row['vendor'];
			$tags=implode(",",$row['tags']);
			$handle=$row['handle'];
			$store_id=$vid;
			if($store_id > 0)
			{
			$pInfo=Product::where('shopify_id', $shopify_id)->first();
			if(!$pInfo)
			{
				$product = new Product;
				$product->title = $title;
				//$product->handle = $handle;
				$product->body_html = $description;
				$product->vendor = $store_id;
				$product->tags = $tags;
				//$product->shopify_id = $shopify_id;
				$product->category = $category_id;
				//$product->status = 1;
				//$product->approve_date = Carbon::now();
				$product->save();
				$product_id=$product->id;
				//$this->linkProductToCollection($shopify_id,$store->collections_ids);
			}
			else
			{
				$product_id=$pInfo->id;
			}
			$i=0;

			foreach($row['variants'] as $var)
			{
				$i++;
				$check=ProductInfo::where('sku',$var['sku'])->exists();
				if (!$check)
				{
					$prices=Helpers::calc_price_fetched_products($var['price'],$var['grams']);
					$product_info = new ProductInfo;
					$product_info->product_id = $product_id;
					$product_info->sku = $var['sku'];
					$product_info->price = $prices['inr'];
					$product_info->price_usd = $prices['usd'];
					$product_info->price_nld = $prices['nld'];
					$product_info->price_gbp = $prices['gbp'];
					$product_info->price_cad = $prices['cad'];
					$product_info->price_aud = $prices['aud'];
					$product_info->base_price = $prices['base_price'];
					$product_info->grams = $var['grams'];
					$product_info->stock = $var['available'];
					$product_info->vendor_id = $store_id;
					$product_info->dimensions = '0-0-0';
					//$product_info->inventory_item_id = $var['inventory_item_id'];
					//$product_info->inventory_id = $var['id'];
					$product_info->varient_name = $row['options'][0]['name'];;
					$product_info->varient_value = $var['option1'];
					$product_info->save();
				}
			}
			if($i>1)
			{
				Product::where('id', $product_id)->update(['is_variants' => 1]);
			}
			foreach($row['images'] as $img_val)
                        {
							$imgCheck=ProductImages::where('image_id',$img_val['id'])->exists();
							if (!$imgCheck)
							{
								$url = $img_val['src'];
								$img = "uploads/shopifyimages/".$img_val['id'].".jpg";
								file_put_contents($img, file_get_contents($url));
								$img_name=url($img);
								$product_img = new ProductImages;
								$product_img->image = $img_name;
								//$product_img->image_id = $img_val['id'];
								$product_img->product_id = $product_id;
								$product_img->save();
							}
                        }
			}
			}
			else  //Existing Product
			{
				$data['title']=$row['title'];
				$data['body_html']=$row['body_html'];
				$data['tags']=implode(",",$row['tags']);
				Product::where('id', $pid)->update($data);
				//$store=Store::where('name',$row['vendor'])->first();
				//$this->linkProductToCollection($row['id'],$store->collections_ids);
				$product_id=$pid;
			$i=0;

			foreach($row['variants'] as $var)
			{
				$i++;
				$check_info=ProductInfo::where('sku',$var['sku'])->first();
				if (!$check_info)
				{
					$prices=Helpers::calc_price_fetched_products($var['price'],$var['grams']);
					$product_info = new ProductInfo;
					$product_info->product_id = $product_id;
					$product_info->sku = $var['sku'];
					$product_info->price = $prices['inr'];
					$product_info->price_usd = $prices['usd'];
					$product_info->price_nld = $prices['nld'];
					$product_info->price_gbp = $prices['gbp'];
					$product_info->price_cad = $prices['cad'];
					$product_info->price_aud = $prices['aud'];
					$product_info->base_price = $prices['base_price'];
					$product_info->grams = $var['grams'];
					$product_info->stock = $var['available'];
					$product_info->vendor_id = $vid;
					$product_info->dimensions = '0-0-0';
					//$product_info->inventory_item_id = $var['inventory_item_id'];
					//$product_info->inventory_id = $var['id'];
					$product_info->varient_name = $row['options'][0]['name'];;
					$product_info->varient_value = $var['option1'];
					$product_info->save();
				}
				else   //update variants
				{
					$prices=Helpers::calc_price_fetched_products($var['price'],$var['grams']);
					$info_id=$check_info->id;
					$info['price']=$prices['inr'];
					$info['price_usd']=$prices['usd'];
					$info['price_nld']=$prices['nld'];
					$info['price_gbp']=$prices['gbp'];
					$info['price_cad']=$prices['cad'];
					$info['price_aud']=$prices['aud'];
					$info['base_price']=$prices['base_price'];
					$info['grams']=$var['grams'];
					$info['stock']=$var['available'];
					//$info['inventory_item_id']=$var['inventory_item_id'];
					//$info['inventory_id']=$var['id'];
					$info['varient_name']=$row['title'];
					$info['varient_value']=$var['option1'];
					ProductInfo::where('id', $info_id)->update($info);
				}
			}
			if($i>1)
			{
				Product::where('id', $product_id)->update(['is_variants' => 1]);
			}
			foreach($row['images'] as $img_val)
                        {
							$imgCheck=ProductImages::where('image_id',$img_val['id'])->exists();
							if (!$imgCheck)
							{
								$url = $img_val['src'];
								DB::table('tests')->insert(array('name' => $url));
								$img = "https://demo25.iitpl.com/uploads/shopifyimages/".$img_val['id'].".jpg";
								file_put_contents($img, file_get_contents($url));
								$img_name=url($img);
								$product_img = new ProductImages;
								$product_img->image = $img_name;
								//$product_img->image_id = $img_val['id'];
								$product_img->product_id = $product_id;
								$product_img->save();
							}
                        }
			}
		//}
		}
		//DB::table('tests')->insert(array('name' => 'OK'));
	}*/
}
