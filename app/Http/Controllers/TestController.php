<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Auth;
use App\Models\Category;
use App\Models\ProductInfo;
use App\Models\Store;
use App\Models\ProductImages;
use App\Models\Banner;
use App\Models\Orderitem;
use DB;
use App\Exports\ProductExport;
use App\Imports\ProductImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Helpers\Helpers;
use App\Exports\PriceExport;
use App\Models\Order;
use Collection;
use Carbon\Carbon;
use Illuminate\Support\Str;

use Mail;

class TestController extends Controller
{
	public function testotp()
	{
		 $data=Order::where('shopify_order_id', 3134)->get();
        foreach($data as $row)
        {
            $store=Store::find($row->vendor);
            $vendor_name=$store->name;
            $otp=$row->otp;
            $order_id=$row->shopify_order_id;
            $ch = curl_init();
			$postdata=array('vendor_name' => $vendor_name,'order_id' => $order_id,'otp' => $otp);
			curl_setopt($ch, CURLOPT_URL,"https://phpstack-711164-2355937.cloudwaysapps.com/save-otp");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,$postdata);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$server_output = curl_exec($ch);
			curl_close($ch);
			echo $server_output;
			$result=json_decode($server_output, true);
			echo "<pre>"; print_r($result);
			if($result['status']=='success')
			{
				echo "1";
				//Order::where('id', $row->id)->update(['otp_status' => '1']);
			}
        }
	}

	public function updateTestPrice()
	{
		$API_KEY = '6bf56fc7a35e4dc3879b8a6b0ff3be8e';
        $PASSWORD = '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';
        $SHOP_URL = 'cityshop-company-store.myshopify.com';
        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2020-04/graphql.json";
		$data=ProductInfo::where('id', 6729)->get();
		foreach($data as $row)
		{
			//DB::table('tests')->insert(['name' => 'start']);
			$INR=$row->price;
			$CAD=$row->price_cad;
			$GBP=$row->price_gbp;
			$AUD=$row->price_aud;
			$EUR=$row->price_nld;
			$USD=$row->price_usd;
			if($row->product_discount > 0) {
				$INR_com=$row->discounted_inr;
				$CAD_com=$row->discounted_cad;
				$GBP_com=$row->discounted_gbp;
				$AUD_com=$row->discounted_aud;
				$EUR_com=$row->discounted_nld;
				$USD_com=$row->discounted_usd;
			}
			else
			{
				$INR_com=$row->price;
				$CAD_com=$row->price_cad;
				$GBP_com=$row->price_gbp;
				$AUD_com=$row->price_aud;
				$EUR_com=$row->price_nld;
				$USD_com=$row->price_usd;
			}
			$arr['23571431599']=array(
				'compare_at_price' => $INR,
				'price' => $INR_com,
				'currecy' => 'INR'
			);
			$arr['23366041775']=array(
				'compare_at_price' => $CAD,
				'price' => $CAD_com,
				'currecy' => 'CAD'
			);
			$arr['23550656687']=array(
				'compare_at_price' => $GBP,
				'price' => $GBP_com,
				'currecy' => 'GBP'
			);
			$arr['23593582767']=array(
				'compare_at_price' => $AUD,
				'price' => $AUD_com,
				'currecy' => 'AUD'
			);
			$arr['23550689455']=array(
				'compare_at_price' => $EUR,
				'price' => $EUR_com,
				'currecy' => 'EUR'
			);
			$variant_id=$row->inventory_id;
			$data['variant']=array(
                    "id" => $variant_id,
                    "price"   => $USD_com,
					"compare_at_price" => $USD
                );
            $SHOPIFY_API1 = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/variants/$variant_id.json";
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $SHOPIFY_API1);
            $headers = array(
                "Authorization: Basic ".base64_encode("$API_KEY:$PASSWORD"),
                "Content-Type: application/json",
                "charset: utf-8"
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER,$headers);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_VERBOSE, 0);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($curl, CURLOPT_POSTFIELDS,json_encode($data));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            echo $response = curl_exec ($curl);
            curl_close ($curl);
			$res=json_decode($response,true); 

		foreach($arr as $k=>$v)
		{			
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
        //curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($curl, CURLOPT_POSTFIELDS, '{
		"query": "mutation priceListFixedPricesAdd($priceListId: ID!, $prices: [PriceListPriceInput!]!) { priceListFixedPricesAdd(priceListId: $priceListId, prices: $prices) { prices { compareAtPrice { amount currencyCode } price { amount currencyCode } } userErrors { field code message } } }",
		"variables": {
		"priceListId": "gid://shopify/PriceList/'.$k.'",
		"prices": [
		{
			"compareAtPrice": {
					"amount": '.$v['compare_at_price'].',
					"currencyCode": "'.$v['currecy'].'"
			},
			"price": {
				"amount": '.$v['price'].',
				"currencyCode": "'.$v['currecy'].'"
		},
        "variantId": "gid://shopify/ProductVariant/'.$variant_id.'"
		}
		]
		}
		}');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $response1 = curl_exec ($curl);
        curl_close ($curl);
		echo $response1;
		echo "<br>";
		echo "<br>";
		}
		echo "hhhh";
		echo "<br>";echo "<br>";echo "<br>";echo "<br>";echo "<br>";
		//ProductInfo::where('id', $row->id)->update(['price_status' => 1]);
		//DB::table('tests')->insert(['name' => 'okk']);
		}
        //return Command::SUCCESS;
	}
	public function test()
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
			$headers = @get_headers("https://".$url."/collections/all/products.json");
			if(!$headers || strpos( $headers[0], '404')) {
				echo "URL Doesn't Exist"; die();
			}
			for($i=1;$i<=10;$i++)
			{
	
			$str=file_get_contents("https://".$url."/collections/all/products.json?page=".$i."&limit=250", false, $context);
                $arr=json_decode($str,true);
				//echo count($arr['products']);
				if(count($arr['products']) < 250)
				{
					echo $i; die();
				}
				//echo "<pre>"; print_r($arr['products']); die();
			}
            //for($i=1;$i<=$page_count;$i++)
            //{
                //$str=file_get_contents("https://".$url."/collections/all/products.json?page=".$i."&limit=250", false, $context);
                //$arr=json_decode($str,true);
				//$this->saveStoreFetchProductsFromJson($arr['products']);
            //}
			echo "Completed...";
	}
	public function conprice()
	{
		$data=Orderitem::where('price', 0)->get();
		foreach($data as $row)
		{
			$id=$row->shopify_variant_id;
			if($id!='')
			{
			$v=ProductInfo::where('inventory_id', $id)->first();
			if($v) {
				//Product::where('id', $id)->delete();
			echo $id."===".$row->price."===".$v->base_price."===".$row->quantity;
			$pp=$v->base_price * $row->quantity;
			Orderitem::where('id', $row->id)->update(['price' => $pp]);
			echo "<br>";
			}
		}
		}
	}
	
	public function checkpricelist()
	{
        $SHOPIFY_API = "https://cityshop-company-store.myshopify.com/admin/api/2022-04/graphql.json";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $SHOPIFY_API);
        $headers = array(
            "X-Shopify-Access-Token: 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",
            "Content-Type: application/json",
            "X-Shopify-Api-Features: include-presentment-prices",
            "charset: utf-8"
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER,$headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_VERBOSE, 0);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($curl, CURLOPT_POSTFIELDS, '{"query":"query {\r\npriceLists(first: 20) {\r\npageInfo {\r\nhasNextPage\r\n}\r\nedges {\r\ncursor\r\nnode {\r\nid\r\nname\r\ncurrency\r\n\r\n\r\n\r\ncontextRule {\r\ncountries\r\nmarket{ id }\r\n}\r\nparent {\r\nadjustment {\r\ntype\r\nvalue\r\n}\r\n}\r\n}\r\n}\r\n}\r\n}","variables":{}}');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        echo $response = curl_exec ($curl);
        curl_close ($curl);
        $result=json_decode($response, true);
        echo "<pre>"; //print_r($result);
		//echo count($result['data']['priceLists']['edges']);
		print_r($result['data']['priceLists']['edges']);
		foreach($result['data']['priceLists']['edges'] as $row)
		{
			echo $row['node']['id'];
			echo "<br>";
		}

	}
	public function pricelist()
	{
		$API_KEY = '6bf56fc7a35e4dc3879b8a6b0ff3be8e';
        $PASSWORD = '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';
        $SHOP_URL = 'cityshop-company-store.myshopify.com';
		
        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2023-01/graphql.json";
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
        curl_setopt($curl, CURLOPT_POSTFIELDS, '{
"query": "mutation PriceListCreate($input: PriceListCreateInput!) { priceListCreate(input: $input) { userErrors { field message } priceList { id name currency contextRule { market { id } } parent { adjustment { type value } } } } }",
 "variables": {
    "input": {
      "name": "USD Price List",
      "currency": "USD",
      "contextRule": {
        "marketId": "gid://shopify/Market/81625263"
      },
      "parent": {
        "adjustment": {
          "type": "PERCENTAGE_INCREASE",
          "value": 10
        }
      }
    }
  }
}');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        echo $response = curl_exec ($curl);
        curl_close ($curl);
        $result=json_decode($response, true);
        echo "<pre>"; print_r($result);
	}

	public function price()
	{
		$API_KEY = '6bf56fc7a35e4dc3879b8a6b0ff3be8e';
        $PASSWORD = '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';
        $SHOP_URL = 'cityshop-company-store.myshopify.com';
        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2020-04/graphql.json";
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
        curl_setopt($curl, CURLOPT_POSTFIELDS, '{
"query": "mutation priceListFixedPricesAdd($priceListId: ID!, $prices: [PriceListPriceInput!]!) { priceListFixedPricesAdd(priceListId: $priceListId, prices: $prices) { prices { compareAtPrice { amount currencyCode } price { amount currencyCode } } userErrors { field code message } } }",
 "variables": {
    "priceListId": "gid://shopify/PriceList/23550656687",
    "prices": [
      {
        "price": {
          "amount": "60",
          "currencyCode": "GBP"
        },
        "variantId": "gid://shopify/ProductVariant/42287424143535"
      }
    ]
  }
}');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec ($curl);
        curl_close ($curl);
        $result=json_decode($response, true);
        echo "<pre>"; print_r($result);
	}
	
	
	
	
	
	public function createCollection()
	{
		$API_KEY = '6bf56fc7a35e4dc3879b8a6b0ff3be8e';
        $PASSWORD = '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';
        $SHOP_URL = 'cityshop-company-store.myshopify.com';
        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2020-04/custom_collections.json";
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
        curl_setopt($curl, CURLOPT_POSTFIELDS, '{"custom_collection":{"title":"'.$title.'"}}');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec ($curl);
        curl_close ($curl);
        $result=json_decode($response, true);
        echo "<pre>"; print_r($result); die();
	}
	public function linkProductCollection()
	{
		$API_KEY = '6bf56fc7a35e4dc3879b8a6b0ff3be8e';
        $PASSWORD = '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';
        $SHOP_URL = 'cityshop-company-store.myshopify.com';
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
        curl_setopt($curl, CURLOPT_POSTFIELDS, '{"collect":{"product_id":7448754127023,"collection_id":306306121903}}');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec ($curl);
        curl_close ($curl);
        $result=json_decode($response, true);
        echo "<pre>"; print_r($result); die();
	}
     public function pricecalculate(Request $request)
	{
            return Excel::download(new PriceExport, 'price.xlsx');
//            $Tags=array('dfggdf','Namkeen');
//            $product=array('Tags' => $Tags, 'Variant Price' => '100', 'Variant Grams' => '350');
//            $location_id=Helpers::calc_price(100,400);
//            echo "<pre>"; print_r($location_id);
	}
	public function uploadeVariantImage(Request $request)
	{
			$ch = curl_init();
$postdata=array('vendor_name' => 'Vellanki Foods','order_id' => '1040','otp' => '454545');
curl_setopt($ch, CURLOPT_URL,"https://phpstack-711164-2355937.cloudwaysapps.com/save-otp");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,$postdata);

// In real life you should use something like:
// curl_setopt($ch, CURLOPT_POSTFIELDS, 
//          http_build_query(array('postvar1' => 'value1')));

// Receive server response ...
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$server_output = curl_exec($ch);

curl_close($ch);
$result=json_decode($server_output, true);
echo $result['status'];
echo $server_output;
	}
    public function banner(Request $request)
	{
		//echo "<pre>"; print_r($request->all());
		// $data = ['src' => url('uploads/banner/banner.jpg')];

		// return json_encode($data);
		$id=$request->id;
            $data=Banner::where('store_slug', $id)->where('approve_status', 'Approved')->first();
            if($data)
                $data = ['src' => url('uploads/banner/'.$data->store_desktop_banner)];
            else
                $data = ['src' => ''];
            return json_encode($data);
	}
    public function testEvent(Request $request)
    {
        //echo $request->search;
        $name=$request->search;
        $API_KEY = '6bf56fc7a35e4dc3879b8a6b0ff3be8e';
        $PASSWORD = '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';
        $SHOP_URL = 'cityshop-company-store.myshopify.com';
        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/customers/search.json?limit=10&query=customer_first_name:$name";
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
        $cust_result=json_decode($response,true);
        //echo "<pre>"; 
        foreach($cust_result['customers'] as $cus_v )
        {
            $data[]=array(
                'id' => $cus_v['id'],
                'text' => $cus_v['first_name']." ".$cus_v['last_name']
                );
        }
        return json_encode($data);
    }
    
       public function testProduct()
    {
    
    
        set_time_limit(0);
            $context = stream_context_create(
                array(
                    "http" => array(
                        "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36"
                    )
                )
            );
	    
	   $vendor_data=DB::table('cron_json_url')->get();
	    
	   foreach($vendor_data as $val){
	  
	   $vid=$val->vendor_id;
	   
	   
	    $url=$val->url;
           // $str_cnt=file_get_contents("https://".$url."/collections/all/count.json", false, $context);
            //$arr_1=json_decode($str_cnt,true);
            //$page_total=$arr_1['collection']['products_count'];
            
            
            
            
            for($i=1;$i<=1;$i++)
			{	
				$str=file_get_contents("https://".$url."/collections/all/products.json?page=".$i."&limit=250", false, $context);
				$arr=json_decode($str,true);

				$data=collect($arr['products']);
				
				$date=date('Y-m-d');
				//$date='2023-06-24';
                                  
				$product=$data->filter(function ($q) use ($date) {
				return Str::startsWith($q['updated_at'], $date);
				});
				
				
				 // dd($product);
		
		                  
		                    
		                if(sizeof($product) > 0){
					$this->saveStoreFetchProductsFromJson($product,$vid,'');
					echo "updated";
					//return back()->with('success','Product imported successfully');
				
				}
				
				
				//echo "<pre>"; print_r($arr['products']); die();
			}
            
            
   
			
			} 			
        //return Command::SUCCESS;
    }
    
    
    
    
    
        function saveStoreFetchProductsFromJson($products,$vid,$tag_url=null)
	{
		//echo "<pre>"; print_r($products); die;
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
			//echo $pid; die;
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
			// $pInfo=Product::where('shopify_id', $shopify_id)->first();
			// if(!$pInfo)
			// {
				$product = new Product;
				$product->title = $title;
				$product->body_html = $description;
				$product->vendor = $store_id;
				$product->tags = $tags;
				$product->category = $category_id;
				$product->save(); 
				$product_id=$product->id;
			// }
			// else
			// {
				// $product_id=$pInfo->id;
			// } 
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
					$product_info->varient_name = isset($row['options'][1]['name'])?$row['options'][1]['name']:$row['options'][0]['name'];
					$product_info->varient_value = isset($var['option2'])?$var['option2']:$var['option1'];
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
			else  //Existing Product
			{
				$data['title']=$row['title'];
				$data['body_html']=$row['body_html'];
				$data['tags']=implode(",",$row['tags']);
				Product::where('id', $pid)->update($data);
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
					$product_info->varient_name = isset($row['options'][1]['name'])?$row['options'][1]['name']:$row['options'][0]['name'];
					$product_info->varient_value = isset($var['option2'])?$var['option2']:$var['option1'];
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
    
    
    
   
}
