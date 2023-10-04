<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductInfo;
use App\Models\ProductImages;
use App\Models\Store;
use DB;
use App\Helpers\Helpers;
use Carbon\Carbon;
use App\Models\ProductInventoryLocation;
class approveProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:approve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'approve products in bulk';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
		// DB::table('tests')->insert(
			// array(
				// 'name'   =>   'live inventory update'
			// )
		// );
        $product_data = Product::where('status', 1)->whereNull('shopify_id')->where('vendor', 32)->get();
        foreach($product_data as $product)
        {
            $category=Category::find($product->category);
            $vendor=Store::find($product->vendor);
            $variants=[];
			$opt=[];
            $product_info =ProductInfo::where('product_id',$product->id)->get();
            foreach($product_info as $v)
            {
                $variants[]=array(
                    "title" => $v->varient_name,
                    "option1" => $v->varient_value,
                    "sku"     => $v->sku,
                    "price"   => $v->price_usd,
                    "grams"   => $v->grams,
                    "taxable" => false,
                    "inventory_management" => "shopify",
                    "inventory_quantity" => $v->stock,
                );
            }
			if($product_info[0]->varient_name!='')
				$opt[]=array('name' => $product_info[0]->varient_name);
			else
				$opt[]=array('name' => 'Title');
        $products_array = array(
            "product" => array(
                "title"        => $product->title,
                "body_html"    => $product->body_html,
                "vendor"       => $vendor->name,
                "product_type" => $category->category??'',
                "published"    => true ,
                "tags"         => explode(",",$product->tags),
                "variants"     =>$variants,
				"options"     =>  $opt
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
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($products_array));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        $response = curl_exec ($curl);
        curl_close ($curl);
        $result=json_decode($response, true);
        $shopify_product_id=$result['product']['id'];
		$shopify_handle=$result['product']['handle'];
        Product::where('id', $product->id)->update(['shopify_id' => $shopify_product_id, 'handle' => $shopify_handle, 'status' => '1', 'approve_date' => Carbon::now()]);
        foreach($result['product']['variants'] as $prd)
        {
            ProductInfo::where('sku', $prd['sku'])->update(['inventory_item_id' => $prd['inventory_item_id'], 'inventory_id' => $prd['id']]);
			$location_id=Helpers::DiffalultLocation();
            ProductInventoryLocation::updateOrCreate(
                                ['items_id' => $prd['inventory_item_id'], 'location_id' => $location_id],
                                ['items_id' => $prd['inventory_item_id'], 'stock' => $prd['inventory_quantity'], 'location_id' => $location_id]
                            );
        }
        $this->shopifyUploadeImage($product->id,$shopify_product_id);
		$this->linkProductToCollection($shopify_product_id,$vendor->collections_ids);
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
			$data['image']=array(
				'src' => $img_val->image,
			);
            //$image='{"image":{"src":"https://sslimages.shoppersstop.com/sys-master/images/h98/hcf/28719001468958/GHM9150K_BLUE.jpg_230Wx334H"}}';
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
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            $response = curl_exec ($curl);
			$img_result=json_decode($response, true);
			if(isset($img_result['image']['id']))
			ProductImages::where('id', $img_val->id)->update(['image_id' => $img_result['image']['id']]);
        }
    }
}
