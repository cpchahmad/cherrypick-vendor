<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Discount;
use App\Models\Store;
use App\Models\Product;
use Auth;
use DB;
use App\Helpers\Helpers;
use App\Models\ProductInfo;

class DiscountController extends Controller
{
	public function productsDiscountlist(Request $request){
          $vendor_id=Helpers::VendorID();
		  $res = Product::where('vendor', $vendor_id)->where('discount', '>', 0);
		  if($request->code != ""){
            $res->where('title' , 'LIKE', '%' . $request->code . '%');
          }
          $data=$res->paginate(30);
    	return view('subadmin.product-discount-view',compact('data'));
    }
	public function productsDiscountAddForm(Request $request){
          $vendor_id=Helpers::VendorID();
		  $res = Product::where('vendor', $vendor_id)->whereNotNull('shopify_id');
		$product = $res->orderBy('id', 'DESC')->get();
            foreach($product as $v )
            {
                $products[]=array(
                    'id' => $v->id,
                    'title' => $v->title
                    );
            }
			if(!isset($products))
				$products[]=array();
    	return view('subadmin.product-discount-add',compact('products'));
    }
    public function saveProductDiscount(Request $request)
	{
		$request->validate([
			'discount'=>'required',
        ]);
		if($request->applies_to=='all')
		{
			$vendor_id=Helpers::VendorID();
			$data=Product::where('vendor', $vendor_id)->get();
			foreach($data as $product_row)
			{
				Product::where('id', $product_row->id)->update(['discount' => $request->discount]);
				$products_info=ProductInfo::where('product_id', $product_row->id)->get();
				foreach($products_info as $row)
				{
					$base_price=$row->base_price;
					$discounted_price=$base_price-($base_price*$request->discount/100);
				
					$Tags=explode(",",$product_row->tags);
					if(in_array("Saree",$Tags))
						$is_saree = 1;
					else
						$is_saree = 0;
					if(in_array("furniture",$Tags))
					{
						$is_furniture = 1;
						$dimensions=explode(",",$product_row->dimensions);
						$volumetric_Weight = ($dimensions[0]*$dimensions[1]*$dimensions[2])/5000;
					}
					else
					{
						$is_furniture = 0;
						$volumetric_Weight = 0;
					}
					$prices=Helpers::calc_price($discounted_price,$row->grams,$is_saree,$is_furniture,$volumetric_Weight);
				
					ProductInfo::where('id', $row->id)->update(['product_discount' => $request->discount, 'discounted_base_price' => $discounted_price, 'price_status' => 0, 'discounted_inr' => $prices['inr'], 'discounted_usd' => $prices['usd'], 'discounted_aud' => $prices['aud'], 'discounted_cad' => $prices['cad'], 'discounted_gbp' => $prices['gbp'], 'discounted_nld' => $prices['nld']]);
					$variant_id=$row->inventory_id;
					//$this->updatePrimaryStorePrice($variant_id,$prices['usd'],$row->price_usd);
				}
			}
		}
		else
		{
			if(!isset($request->products_ids) && empty($request->products_ids))
			{
				return back()->withErrors(["products_ids" => "Please Select Atleast One Product!"]);
			}
		foreach($request->products_ids as $product)
		{
			$product_data=Product::find($product);
			$product_data->discount=$request->discount;
			$product_data->save();
			//$product_data=Product::where('id', $product)->update(['discount' => $request->discount]);
			$products_info=ProductInfo::where('product_id', $product)->get();
			foreach($products_info as $row)
			{
				$base_price=$row->base_price;
				$discounted_price=$base_price-($base_price*$request->discount/100);
				
				$Tags=explode(",",$product_data->tags);
				if(in_array("Saree",$Tags))
					$is_saree = 1;
				else
					$is_saree = 0;
				if(in_array("furniture",$Tags))
				{
					$is_furniture = 1;
					$dimensions=explode(",",$product_data->dimensions);
					$volumetric_Weight = ($dimensions[0]*$dimensions[1]*$dimensions[2])/5000;
				}
				else
				{
					$is_furniture = 0;
					$volumetric_Weight = 0;
				}
				$prices=Helpers::calc_price($discounted_price,$row->grams,$is_saree,$is_furniture,$volumetric_Weight);
				
				ProductInfo::where('id', $row->id)->update(['product_discount' => $request->discount, 'discounted_base_price' => $discounted_price, 'price_status' => 0, 'discounted_inr' => $prices['inr'], 'discounted_usd' => $prices['usd'], 'discounted_aud' => $prices['aud'], 'discounted_cad' => $prices['cad'], 'discounted_gbp' => $prices['gbp'], 'discounted_nld' => $prices['nld']]);
				
				$variant_id=$row->inventory_id;
				//$this->updatePrimaryStorePrice($variant_id,$prices['usd'],$row->price_usd); 
			}
			
		}
		}
		return redirect()->route('manage-product-discount')->with('success','Discount Created Successfully.');
	}
	public function deleteStoreProductsDiscount(Request $request){
		$vendor_id=Helpers::VendorID();
		Product::where('vendor', $vendor_id)->update(['discount' => 0]);
		ProductInfo::where('product_id', $request->id)->update(['product_discount' => 0, 'price_status' => 0]);
		return redirect()->route('manage-product-discount')->with('success','Discount Deleted Successfully.');
	}
	public function deleteProductsDiscount(Request $request){
		Product::where('id', $request->id)->update(['discount' => 0]);
		ProductInfo::where('product_id', $request->id)->update(['product_discount' => 0, 'price_status' => 0]);
		return json_encode(array('status'=>'success','qty'=>$request->qty));
	}
	public function updatePrimaryStorePrice($variant_id,$price,$compare_price)
	{
		$data['variant']=array(
                    "id" => $variant_id,
                    "price"   => $price,
					"compare_at_price" => $compare_price
                );
			$API_KEY = '6bf56fc7a35e4dc3879b8a6b0ff3be8e';
            $PASSWORD = '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';
            $SHOP_URL = 'cityshop-company-store.myshopify.com';
            $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/variants/$variant_id.json";
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
            curl_setopt($curl, CURLOPT_POSTFIELDS,json_encode($data));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            $response = curl_exec ($curl);
            curl_close ($curl);
			$res=json_decode($response,true);
	}
	
    public function discountlist(Request $request){
        //echo $_ENV['par']; die();
          $vendor_id=Helpers::VendorID();
    	  $res = Discount::where('vendor_id',$vendor_id);
          if($request->code != ""){
            $res->where('code' , 'LIKE', '%' . $request->code . '%');
          }
          $data=$res->get();
    	return view('subadmin.discount-view',compact('data'));
    }
    public function addDiscount(){
        $vendor_id=Helpers::VendorID();
        $store_data=Store::find($vendor_id);
        $collections_ids=$store_data->collections_ids;
        ////Products
        // $vendor_name=Auth::user()->name;
        // $API_KEY = $_ENV['API_KEY'];
        // $PASSWORD = $_ENV['PASSWORD'];
        // $SHOP_URL = $_ENV['SHOP_URL'];
        // $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/products.json?vendor=$vendor_name";
        // $curl = curl_init();
        // curl_setopt($curl, CURLOPT_URL, $SHOPIFY_API);
        // $headers = array(
            // "Authorization: Basic ".base64_encode("$API_KEY:$PASSWORD"),
            // "Content-Type: application/json",
            // "charset: utf-8"
        // );
        // curl_setopt($curl, CURLOPT_HTTPHEADER,$headers);
        // curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($curl, CURLOPT_VERBOSE, 0);
        // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        // $response = curl_exec ($curl);
        // curl_close ($curl);
        // $result=json_decode($response,true);
        // if(count($result['products'])==0)
            // $products[]=array();
        // else
        // {
            // foreach($result['products'] as $v )
            // {
                // $products[]=array(
                    // 'id' => $v['id'],
                    // 'title' => $v['title']
                    // );
            // }
        // } 
		$res = Product::where('vendor', $vendor_id)->whereNotNull('shopify_id');
		$product = $res->orderBy('id', 'DESC')->get();
		//$products[]=array();
            foreach($product as $v )
            {
                $products[]=array(
                    'id' => $v->shopify_id,
                    'title' => $v->title
                    );
            }
			if(!isset($products))
				$products[]=array();
    	return view('subadmin.discount-add',compact('products','collections_ids'));
    }
    public function saveDiscount(Request $request)
    {
       $request->validate([
        'discount_code'=>'required',
        'discount_value'=>'required',
        'start_date'=>'required',
        'start_time'=>'required',
        ]);        
       if ( $request->applies_to=='collection') {
            $this->validate($request, [
                'collection_ids'=>'required',
            ]);
        }
        if ( $request->applies_to=='products') {
            $this->validate($request, [
                'products_ids'=>'required',
            ]);
        }
        if ( $request->eligibility=='customers') {
            $this->validate($request, [
                'customer_ids'=>'required',
            ]);
        }
        $entitled_product_ids=array();
        $entitled_collection_ids=array();
        if($request->applies_to=='all_product')
        {
            $target_selection='all';
        }
        else if($request->applies_to=='collection')
        {
            $target_selection='entitled';
            $entitled_collection_ids=explode(",",$request->collection_ids);
        }
        else
        {
            $target_selection='entitled';
            //$entitled_product_ids=explode(",",$request->products_ids);
            $entitled_product_ids=$request->products_ids;
        }
        //////////////////////
        $prerequisite_customer_ids=array();
        if($request->eligibility=='everyone')
        {
            $customer_selection='all';
        }
        else if($request->eligibility=='group')
        {
            $customer_selection='prerequisite';
            $prerequisite_customer_ids=explode(",",$request->group_ids);
        }
        else
        {
            $customer_selection='prerequisite';
            //$prerequisite_customer_ids=explode(",",$request->customer_ids);
            $prerequisite_customer_ids=$request->customer_ids;
        }
        ////////////////////////////////////
        $usage_limit='';
        if($request->usage_limit=='2')
        {
            $usage_limit=$request->usage_value;
        }
        ////////////////////////////////
        $price_arr=null;
        $qty_arr=null;
        if($request->minimum_requirements=='price')
        {
            $price_arr=array('greater_than_or_equal_to' => floor($request->minimum_price/82.33));
        }
        else if($request->minimum_requirements=='quantity')
        {
            $qty_arr=array('greater_than_or_equal_to' => $request->minimum_quantity);
        }
        ///////////////////////////////////////
        $end_date=null;
        if($request->end_date!='' && $request->end_time!='')
        {
            $end_date=$request->end_date.'T'.$request->end_time;
        }
        ////////////////////////////////////
        $value=$request->discount_value;
        $target_type='line_item';
        $allocation_method='across';
        $value_type=$request->discount_type;
        if($request->discount_type=='free_shipping')
        {
            $value='100';
            $target_type='shipping_line';
            $allocation_method='each';
            $value_type='percentage';
        }
		if($request->discount_type=='fixed_amount')
        {
            $value=floor($value/82.33);
        }
        $data['price_rule']=array(
            'title' => $request->discount_code,
            'target_type' => $target_type,
            'target_selection' => $target_selection,
            'allocation_method' => $allocation_method,
            'usage_limit' => $usage_limit,
            'once_per_customer' => true,
            'customer_selection' => $customer_selection,
            'value_type' => $value_type,
            'value' => '-'.$value,
            'starts_at' => $request->start_date.'T'.$request->start_time.":00-00:00",
            'ends_at' => $end_date,
            'entitled_product_ids' => $entitled_product_ids,
            'entitled_variant_ids' => [],
            'entitled_collection_ids' => $entitled_collection_ids,
            'prerequisite_customer_ids' => $prerequisite_customer_ids,
            'prerequisite_subtotal_range' => $price_arr,
            'prerequisite_quantity_range' => $qty_arr,
        );
        $API_KEY = $_ENV['API_KEY'];
        $PASSWORD = $_ENV['PASSWORD'];
        $SHOP_URL = $_ENV['SHOP_URL'];
        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/price_rules.json";
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
//echo "<pre>"; print_r($result);
//        echo $result['price_rule']['id'];
        if(isset($result['price_rule']['id']))
        {
            if(Auth::user()->role=='Vendor')
                $vendor_id=Auth::user()->id;
            else
                $vendor_id=Auth::user()->vendor_id;
            $this->createDiscountCode($result['price_rule']['id'],$request->discount_code);
            
            $minimum_requirement_value='';
            $usage_limit='0';
            if($request->minimum_price!='')
               $minimum_requirement_value=$request->minimum_price;
            if($request->minimum_quantity!='')
               $minimum_requirement_value=$request->minimum_quantity;
            if($request->usage_limit!='')
                $usage_limit=$request->usage_limit;
            $res=new Discount;
            $res->code=$request->discount_code;
            $res->type=$request->discount_type;
            $res->discount_value=$request->discount_value;
            $res->minimum_requirement=$request->minimum_requirements;            
            $res->minimum_requirement_value=$minimum_requirement_value;
            $res->usage_limit=$usage_limit;
            if(isset($request->usage_value))
            $res->usage_limit_value=$request->usage_value;
            if(isset($request->products_ids) && is_array($request->products_ids)) 
            $res->products=implode(",",$request->products_ids);
            $res->collections=$request->collection_ids;
            if(isset($request->customer_ids) && is_array($request->customer_ids)) 
            $res->customers=implode(",",$request->customer_ids);
            $res->start_date=$request->start_date.' '.$request->start_time;
            if($request->end_date!='' && $request->end_time!='')
            $res->end_date=$request->end_date.' '.$request->end_time;
            $res->status=1;
            $res->price_rule_id=$result['price_rule']['id'];
            $res->vendor_id=$vendor_id;
            $res->save();
            return redirect('manage-discount');
        }
    }
    public function createDiscountCode($id,$code)
    {
        $API_KEY = $_ENV['API_KEY'];
        $PASSWORD = $_ENV['PASSWORD'];
        $SHOP_URL = $_ENV['SHOP_URL'];
        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/price_rules/$id/discount_codes.json";
         $data['discount_code']=array(
            'code' => $code,
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
    public function deleteDiscount($id)
    {
        $API_KEY = $_ENV['API_KEY'];
        $PASSWORD = $_ENV['PASSWORD'];
        $SHOP_URL = $_ENV['SHOP_URL'];
        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/price_rules/$id.json";
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
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST,"DELETE");
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec ($curl);
        curl_close ($curl);
        Discount::where('price_rule_id',$id)->delete();
        //$result=json_decode($response, true);
        return redirect('manage-discount');
    }
    public function editDiscount($id)
    {
        $data = Discount::find($id);
        $vendor_id=Helpers::VendorID();
        $store_data=Store::find($vendor_id);
        $collections_ids=$store_data->collections_ids;
        ////Products
        $vendor_name=Auth::user()->name;
        $API_KEY = $_ENV['API_KEY'];
        $PASSWORD = $_ENV['PASSWORD'];
        $SHOP_URL = $_ENV['SHOP_URL'];
        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/products.json?vendor=$vendor_name";
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
        if(count($result['products'])==0)
            $products[]=array();
        else
        {
            foreach($result['products'] as $v )
            {
                $products[]=array(
                    'id' => $v['id'],
                    'title' => $v['title']
                    );
            }
        }
    	return view('subadmin.discount-edit',compact('data','collections_ids','products'));
    }
}
