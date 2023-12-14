<?php

namespace App\Http\Controllers;

use App\Imports\BluckProductImport;
use App\Jobs\UploadBulkProducts;
use App\Models\ProductChange;
use App\Models\ProductImagesNew;
use App\Models\ProductType;
use App\Models\Setting;
use App\Models\VariantChange;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use App\Models\Product;
use Auth;
use App\Models\Category;
use App\Models\ProductInfo;
use App\Models\Store;
use App\Models\ProductImages;
use App\Models\Banner;
use DB;
use App\Exports\ProductExport;
use App\Imports\ProductImport;
use App\Imports\InventoryImport;
use Maatwebsite\Excel\Facades\Excel;
use Session;
use App\Helpers\Helpers;
use App\Models\ProductInventoryLocation;
use Carbon\Carbon;

class ProductController extends Controller
{



	public function demoTestProduct()
	{




        $p_w=new ProductImagesNew();
        $p_w->product_id=2;
        $p_w->save();

        dd($p_w);

        $product_img = new ProductImages();
        $product_img->image = 'dsds';
        $product_img->product_id = 2;
        $product_img->save();
        dd($product_img);



		echo "hiii";
		$products_price=1039;
		$products_grams=200;
		$prices=Helpers::calc_price_fetched_products($products_price,$products_grams);
		echo "<pre>"; print_r($prices);
		die;
        $data=ProductInfo::join('product_master', 'product_master.id', 'products_variants.product_id')
		->select('product_master.tags','products_variants.*')
		->where('price_conversion_update_status', 1)->get();
		foreach($data as $row)
		{
			$volumetric_Weight=0;
			$arr=explode("-",$row->dimensions);
			if(is_numeric($arr[0]) && is_numeric($arr[1]) && is_numeric($arr[2]))
				$volumetric_Weight=$arr[0] * $arr[1] * $arr[2]/5000;
			$prices=Helpers::calc_price_new($row->base_price,$row->grams,$row->tags,$volumetric_Weight);
			if($prices)
			{
				ProductInfo::where('id', $row->id)->update(['price_status' => 0, 'price_conversion_update_status' => 0, 'price' => $prices['inr'], 'price_usd' => $prices['usd'], 'price_aud' => $prices['aud'], 'price_cad' => $prices['cad'], 'price_gbp' => $prices['gbp'], 'price_nld' => $prices['nld']]);
			}
		}
	}

	public function shopifyProductTest()
	{
		set_time_limit(0);
		$get_products_details=$this->getProducts($page_info='');
		$get_products_details_arr=explode("\n",$get_products_details);
		$link=explode("&",$get_products_details_arr[17]);
		$lnk=$link[1];
		$lnk2=explode(">",$lnk);
		$lnk_2=$lnk2[0];
		$get_products_response_json=end($get_products_details_arr);
		$pr_d=json_decode($get_products_response_json,true);
		$products[]=$pr_d["products"];
		$this->saveStoreFetchProducts($pr_d["products"]);
		// foreach($pr_d["products"] as $product)
		// {
			// echo $product['id']; echo "<br>";
		// }
		$count=$this->getProductsCount();
		$pages = ceil($count / 250);
		for($i=1; $i<$pages; $i++)
		{
			$page_info=$lnk_2;
			$get_products_details_arr=array();
			$pr_d=array();
			$link1=array();
			$link_2=array();
			$lnkk2=array();
			$get_products_details=$this->getProducts($page_info);
			$get_products_details_arr=explode("\n",$get_products_details);

			$get_products_response_json=end($get_products_details_arr);
			$pr_d=json_decode($get_products_response_json,true);
			$this->saveStoreFetchProducts($pr_d["products"]);

			$link1=explode(",",$get_products_details_arr[17]);
			if(isset($link1[1])) {
			$lnk2=$link1[1];
			$link_2=explode("&",$lnk2);
			$lnk=$link_2[1];
			$lnkk2=explode(">",$lnk);
			$lnk_2=$lnkk2[0];
			$page_info=$lnk_2;
			}
			else
			{
				$data=Product::all();
				foreach($data as $row)
				{
					$id=$row->id;
					$v=ProductInfo::where('product_id', $id)->count();
					if($v==0) {
						Product::where('id', $id)->delete();
					}
				}
				echo "Product Sync Successfully Completed....";
			}
		}
	}
	function saveStoreFetchProducts($products)
	{
		foreach($products as $row)
		{
			$pid=0;
			foreach($row['variants'] as $var)
			{
				$check=ProductInfo::where('sku',$var['sku'])->first();
				if ($check)
				{
					$pid=$check->product_id;
				}
			}
			if($pid==0)  ////////New Product
			{
			//echo $row['vendor']; echo "<br>";
			$shopify_id=$row['id'];
			$title=$row['title'];
			$description=$row['body_html'];
			$vendor=$row['vendor'];
			$tags=$row['tags'];
			$handle=$row['handle'];
			$store=Store::where('name',$vendor)->first();
			$store_id=0;
            if($store)
            {
                $store_id=$store->id;
            }
			if($store_id > 0)
			{
			$pInfo=Product::where('shopify_id', $shopify_id)->first();
			if(!$pInfo)
			{
				$product = new Product;
				$product->title = $title;
				$product->handle = $handle;
				$product->body_html = $description;
				$product->vendor = $store_id;
				$product->tags = $tags;
				$product->shopify_id = $shopify_id;
				$product->category = '';
				$product->status = 1;
				$product->approve_date = Carbon::now();
				$product->save();
				$product_id=$product->id;
				$this->linkProductToCollection($shopify_id,$store->collections_ids);
			}
			else
			{
				$product_id=$pInfo->id;
			}
			$i=0;

			foreach($row['variants'] as $var)
			{
				if($var['sku']!='')
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
					$product_info->price_irl = $prices['nld'];
					$product_info->price_ger = $prices['nld'];
					$product_info->base_price = $prices['base_price'];
					$product_info->grams = $var['grams'];
					$product_info->stock = $var['inventory_quantity'];
					$product_info->vendor_id = $store_id;
					$product_info->dimensions = '0-0-0';
					$product_info->inventory_item_id = $var['inventory_item_id'];
					$product_info->inventory_id = $var['id'];
					$product_info->varient_name = $row['options'][0]['name'];
					$product_info->varient_value = $var['option1'];
					$product_info->save();
				}
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
								$product_img->image_id = $img_val['id'];
								$product_img->product_id = $product_id;
								$product_img->save();
							}
                        }
			}
			}
			else  //Existing Product
			{
				$data['shopify_id']=$row['id'];
				$data['title']=$row['title'];
				$data['body_html']=$row['body_html'];
				$data['tags']=$row['tags'];
				$data['handle']=$row['handle'];
				$data['status']=1;
				$data['approve_date']=Carbon::now();
				Product::where('id', $pid)->update($data);
				$store=Store::where('name',$row['vendor'])->first();
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
					$product_info->sku = $var['sku']??'';
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
					$product_info->stock = $var['inventory_quantity'];
					$product_info->vendor_id = $store_id;
					$product_info->dimensions = '0-0-0';
					$product_info->inventory_item_id = $var['inventory_item_id'];
					$product_info->inventory_id = $var['id'];
					$product_info->varient_name = $row['options'][0]['name'];
					$product_info->varient_value = $var['option1'];
					$product_info->save();
				}
				else   //update variants
				{
					//$prices=Helpers::calc_price_fetched_products($var['price'],$var['grams']);
					$info_id=$check_info->id;
					//$info['price']=$prices['inr'];
					//$info['price_usd']=$prices['usd'];
					//$info['price_nld']=$prices['nld'];
					//$info['price_gbp']=$prices['gbp'];
					//$info['price_cad']=$prices['cad'];
					//$info['price_aud']=$prices['aud'];
					//$info['base_price']=$prices['base_price'];
					$info['grams']=$var['grams'];
					$info['stock']=$var['inventory_quantity'];
					$info['inventory_item_id']=$var['inventory_item_id'];
					$info['inventory_id']=$var['id'];
					$info['varient_name']=$row['options'][0]['name'];
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
								$img = "uploads/shopifyimages/".$img_val['id'].".jpg";
								file_put_contents($img, file_get_contents($url));
								$img_name=url($img);
								$product_img = new ProductImages;
								$product_img->image = $img_name;
								$product_img->image_id = $img_val['id'];
								$product_img->product_id = $product_id;
								$product_img->save();
							}
                        }
			}
		}
	}
	public function linkProductToCollection($product_id,$collection_id)
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

        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2020-04/collects.json";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $SHOPIFY_API);
        $headers = array(
            "Authorization: Basic ".base64_encode("$API_KEY:$PASSWORD"),
            "Content-Type: application/json",
            "X-Shopify-Api-Features: include-presentment-prices",
            "charset: utf-8"
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER,$headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_VERBOSE, 0);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($curl, CURLOPT_POSTFIELDS, '{"collect":{"product_id":'.$product_id.',"collection_id":'.$collection_id.'}}');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec ($curl);
        curl_close ($curl);
	}
	function getProducts($page_info)
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

			if($page_info=='')
				$SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/products.json?limit=250";
			else
				$SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/products.json?limit=250&".$page_info."";
			$header=array(
				"Authorization: Basic ".base64_encode("$API_KEY:$PASSWORD"),
				"Content-Type: application/json",
				"charset: utf-8"
				);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $SHOPIFY_API);
			curl_setopt($ch, CURLOPT_HEADER,TRUE);
			curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_VERBOSE, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			$response = curl_exec($ch);
			curl_close($ch);
			return($response);
		}
	function getProductsCount()
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

				$SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/products/count.json";
			$header=array(
				"Authorization: Basic ".base64_encode("$API_KEY:$PASSWORD"),
				"Content-Type: application/json",
				"charset: utf-8"
				);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $SHOPIFY_API);
			curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_VERBOSE, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			$response = curl_exec($ch);
			curl_close($ch);
			$result=json_decode($response, true);
			return $result['count'];
		}




	public function shopifyProductSync($cid)
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

        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/products.json?collection_id=$cid&limit=250";
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
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec ($curl);
        curl_close ($curl);
        $result=json_decode($response,true);
		//echo "<pre>"; print_r($result);
		foreach($result['products'] as $row)
		{
			$shopify_id=$row['id'];
			$title=$row['title'];
			$description=$row['body_html'];
			$vendor=$row['vendor'];
			$tags=$row['tags'];
			$handle=$row['handle'];
			$store=Store::where('name',$vendor)->first();
            if($store)
            {
                $store_id=$store->id;
            }
			if($store_id > 0)
			{
			$pInfo=Product::where('shopify_id', $shopify_id)->first();
			if(!$pInfo)
			{
				$product = new Product;
				$product->title = $title;
				$product->handle = $handle;
				$product->body_html = $description;
				$product->vendor = $store_id;
				$product->tags = $tags;
				$product->shopify_id = $shopify_id;
				$product->category = '';
				$product->status = 1;
				$product->save();
				$product_id=$product->id;
			}
			else
			{
				$product_id=$pInfo->id;
			}
			$i=0;
			foreach($row['variants'] as $var)
			{
				$i++;
				$check=ProductInfo::where('inventory_id',$var['id'])->exists();
				if (!$check)
				{
					$product_info = new ProductInfo;
					$product_info->product_id = $product_id;
					$product_info->sku = $var['sku'];
					$product_info->price = $var['price'];
					$product_info->grams = $var['grams'];
					$product_info->stock = $var['inventory_quantity'];
					$product_info->vendor_id = $store_id;
					$product_info->dimensions = '0-0-0';
					$product_info->inventory_item_id = $var['inventory_item_id'];
					$product_info->inventory_id = $var['id'];
					$product_info->varient_name = $row['options'][0]['name'];
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
								$product_img->image_id = $img_val['id'];
								$product_img->product_id = $product_id;
								$product_img->save();
							}
                        }
			}
			//echo $row['id']."===".$row['$row['id'];'];
			//echo "<br>";
		}
		echo "completd";
    }
    public function cronInventoryUpdate()
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

        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2020-04/inventory_levels/set.json";
        $product_inv = ProductInfo::where('stock', '>', 0)->whereNotNull('inventory_item_id')->get();
        foreach($product_inv as $row)
        {
            $data=array(
                'location_id' => '62600577199',
                'inventory_item_id' => $row['inventory_item_id'],
                'available_adjustment' => $row['stock']
            );
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
            //curl_setopt($curl, CURLOPT_HEADER, 1);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

            $response = curl_exec ($curl);
            curl_close ($curl);
        }
    }
    public function cronUpdateInventory()
    {
        $inventory_id=$data[0]->inventory_id;
        $data['variant']=array(
                    "id" => $inventory_id,
                    "option1" => 'newoption',
                    "price"   => 200,
                    "sku"   => 'vvvvvvvvv',
                );
            $product_id=$data[0]->shopify_id;
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

            $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-04//variants/$inventory_id.json";
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
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            curl_setopt($curl, CURLOPT_POSTFIELDS,json_encode($data));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

            $response = curl_exec ($curl);
            curl_close ($curl);
    }
    public function testinv()
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


        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/price_rules.json";
        $data['price_rule']=array(
            'title' => 'OMTest',
            'target_type' => 'line_item',
            'target_selection' => 'entitled',
            'allocation_method' => 'across',
            'customer_selection' => 'all',
            'value_type' => 'percentage',
            'value' => '-5',
            'starts_at' => '2022-12-18T10:00:00Z',
            'entitled_product_ids' => ['7373527842991'],
            'entitled_variant_ids' => [],
            'entitled_collection_ids' => [],
        );
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
        //curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec ($curl);
        curl_close ($curl);
        $result=json_decode($response, true);
        echo "<pre>"; print_r($result); die();
    }
    public function testcode()
    {
        $data['product']=array(
                    "id" => 7426712764591,
                    "title" => 'om new title',
                    "tags"   => 'hhhh,bbbb',
                );
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
        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/products/7426712764591.json";
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
        //curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec ($curl);
        curl_close ($curl);
        $result=json_decode($response, true);
        echo "<pre>"; print_r($result); die();
    }
	public function exportProductView()
    {
        return view('subadmin.export-product');
    }
    public function exportProduct()
    {
        return Excel::download(new ProductExport, 'products.xlsx');
    }
    public function importProduct(Request $request)
    {
    	$request->validate([
            'file'=>'required|mimes:xlsx,csv',
        ]);

        if(\Illuminate\Support\Facades\Auth::user()->role=='Vendor')
            $vid=\Illuminate\Support\Facades\Auth::user()->id;
        else
            $vid=\Illuminate\Support\Facades\Auth::user()->vendor_id;


        $file=request()->file('file');
        $name = str_replace(' ', '', $file->getClientOriginalName());
        $name = "vendor_file". $name;
        $file->move(public_path() . '/', $name);
        $hashName =  public_path($name);
        UploadBulkProducts::dispatch($hashName,$vid);
//        Excel::import(new ProductImport,request()->file('file'));

                return redirect()->back()->with('success', 'Import In Progress');
//        return back()->with('success', 'Excel file imported successfully!');
    }
    public function importInventory()
    {
        Excel::import(new InventoryImport,request()->file('file'));

        return back()->with('success', 'Excel file imported successfully!');
    }
    public function importProductView()
    {
       return view('subadmin.import-product');
    }
    public function productview(){
      $id = Auth::id();
    	$category = Category::all();
    	return view('subadmin.add-product',compact('category'));
    }

    public function saveproduct(Request $request){
      //echo "<pre>"; print_r($request->file()); die();
        if($request->hasfile('profile')){
            $file = $request->file('profile');
//            $extension = $file->getClientOriginalExtension();
//            $filename = time().'.'.$extension;
            $filename = time() . '.jpg';
            $file->move('uploads/profile/',$filename);
            $product = new ProductImages;
            $product->image = url('uploads/profile/'.$filename);
            $product->save();
            $product_id=$product->id;
            $response['success'] = true;
            $response['message'] = $product_id;
          }
        return json_encode($response);
  }
   public function vendorId(){
       if(Auth::user()->role=='Vendor')
            $vendor_id=Auth::user()->id;
       else
           $vendor_id=Auth::user()->vendor_id;
       return $vendor_id;
   }
   public function saveproducts(Request $request){
     $input = $request->all();


    if($request->payradious =='1'){
       $this->validate($request,([
        'name'=>'required',
        'description'=>'required',
        'tags'=>'required',
		'varient_name.0' => 'required',
		'varient_value.0' => 'required',
		'varient_price.0' => 'required',
		'varient_sku.0' => 'required',
		'varient_grams.0' => 'required',
//		'varient_quantity.0' => 'required'
       ]));
     }
     else{
       $request->validate([
        'name'=>'required',
        'description'=>'required',
        'tags'=>'required',
        'price'=>'required',
        'sku'=>'required',
        'grams'=>'required',
//        'quantity'=>'required',
        'category'=>'required',
        ]);
       }


        $vendor=$this->vendorId();
       $product_type_id=null;
       $category=Category::find($request->category);
       if($category) {
           $product_type = ProductType::where('product_type', $category->category)->where('vendor_id', $vendor)->first();
           if ($product_type == null) {
               $product_type = new ProductType();
           }
           $product_type->product_type = $category->category;
           $product_type->vendor_id = $vendor;
           $product_type->save();
           $product_type_id=$product_type->id;
       }


       $product = new Product;
        $product->title = $request->name;
        $product->body_html = $request->description;
        $product->vendor = $vendor;
        $product->tags = $request->tags;
        $product->is_variants = $request->payradious;
        $product->category = $request->category;
        $product->product_type_id = $product_type_id;
        $product->save();
        $product_id=$product->id;
		$Tags=explode(",",$request->tags);
            if(in_array("Saree",$Tags))
                $is_saree = 1;
            else
                $is_saree = 0;
		if(in_array("furniture",$Tags))
            {
                $is_furniture = 1;
                $volumetric_Weight = 10000/5000;
            }
            else
            {
                $is_furniture = 0;
                $volumetric_Weight = 0;
            }
        if($request->payradious!=1)
        {
            $grams=$request->grams;
            $pricing_weight=$grams;
            if($product_type && $product_type->base_weight){
                $pricing_weight=max($grams, $product_type->base_weight);
            }

			$volumetric_Weight = 0;
			if( $request->height!='' && $request->width!='' && $request->length!='')
				$volumetric_Weight = $request->height * $request->width * $request->length/5000;
            $product_info = new ProductInfo;
            $product_info->product_id = $product_id;
            $product_info->sku = $request->sku;
			$prices=Helpers::calc_price_new($request->price,$pricing_weight,$request->tags,$volumetric_Weight,$vendor);
			$product_info->price = $prices['inr'];
			$product_info->price_usd = $prices['usd'];
			$product_info->price_aud = $prices['aud'];
			$product_info->price_cad = $prices['cad'];
			$product_info->price_gbp = $prices['gbp'];
			$product_info->price_nld = $prices['nld'];
			$product_info->price_irl = $prices['irl'];
			$product_info->price_ger = $prices['ger'];
			$product_info->base_price = $request->price;
            $product_info->grams = $grams;
            $product_info->pricing_weight = $pricing_weight;
            $product_info->qty = $request->quantity;
            $product_info->stock = 1;
            $product_info->shelf_life = $request->shelf_life;
            $product_info->temp_require = $request->temp;
            $product_info->dimensions = $request->height.'-'.$request->width.'-'.$request->length;
            $product_info->vendor_id = $vendor;
            $product_info->save();
			$info=$product_info->id;
			///image
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = $info . "-" . time() . '.jpg'; // Force the extension to be jpg
                // Save the file
                $file->move('uploads/profile/', $filename);

                // Get the full path of the saved image
                $img_full_path = url('uploads/profile/' . $filename);

                // Update or create a record in the database
                ProductImages::updateOrCreate(['variant_ids' => $info],
                    ['image' => $img_full_path, 'product_id' => $product_id, 'variant_ids' => $info],
                );
            }

        }
        else
        {

            foreach($request->varient_name as $key => $value) {

                $grams=$request->varient_grams[$key];
                $pricing_weight=$grams;
                if($product_type && $product_type->base_weight){
                    $pricing_weight=max($grams, $product_type->base_weight);
                }

				$volumetric_Weight = 0;
			if( $request->varient_height[$key]!='' && $request->varient_width[$key]!='' && $request->varient_length[$key]!='')
				$volumetric_Weight = $request->varient_height[$key] * $request->varient_width[$key] * $request->varient_length[$key]/5000;
            $product_info = new ProductInfo;
            $product_info->product_id = $product_id;
            $product_info->vendor_id = Auth::user()->id;
            $product_info->sku = $request->varient_sku[$key];;
            $product_info->varient_name = $request->varient_name[$key];
            $product_info->varient_value = $request->varient_value[$key];
			//$prices=Helpers::($request->varient_price[$key],$request->varient_grams[$key],$is_saree,$is_furniture,$volumetric_Weight);
			$prices=Helpers::calc_price_new($request->varient_price[$key],$pricing_weight,$request->tags,$volumetric_Weight,$vendor);
			$product_info->price = $prices['inr'];
			$product_info->price_usd = $prices['usd'];
			$product_info->price_aud = $prices['aud'];
			$product_info->price_cad = $prices['cad'];
			$product_info->price_gbp = $prices['gbp'];
			$product_info->price_nld = $prices['nld'];
			$product_info->price_irl = $prices['irl'];
			$product_info->price_ger = $prices['ger'];
			$product_info->base_price = $request->varient_price[$key];
            $product_info->grams = $grams;
            $product_info->pricing_weight = $pricing_weight;
            $product_info->qty = $request->varient_quantity[$key];
            $product_info->shelf_life = $request->varient_shelf_life[$key];
            $product_info->temp_require = $request->varient_temp[$key];
            $product_info->stock = 1;
            $product_info->dimensions = $request->varient_height[$key].'-'.$request->varient_width[$key].'-'.$request->varient_length[$key];
            $product_info->save();
			$info=$product_info->id;


			if ($request->hasfile('imag')) {
			$file = $request->file('imag');
			if(isset($file[$key])) {
            $extension = $file[$key]->getClientOriginalExtension();
			if($extension!=''){
            $file = $file[$key];
//            $extension = $file->getClientOriginalExtension();
                $filename = $info . "-" . time() . '.jpg';
            $file->move('uploads/profile/',$filename);
			$img_full_path=url('uploads/profile/'.$filename);
			ProductImages::updateOrCreate(['variant_ids' => $info],
				['image' => $img_full_path, 'product_id' => $product_id, 'variant_ids' => $info],
			);
          }
			}
			}
       }
       }
       if($request->images!='')
       {
           $img_arr=explode(",",$request->images);
           foreach($img_arr as $v)
           {
               ProductImages::where('id', $v)->update(['product_id' => $product_id]);
           }
       }
        return redirect()->route('product-list');
     }

     public function updateProducts(Request $request)
     {
        $this->validate($request,([
            'name'=>'required',
            'description'=>'required',
            'tags'=>'required',
           ]));
        $product =Product::find($request->pid);

         $category=Category::find($request->category);
         $product_type_id=null;
         if($category) {
             $product_type = ProductType::where('product_type', $category->category)->where('vendor_id', $product->vendor)->first();
             if ($product_type == null) {
                 $product_type = new ProductType();
             }
             $product_type->product_type = $category->category;
             $product_type->vendor_id = $product->vendor;
             $product_type->save();
             $product_type_id=$product_type->id;
         }


         //zain
         $product_change=new ProductChange();
         $product_change->title = $product->title;
         $product_change->body_html = $product->body_html;
         $product_change->tags = $product->tags;
         $product_change->category = $product->category;
         $product_change->status = $product->status;
         $product_change->product_id = $product->id;
         $product_change->save();
        $product->title = $request->name;
        $product->body_html = $request->description;
        $product->tags = $request->tags;
        $product->category = $request->category;
        $product->product_type_id = $product_type_id;

        if($product->status==3)
        {
            $product->status = 0;
        }

         if($product->status==1)
         {
             $category=Category::find($product->category);
             $product->status = 2;
             $product->title=$request->name;
             $product->body_html= $request->description;
             $product->tags= $request->tags;
             $product->category=$category->id;
         }



        $product->save();





		ProductInfo::where('product_id', $request->pid)->update(['price_conversion_update_status' => 1]);
		if($product->shopify_id!=null && $product->status==1)
		{
			$shopify_id=$product->shopify_id;
			$category=Category::find($product->category);
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


//            $API_KEY = 'fd46f1bf9baedd514ed7075097c53995';
//            $PASSWORD = 'shpua_daf4f90db21249801ebf3d93bdfd0335';
//            $SHOP_URL = 'cherrpick-zain.myshopify.com';
            $data['product']=array(
                    "id" => $product->shopify_id,
                    "title" => $product->title,
                    "tags"   => $product->tags,
					"body_html"   => $product->body_html,
					"product_type"   => $category->category??'',
                );

            $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/products/$shopify_id.json";
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
            //curl_setopt($curl, CURLOPT_HEADER, 1);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
            //curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

            $response = curl_exec ($curl);

            curl_close ($curl);
		}
		if($request->hasfile('image')){
            $file = $request->file('image');
//            $extension = $file->getClientOriginalExtension();
//            $filename = $request->pid."-".time().'.'.$extension;
            $filename = $request->pid . "-" . time() . '.jpg';
            $file->move('uploads/profile/',$filename);
            $product1 = new ProductImages;
            $product1->image = url('uploads/profile/'.$filename);
			$product1->product_id = $request->pid;
            $product1->save();
			$pid=$product1->id;
			if($product->shopify_id!=null && $product->status==1) {
				$shopify_id=$product->shopify_id;

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


//                $API_KEY = 'fd46f1bf9baedd514ed7075097c53995';
//                $PASSWORD = 'shpua_daf4f90db21249801ebf3d93bdfd0335';
//                $SHOP_URL = 'cherrpick-zain.myshopify.com';
        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2020-04/products/$shopify_id/images.json";
			$data1['image']=array(
				'src' => url('uploads/profile/'.$filename),
			);
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
            curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data1));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            $response = curl_exec ($curl);
			$img_result=json_decode($response, true);
			if(isset($img_result['image']['id']))
			ProductImages::where('id', $pid)->update(['image_id' => $img_result['image']['id']]);
        }

		}
        return redirect()->route('product-list');
     }

    public function productlist( Request $request){
      $vendor=$this->vendorId();
      $res = Product::where('vendor', $vendor);
      if($request->search != ""){
          $res->where('title' , 'LIKE', '%' . $request->search . '%');
      }
        if($request->status!=""){
            $res->where('status',$request->status);
        }
      $product = $res->orderBy('id', 'DESC')->paginate(20);
      return view('subadmin.view-products',compact('product'));
    }
    public function outOfStockProductsList(Request $request)
    {
        $vendor=$this->vendorId();

        $res = ProductInfo::select('product_master.id as pid','product_master.title','product_master.is_variants','products_variants.varient_name','products_variants.varient_value','products_variants.stock','products_variants.id')->join('product_master','product_master.id','products_variants.product_id')
                ->where('product_master.vendor', $vendor)
                ->where('products_variants.stock', 0);
        if($request->search != ""){
          $res->where('product_master.title' , 'LIKE', '%' . $request->search . '%');
        }
        $product = $res->orderBy('product_master.id', 'DESC')->paginate(20);
        return view('subadmin.outofstock-products',compact('product'));
    }
    public function updateStock(Request $request)
    {
        $product =ProductInfo::find($request->id);
        $product->qty = $request->qty;
        $product->save();
        if($product->inventory_item_id!=null)
        {
			$invid=$product->inventory_id;
            $data['variant']=array(
                    "id" => $invid,
                    "fulfillment_service"   => "manual",
                    "inventory_management" => "shopify",
                );
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

            $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/variants/$invid.json";
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
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            curl_setopt($curl, CURLOPT_POSTFIELDS,json_encode($data));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            $response = curl_exec ($curl);
            curl_close ($curl);
		//Update Inventory
		$SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2020-04/inventory_levels/set.json";
           $data=array(
               'location_id' => '62600577199',
               'inventory_item_id' => $product->inventory_item_id,
               'available' => $request->qty
           );
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
           //curl_setopt($curl, CURLOPT_HEADER, 1);
           curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
           //curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
           curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
           curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
           curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

           $response = curl_exec ($curl);
           curl_close ($curl);;
            // ProductInventoryLocation::updateOrCreate(
                                // ['items_id' => $product->inventory_item_id, 'location_id' => $location_id],
                                // ['items_id' => $product->inventory_item_id, 'stock' => $request->qty, 'location_id' => $location_id]
                            // );
        }
        return json_encode(array('status'=>'success','qty'=>$request->qty));
    }
    public function deleteproduct(Request $request,$id){
      $res = Product::findOrFail($id);
      $res->delete();
      DB::table('products_variants')->where('product_id', $id)->delete();
      DB::table('products_images')->where('product_id', $id)->delete();
      if($res->shopify_id!=null)
      {
            $shopify_product_id=$res->shopify_id;
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

            $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2023-01/products/$shopify_product_id.json";
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
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            $response = curl_exec ($curl);
            curl_close ($curl);
      }
      return redirect()->route('product-list')->with('success','Product Deleted.');
    }
    public function deleteImage($id){
      $res = ProductImages::find($id);
	  if($res->image_id!=null)
	  {
		  $productInfo = Product::find($res->product_id);
		  $product_id=$productInfo->shopify_id;
		  $image_id=$res->image_id;
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

            $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/products/$product_id/images/$image_id.json";
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
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            $response = curl_exec ($curl);
            curl_close ($curl);
	  }
	  ProductImages::where('id', $id)->delete();
    }
    public function editproduct($id){
      $product = Product::find($id);
      //$prodcut_info=ProductInfo::where('product_id',$id)->get();
      $prodcut_images=ProductImages::where('product_id',$id)->get();
      $category = Category::all();
      return view('subadmin.edit-product',compact('product','category','prodcut_images'));

    }
    public function editVariant($id){
      $product = Product::find($id);
      $is_variants=$product->is_variants;
      $pid=$product->id;
      $prodcut_info=ProductInfo::where('product_id',$id)->get();
      return view('subadmin.edit-variant',compact('prodcut_info','is_variants','pid'));

    }
    public function updateVariant(Request $request)
    {

        //echo "<pre>"; print_r($request->all()); die();
        $this->validate($request,([
            'price'=>'required',
            'sku'=>'required',
            'grams'=>'required',
//            'quantity'=>'required',
           ]));

        $product_info =ProductInfo::find($request->id);
        $variant_change=new VariantChange();
        $variant_change->product_id=$product_info->product_id;
        $variant_change->sku=$product_info->sku;
        $variant_change->taxable=$product_info->taxable;
        $variant_change->shipping_weight=$product_info->shipping_weight;
        $variant_change->price=$product_info->price;
        $variant_change->base_price=$product_info->base_price;
        $variant_change->price_usd=$product_info->price_usd;
        $variant_change->price_aud=$product_info->price_aud;
        $variant_change->price_cad=$product_info->price_cad;
        $variant_change->price_gbp=$product_info->price_gbp;
        $variant_change->price_nld=$product_info->price_nld;
        $variant_change->price_irl=$product_info->price_irl;
        $variant_change->price_ger=$product_info->price_ger;
        $variant_change->grams=$product_info->grams;
        $variant_change->stock=$product_info->stock;
        $variant_change->shelf_life=$product_info->shelf_life;
        $variant_change->temp_require=$product_info->temp_require;
        $variant_change->dimensions=$product_info->dimensions;
        $variant_change->varient_name=$product_info->varient_name;
        $variant_change->varient_value=$product_info->varient_value;
        $variant_change->vendor_id=$product_info->vendor_id;
        $variant_change->inventory_item_id=$product_info->inventory_item_id;
        $variant_change->inventory_id=$product_info->inventory_id;
        $variant_change->edit_status=$product_info->edit_status;
        $variant_change->new_add_status=$product_info->new_add_status;
        $variant_change->price_status=$product_info->price_status;
        $variant_change->inventory_status=$product_info->inventory_status;
        $variant_change->product_discount=$product_info->product_discount;
        $variant_change->discounted_base_price=$product_info->discounted_base_price;
        $variant_change->discounted_inr=$product_info->discounted_inr;
        $variant_change->discounted_usd=$product_info->discounted_usd;
        $variant_change->discounted_aud=$product_info->discounted_aud;
        $variant_change->discounted_cad=$product_info->discounted_cad;
        $variant_change->discounted_gbp=$product_info->discounted_gbp;
        $variant_change->discounted_nld=$product_info->discounted_nld;
        $variant_change->discounted_irl=$product_info->discounted_irl;
        $variant_change->discounted_ger=$product_info->discounted_ger;
        $variant_change->price_conversion_update_status=$product_info->price_conversion_update_status;
        $variant_change->save();



		$products=Product::where('id', $product_info->product_id)->first();
		if($products->status==3)
		{
			$products->status = 0;
			$products->save();
		}
        if($products->status==1)
        {

            $products->status = 2;
            $products->save();
        }

		$Tags=explode(",",$products->tags);
            if(in_array("Saree",$Tags))
                $is_saree = 1;
            else
                $is_saree = 0;
		if(in_array("furniture",$Tags))
            {
                $is_furniture = 1;
                $volumetric_Weight = ($request->height * $request->width * $request->length)/5000;
            }
            else
            {
                $is_furniture = 0;
                $volumetric_Weight = 0;
            }
		$volumetric_Weight = 0;
		if($request->height!='' && $request->width!='' && $request->length!='')
			$volumetric_Weight = $request->height * $request->width * $request->length/5000;
        if($request->is_variants==1)
        {
            $product_info->varient_name = $request->varient_name;
            $product_info->varient_value = $request->varient_value;
        }


        $grams=$request->grams;
        $pricing_weight=$grams;
        if($product_type && $product_type->base_weight){
            $pricing_weight=max($grams, $product_type->base_weight);
        }

		//$prices=Helpers::calc_price($request->price,$request->grams,$is_saree,$is_furniture,$volumetric_Weight);
		$prices=Helpers::calc_price_new($request->price,$pricing_weight,$products->tags,$volumetric_Weight,$product_info->vendor_id);
        $product_info->sku = $request->sku;
        //$product_info->price = $price;
			$product_info->price = $prices['inr'];
			$product_info->price_usd = $prices['usd'];
			$product_info->price_aud = $prices['aud'];
			$product_info->price_cad = $prices['cad'];
			$product_info->price_gbp = $prices['gbp'];
			$product_info->price_nld = $prices['nld'];
			$product_info->price_irl = $prices['irl'];
			$product_info->price_ger = $prices['ger'];
		//if already discount added
			if($product_info->product_discount > 0)
			{
				$discounted_price=$request->price-($request->price*$product_info->product_discount/100);
				//$prices_dis=Helpers::calc_price($discounted_price,$request->grams,$is_saree,$is_furniture,$volumetric_Weight);
				$prices_dis=Helpers::calc_price_new($discounted_price,$request->grams,$products->tags,$volumetric_Weight,$product_info->vendor_id);
				$product_info->discounted_base_price = $discounted_price;
				$product_info->discounted_inr = $prices_dis['inr'];
				$product_info->discounted_usd = $prices_dis['usd'];
				$product_info->discounted_aud = $prices_dis['aud'];
				$product_info->discounted_cad = $prices_dis['cad'];
				$product_info->discounted_gbp = $prices_dis['gbp'];
				$product_info->discounted_nld = $prices_dis['nld'];
				$product_info->discounted_irl = $prices_dis['irl'];
				$product_info->discounted_ger = $prices_dis['ger'];
			}
		$product_info->base_price = $request->price;
        $product_info->grams = $grams;
        $product_info->pricing_weight = $pricing_weight;
        $product_info->stock = $request->quantity;
        $product_info->shelf_life = $request->shelf_life;
        $product_info->temp_require = $request->temp;
        $product_info->dimensions = $request->height.'-'.$request->width.'-'.$request->length;
        $product_info->edit_status = 1;
		$product_info->price_status = 0;
        $product_info->save();
		///image
		if($request->hasfile('image')){
            $file = $request->file('image');
//            $extension = $file->getClientOriginalExtension();
            $filename = $request->id."-".time(). '.jpg';
            $file->move('uploads/profile/',$filename);
			$img_full_path=url('uploads/profile/'.$filename);
			ProductImages::updateOrCreate(['variant_ids' => $product_info->id],
				['image' => $img_full_path, 'product_id' => $product_info->product_id, 'variant_ids' => $product_info->id],
			);
          }
//        if($product_info->inventory_id!=null)
//        {
//			//update stock on live store
//			$this->updateStockLiveStore($product_info->inventory_id,$request->quantity,$product_info->inventory_item_id);
//			//update variant
//			$this->updateVarianatLiveStore($product_info->id);
//            ///create new varient
//            $invid=$product_info->inventory_id;
//			if($request->varient_name!='' && $request->varient_value!='')
//			{
//            $data['variant']=array(
//                    "id" => $invid,
//                    "title" => $request->varient_name,
//                    "option1" => $request->varient_value,
//                    "sku"     => $request->sku,
//                    "price"   => $product_info->product_discount > 0 ? $prices_dis['usd']:$prices['usd'],
//					"compare_at_price" => $prices['usd'],
//                    "grams"   => $request->grams,
//                    "taxable" => false,
//                    "inventory_management" => "shopify",
//                );
//			}
//			else
//			{
//				$data['variant']=array(
//                    "id" => $invid,
//                    "sku"     => $request->sku,
//                    "price"   => $product_info->product_discount > 0 ? $prices_dis['usd']:$prices['usd'],
//					"compare_at_price" => $prices['usd'],
//                    "grams"   => $request->grams,
//                    "taxable" => false,
//                    "inventory_management" => "shopify",
//                );
//			}
//            $API_KEY = '6bf56fc7a35e4dc3879b8a6b0ff3be8e';
//            $PASSWORD = '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';
//            $SHOP_URL = 'cityshop-company-store.myshopify.com';
//            $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/variants/$invid.json";
//            $curl = curl_init();
//            curl_setopt($curl, CURLOPT_URL, $SHOPIFY_API);
//            $headers = array(
//                "Authorization: Basic ".base64_encode("$API_KEY:$PASSWORD"),
//                "Content-Type: application/json",
//                "charset: utf-8"
//            );
//            curl_setopt($curl, CURLOPT_HTTPHEADER,$headers);
//            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//            curl_setopt($curl, CURLOPT_VERBOSE, 0);
//            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
//            curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
//            curl_setopt($curl, CURLOPT_POSTFIELDS,json_encode($data));
//            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
//            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
//            $response = curl_exec ($curl);
//            curl_close ($curl);
//			$res=json_decode($response,true);
//			//echo "<pre>"; print_r($data); print_r($res); die();
//
//			////Update Image for variant
//			$productDetails = Product::find($product_info->product_id);
//			if($productDetails->shopify_id!=null && $productDetails->status==1)
//			{
//				$shopify_product_id=$productDetails->shopify_id;
//				$SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2020-04/products/$shopify_product_id/images.json";
//				$variant_id=$product_info->id;
//				$imagesResult=ProductImages::where('variant_ids',$variant_id)->first();
//				if($imagesResult) {
//					$data['image']=array(
//						'src' => $imagesResult->image,
//						'variant_ids'=> array($product_info->inventory_id),
//					);
//					$curl = curl_init();
//					curl_setopt($curl, CURLOPT_URL, $SHOPIFY_API);
//					$headers = array(
//						"Authorization: Basic ".base64_encode("$API_KEY:$PASSWORD"),
//						"Content-Type: application/json",
//						"charset: utf-8"
//					);
//					curl_setopt($curl, CURLOPT_HTTPHEADER,$headers);
//					curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//					curl_setopt($curl, CURLOPT_VERBOSE, 0);
//					curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
//					curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
//					curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
//					curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
//					curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
//					$response = curl_exec ($curl);
//					curl_close ($curl);
//					$img_result=json_decode($response, true);
//					ProductImages::where('id', $imagesResult->id)->update(['image_id' => $img_result['image']['id']]);
//				}
//            }
//            $location_id=Helpers::DiffalultLocation();
//            ProductInventoryLocation::updateOrCreate(
//                                ['items_id' => $product_info->inventory_item_id, 'location_id' => $location_id],
//                                ['items_id' => $product_info->inventory_item_id, 'stock' => $request->quantity, 'location_id' => $location_id]
//                            );
//        }
        Session::flash('success', 'Variant update successfully');
        return redirect()->route('edit-variant', $request->pid);
    }
	public function updateVarianatLiveStore($id)
	{
		$product_info =ProductInfo::find($id);
		$invid=$product_info->inventory_id;
		$data['variant']=array(
                    "id" => $invid,
                    "sku"   => $product_info->sku,
                    "weight" => $product_info->grams,
					"option1" => $product_info->varient_value??'',
                );
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


//        $API_KEY = 'fd46f1bf9baedd514ed7075097c53995';
//        $PASSWORD = 'shpua_daf4f90db21249801ebf3d93bdfd0335';
//        $SHOP_URL = 'cherrpick-zain.myshopify.com';
            $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/variants/$invid.json";
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
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            curl_setopt($curl, CURLOPT_POSTFIELDS,json_encode($data));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            $response = curl_exec ($curl);
            curl_close ($curl);
	}
	public function updateStockLiveStore($invid,$stock,$inventory_item_id)
    {
            $data['variant']=array(
                    "id" => $invid,
                    "fulfillment_service"   => "manual",
                    "inventory_management" => "shopify",
                );
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


//                $API_KEY = 'fd46f1bf9baedd514ed7075097c53995';
//        $PASSWORD = 'shpua_daf4f90db21249801ebf3d93bdfd0335';
//        $SHOP_URL = 'cherrpick-zain.myshopify.com';
            $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/variants/$invid.json";
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
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            curl_setopt($curl, CURLOPT_POSTFIELDS,json_encode($data));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            $response = curl_exec ($curl);
            curl_close ($curl);
		//Update Inventory
		$SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2020-04/inventory_levels/set.json";
           $data=array(
               'location_id' => '62600577199',
               'inventory_item_id' => $inventory_item_id,
               'available' => $stock
           );
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
           //curl_setopt($curl, CURLOPT_HEADER, 1);
           curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
           //curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
           curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
           curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
           curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

           $response = curl_exec ($curl);
           curl_close ($curl);;

    }

    public function deleteVariant(Request $request)
    {
        $id=$request->id;
        $data=ProductInfo::join('product_master','product_master.id','products_variants.product_id')
                            ->select('product_master.id','product_master.title','product_master.shopify_id','products_variants.inventory_id')
                            ->where('products_variants.id',$id)
                            ->get();
        if($data[0]->shopify_id!='' && $data[0]->inventory_id!='')
        {
            $product_id=$data[0]->shopify_id;
            $inventory_id=$data[0]->inventory_id;
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

            $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-04/products/$product_id/variants/$inventory_id.json";
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
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

            $response = curl_exec ($curl);
            curl_close ($curl);
        }
        $res = ProductInfo::findOrFail($id);
        $res->delete();
        Session::flash('success', 'Variant deleted successfully');
        return redirect()->route('edit-variant', $data[0]->id);
    }
    public function addNewVariant($id){
      $data = Product::find($id);
      return view('subadmin.add-new-variant',compact('data'));

    }
    public function saveNewVariant(Request $request){
        $this->validate($request,([
            'varient_name'=>'required',
            'varient_value'=>'required',
            'price'=>'required',
            'sku'=>'required',
            'grams'=>'required',
            'quantity'=>'required',
           ]));
        $vendor=$this->vendorId();
        $product_info = new ProductInfo;
        $product_info->product_id = $request->pid;
        $product_info->vendor_id = $vendor;
        $product_info->sku = $request->sku;;
        $product_info->varient_name = $request->varient_name;
        $product_info->varient_value = $request->varient_value;
        //$product_info->price = $price;
		$products=Product::where('id', $request->pid)->first();
		$Tags=explode(",",$products->tags);
            if(in_array("Saree",$Tags))
                $is_saree = 1;
            else
                $is_saree = 0;
		if(in_array("furniture",$Tags))
            {
                $is_furniture = 1;
                $volumetric_Weight = ($request->height * $request->width * $request->length)/5000;
            }
            else
            {
                $is_furniture = 0;
                $volumetric_Weight = 0;
            }
		$volumetric_Weight = 0;
		if($request->height!='' && $request->width!='' && $request->length!='')
			$volumetric_Weight = $request->height * $request->width * $request->length/5000;
		//$prices=Helpers::calc_price($request->price,$request->grams,$is_saree,$is_furniture,$volumetric_Weight);
		$prices=Helpers::calc_price_new($request->price,$request->grams,$products->tags,$volumetric_Weight,$vendor);
			$product_info->price = $prices['inr'];
			$product_info->price_usd = $prices['usd'];
			$product_info->price_aud = $prices['aud'];
			$product_info->price_cad = $prices['cad'];
			$product_info->price_gbp = $prices['gbp'];
			$product_info->price_nld = $prices['nld'];
			$product_info->price_irl = $prices['irl'];
			$product_info->price_ger = $prices['ger'];
		$product_info->base_price = $request->price;
        $product_info->grams = $request->grams;
        $product_info->stock = $request->quantity;
        $product_info->shelf_life = $request->shelf_life;
        $product_info->temp_require = $request->temp;
        $product_info->dimensions = $request->height.'-'.$request->width.'-'.$request->length;
        $product_info->save();
        $info_id=$product_info->id;

		///image
		if($request->hasfile('image')){
            $file = $request->file('image');
//            $extension = $file->getClientOriginalExtension();
            $filename = $info_id."-".time(). '.jpg';
            $file->move('uploads/profile/',$filename);
            $product = new ProductImages;
            $product->image = url('uploads/profile/'.$filename);
			$product->product_id = $request->pid;
			$product->variant_ids = $info_id;
            $product->save();
          }
        //check product is uploaded on store
        $product=Product::find($request->pid);
        if($product->status==1 && $product->shopify_id!=null)
        {
            ///create new varient
            $pid=$product->shopify_id;
            $data['variant']=array(
                    "title" => $request->varient_name,
                    "option1" => $request->varient_value,
                    "sku"     => $request->sku,
                    "price"   => $prices['usd'],
                    "grams"   => $request->grams,
                    "taxable" => false,
                    "inventory_management" => "shopify",
                    //"inventory_quantity" => $request->quantity,
                );
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

            $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/products/$pid/variants.json";
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
            curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            curl_setopt($curl, CURLOPT_POSTFIELDS,json_encode($data));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            $response = curl_exec ($curl);
            curl_close ($curl);
            $result=json_decode($response, true);
			//echo "<pre>"; print_r($result); die();
            ProductInfo::where('id', $info_id)->update(['inventory_item_id' => $result['variant']['inventory_item_id'], 'inventory_id' => $result['variant']['id']]);

			////Update Image for variant
			$productDetails = Product::find($request->pid);
			if($productDetails->shopify_id!=null)
			{
				$shopify_product_id=$productDetails->shopify_id;
				$SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2020-04/products/$shopify_product_id/images.json";
				$variant_id=$info_id;
				$imagesResult=ProductImages::where('variant_ids',$variant_id)->first();
				if($imagesResult) {
					$data['image']=array(
						'src' => $imagesResult->image,
						'variant_ids'=> array($result['variant']['id']),
					);
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
					curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
					curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
					curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
					$response = curl_exec ($curl);
					curl_close ($curl);
					$img_result=json_decode($response, true);
					ProductImages::where('id', $imagesResult->id)->update(['image_id' => $img_result['image']['id']]);
				}
            }
        }
      return redirect()->route('product-list')->with('success','Product Variant Added Successfully.');
    }
    public function uploadeImage()
    {

        $API_KEY = '03549b537b31aeff2bdc45aa7c98d06d';
        $PASSWORD = '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';
        $SHOP_URL = 'mystore-3220.myshopify.com';
        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2020-04/products/8047518646558/images.json";
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
        //curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($curl, CURLOPT_POSTFIELDS, '{"image":{"src":"https://sslimages.shoppersstop.com/sys-master/images/h98/hcf/28719001468958/GHM9150K_BLUE.jpg_230Wx334H"}}');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec ($curl);
        curl_close ($curl);

//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, 'https://mystore-3220.myshopify.com/admin/api/2022-10/products/8047518646558/images.json');
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
//        curl_setopt($ch, CURLOPT_HTTPHEADER, [
//            'X-Shopify-Access-Token' => '03549b537b31aeff2bdc45aa7c98d06d',
//            'Content-Type' => 'application/json',
//        ]);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, '{"image":{"src":"https://cdn57.androidauthority.net/wp-content/uploads/2019/02/Acer-Swift-7-840x472.jpg"}}');
//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
//        $response = curl_exec($ch);
//
//        curl_close($ch);
        if($errno = curl_errno($curl)) {
    $error_message = curl_strerror($errno);
    echo "cURL error ({$errno}):\n {$error_message}";
}
echo $response;
    }
    public function createProductShopifyMultiple()
    {
        $product_data = Product::where('status', 0)->where('vendor', Auth::id())->get();
        foreach($product_data as $product)
        {
        $category=Category::find($product->category);
            $variants=[];
            $product_info =ProductInfo::where('product_id',$product->id)->get();
            foreach($product_info as $v)
            {
                $variants[]=array(
                    "title" => $v->varient_name,
                    "option1" => $v->varient_value,
                    "sku"     => $v->sku,
                    "price"   => $v->price,
                    "grams"   => $v->grams,
                    "taxable" => false,
                    "inventory_management" => "shopify",
                    "inventory_quantity" => $v->stock,
                );
            }
        $products_array = array(
            "product" => array(
                "title"        => $product->title,
                "body_html"    => $product->body_html,
                "vendor"       =>  Auth::user()->name,
                "product_type" => $category->category,
                "published"    => true ,
                "tags"         => explode(",",$product->tags),
                "variants"     =>$variants,
            )
        );
        //echo "<pre>"; print_r($products_array); die();
//        $API_KEY = '03549b537b31aeff2bdc45aa7c98d06d';
//        $PASSWORD = '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';
//        $SHOP_URL = 'mystore-3220.myshopify.com';
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

        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2020-04/products.json";
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
        //curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($products_array));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec ($curl);
        curl_close ($curl);
        $result=json_decode($response, true);
        //echo "<pre>"; print_r($result); die();
        $shopify_product_id=$result['product']['id'];
        Product::where('id', $product->id)->update(['shopify_id' => $shopify_product_id, 'status' => '1']);
        $this->shopifyUploadeImage($product->id,$shopify_product_id);
        }
//        echo "<pre>";
//        echo $response;
//        print_r($result);
//        echo "</pre>";
//        echo $result['product']['id'];
        //return redirect()->route('product-list')->with('success','Product Created Successfully.');
    }
    public function createProductShopify($id)
    {
        $product = Product::find($id);
        $category=Category::find($product->category);
//        if($product->is_variants==1)
//        {
            $variants=[];
            $product_info =ProductInfo::where('product_id',$product->id)->get();
            foreach($product_info as $v)
            {
                $variants[]=array(
                    "title" => $v->varient_name,
                    "option1" => $v->varient_value,
                    "sku"     => $v->sku,
                    "price"   => $v->price,
                    "grams"   => $v->grams,
                    "taxable" => false,
                    "inventory_management" => "shopify",
                    "inventory_quantity" => $v->stock,
                );
            }
        //}
        $products_array = array(
            "product" => array(
                "title"        => $product->title,
                "body_html"    => $product->body_html,
                "vendor"       =>  Auth::user()->name,
                "product_type" => $category->category,
                "published"    => true ,
                "tags"         => explode(",",$product->tags),
                "variants"     =>$variants,
            )
        );

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

        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2020-04/products.json";
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
        //curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($products_array));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec ($curl);
        curl_close ($curl);
        $result=json_decode($response, true);
        //echo "<pre>"; print_r($result); die();
        $shopify_product_id=$result['product']['id'];
        Product::where('id', $product->id)->update(['shopify_id' => $shopify_product_id]);
        $this->shopifyUploadeImage($product->id,$shopify_product_id);
//        echo "<pre>";
//        echo $response;
//        print_r($result);
        foreach($result['product']['variants'] as $prd)
        {
            ProductInfo::where('sku', $prd['sku'])->update(['inventory_item_id' => $prd['inventory_item_id']]);
        }
        return redirect()->route('product-list')->with('success','Product Created Successfully.');
    }
    public function shopifyUploadeImage($id,$shopify_id)
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
        foreach($product_images as $img_val)
        {
            $image='{"image":{"src":"https://sslimages.shoppersstop.com/sys-master/images/h98/hcf/28719001468958/GHM9150K_BLUE.jpg_230Wx334H"}}';
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
            //curl_setopt($curl, CURLOPT_HEADER, 1);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $image);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            $response = curl_exec ($curl);
        }
    }
    public function updateProductShopify($id)
    {
        $product = Product::find($id);
        $category=Category::find($product->category);
//        if($product->is_variants==1)
//        {
            $variants=[];
            $product_info =ProductInfo::where('product_id',$product->id)->get();
            foreach($product_info as $v)
            {
                $variants[]=array(
                    "title" => $v->varient_name,
                    "option1" => $v->varient_value,
                    "sku"     => $v->sku,
                    "price"   => $v->price,
                    "grams"   => $v->grams,
                    "taxable" => false,
                    "inventory_management" => "shopify",
                    "inventory_quantity" => $v->stock,
                );
            }
        //}
        $products_array = array(
            "product" => array(
                "id"        => $product->shopify_id,
                "title"        => $product->title,
                "body_html"    => $product->body_html,
                "vendor"       =>  Auth::user()->name,
                "product_type" => $category->category,
                "published"    => true ,
                "tags"         => explode(",",$product->tags),
                "variants"     =>$variants,
            )
        );
        $shop=$product->shopify_id;
        $API_KEY = '03549b537b31aeff2bdc45aa7c98d06d';
        $PASSWORD = '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';
        $SHOP_URL = 'mystore-3220.myshopify.com';
        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2020-04/products/$shop.json";
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
        //curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($products_array));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec ($curl);
        curl_close ($curl);
        if($errno = curl_errno($curl)) {
    $error_message = curl_strerror($errno);
    echo "cURL error ({$errno}):\n {$error_message}";
}
echo "ok";
    }
    public function fetchShopifyOrders()
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

        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2020-04/orders.json";
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
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec ($curl);
        curl_close ($curl);
        $result=json_decode($response,true);
//        echo "<pre>";
//        print_r($result);
        $data=array();
        $arr_key=0;
        foreach($result['orders'] as $k=>$v)
        {
            echo "<pre>";
            //echo $k;
           //print_r($v['line_items']);
            $i=0;
            foreach($v['line_items'] as $item_val)
            {
                if($item_val['vendor']==Auth::user()->name)
                {
                    $i=1;
//                    echo $v['id']."==".$item_val['id'];
//                    echo "<br>";
                    $data[$arr_key]['line_items'][]=array(
                            'id' => $item_val['id'],
                            'name' => $item_val['name']
                        );
                }
            }
            if($i==1)
            {
                $data[$arr_key]['id']=$v['id'];
                $data[$arr_key]['created_at']=$v['created_at'];
                $data[$arr_key]['current_total_price']=$v['current_total_price'];
                $data[$arr_key]['fulfillment_status']=$v['fulfillment_status'];
            }
            $arr_key++;
        }
        echo "<pre>"; print_r($data);
//        foreach($result['orders'] as $v)
//        {
//            echo $v['order_number'].",Date=".$v['created_at'].",Total price=".$v['current_total_price'].",Paid=".$v['financial_status'];
//            echo "<br>";
//        }
    }
    public function allOrders()
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

        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2020-04/orders.json?limit=20";
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
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec ($curl);
        curl_close ($curl);
        $result=json_decode($response,true);
        $data=$result['orders'];
        return view('subadmin.orders',compact('data'));
    }
    public function detailsShopifyOrders($id)
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

        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2020-04/orders/$id.json";
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
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec ($curl);
        curl_close ($curl);
        $result=json_decode($response,true);
        //echo "<pre>"; print_r($result); die();
        $data=$result['order'];
       // echo "<pre>"; print_r($data); die();
        return view('subadmin.orders-details',compact('data'));
    }
    public function openOrders()
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

        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2020-04/orders.json?status=open";
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
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec ($curl);
        curl_close ($curl);
        $result=json_decode($response,true);
        $data=$result['orders'];
        return view('subadmin.open-orders',compact('data'));
    }
    public function closeOrders()
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

        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2020-04/orders.json?status=closed";
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
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec ($curl);
        curl_close ($curl);
        $result=json_decode($response,true);
        $data=$result['orders'];
        return view('subadmin.close-orders',compact('data'));
    }
    public function fetchProductFromUrl()
    {
        set_time_limit(0);
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
				$this->saveStoreFetchProductsFromJson($arr['products']);
            }
			echo "Completed...";
			}
			else{
				echo "No Product Found";
			}
    }
    function saveStoreFetchProductsFromJson($products)
	{
		////Update Stock Status for this vendor
		$store_info=Store::where('name',$products[0]['vendor'])->first();
		$vid=$store_info->id;
		ProductInfo::where('vendor_id', $vid)->update(['stock' => '0']);
		foreach($products as $row)
		{
			$pid=0;
			foreach($row['variants'] as $var)
			{
				$check=ProductInfo::where('sku',$var['sku'])->first();
				if ($check)
				{
					$pid=$check->product_id;
				}
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
			$store=Store::where('name',$vendor)->first();
			$store_id=0;
            if($store)
            {
                $store_id=$store->id;
            }
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
					$product_info->price_irl = $prices['nld'];
					$product_info->price_ger = $prices['nld'];
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
				//$data['shopify_id']=$row['id'];
				$data['title']=$row['title'];
				$data['body_html']=$row['body_html'];
				$data['tags']=implode(",",$row['tags']);
				//$data['handle']=$row['handle'];
				//$data['status']=1;
				//$data['approve_date']=Carbon::now();
				Product::where('id', $pid)->update($data);
				$store=Store::where('name',$row['vendor'])->first();
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
					$product_info->price_irl = $prices['nld'];
					$product_info->price_ger = $prices['nld'];
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
	}
    public function curlTest()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://Fav]}}' -X POST https://pradeep-6342.myshopify.com/admin/api/2022-10/products.json -H X-Shopify-Access-Token:prtapi_761cfde0a831dfaf6d5ee99bfef8846b");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{"product":{"title":"Burton Custom Freestyle 151","body_html":"<strong>Good snowboard!</strong>","vendor":"Burton","product_type":"Snowboard","tags":["Barnes & Noble","Big Air","Johns');
        $response = curl_exec($ch);
        curl_close($ch);
        echo $response."==----";
    }

	public function allCategory()
	{
		$data=Category::all();
		return view('subadmin.category',compact('data'));
	}
	public function addCategory()
	{
		return view('subadmin.add-category');
	}
	public function saveCategory(Request $request)
	{
		$res=new Category;
		$res->category=$request->name;
		$res->save();
		return redirect()->to('/category');
	}
	public function editCategory($id)
	{
		$data=Category::find($id);
		return view('subadmin.edit-category', compact('data'));
	}
	public function updateCategory(Request $request)
	{
		$res=Category::find($request->id);
		$res->category=$request->name;
		$res->save();
		return redirect()->to('/category');
	}
	public function deleteCategory($id)
	{
		$res=Category::find($id);
		$res->delete();
		return redirect()->to('/category')->with('success', 'Category Deleted Successfully!!');
	}


    public function ChangeImageUrl(){

//        $get_images=ProductImages::where('image', 'like', '%.jfif')->get();
        $get_images=ProductImages::orderBy('updated_at', 'desc')->limit('66')->get();

        foreach ($get_images as $image){

            try {
//            $imageUrl = str_replace('https://vendor.cherrypick.city', '', $image->image);


//                $contents = file_get_contents($image->image);
//                $filename='/uploads/profile/'.time().'.jpg';
//                $path = public_path($filename);
//                file_put_contents($path, $contents );


                // New image path with the ".jpg" extension
//                $newImagePath = str_replace('.jfif', '.jpg', $originalImagePath);


//            dd($originalImagePath,$newImagePath);



                // Update the database record
            $image->image = asset($image->image);
            $image->save();


            }catch (\Exception $exception){

                dump($exception->getMessage());

            }


        }
        dd(2);

    }



}
