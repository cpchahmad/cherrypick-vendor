<?php

namespace App\Http\Controllers\superadmin;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ProductController;
use App\Jobs\ApproveAllProducts;
use App\Jobs\DenyAllProducts;
use App\Jobs\ProductsSyncFromApi;
use App\Jobs\UpdateProductPricesByProductType;
use App\Jobs\UpdateProductsWeight;
use App\Jobs\UpdateShopifyPricesByProductType;
use App\Jobs\UploadBulkProducts;
use App\Models\Log;
use App\Models\Markets;
use App\Models\MarketVendor;
use App\Models\ProductChange;
use App\Models\ProductLog;
use App\Models\ProductType;
use App\Models\ProductTypeSubCategory;
use App\Models\Setting;
use App\Models\ThirdPartyAPICategory;
use App\Models\ThirdPartyAPIProductAttribute;
use App\Models\ThirdPartyAPIProductAttributeOptions;
use App\Models\VariantChange;
use http\Client;
use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\Payment;
use App\Models\Document;
use App\Models\Banner;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductInfo;
use App\Models\ProductImages;
use App\Models\ProductInventoryLocation;
use App\Models\Order;
use App\Models\Orderitem;
use App\Models\ConversionRate;
use App\Models\ShipingCharges;
use Auth;
use Illuminate\Support\Facades\File;
use Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Session;
use DB;
use App\Helpers\Helpers;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PriceExport;
use App\Imports\BluckProductImport;
use App\Models\SareeFurniture;
use Validator;
use Spatie\SimpleExcel\SimpleExcelReader;

class SuperadminController extends Controller
{
	public function storeOrdersAmount($id,Request $request){
		$sql=Order::where('orders.vendor',$id);
		$sql->select('shopify_order_id','id','order_date');
        if($request->query('order') != ""){
          $sql->where('shopify_order_id' , $request->query('order'));
        }
        if($request->query('date') != ""){
          $sql->where('order_date' , $request->query('date'));
        }
		if($request->query('flag') != "" && $request->query('flag') == "week"){
          $sql->whereBetween('order_date',[Carbon::now()->startOfWeek()->toDateString(), Carbon::now()->endOfWeek()->toDateString()]);
        }
		if($request->query('flag') != "" && $request->query('flag') == "month"){
          $sql->whereBetween('order_date',[Carbon::now()->startOfMonth()->toDateString(), Carbon::now()->lastOfMonth()->toDateString()]);
        }
		if ($request->has('min') && $request->has('max')) {
                $sql=$sql->whereDate('created_at', '>=', $request->min)->whereDate('created_at', '<=', $request->max);
            }
        $data=$sql->orderBy('orders.shopify_order_id', 'desc')->paginate(10);
		foreach($data as $k=>$row)
		{
			$data[$k]->price=Orderitem::where('shopify_orders_id',$row->shopify_order_id)->where('vendor_id',$id)->sum('price');
		}
		//echo "<pre>"; print_r($data); die;
		return view('superadmin.store-orders-history',compact('data','id'));
    }
	public function storeOrdersDetails($id){
        $data=Order::find($id);
        $items_data=Orderitem::where('vendor_id',$data->vendor)->where('shopify_orders_id',$data->shopify_order_id)->get();
        return view('superadmin.orders-details',compact('data','items_data'));
	}
	public function storeAmount(Request $request){
		$data = Store::get()->toArray();
		foreach($data as $k=>$row)
		{
			$amount_sum=Orderitem::where('vendor_id', $row['id']);
			if ($request->has('min') && $request->has('max')) {
                $amount_sum=$amount_sum->whereDate('created_at', '>=', $request->min)->whereDate('created_at', '<=', $request->max);
            }
			$data[$k]['total_amount']=$amount_sum->sum('price');
		}
		//echo "<pre>"; print_r($data);
		return view('superadmin.store-amount',compact('data'));
    }
	public function conversionRate(){
		$data=ConversionRate::first();
		return view('superadmin.conversion-rate', compact('data'));
	}
	public function updateConversionRate(Request $request)
	{
		$data=ConversionRate::find(1);
		$data->update(['usd_inr' => $request->usd_inr, 'euro_inr' => $request->euro_inr, 'gbp_inr' => $request->gbp_inr, 'dirham_inr' => $request->dirham_inr, 'cad_inr' => $request->cad_inr, 'aud_inr' => $request->aud_inr]);
		$shiping_result=ShipingCharges::all();
		foreach($shiping_result as $row)
		{
		if($row->market==1)
		{
			$data_arr=array(
					'gms_50' =>round(($row->gms_50_inr/$data->usd_inr),2),
					'gms_100' =>round(($row->gms_100_inr/$data->usd_inr),2),
					'gms_150' =>round(($row->gms_150_inr/$data->usd_inr),2),
					'gms_200' =>round(($row->gms_200_inr/$data->usd_inr),2),
					'gms_250' =>round(($row->gms_250_inr/$data->usd_inr),2),
					'gms_300' =>round(($row->gms_300_inr/$data->usd_inr),2),
					'gms_400' =>round(($row->gms_400_inr/$data->usd_inr),2),
					'gms_500' =>round(($row->gms_500_inr/$data->usd_inr),2),
					'gms_750' =>round(($row->gms_750_inr/$data->usd_inr),2),
					'gms_1000' =>round(($row->gms_1000_inr/$data->usd_inr),2),
					'saree' =>round(($row->saree_inr/$data->usd_inr),2),
					'furniture' =>round(($row->furniture_inr/$data->usd_inr),2)
			);
		}
		if($row->market==2)
		{
			$data_arr=array(
					'gms_50' =>round(($row->gms_50_inr/$data->gbp_inr),2),
					'gms_100' =>round(($row->gms_100_inr/$data->gbp_inr),2),
					'gms_150' =>round(($row->gms_150_inr/$data->gbp_inr),2),
					'gms_200' =>round(($row->gms_200_inr/$data->gbp_inr),2),
					'gms_250' =>round(($row->gms_250_inr/$data->gbp_inr),2),
					'gms_300' =>round(($row->gms_300_inr/$data->gbp_inr),2),
					'gms_400' =>round(($row->gms_400_inr/$data->gbp_inr),2),
					'gms_500' =>round(($row->gms_500_inr/$data->gbp_inr),2),
					'gms_750' =>round(($row->gms_750_inr/$data->gbp_inr),2),
					'gms_1000' =>round(($row->gms_1000_inr/$data->gbp_inr),2),
					'saree' =>round(($row->saree_inr/$data->gbp_inr),2),
					'furniture' =>round(($row->furniture_inr/$data->gbp_inr),2)
			);
		}
		if($row->market==3)
		{
			$data_arr=array(
					'gms_50' =>round(($row->gms_50_inr/$data->euro_inr),2),
					'gms_100' =>round(($row->gms_100_inr/$data->euro_inr),2),
					'gms_150' =>round(($row->gms_150_inr/$data->euro_inr),2),
					'gms_200' =>round(($row->gms_200_inr/$data->euro_inr),2),
					'gms_250' =>round(($row->gms_250_inr/$data->euro_inr),2),
					'gms_300' =>round(($row->gms_300_inr/$data->euro_inr),2),
					'gms_400' =>round(($row->gms_400_inr/$data->euro_inr),2),
					'gms_500' =>round(($row->gms_500_inr/$data->euro_inr),2),
					'gms_750' =>round(($row->gms_750_inr/$data->euro_inr),2),
					'gms_1000' =>round(($row->gms_1000_inr/$data->euro_inr),2),
					'saree' =>round(($row->saree_inr/$data->euro_inr),2),
					'furniture' =>round(($row->furniture_inr/$data->euro_inr),2)
			);
		}
		if($row->market==4)
		{
			$data_arr=array(
					'gms_50' =>round($row->gms_50_inr),
					'gms_100' =>round($row->gms_100_inr),
					'gms_150' =>round($row->gms_150_inr),
					'gms_200' =>round($row->gms_200_inr),
					'gms_250' =>round($row->gms_250_inr),
					'gms_300' =>round($row->gms_300_inr),
					'gms_400' =>round($row->gms_400_inr),
					'gms_500' =>round($row->gms_500_inr),
					'gms_750' =>round($row->gms_750_inr),
					'gms_1000' =>round($row->gms_1000_inr),
					'saree' =>round($row->saree),
					'furniture' =>round($row->furniture)
			);
		}
		if($row->market==5)
		{
			$data_arr=array(
					'gms_50' =>round(($row->gms_50_inr/$data->cad_inr),2),
					'gms_100' =>round(($row->gms_100_inr/$data->cad_inr),2),
					'gms_150' =>round(($row->gms_150_inr/$data->cad_inr),2),
					'gms_200' =>round(($row->gms_200_inr/$data->cad_inr),2),
					'gms_250' =>round(($row->gms_250_inr/$data->cad_inr),2),
					'gms_300' =>round(($row->gms_300_inr/$data->cad_inr),2),
					'gms_400' =>round(($row->gms_400_inr/$data->cad_inr),2),
					'gms_500' =>round(($row->gms_500_inr/$data->cad_inr),2),
					'gms_750' =>round(($row->gms_750_inr/$data->cad_inr),2),
					'gms_1000' =>round(($row->gms_1000_inr/$data->cad_inr),2),
					'saree' =>round(($row->saree_inr/$data->cad_inr),2),
					'furniture' =>round(($row->furniture_inr/$data->cad_inr),2)
			);
		}
		if($row->market==6)
		{
			$data_arr=array(
					'gms_50' =>round(($row->gms_50_inr/$data->aud_inr),2),
					'gms_100' =>round(($row->gms_100_inr/$data->aud_inr),2),
					'gms_150' =>round(($row->gms_150_inr/$data->aud_inr),2),
					'gms_200' =>round(($row->gms_200_inr/$data->aud_inr),2),
					'gms_250' =>round(($row->gms_250_inr/$data->aud_inr),2),
					'gms_300' =>round(($row->gms_300_inr/$data->aud_inr),2),
					'gms_400' =>round(($row->gms_400_inr/$data->aud_inr),2),
					'gms_500' =>round(($row->gms_500_inr/$data->aud_inr),2),
					'gms_750' =>round(($row->gms_750_inr/$data->aud_inr),2),
					'gms_1000' =>round(($row->gms_1000_inr/$data->aud_inr),2),
					'saree' =>round(($row->saree_inr/$data->aud_inr),2),
					'furniture' =>round(($row->furniture_inr/$data->aud_inr),2)
			);
		}
		ShipingCharges::updateOrCreate(['market' => $row->market],$data_arr);
		ProductInfo::where('price_conversion_update_status', 0)->update(['price_conversion_update_status' => 1]);
		}
		return redirect()->to('superadmin/conversion-rate')->with('success','Conversion rate update successfully.');
	}
	public function shipingCharges($id){
		$data=ShipingCharges::where('market',$id)->first();
		$sareedata = SareeFurniture::where('market_id',$id)->where('type','saree')->first();
		$fdata = SareeFurniture::where('market_id',$id)->where('type','furniture')->first();
		return view('superadmin.shipingcharges', compact('data','id','sareedata','fdata'));
	}
	public function updateShipingCharges(Request $request)
	{


		//echo "<pre>"; print_r($request->all()); die;
		$conversion_rate=ConversionRate::find(1);
		if($request->market==1)
		{
			$data=array(
					'gms_50' =>round(($request->gms_50/$conversion_rate->usd_inr),2),
					'gms_100' =>round(($request->gms_100/$conversion_rate->usd_inr),2),
					'gms_150' =>round(($request->gms_150/$conversion_rate->usd_inr),2),
					'gms_200' =>round(($request->gms_200/$conversion_rate->usd_inr),2),
					'gms_250' =>round(($request->gms_250/$conversion_rate->usd_inr),2),
					'gms_300' =>round(($request->gms_300/$conversion_rate->usd_inr),2),
					'gms_400' =>round(($request->gms_400/$conversion_rate->usd_inr),2),
					'gms_500' =>round(($request->gms_500/$conversion_rate->usd_inr),2),
					'gms_750' =>round(($request->gms_750/$conversion_rate->usd_inr),2),
					'gms_1000' =>round(($request->gms_1000/$conversion_rate->usd_inr),2),
					'gms_5000' =>round(($request->gms_5000/$conversion_rate->usd_inr),2),
					'gms_50_inr' =>$request->gms_50,
					'gms_100_inr' =>$request->gms_100,
					'gms_150_inr' =>$request->gms_150,
					'gms_200_inr' =>$request->gms_200,
					'gms_250_inr' =>$request->gms_250,
					'gms_300_inr' =>$request->gms_300,
					'gms_400_inr' =>$request->gms_400,
					'gms_500_inr' =>$request->gms_500,
					'gms_750_inr' =>$request->gms_750,
					'gms_1000_inr' =>$request->gms_1000,
					'gms_5000_inr' =>$request->gms_5000,
					'savory_gms_50' =>round(($request->savory_gms_50/$conversion_rate->usd_inr),2),
					'savory_gms_100' =>round(($request->savory_gms_100/$conversion_rate->usd_inr),2),
					'savory_gms_150' =>round(($request->savory_gms_150/$conversion_rate->usd_inr),2),
					'savory_gms_200' =>round(($request->savory_gms_200/$conversion_rate->usd_inr),2),
					'savory_gms_250' =>round(($request->savory_gms_250/$conversion_rate->usd_inr),2),
					'savory_gms_300' =>round(($request->savory_gms_300/$conversion_rate->usd_inr),2),
					'savory_gms_400' =>round(($request->savory_gms_400/$conversion_rate->usd_inr),2),
					'savory_gms_500' =>round(($request->savory_gms_500/$conversion_rate->usd_inr),2),
					'savory_gms_750' =>round(($request->savory_gms_750/$conversion_rate->usd_inr),2),
					'savory_gms_1000' =>round(($request->savory_gms_1000/$conversion_rate->usd_inr),2),
					'savory_gms_5000' =>round(($request->savory_gms_5000/$conversion_rate->usd_inr),2),
					'savory_gms_50_inr' =>$request->savory_gms_50,
					'savory_gms_100_inr' =>$request->savory_gms_100,
					'savory_gms_150_inr' =>$request->savory_gms_150,
					'savory_gms_200_inr' =>$request->savory_gms_200,
					'savory_gms_250_inr' =>$request->savory_gms_250,
					'savory_gms_300_inr' =>$request->savory_gms_300,
					'savory_gms_400_inr' =>$request->savory_gms_400,
					'savory_gms_500_inr' =>$request->savory_gms_500,
					'savory_gms_750_inr' =>$request->savory_gms_750,
					'savory_gms_1000_inr' =>$request->savory_gms_1000,
					'savory_gms_5000_inr' =>$request->savory_gms_5000,
					'saree_inr' =>$request->saree,
					'saree' =>round(($request->saree/$conversion_rate->usd_inr),2),
					'furniture_inr' =>$request->furniture,
					'furniture' =>round(($request->furniture/$conversion_rate->usd_inr),2),
			);
		}
		if($request->market==2)
		{
			$data=array(
					'gms_50' =>round(($request->gms_50/$conversion_rate->gbp_inr),2),
					'gms_100' =>round(($request->gms_100/$conversion_rate->gbp_inr),2),
					'gms_150' =>round(($request->gms_150/$conversion_rate->gbp_inr),2),
					'gms_200' =>round(($request->gms_200/$conversion_rate->gbp_inr),2),
					'gms_250' =>round(($request->gms_250/$conversion_rate->gbp_inr),2),
					'gms_300' =>round(($request->gms_300/$conversion_rate->gbp_inr),2),
					'gms_400' =>round(($request->gms_400/$conversion_rate->gbp_inr),2),
					'gms_500' =>round(($request->gms_500/$conversion_rate->gbp_inr),2),
					'gms_750' =>round(($request->gms_750/$conversion_rate->gbp_inr),2),
					'gms_1000' =>round(($request->gms_1000/$conversion_rate->gbp_inr),2),
					'gms_5000' =>round(($request->gms_5000/$conversion_rate->gbp_inr),2),
					'gms_50_inr' =>$request->gms_50,
					'gms_100_inr' =>$request->gms_100,
					'gms_150_inr' =>$request->gms_150,
					'gms_200_inr' =>$request->gms_200,
					'gms_250_inr' =>$request->gms_250,
					'gms_300_inr' =>$request->gms_300,
					'gms_400_inr' =>$request->gms_400,
					'gms_500_inr' =>$request->gms_500,
					'gms_750_inr' =>$request->gms_750,
					'gms_1000_inr' =>$request->gms_1000,
					'gms_5000_inr' =>$request->gms_5000,
					'savory_gms_50' =>round(($request->savory_gms_50/$conversion_rate->gbp_inr),2),
					'savory_gms_100' =>round(($request->savory_gms_100/$conversion_rate->gbp_inr),2),
					'savory_gms_150' =>round(($request->savory_gms_150/$conversion_rate->gbp_inr),2),
					'savory_gms_200' =>round(($request->savory_gms_200/$conversion_rate->gbp_inr),2),
					'savory_gms_250' =>round(($request->savory_gms_250/$conversion_rate->gbp_inr),2),
					'savory_gms_300' =>round(($request->savory_gms_300/$conversion_rate->gbp_inr),2),
					'savory_gms_400' =>round(($request->savory_gms_400/$conversion_rate->gbp_inr),2),
					'savory_gms_500' =>round(($request->savory_gms_500/$conversion_rate->gbp_inr),2),
					'savory_gms_750' =>round(($request->savory_gms_750/$conversion_rate->gbp_inr),2),
					'savory_gms_1000' =>round(($request->savory_gms_1000/$conversion_rate->gbp_inr),2),
					'savory_gms_5000' =>round(($request->savory_gms_5000/$conversion_rate->gbp_inr),2),
					'savory_gms_50_inr' =>$request->savory_gms_50,
					'savory_gms_100_inr' =>$request->savory_gms_100,
					'savory_gms_150_inr' =>$request->savory_gms_150,
					'savory_gms_200_inr' =>$request->savory_gms_200,
					'savory_gms_250_inr' =>$request->savory_gms_250,
					'savory_gms_300_inr' =>$request->savory_gms_300,
					'savory_gms_400_inr' =>$request->savory_gms_400,
					'savory_gms_500_inr' =>$request->savory_gms_500,
					'savory_gms_750_inr' =>$request->savory_gms_750,
					'savory_gms_1000_inr' =>$request->savory_gms_1000,
					'savory_gms_5000_inr' =>$request->savory_gms_5000,
					'saree_inr' =>$request->saree,
					'saree' =>round(($request->saree/$conversion_rate->gbp_inr),2),
					'furniture_inr' =>$request->furniture,
					'furniture' =>round(($request->furniture/$conversion_rate->gbp_inr),2),
			);
		}
		if($request->market==3 || $request->market==7 || $request->market==8)
		{
			$data=array(
					'gms_50' =>round(($request->gms_50/$conversion_rate->euro_inr),2),
					'gms_100' =>round(($request->gms_100/$conversion_rate->euro_inr),2),
					'gms_150' =>round(($request->gms_150/$conversion_rate->euro_inr),2),
					'gms_200' =>round(($request->gms_200/$conversion_rate->euro_inr),2),
					'gms_250' =>round(($request->gms_250/$conversion_rate->euro_inr),2),
					'gms_300' =>round(($request->gms_300/$conversion_rate->euro_inr),2),
					'gms_400' =>round(($request->gms_400/$conversion_rate->euro_inr),2),
					'gms_500' =>round(($request->gms_500/$conversion_rate->euro_inr),2),
					'gms_750' =>round(($request->gms_750/$conversion_rate->euro_inr),2),
					'gms_1000' =>round(($request->gms_1000/$conversion_rate->euro_inr),2),
					'gms_5000' =>round(($request->gms_5000/$conversion_rate->euro_inr),2),
					'gms_50_inr' =>$request->gms_50,
					'gms_100_inr' =>$request->gms_100,
					'gms_150_inr' =>$request->gms_150,
					'gms_200_inr' =>$request->gms_200,
					'gms_250_inr' =>$request->gms_250,
					'gms_300_inr' =>$request->gms_300,
					'gms_400_inr' =>$request->gms_400,
					'gms_500_inr' =>$request->gms_500,
					'gms_750_inr' =>$request->gms_750,
					'gms_1000_inr' =>$request->gms_1000,
					'gms_5000_inr' =>$request->gms_5000,
					'savory_gms_50' =>round(($request->savory_gms_50/$conversion_rate->euro_inr),2),
					'savory_gms_100' =>round(($request->savory_gms_100/$conversion_rate->euro_inr),2),
					'savory_gms_150' =>round(($request->savory_gms_150/$conversion_rate->euro_inr),2),
					'savory_gms_200' =>round(($request->savory_gms_200/$conversion_rate->euro_inr),2),
					'savory_gms_250' =>round(($request->savory_gms_250/$conversion_rate->euro_inr),2),
					'savory_gms_300' =>round(($request->savory_gms_300/$conversion_rate->euro_inr),2),
					'savory_gms_400' =>round(($request->savory_gms_400/$conversion_rate->euro_inr),2),
					'savory_gms_500' =>round(($request->savory_gms_500/$conversion_rate->euro_inr),2),
					'savory_gms_750' =>round(($request->savory_gms_750/$conversion_rate->euro_inr),2),
					'savory_gms_1000' =>round(($request->savory_gms_1000/$conversion_rate->euro_inr),2),
					'savory_gms_5000' =>round(($request->savory_gms_5000/$conversion_rate->euro_inr),2),
					'savory_gms_50_inr' =>$request->savory_gms_50,
					'savory_gms_100_inr' =>$request->savory_gms_100,
					'savory_gms_150_inr' =>$request->savory_gms_150,
					'savory_gms_200_inr' =>$request->savory_gms_200,
					'savory_gms_250_inr' =>$request->savory_gms_250,
					'savory_gms_300_inr' =>$request->savory_gms_300,
					'savory_gms_400_inr' =>$request->savory_gms_400,
					'savory_gms_500_inr' =>$request->savory_gms_500,
					'savory_gms_750_inr' =>$request->savory_gms_750,
					'savory_gms_1000_inr' =>$request->savory_gms_1000,
					'savory_gms_5000_inr' =>$request->savory_gms_5000,
					'saree_inr' =>$request->saree,
					'saree' =>round(($request->saree/$conversion_rate->euro_inr),2),
					'furniture_inr' =>$request->furniture,
					'furniture' =>round(($request->furniture/$conversion_rate->euro_inr),2),
			);
		}
		if($request->market==4)
		{
			$data=array(
					'gms_50' =>round($request->gms_50,2),
					'gms_100' =>round($request->gms_100,2),
					'gms_150' =>round($request->gms_150,2),
					'gms_200' =>round($request->gms_200,2),
					'gms_250' =>round($request->gms_250,2),
					'gms_300' =>round($request->gms_300,2),
					'gms_400' =>round($request->gms_400,2),
					'gms_500' =>round($request->gms_500,2),
					'gms_750' =>round($request->gms_750,2),
					'gms_1000' =>round($request->gms_1000,2),
					'gms_5000' =>round($request->gms_5000,2),
					'gms_50_inr' =>$request->gms_50,
					'gms_100_inr' =>$request->gms_100,
					'gms_150_inr' =>$request->gms_150,
					'gms_200_inr' =>$request->gms_200,
					'gms_250_inr' =>$request->gms_250,
					'gms_300_inr' =>$request->gms_300,
					'gms_400_inr' =>$request->gms_400,
					'gms_500_inr' =>$request->gms_500,
					'gms_750_inr' =>$request->gms_750,
					'gms_1000_inr' =>$request->gms_1000,
					'gms_5000_inr' =>$request->gms_5000,
					'savory_gms_50' =>round($request->savory_gms_50,2),
					'savory_gms_100' =>round($request->savory_gms_100,2),
					'savory_gms_150' =>round($request->savory_gms_150,2),
					'savory_gms_200' =>round($request->savory_gms_200,2),
					'savory_gms_250' =>round($request->savory_gms_250,2),
					'savory_gms_300' =>round($request->savory_gms_300,2),
					'savory_gms_400' =>round($request->savory_gms_400,2),
					'savory_gms_500' =>round($request->savory_gms_500,2),
					'savory_gms_750' =>round($request->savory_gms_750,2),
					'savory_gms_1000' =>round($request->savory_gms_1000,2),
					'savory_gms_5000' =>round($request->savory_gms_5000,2),
					'savory_gms_50_inr' =>$request->savory_gms_50,
					'savory_gms_100_inr' =>$request->savory_gms_100,
					'savory_gms_150_inr' =>$request->savory_gms_150,
					'savory_gms_200_inr' =>$request->savory_gms_200,
					'savory_gms_250_inr' =>$request->savory_gms_250,
					'savory_gms_300_inr' =>$request->savory_gms_300,
					'savory_gms_400_inr' =>$request->savory_gms_400,
					'savory_gms_500_inr' =>$request->savory_gms_500,
					'savory_gms_750_inr' =>$request->savory_gms_750,
					'savory_gms_1000_inr' =>$request->savory_gms_1000,
					'savory_gms_5000_inr' =>$request->savory_gms_5000,
					'saree_inr' =>$request->saree,
					'saree' =>round($request->saree,2),
					'furniture_inr' =>$request->furniture,
					'furniture' =>round($request->furniture,2),
			);
		}
		if($request->market==5)
		{
			$data=array(
					'gms_50' =>round(($request->gms_50/$conversion_rate->cad_inr),2),
					'gms_100' =>round(($request->gms_100/$conversion_rate->cad_inr),2),
					'gms_150' =>round(($request->gms_150/$conversion_rate->cad_inr),2),
					'gms_200' =>round(($request->gms_200/$conversion_rate->cad_inr),2),
					'gms_250' =>round(($request->gms_250/$conversion_rate->cad_inr),2),
					'gms_300' =>round(($request->gms_300/$conversion_rate->cad_inr),2),
					'gms_400' =>round(($request->gms_400/$conversion_rate->cad_inr),2),
					'gms_500' =>round(($request->gms_500/$conversion_rate->cad_inr),2),
					'gms_750' =>round(($request->gms_750/$conversion_rate->cad_inr),2),
					'gms_1000' =>round(($request->gms_1000/$conversion_rate->cad_inr),2),
					'gms_5000' =>round(($request->gms_5000/$conversion_rate->cad_inr),2),
					'gms_50_inr' =>$request->gms_50,
					'gms_100_inr' =>$request->gms_100,
					'gms_150_inr' =>$request->gms_150,
					'gms_200_inr' =>$request->gms_200,
					'gms_250_inr' =>$request->gms_250,
					'gms_300_inr' =>$request->gms_300,
					'gms_400_inr' =>$request->gms_400,
					'gms_500_inr' =>$request->gms_500,
					'gms_750_inr' =>$request->gms_750,
					'gms_1000_inr' =>$request->gms_1000,
					'gms_5000_inr' =>$request->gms_5000,
					'savory_gms_50' =>round(($request->savory_gms_50/$conversion_rate->cad_inr),2),
					'savory_gms_100' =>round(($request->savory_gms_100/$conversion_rate->cad_inr),2),
					'savory_gms_150' =>round(($request->savory_gms_150/$conversion_rate->cad_inr),2),
					'savory_gms_200' =>round(($request->savory_gms_200/$conversion_rate->cad_inr),2),
					'savory_gms_250' =>round(($request->savory_gms_250/$conversion_rate->cad_inr),2),
					'savory_gms_300' =>round(($request->savory_gms_300/$conversion_rate->cad_inr),2),
					'savory_gms_400' =>round(($request->savory_gms_400/$conversion_rate->cad_inr),2),
					'savory_gms_500' =>round(($request->savory_gms_500/$conversion_rate->cad_inr),2),
					'savory_gms_750' =>round(($request->savory_gms_750/$conversion_rate->cad_inr),2),
					'savory_gms_1000' =>round(($request->savory_gms_1000/$conversion_rate->cad_inr),2),
					'savory_gms_5000' =>round(($request->savory_gms_5000/$conversion_rate->cad_inr),2),
					'savory_gms_50_inr' =>$request->savory_gms_50,
					'savory_gms_100_inr' =>$request->savory_gms_100,
					'savory_gms_150_inr' =>$request->savory_gms_150,
					'savory_gms_200_inr' =>$request->savory_gms_200,
					'savory_gms_250_inr' =>$request->savory_gms_250,
					'savory_gms_300_inr' =>$request->savory_gms_300,
					'savory_gms_400_inr' =>$request->savory_gms_400,
					'savory_gms_500_inr' =>$request->savory_gms_500,
					'savory_gms_750_inr' =>$request->savory_gms_750,
					'savory_gms_1000_inr' =>$request->savory_gms_1000,
					'savory_gms_5000_inr' =>$request->savory_gms_5000,
					'saree_inr' =>$request->saree,
					'saree' =>round(($request->saree/$conversion_rate->cad_inr),2),
					'furniture_inr' =>$request->furniture,
					'furniture' =>round(($request->furniture/$conversion_rate->cad_inr),2),
			);
		}
		if($request->market==6)
		{
			$data=array(
					'gms_50' =>round(($request->gms_50/$conversion_rate->aud_inr),2),
					'gms_100' =>round(($request->gms_100/$conversion_rate->aud_inr),2),
					'gms_150' =>round(($request->gms_150/$conversion_rate->aud_inr),2),
					'gms_200' =>round(($request->gms_200/$conversion_rate->aud_inr),2),
					'gms_250' =>round(($request->gms_250/$conversion_rate->aud_inr),2),
					'gms_300' =>round(($request->gms_300/$conversion_rate->aud_inr),2),
					'gms_400' =>round(($request->gms_400/$conversion_rate->aud_inr),2),
					'gms_500' =>round(($request->gms_500/$conversion_rate->aud_inr),2),
					'gms_750' =>round(($request->gms_750/$conversion_rate->aud_inr),2),
					'gms_1000' =>round(($request->gms_1000/$conversion_rate->aud_inr),2),
					'gms_5000' =>round(($request->gms_5000/$conversion_rate->aud_inr),2),
					'gms_50_inr' =>$request->gms_50,
					'gms_100_inr' =>$request->gms_100,
					'gms_150_inr' =>$request->gms_150,
					'gms_200_inr' =>$request->gms_200,
					'gms_250_inr' =>$request->gms_250,
					'gms_300_inr' =>$request->gms_300,
					'gms_400_inr' =>$request->gms_400,
					'gms_500_inr' =>$request->gms_500,
					'gms_750_inr' =>$request->gms_750,
					'gms_1000_inr' =>$request->gms_1000,
					'gms_5000_inr' =>$request->gms_5000,
					'savory_gms_50' =>round(($request->savory_gms_50/$conversion_rate->aud_inr),2),
					'savory_gms_100' =>round(($request->savory_gms_100/$conversion_rate->aud_inr),2),
					'savory_gms_150' =>round(($request->savory_gms_150/$conversion_rate->aud_inr),2),
					'savory_gms_200' =>round(($request->savory_gms_200/$conversion_rate->aud_inr),2),
					'savory_gms_250' =>round(($request->savory_gms_250/$conversion_rate->aud_inr),2),
					'savory_gms_300' =>round(($request->savory_gms_300/$conversion_rate->aud_inr),2),
					'savory_gms_400' =>round(($request->savory_gms_400/$conversion_rate->aud_inr),2),
					'savory_gms_500' =>round(($request->savory_gms_500/$conversion_rate->aud_inr),2),
					'savory_gms_750' =>round(($request->savory_gms_750/$conversion_rate->aud_inr),2),
					'savory_gms_1000' =>round(($request->savory_gms_1000/$conversion_rate->aud_inr),2),
					'savory_gms_5000' =>round(($request->savory_gms_5000/$conversion_rate->aud_inr),2),
					'savory_gms_50_inr' =>$request->savory_gms_50,
					'savory_gms_100_inr' =>$request->savory_gms_100,
					'savory_gms_150_inr' =>$request->savory_gms_150,
					'savory_gms_200_inr' =>$request->savory_gms_200,
					'savory_gms_250_inr' =>$request->savory_gms_250,
					'savory_gms_300_inr' =>$request->savory_gms_300,
					'savory_gms_400_inr' =>$request->savory_gms_400,
					'savory_gms_500_inr' =>$request->savory_gms_500,
					'savory_gms_750_inr' =>$request->savory_gms_750,
					'savory_gms_1000_inr' =>$request->savory_gms_1000,
					'savory_gms_5000_inr' =>$request->savory_gms_5000,
					'saree_inr' =>$request->saree,
					'saree' =>round(($request->saree/$conversion_rate->aud_inr),2),
					'furniture_inr' =>$request->furniture,
					'furniture' =>round(($request->furniture/$conversion_rate->aud_inr),2),
			);
		}
		ShipingCharges::updateOrCreate(['market' => $request->market],$data);
		return back()->with('success','Shiping charges update successfully.');
	}
    public function dashboard(){


//        $sql=Product::where('status', 1)->get()->toArray();
//        $total_approval=count($sql);
//        $sql_pending=Product::whereIn('status', [0,2])->get()->toArray();
//        $total_pending_approval=count($sql_pending);
//		$sql_pending_reject=Product::where('status', 3)->get()->toArray();
//        $total_deny=count($sql_pending_reject);
//        $sql_today=Product::where('status', 1)->where('approve_date', Carbon::now()->format('Y-m-d'))->get()->toArray();
//        $total_today_approval=count($sql_today);
//        $sql_weekly=Product::where('status', 1)->whereBetween('approve_date',[Carbon::now()->startOfWeek()->toDateString(), Carbon::now()->endOfWeek()->toDateString()])->get()->toArray();
//        $total_weekly_approval=count($sql_weekly);
//        $sql_month=Product::where('status', 1)->whereBetween('approve_date',[Carbon::now()->startOfMonth()->toDateString(), Carbon::now()->lastOfMonth()->toDateString()])->get()->toArray();
//        $total_month_approval=count($sql_month);


        $total_approval=Product::where('status', 1)->count();

        $total_pending_approval=Product::whereIn('status', [0,2])->count();

        $total_deny=Product::where('status', 3)->count();

        $total_today_approval=Product::where('status', 1)->where('approve_date', Carbon::now()->format('Y-m-d'))->count();

        $total_weekly_approval=Product::where('status', 1)->whereBetween('approve_date',[Carbon::now()->startOfWeek()->toDateString(), Carbon::now()->endOfWeek()->toDateString()])->count();

        $total_month_approval=Product::where('status', 1)->whereBetween('approve_date',[Carbon::now()->startOfMonth()->toDateString(), Carbon::now()->lastOfMonth()->toDateString()])->count();


        $sql_out_of_stock=ProductInfo::where('stock', 0)->get()->toArray();
        $total_out_of_stock=count($sql_out_of_stock);






        $shopify_products_pending=Product::where('shopify_status','Pending')->count();

        $shopify_products_inprogress=Product::where('shopify_status','In-Progress')->count();
        $shopify_products_complete=Product::where('shopify_status','Complete')->count();

        $data = DB::table('products_variants')
                ->select(array(DB::raw('COUNT(products_variants.id) as products'),'stores.name','stores.email','stores.status','stores.id'))
                ->where('products_variants.stock', '=', 0)
                ->join('stores', 'products_variants.vendor_id', '=', 'stores.id')
                ->groupBy('products_variants.vendor_id')
                ->get();

    	return view('superadmin.index', compact('total_approval','total_pending_approval','total_today_approval','total_weekly_approval','total_month_approval','total_out_of_stock','data','total_deny','shopify_products_pending','shopify_products_inprogress','shopify_products_complete'));
    }
    public function changeVendorStatus(Request $request)
    {
        $id=$request->id;
        $result = Store::find($id);
        if($result->status=='Active')
            $new_status='InActive';
        else
            $new_status='Active';
        Store::where('id',$id)->update(['status' => $new_status]);
        return json_encode(array('status'=>'success'));
    }
	public function changePremiumStatus(Request $request)
    {
        $id=$request->id;
        $result = Store::find($id);
        if($result->premium=='1')
            $new_status='0';
        else
            $new_status='1';
        Store::where('id',$id)->update(['premium' => $new_status]);
		ProductInfo::where('vendor_id', $id)->update(['price_conversion_update_status' => 1]);
        return json_encode(array('status'=>'success'));
    }
    public function changeVendorDiscount(Request $request)
    {
        $id=$request->id;
        $discount=$request->discount;
        Store::where('id',$id)->update(['vendor_discount' => $discount]);
        return json_encode(array('status'=>'success'));
    }
    public function vendorlist(){
     $vendorlist = Store::where('role','Vendor')->get();
     return view('superadmin.store-configuration',compact('vendorlist'));
    }
    public function vendordetails($id){
    	$data = Store::findOrFail($id);
    	$id = $data->id;
    	$payment = Payment::where('vendor_id',$id)->first();
        return view('superadmin.store-configration-details',compact('data','payment'));
    }

	public function updateGeneralConfiguration(Store $store, Request $request) {

		$validatedData = $request->validate([
			'emailid'=>'required|email|email:rfc,dns',
            'mobile'=>'required|min:10',
			'address' => 'required',
        ]);
		$store->update($validatedData);

        return redirect()->back()->with([
            'success' => 'Setting Saved Successfully',
        ]);
//		return redirect()->back();
	}

	public function updatePaymentDetails(Payment $payment, Request $request) {
		$validatedData = $request->validate([
    		'account_no'=>'required',
			'bank_name' => 'required',
			'ifsc' => 'required',
			'gst' => 'required',
			'account_type' => 'required',
			'address' => 'required',
        ]);
		$payment->update($validatedData);
		return redirect()->back();
	}

	public function updateStoreFront(Store $store, Request $request) {

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
		$request->validate([
            'logo'=>'image|mimes:jpg,jpeg,png,gif',
            'about_store'=>'required',
            'store_carry'=>'required',
          ]);
		if($request->hasfile('logo')){
            $file = $request->file('logo');
            $extension = $file->getClientOriginalExtension();
            $filename = time().'.'.$extension;
            $file->move('uploads/logo/',$filename);
            $store->logo=$filename;
        }
        $store->about_store=$request->about_store;
        $store->store_carry=$request->store_carry;
		$store->save();


        $values = [
            'store_logo' => 'http://phpstack-1103991-3868726.cloudwaysapps.com/uploads/logo/'.$store->logo,
            'about_store' => $store->about_store,
            'store_carry' => $store->store_carry
        ];
        $metafield_data=[
            "metafield" =>
                [
                    "key" => 'store_front',
                    "value" => json_encode($values),
                    "type" => "json_string",
                    "namespace" => "configuration",

                ]
        ];


        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/smart_collections/$store->collections_ids/metafields.json";

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
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($metafield_data));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec ($curl);

        curl_close ($curl);

        return redirect()->back()->with([
            'success' => 'Store Front Setting Saved Successfully',
            'active_tab' => $request->active_tab,
        ]);
	}
	public function updateVendorTag($id, Request $request) {
		$request->validate([
    		'tags'=>'required',
        ]);
		$products = Product::select('id','tags')->where('vendor',$id)->get();
		foreach($products as $product) {
			$productTag = Product::select('id','tags')->find($product->id);
			$productTag->tags = $productTag->tags.','.$request->tags;
			$productTag->save();
		}
		ProductInfo::where('vendor_id', $id)->update(['price_conversion_update_status' => 1]);
        return redirect()->back()->with([
            'success' => 'Tags Saved Successfully',
            'active_tab' => $request->active_tab,
        ]);
	}

	public function storesList(){
     $vendorlist = Store::where('role','Vendor')->get();
     return view('superadmin.stores',compact('vendorlist'));
    }
    public function bannerlist(){
     $list = Banner::join('stores','stores.id','banners.vendor_id')
             ->select('banners.*','stores.name','stores.logo')
             ->get();
     return view('superadmin.banners',compact('list'));
    }
    public function changeBannerStatus(Request $request)
    {
        $id=$request->id;
		$new_status=$request->status;
        // $result = Banner::find($id);
        // if($result->approve_status=='Approved')
            // $new_status='Disable';
        // else
            // $new_status='Approved';
        Banner::where('id',$id)->update(['approve_status' => $new_status]);
        return json_encode(array('status'=>'success'));
    }
    public function documentslist(){
    	$data = Store::where('role','Vendor')->get();
    	foreach($data as $k=>$row){
    		$document[] = Document::join('stores','stores.id','documents.vendor_id')->select('stores.name as vendorname','documents.*')->where('documents.vendor_id',$row['id'])->get();
    	}
       return view('superadmin.documents',compact('document'));

    }
    public function downloadfile($file){
    $file_path = public_path('assets/'.$file);
    return response()->download( $file_path);
    return redirect()->route('superadmin.documents');

    }
    public function submitdocument(Request $request){
    	$request->validate([
    		'name'=>'required',
    		'email'=>'required',
    		'file'=>'required',
        ]);
        $id = Auth::id();
        $document = new Document;
        $document->name = $request->name;
        $document->email = $request->email;
         if($request->hasfile('file')){
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $filename = time().'.'.$extension;
            $file->move('uploads/document/',$filename);
            $document->document = $filename;
        }
        $document->vendor_id = Auth::id();
        $document->save();
        return redirect()->route('documents')->with('success','Documents Saved.');
    }
    public function profile(){
    	$id = Auth::id();
    	$data = Store::where('id',$id)->first();
    	return view('superadmin.users-profile',compact('data'));
    }
    public function saveuserprofile(Request $request ){
    	$request->validate([
    		'fullName'=>'required',
    		'about'=>'required',
    		'company'=>'required',
    		'job'=>'required',
    		'country'=>'required',
    		'address'=>'required',
    		'phone'=>'required',
    		'email'=>'required',
          ]);
    	if($request->hasfile('file')){
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $filename = time().'.'.$extension;
            $file->move('uploads/superadminprofile/',$filename);
          }
    	 $id = Auth::id();
         Store::where('id',$id)->update(['name'=>$request->fullName,'mobile'=>$request->phone,'about'=>$request->about,'company'=>$request->company,'job'=>$request->job,'country'=>$request->country,'address'=>$request->address,'profile_picture'=>$filename]);
        return redirect()->route('profile')->with('success','profile updated');
    }
   public function removeprofilepicture($id){
   	 $id = Store::findOrFail($id);
   	 $path = 'uploads/superadminprofile/'.$id->profile_picture;
   	 if(File::exists($path)){
   	 	File::delete($path);
   	 }
   	return redirect()->route('profile');
   }
   public function password(Request $request){
   	 $request->validate([
             'current_password'=>'required',
             'new_password'=>'required|min:8|max:8',
             'renew_password'=>'required|same:new_password',
          ]);
   	 $authid = Auth::id();
   	 $data = Store::where('id',$authid)->first();
   	 if(Hash::check($request->current_password,$data->password)){
            Store::where('id',$authid)->update(['password'=>Hash::make($request->new_password)]);
            return redirect()->route('profile')->with('success','password changed');

         }

          else{
            return redirect()->route('profile')->with('error','Current Password does not match');
          }


   }

   public function productlist(Request $request){


//     $res = Product::whereIn('status',[0,2]);
     $res = Product::query();


       if ($request->search != "") {
           $res->where('title', 'LIKE', '%' . $request->search . '%')
               ->orWhereHas('productInfo', function ($query) use ($request) {
                   $query->where('sku', 'LIKE', '%' . $request->search . '%');
               });
       }
      if($request->vendor != ""){
         $res->where('vendor' , $request->vendor);
      }

      if($request->date != "" && $request->date!='undefined'){
          $request->date = str_replace('/', '-', $request->date);
          $res->whereDate('created_at' , date('Y-m-d',strtotime($request->date)));
      }
      if($request->status!=""){
          $res->where('status',$request->status);
      }

       if($request->shopify_status!=""){
           $res->where('shopify_status',$request->shopify_status);
       }

      if($request->product_type!=""){

          $ex_product_type=explode(',',$request->product_type);
          $res->whereIn('product_type_id',$ex_product_type);
      }

       $product_type_ids=$res->pluck('product_type_id');

      $product_types=ProductType::whereIn('id',$product_type_ids)->get();




//       $product_tags=$res->pluck('tags');
//       $tag_array=array();
//       foreach ($product_tags as $product_tag){
//
//           $tags_data=explode(',',$product_tag);
//
//           $tag_array = array_merge($tag_array, $tags_data);
//       }
//       $tags = array_unique($tag_array);
//
//      if($request->tags!=""){
//
//          $ex_tags=explode(',',$request->tags);
//
//
//
//      }




       $total_products = $res->count();




       $product_ids = $res->pluck('id')->toArray();
       $total_variants=ProductInfo::whereIn('product_id',$product_ids)->count();
       $total_variants_in_stock=ProductInfo::whereIn('product_id',$product_ids)->where('stock',1)->count();
       $total_variants_out_of_stock=ProductInfo::whereIn('product_id',$product_ids)->where('stock',0)->count();






       $total_products_in_stock=0;
       $total_products_out_of_stock=0;

       $inStockProductIds = [];
       $outOfStockProductIds = [];


       Product::whereIn('id', $product_ids)
           ->with(['productInfos' => function ($query) {
               $query->select('product_id', 'stock');
           }])
           ->chunk(200, function ($products) use (&$total_products_in_stock, &$total_products_out_of_stock, &$inStockProductIds, &$outOfStockProductIds) {
               foreach ($products as $product) {
                   $stockCounts = $product->productInfos->pluck('stock')->toArray();

                   if (in_array(1, $stockCounts)) {
                       // If at least one variant has stock 1
                       $total_products_in_stock++;
                       $inStockProductIds[] = $product->id;
                   } elseif (count(array_unique($stockCounts)) === 1 && reset($stockCounts) === 0) {
                       // If all variants have stock 0
                       $total_products_out_of_stock++;
                       $outOfStockProductIds[] = $product->id;

                   }
               }
           });



       if($request->stock!="")
       {
           if($request->stock=='in-stock'){

           $res->whereIn('id', $inStockProductIds)->get();
               $total_products_out_of_stock=0;
               $total_variants_out_of_stock=0;


           }elseif ($request->stock=='out-of-stock'){

               $res->whereIn('id', $outOfStockProductIds)->get();

               $total_products_in_stock=0;
               $total_variants_in_stock=0;
           }

       }


      $data = $res->orderBy('updated_at', 'DESC')->paginate(30)->appends($request->all());


      $vendorlist = Store::where('role','Vendor')->get();


      //dd($data);
     return view('superadmin.products-list',compact('data','vendorlist','product_types','total_products','total_variants','total_variants_in_stock','total_variants_out_of_stock','total_products_in_stock','total_products_out_of_stock'));
    }
	public function updateAllProductPrices()
	{
		ProductInfo::where('price_conversion_update_status', 0)->update(['price_conversion_update_status' => 1]);
		return back()->with('success','Product price updated successfully');
	}

    public function updateProductPricesByVendor($id)
    {
        ProductInfo::where('vendor_id',$id)->where('price_conversion_update_status', 0)->update(['price_conversion_update_status' => 1]);
        return back()->with('success','Product price updated successfully');
    }

    public function updateProductPricesInShopify($id)
    {
        ProductInfo::where('vendor_id',$id)->whereNotNull('inventory_id')->update(['price_status' => 0]);
        return back()->with('success','Product price updated in Shopify successfully');
    }

	public function rejectProductList(Request $request){
     $res = Product::where('status',3);
      if($request->search != ""){
          $res->where('title' , 'LIKE', '%' . $request->search . '%');
      }
      if($request->vendor != ""){
          $res->where('vendor' , $request->vendor);
      }
      if($request->date != "" && $request->date!='undefined'){
          $request->date = str_replace('/', '-', $request->date);
          $res->whereDate('approve_date' , date('Y-m-d',strtotime($request->date)));
      }
      $data = $res->orderBy('id', 'DESC')->paginate(30);
      $vendorlist = Store::where('role','Vendor')->get();
     return view('superadmin.rejected-products-list',compact('data','vendorlist'));
    }
	public function approvedProductlist(Request $request){
		$days=$request->day;
		$res = Product::where('status',1);
		if($days=='today')
		{
			$res->where('approve_date', Carbon::now()->format('Y-m-d'));
		}
		if($days=='week')
		{
			$res->whereBetween('approve_date',[Carbon::now()->startOfWeek()->toDateString(), Carbon::now()->endOfWeek()->toDateString()]);
		}
		if($days=='month')
		{
			$res->whereBetween('approve_date',[Carbon::now()->startOfMonth()->toDateString(), Carbon::now()->lastOfMonth()->toDateString()]);
		}
      if($request->search != ""){
          $res->where('title' , 'LIKE', '%' . $request->search . '%');
      }
      if($request->vendor != ""){
          $res->where('vendor' , $request->vendor);
      }
      if($request->date != "" && $request->date!='undefined'){
          $request->date = str_replace('/', '-', $request->date);
          $res->whereDate('approve_date' , date('Y-m-d',strtotime($request->date)));
      }
      $data = $res->orderBy('id', 'DESC')->paginate(30);
      $vendorlist = Store::where('role','Vendor')->get();
     return view('superadmin.approved-products-list',compact('data','vendorlist'));
    }
    public function productDetails($id){
     $data = Product::find($id);
     $items = ProductInfo::where('product_id',$id)->get();
     $vendor = Store::where('id',$data->vendor)->first();
     $product_logs=ProductLog::where('product_id',$id)->orderBy('id','desc')->get();
//     $change_products=ProductChange::where('product_id',$id)->get();
//     $change_variants=VariantChange::where('product_id',$id)->get();
     return view('superadmin.products-details',compact('data','items','vendor','product_logs'));
    }
    public function outofstockProduct(){
        $data = DB::table('products_variants')
                ->select(array(DB::raw('COUNT(products_variants.id) as products'),'stores.name','stores.email','stores.id'))
                ->where('products_variants.stock', '=', 0)
                ->join('stores', 'products_variants.vendor_id', '=', 'stores.id')
                ->groupBy('products_variants.vendor_id')
                ->get();
        return view('superadmin.out-of-stock',compact('data'));
    }
	public function outofstockProductLists($id){
        $res = ProductInfo::select('product_master.id as pid','product_master.title','product_master.is_variants','products_variants.varient_name','products_variants.varient_value','products_variants.stock','products_variants.id')->join('product_master','product_master.id','products_variants.product_id')
                ->where('product_master.vendor', $id)
                ->where('products_variants.stock', 0);
        $product = $res->orderBy('product_master.id', 'DESC')->paginate(20);
        return view('superadmin.outofstock-products',compact('product'));
    }
    public function bulkApproveProduct(Request $request)
    {


        $product_array_id=array();
//        $ids=explode(",",$request->ids);
        $ids=$request->ids;

        foreach($ids as $id)
        {
            $product_info =ProductInfo::where('product_id',$id)->get();
            $upload_product=0;
            foreach($product_info as $index=> $v)
            {
                if($v->stock){
                    array_push($product_array_id,$id);
                }
            }

        }


        $product_array_id=array_unique($product_array_id);


        if(count($product_array_id) > 0) {
            $data = Product::whereIn('id',$product_array_id)->update(['status'=>1,'approve_date' => Carbon::now()]);

            $check_log=Log::where('name','Approve Product Push')->where('is_running',1)->where('is_complete',0)->first();
            $currentTime = now();
            if($check_log==null){
                $check_log=new Log();
                $check_log->status='In-Progress';
                $check_log->is_running=1;
                $check_log->is_complete=0;

            }else{
                $check_log=new Log();
                $check_log->is_running=0;
                $check_log->is_complete=0;
                $check_log->status='In-Queue';
            }


            $check_log->name = 'Approve Product Push';
            $check_log->running_at=now();
            $check_log->date = $currentTime->format('F j, Y');
            $check_log->total_product = count($product_array_id);
            $check_log->product_left = count($product_array_id);
            $check_log->product_pushed = 0;
            $check_log->start_time = $currentTime->toTimeString();
            $check_log->product_ids=implode(',',$product_array_id);
            $check_log->filters=json_encode($request->all());
            $check_log->save();

        }



        return json_encode(array('status'=>'success'));
    }
	public function bulkRejectProduct(Request $request)
    {
        $ids=explode(",",$request->ids);

        $product_array_id=array();
        foreach($ids as $id)
        {
            $product_info =ProductInfo::where('product_id',$id)->get();
            $upload_product=0;
            foreach($product_info as $index=> $v)
            {
                if($v->stock){
                    array_push($product_array_id,$id);
                }
            }

        }
       $product_array_id=array_unique($product_array_id);

        if(count($product_array_id) > 0) {
            $data = Product::whereIn('id',$product_array_id)->update(['status'=>3,'approve_date' => Carbon::now()]);

        }
        return json_encode(array('status'=>'success'));
    }
	public function rejectProduct($id)
	{
		Product::where('id', $id)->update(['status' => '3', 'approve_date' => Carbon::now()]);
		return redirect()->route('superadmin.allproduct')->with('success','Product Rejected Successfully.');
	}
    public function createProductShopify($id)
    {

        $product = Product::find($id);


		$store = Store::find($product->vendor);

        $metafield_data=
            [
                [
                    "key" => 'key_ingredients',
                    "value" => $product->additional_key_ingredients,
                    "type" => "multi_line_text_field",
                    "namespace" => "additional_data",
                ],

                [

                    "key" => 'how_to_use',
                    "value" => $product->additional_how_to_use,
                    "type" => "multi_line_text_field",
                    "namespace" => "additional_data",
                ],

                [

                    "key" => 'who_can_use',
                    "value" => $product->additional_who_can_use,
                    "type" => "multi_line_text_field",
                    "namespace" => "additional_data",
                ],

                [

                    "key" => 'why_mama_earth',
                    "value" => $product->additional_why_mama_earth,
                    "type" => "multi_line_text_field",
                    "namespace" => "additional_data",
                ],

                [

                    "key" => 'different_shades',
                    "value" => $product->additional_different_shades,
                    "type" => "multi_line_text_field",
                    "namespace" => "additional_data",
                ],

                [
                    "key" => 'faqs',
                    "value" => $product->additional_faqs,
                    "type" => "multi_line_text_field",
                    "namespace" => "additional_data",
                ],



            ];

//        if($product->status==0 || $product->status==3 )
        if($product->status==0 || $product->status==3 || $product->status==1) {
            if ($product->shopify_status == 'Pending') {
                $category = Category::find($product->category);
                $variants = [];
                $product_info = ProductInfo::where('product_id', $product->id)->get();
                $options_array = [];


                $option_name = [];
                $option_value = [];
                $option1_name = [];
                $option1_value = [];
                $groupedData = [];
                $groupedData1 = [];


                $upload_product = 0;
                foreach ($product_info as $index => $v) {

                    if ($v->stock) {
                        $upload_product = 1;
                    }

                    if($store->name=='Kalamandir'){
                        $variants[] = array(
//                    "title" => $v->varient_name,
                            "option1" => $v->varient_value,
                            "option2" => $v->varient1_value,
                            "sku" => $v->sku,
                            "price" => $v->price_usd,
                            "grams" => $v->pricing_weight,
                            "taxable" => false,
                            "inventory_management" => "shopify",
                            "inventory_quantity" => $v->qty,
                        );
                    }else {

                        $variants[] = array(
//                    "title" => $v->varient_name,
                            "option1" => $v->varient_value,
                            "option2" => $v->varient1_value,
                            "sku" => $v->sku,
                            "price" => $v->price_usd,
                            "grams" => $v->pricing_weight,
                            "taxable" => false,
                            "inventory_management" => ($v->stock ? null : "shopify"),
                        );
                    }
                    $varientName = $v->varient_name;
                    $varientValue = $v->varient_value;


                    $varient1Name = $v->varient1_name;
                    $varient1Value = $v->varient1_value;


                    if ($varientName != '' || $varientName != null) {
                        // Check if the varient_name already exists in the grouped data array
                        if (array_key_exists($varientName, $groupedData)) {
                            // If it exists, add the varient_value to the existing array
                            $groupedData[$varientName]['value'][] = $varientValue;
                        } else {
                            // If it doesn't exist, create a new entry with the varient_name and an array containing the varient_value
                            $groupedData[$varientName] = [
                                'name' => $varientName,
                                'value' => [$varientValue]
                            ];
                        }
                    }


                    if ($varient1Name != '' || $varient1Name != null) {
                        // Check if the varient_name already exists in the grouped data array
                        if (array_key_exists($varient1Name, $groupedData1)) {
                            // If it exists, add the varient_value to the existing array
                            $groupedData1[$varient1Name]['value'][] = $varient1Value;
                        } else {
                            // If it doesn't exist, create a new entry with the varient_name and an array containing the varient_value
                            $groupedData1[$varient1Name] = [
                                'name' => $varient1Name,
                                'value' => [$varient1Value]
                            ];
                        }
                    }


                }


// Convert the grouped data into a simple indexed array
                $result_options = array_values($groupedData);
                $result1_options = array_values($groupedData1);


//        dd($result_options,$result1_options);
                foreach ($result_options as $index => $result_option) {

                    array_push($options_array, [
                        'name' => $result_option['name'],
                        'position' => $index + 1,
                        'values' => $result_option['value']
                    ]);
                }
                foreach ($result1_options as $index => $result1_option) {
                    array_push($options_array, [
                        'name' => $result1_option['name'],
                        'position' => $index + 1,
                        'values' => $result1_option['value']
                    ]);
                }

//			if($product_info[0]->varient_name!='')
//			$opt[]=array('name' => $product_info[0]->varient_name);
//		else
//			$opt[]=array('name' => 'Title');

                $tags = $product->tags;
                if ($product->orignal_vendor) {
                    $result = strcmp($store->name, $product->orignal_vendor);
                    if ($result != 0) {
                        $tags = $product->tags . ',' . $product->orignal_vendor;
                    }
                }

                $use_store_hsncode = 0;
                if ($product->product_type_id) {
                    $product_type_check = ProductType::find($product->product_type_id);
                    if ($product_type_check) {
                        if ($product_type_check->hsn_code) {
                            $use_store_hsncode = 1;
                            $tags = $tags . ',HSN:' . $product_type_check->hsn_code;

                        }
                        $tags = $tags . ',' . $product_type_check->product_type;
                    }
                }

                if ($store && $store->hsn_code) {
                    if ($use_store_hsncode == 0) {
                        $tags = $tags . ',HSN:' . $store->hsn_code;
                    }
                }


                $products_array = array(
                    "product" => array(
                        "title" => $product->title,
                        "body_html" => $product->body_html,
                        "vendor" => $store->name,
                        //"product_type" => $category->category,
                        "product_type" => $category->category ?? '',
                        "published" => true,
                        "tags" => explode(",", $tags),
                        "variants" => $variants,
                        "options" => $options_array,
                        "metafields" => $metafield_data
                    )
                );


                //echo "<pre>"; print_r($products_array); die();

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


//            $API_KEY = 'fd46f1bf9baedd514ed7075097c53995';
//            $PASSWORD = 'shpua_daf4f90db21249801ebf3d93bdfd0335';
//            $SHOP_URL = 'cherrpick-zain.myshopify.com';


                if ($upload_product) {
                    $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2023-01/products.json";
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
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($products_array));
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

                    $response = curl_exec($curl);


                    curl_close($curl);
                    $result = json_decode($response, true);



                    $shopify_product_id = $result['product']['id'];
                    $shopify_handle = $result['product']['handle'];
                    //echo "<pre>"; print_r($result); die();

                    $variant_ids_array = array();
                    Product::where('id', $product->id)->update(['shopify_id' => $shopify_product_id, 'handle' => $shopify_handle, 'status' => '1', 'approve_date' => Carbon::now()]);
                    foreach ($result['product']['variants'] as $prd) {
                        array_push($variant_ids_array, $prd['id']);
                        ProductInfo::where('sku', $prd['sku'])->update(['inventory_item_id' => $prd['inventory_item_id'], 'inventory_id' => $prd['id'], 'price_status' => '0']);
                        $location_id = Helpers::DiffalultLocation();
                        ProductInventoryLocation::updateOrCreate(
                            ['items_id' => $prd['inventory_item_id'], 'location_id' => $location_id],
                            ['items_id' => $prd['inventory_item_id'], 'stock' => $prd['inventory_quantity'], 'location_id' => $location_id]
                        );

                    }
                    $this->shopifyUploadeImage($product->id, $shopify_product_id, $variant_ids_array);

                    $values = array();
                    foreach ($product_info as $index => $v) {

                        $value = [
                            "hex_code" => $v->hex_code,
                            "swatch_image" => $v->swatch_image,
                            "volume" => $v->volume,
                            'dimensions' => $v->dimensions_text,
                            'shelf_life' => $v->shelf_life,
                            'temp_require' => $v->temp_require,
                            'height' => $v->height,
                            'width' => $v->width,
                            'length' => $v->length,
                            'sku' => $v->sku
                        ];
                        array_push($values, $value);
                    }


                    $metafield_variant_data = [
                        "metafield" =>
                            [
                                "key" => 'detail',
                                "value" => json_encode($values),
                                "type" => "json_string",
                                "namespace" => "variants",

                            ]
                    ];


                    $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/products/$shopify_product_id/metafields.json";

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
                    //curl_setopt($curl, CURLOPT_HEADER, 1);
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
                    //curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($metafield_variant_data));
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

                    $response1 = curl_exec($curl);

                    curl_close($curl);


                    //$this->linkProductToCollection($shopify_product_id,$store->collections_ids);

                    $product->shopify_status = 'Complete';
                    $product->save();

                    ProductInfo::where('product_id', $product->id)->update(['price_status' => '0']);
                } else {
                    return redirect()->to('superadmin/approved-products/all')->with('error', 'Product Out of Stock.');
                }
            }
        }

        else if($product->status==2)
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

            $shopify_id=$product->shopify_id;
            $SHOPIFY_API_meta = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/products/$shopify_id/metafields.json";

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $SHOPIFY_API_meta);
            $headers = array(
                "Authorization: Basic " . base64_encode("$API_KEY:$PASSWORD"),
                "Content-Type: application/json",
                "charset: utf-8"
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_VERBOSE, 0);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
//            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            $response = curl_exec($curl);
            curl_close($curl);
            $res = json_decode($response, true);

            if(isset($res['metafields'])) {
                foreach ($res['metafields'] as $ress) {

                    if ($ress['key'] =='key_ingredients') {
                        $SHOPIFY_update = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/metafields/" . $ress['id'] . ".json";
                        $curl = curl_init();
                        curl_setopt($curl, CURLOPT_URL, $SHOPIFY_update);
                        $headers = array(
                            "Authorization: Basic " . base64_encode("$API_KEY:$PASSWORD"),
                            "Content-Type: application/json",
                            "charset: utf-8"
                        );
                        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($curl, CURLOPT_VERBOSE, 0);
                        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
                        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
//            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($metafield_data));
                        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                        $response = curl_exec($curl);

                        curl_close($curl);
                    }
                    if ($ress['key'] =='how_to_use') {
                        $SHOPIFY_update = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/metafields/" . $ress['id'] . ".json";
                        $curl = curl_init();
                        curl_setopt($curl, CURLOPT_URL, $SHOPIFY_update);
                        $headers = array(
                            "Authorization: Basic " . base64_encode("$API_KEY:$PASSWORD"),
                            "Content-Type: application/json",
                            "charset: utf-8"
                        );
                        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($curl, CURLOPT_VERBOSE, 0);
                        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
                        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
//            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($metafield_data));
                        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                        $response = curl_exec($curl);

                        curl_close($curl);
                    }
                    if ($ress['key'] =='who_can_use') {
                        $SHOPIFY_update = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/metafields/" . $ress['id'] . ".json";
                        $curl = curl_init();
                        curl_setopt($curl, CURLOPT_URL, $SHOPIFY_update);
                        $headers = array(
                            "Authorization: Basic " . base64_encode("$API_KEY:$PASSWORD"),
                            "Content-Type: application/json",
                            "charset: utf-8"
                        );
                        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($curl, CURLOPT_VERBOSE, 0);
                        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
                        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
//            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($metafield_data));
                        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                        $response = curl_exec($curl);

                        curl_close($curl);
                    }
                    if ($ress['key'] =='why_mama_earth') {
                        $SHOPIFY_update = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/metafields/" . $ress['id'] . ".json";
                        $curl = curl_init();
                        curl_setopt($curl, CURLOPT_URL, $SHOPIFY_update);
                        $headers = array(
                            "Authorization: Basic " . base64_encode("$API_KEY:$PASSWORD"),
                            "Content-Type: application/json",
                            "charset: utf-8"
                        );
                        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($curl, CURLOPT_VERBOSE, 0);
                        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
                        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
//            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($metafield_data));
                        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                        $response = curl_exec($curl);

                        curl_close($curl);
                    }
                    if ($ress['key'] =='different_shades') {
                        $SHOPIFY_update = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/metafields/" . $ress['id'] . ".json";
                        $curl = curl_init();
                        curl_setopt($curl, CURLOPT_URL, $SHOPIFY_update);
                        $headers = array(
                            "Authorization: Basic " . base64_encode("$API_KEY:$PASSWORD"),
                            "Content-Type: application/json",
                            "charset: utf-8"
                        );
                        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($curl, CURLOPT_VERBOSE, 0);
                        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
                        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
//            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($metafield_data));
                        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                        $response = curl_exec($curl);

                        curl_close($curl);
                    }
                    if ($ress['key'] =='faqs') {
                        $SHOPIFY_update = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/metafields/" . $ress['id'] . ".json";
                        $curl = curl_init();
                        curl_setopt($curl, CURLOPT_URL, $SHOPIFY_update);
                        $headers = array(
                            "Authorization: Basic " . base64_encode("$API_KEY:$PASSWORD"),
                            "Content-Type: application/json",
                            "charset: utf-8"
                        );
                        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($curl, CURLOPT_VERBOSE, 0);
                        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
                        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
//            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($metafield_data));
                        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                        $response = curl_exec($curl);

                        curl_close($curl);
                    }
                    if($ress['namespace']=='variants'){
                        $SHOPIFY_update = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/metafields/" . $ress['id'] . ".json";
                        $curl = curl_init();
                        curl_setopt($curl, CURLOPT_URL, $SHOPIFY_update);
                        $headers = array(
                            "Authorization: Basic " . base64_encode("$API_KEY:$PASSWORD"),
                            "Content-Type: application/json",
                            "charset: utf-8"
                        );
                        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($curl, CURLOPT_VERBOSE, 0);
                        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
                        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
//            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($metafield_data));
                        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                        $response = curl_exec($curl);

                        curl_close($curl);

                    }

                }
            }


            $options_array=array();
            $category=Category::find($product->category);


            $product_infos =ProductInfo::where('product_id',$product['id'])->get();
            $groupedData = [];
            $groupedData1 = [];
            $values = array();
            foreach ($product_infos as $index=> $product_info) {


                $value = [
                    "hex_code" => $product_info->hex_code,
                    "swatch_image" => $product_info->swatch_image,
                    "volume" => $product_info->volume,
                    'dimensions' => $product_info->dimensions_text,
                    'shelf_life' => $product_info->shelf_life,
                    'temp_require' => $product_info->temp_require,
                    'height' => $product_info->height,
                    'width' => $product_info->width,
                    'length' => $product_info->length,
                    'sku'=>$product_info->sku
                ];
                array_push($values, $value);

                $varientName = $product_info->varient_name;
                $varientValue = $product_info->varient_value;


                $varient1Name = $product_info->varient1_name;
                $varient1Value = $product_info->varient1_value;


                if($varientName!=''|| $varientName!=null){
                    // Check if the varient_name already exists in the grouped data array
                    if (array_key_exists($varientName, $groupedData)) {
                        // If it exists, add the varient_value to the existing array
                        $groupedData[$varientName]['value'][] = $varientValue;
                    } else {
                        // If it doesn't exist, create a new entry with the varient_name and an array containing the varient_value
                        $groupedData[$varientName] = [
                            'name' => $varientName,
                            'value' => [$varientValue]
                        ];
                    }
                }


                if($varient1Name!=''|| $varient1Name!=null){
                    // Check if the varient_name already exists in the grouped data array
                    if (array_key_exists($varient1Name, $groupedData1)) {
                        // If it exists, add the varient_value to the existing array
                        $grouped1Data[$varient1Name]['value'][] = $varient1Value;
                    } else {
                        // If it doesn't exist, create a new entry with the varient_name and an array containing the varient_value
                        $groupedData[$varient1Name] = [
                            'name' => $varient1Name,
                            'value' => [$varient1Value]
                        ];
                    }
                }

            }
            $metafield_variant_data=[
                "metafield" =>
                    [
                        "key" => 'detail',
                        "value" => json_encode($values),
                        "type" => "json_string",
                        "namespace" => "variants",

                    ]
            ];


            $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/products/$shopify_id/metafields.json";

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
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($metafield_variant_data));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

            $response1 = curl_exec ($curl);


            curl_close ($curl);


            $result_options = array_values($groupedData);
            $result1_options = array_values($groupedData1);

            foreach ($result_options as $index=>  $result_option) {

                array_push($options_array, [
                    'name' => $result_option['name'],
                    'position' => $index + 1,
                    'values' => $result_option['value']
                ]);

            }
            foreach ($result1_options as $index=>  $result1_option) {
                array_push($options_array, [
                    'name' => $result1_option['name'],
                    'position' => $index + 1,
                    'values' => $result1_option['value']
                ]);
            }

            $tags=$product->tags;
            if($product->orignal_vendor){
                $result = strcmp($store->name, $product->orignal_vendor);
                if ($result != 0) {
                    $tags = $product->tags . ',' . $product->orignal_vendor;
                }
            }

            $use_store_hsncode=0;
            if($product->product_type_id){
                $product_type_check=ProductType::find($product->product_type_id);
                if($product_type_check){
                    if($product_type_check->hsn_code) {
                        $use_store_hsncode=1;
                        $tags = $tags . ',HSN:' . $product_type_check->hsn_code;

                    }
                    $tags=$tags.','.$product_type_check->product_type;
                }
            }

            if($store && $store->hsn_code){
                if($use_store_hsncode==0){
                    $tags = $tags . ',HSN:' . $store->hsn_code;
                }
            }

            $data['product']=array(
                    "id" => $shopify_id,
                    "title" => $product->title,
                    "tags"   => $tags,
                    "product_type" => $category->category,
                "options"     =>  $options_array,
                "metafields"=>$metafield_data

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
            Product::where('id', $product['id'])->update(['edit_status' => 0, 'status' => '1', 'approve_date' => Carbon::now()]);



            if(count($product_infos) > 0) {

                foreach ($product_infos as $index=> $product_info) {
                    try {

                        //update stock on live store
                        $productController = new ProductController();
                        $productController->updateStockLiveStore($product_info->inventory_id, $product_info->stock, $product_info->inventory_item_id);
                        //update variant
                        $productController->updateVarianatLiveStore($product_info->id);
                        ///create new varient
                        $invid = $product_info->inventory_id;




                        if ($product_info->varient_name != '' && $product_info->varient_value != '') {
                            if($store->name=='Kalamandir'){
                                $data['variant'] = array(
                                    "id" => $invid,
                                    "option1" => $product_info->varient_value,
                                    "option2" => $product_info->varient1_value,
                                    "sku" => $product_info->sku,
                                    "price" => $product_info->price_usd,
                                    "compare_at_price" => $product_info->price_usd,
                                    "grams" => $product_info->pricing_weight,
                                    "taxable" => false,
                                    "inventory_management" => "shopify",
                                    "inventory_quantity" => $product_info->qty,
                                );

                            }else {

                                $data['variant'] = array(
                                    "id" => $invid,
                                    "option1" => $product_info->varient_value,
                                    "option2" => $product_info->varient1_value,
                                    "sku" => $product_info->sku,
                                    "price" => $product_info->price_usd,
                                    "compare_at_price" => $product_info->price_usd,
                                    "grams" => $product_info->pricing_weight,
                                    "taxable" => false,
                                    "inventory_management" => ($product_info->stock) ? null : "shopify",
                                );
                            }




                        } else {

                            if ($store->name == 'Kalamandir') {

                                $data['variant'] = array(
                                    "id" => $invid,
                                    "sku" => $product_info->sku,
                                    "price" => $product_info->price_usd,
                                    "compare_at_price" => $product_info->price_usd,
                                    "grams" => $product_info->pricing_weight,
                                    "taxable" => false,
                                    "inventory_management" => "shopify",
                                    "inventory_quantity" => $product_info->qty,
                                );
                            }else{
                                $data['variant'] = array(
                                    "id" => $invid,
                                    "sku" => $product_info->sku,
                                    "price" => $product_info->price_usd,
                                    "compare_at_price" => $product_info->price_usd,
                                    "grams" => $product_info->pricing_weight,
                                    "taxable" => false,
                                    "inventory_management" => ($product_info->stock) ? null : "shopify",
                                );
                            }
                        }






//                $API_KEY = '6bf56fc7a35e4dc3879b8a6b0ff3be8e';
//                $PASSWORD = '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';
//                $SHOP_URL = 'cityshop-company-store.myshopify.com';
                        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/variants/$invid.json";
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
                        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
                        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                        $response = curl_exec($curl);
                        curl_close($curl);
                        $res = json_decode($response, true);
                        //echo "<pre>"; print_r($data); print_r($res); die();

                        ////Update Image for variant
                        $productDetails = Product::find($product_info->product_id);
                        if ($productDetails->shopify_id != null && $productDetails->status == 1) {
                            $shopify_product_id = $productDetails->shopify_id;
                            $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2020-04/products/$shopify_product_id/images.json";
                            $variant_id = $product_info->id;
                            $imagesResult = ProductImages::where('variant_ids', $variant_id)->first();
                            if ($imagesResult) {
                                $data['image'] = array(
                                    'src' => $imagesResult->image,
                                    'variant_ids' => array($product_info->inventory_id),
                                );
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
                                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
                                curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
                                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                                $response = curl_exec($curl);
                                curl_close($curl);
                                $img_result = json_decode($response, true);
                                ProductImages::where('id', $imagesResult->id)->update(['image_id' => $img_result['image']['id']]);
                            }
                        }
                        $location_id = Helpers::DiffalultLocation();
                        ProductInventoryLocation::updateOrCreate(
                            ['items_id' => $product_info->inventory_item_id, 'location_id' => $location_id],
                            ['items_id' => $product_info->inventory_item_id, 'stock' => $product_info->stock, 'location_id' => $location_id]
                        );


                    }catch (\Exception $exception){
                        dd($exception->getMessage());
                    }
            }
            }


        }
        return redirect()->to('superadmin/approved-products/all')->with('success','Product Created Successfully.');
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
    public function shopifyUploadeImage($id,$shopify_id,$variant_ids_array)
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
        foreach($product_images as $index=> $img_val)
        {
            if($img_val->variant_ids && isset($variant_ids_array[$index])) {

                $data['image'] = array(
                    'src' => $img_val->image,
                    'alt' => $img_val->alt_text,
                    'variant_ids' => [$variant_ids_array[$index]]

                );
            }else{
                $data['image'] = array(
                    'src' => $img_val->image,
                    'alt' => $img_val->alt_text,


                );
            }

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
			$img_result=json_decode($response, true);

			if(isset($img_result['image']['id']))
			ProductImages::where('id', $img_val->id)->update(['image_id' => $img_result['image']['id']]);


            if($img_val->image2) {
                $data['image'] = array(
                    'src' => $img_val->image2,
                    'alt' => $img_val->alt_text,

                );
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
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                $response = curl_exec($curl);
                $img_result = json_decode($response, true);

            }

            if($img_val->image3) {
                $data['image'] = array(
                    'src' => $img_val->image3,
                    'alt' => $img_val->alt_text,


                );
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
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                $response = curl_exec($curl);
                $img_result = json_decode($response, true);

            }

            if($img_val->image4) {
                $data['image'] = array(
                    'src' => $img_val->image4,
                    'alt' => $img_val->alt_text,


                );
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
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                $response = curl_exec($curl);
                $img_result = json_decode($response, true);
            }

            if($img_val->image5) {
                $data['image'] = array(
                    'src' => $img_val->image5,
                    'alt' => $img_val->alt_text,


                );
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
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                $response = curl_exec($curl);
                $img_result = json_decode($response, true);
            }
        }
    }






	 public function pricecalculate($id)
	{
            return Excel::download(new PriceExport($id), 'price.xlsx');
//            $Tags=array('dfggdf','Namkeen');
//            $product=array('Tags' => $Tags, 'Variant Price' => '100', 'Variant Grams' => '350');
//            $location_id=Helpers::calc_price(100,400);
//            echo "<pre>"; print_r($location_id);
	}
   public function logout(){
   	Session::flush();
    Auth::logout();
   return redirect()->route('login');

   }

   ///Fetch product from url and store in db
   public function fetchProductUrl(Request $request)
   {
   //echo "<pre>"; print_r($request->all()); die;

	   set_time_limit(0);
	   $request->validate([
			'username' => 'required',
            'url'=>'required|min:6',
            'password'=>'required|min:8',
        ]);

		$vendor=Store::where('name', $request->username)->first();

		$check=DB::table('cron_json_url')->where('url',$request->url)->where('type','fetch_from_url')->get();

		if($vendor)
		{
			$vid=$vendor->id;

		}
		else{
			$store = new Store;
			$store->name = $request->username;
			$store->email = $request->username.'@gmail.com';
			$store->role = 'Vendor';
			$store->username = $request->username;
			$store->password = Hash::make($request->password);
			$store->save();
            $adminController=new AdminController();
            $collection_id=$adminController->createCollection($request->username);
            Store::where('id', $store->id)->update(['collections_ids' => $collection_id]);
			$vid=$store->id;
		}


		if(sizeof($check) == 0){
		DB::table('cron_json_url')->insert(['vendor_id' =>$vid , 'url' => $request->url,'type'=>'fetch_from_url']);

		}

            $context = stream_context_create(
                array(
                    "http" => array(
                        "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36"
                    )
                )
            );
			 $url=$request->url;
			 $tag_url=$request->tag;

			$headers = @get_headers("https://".$url."/collections/all/products.json");
			if(!$headers || strpos( $headers[0], '404')) {
				return back()->with('error','Invalid Url');
			}

       Product::where('vendor', $vid)->update(['is_available' => 0]);
			//$vid=0;
			for($i=1;$i<=100;$i++)
			{
				$str=file_get_contents("https://".$url."/collections/all/products.json?page=".$i."&limit=250", false, $context);
                $arr=json_decode($str,true);

				if(count($arr['products']) < 250)
				{
					$this->saveStoreFetchProductsFromJson($arr['products'],$vid,$tag_url);
					return back()->with('success','Product imported successfully');
				}
				else
				{
					$this->saveStoreFetchProductsFromJson($arr['products'],$vid,$tag_url);

				}
				//echo "<pre>"; print_r($arr['products']); die();
			}



       $delete_products=Product::where('vendor', $vid)->whereNull('shopify_id')->where('is_available',0)->get();
       foreach ($delete_products as $delete_product){

           ProductInfo::where('product_id',$delete_product->id)->delete();
           $delete_product->delete();
       }

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

       $draft_products=Product::where('vendor',$vid)->whereNotNull('shopify_id')->where('is_updated_by_url',0)->get();
       $update_products=Product::where('vendor',$vid)->whereNotNull('shopify_id')->where('is_updated_by_url',1)->get();


       $data['product']=array(
           "status" =>'draft',
       );

       foreach ($draft_products as $draft_product){



           $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/products/$draft_product->shopify_id.json";
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

       foreach ($update_products as $update_product){
           $upload_product=0;
           $product_variants=ProductInfo::where('product_id',$update_product->id)->get();
           $variants=[];
           foreach ($product_variants as $product_variant){
               if($product_variant->stock) {
                   $upload_product = 1;
               }
               $variants[]=array(
                   "option1" => $product_variant->varient_value,
                   "option2" => $product_variant->varient1_value,
                   "sku"     => $product_variant->sku,
                   "price"   => $product_variant->price_usd,
                   "grams"   => $product_variant->pricing_weight,
                   "taxable" => false,
                   "inventory_management" => ($product_variant->stock ? null :"shopify"),
               );
           }

           $products_array = array(
               "product" => array(
                   "status"=>'active',
                   "variants"=>$variants,
               )
           );
           if($upload_product) {

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

           }else{

               $products_array = array(
                   "product" => array(
                       "status"=>'draft',
                       "variants"=>$variants,

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


       }


       Product::where('vendor',$vid)->update(['is_updated_by_url'=>0]);


   }
   public function fetchProductFromAPI(Request $request)
   {


       set_time_limit(0);
       $request->validate([
           'username' => 'required',
           'api_link' => 'required|min:6',
           'password' => 'required|min:8',
       ]);

       $vendor = Store::where('name', $request->username)->first();

       $check = DB::table('cron_json_url')->where('api_link', $request->api_link)->where('type', 'fetch_from_api')->get();

       if ($vendor) {
           $vid = $vendor->id;

       } else {
           $store = new Store;
           $store->name = $request->username;
           $store->email = $request->username . '@gmail.com';
           $store->role = 'Vendor';
           $store->username = $request->username;
           $store->password = Hash::make($request->password);
           $store->save();
           $adminController = new AdminController();
           $collection_id = $adminController->createCollection($request->username);
           Store::where('id', $store->id)->update(['collections_ids' => $collection_id]);
           $vid = $store->id;
       }


       if (sizeof($check) == 0) {
           DB::table('cron_json_url')->insert(['vendor_id' => $vid, 'api_link' => $request->api_link, 'type' => 'fetch_from_api', 'authorization_token' => $request->authorization_token]);

       }

       Product::where('vendor', $vid)->update(['is_available' => 0]);




       $curl = curl_init();

       curl_setopt_array($curl, array(
           CURLOPT_URL => $request->api_link,
           CURLOPT_RETURNTRANSFER => true,
           CURLOPT_ENCODING => '',
           CURLOPT_MAXREDIRS => 10,
           CURLOPT_TIMEOUT => 0,
           CURLOPT_FOLLOWLOCATION => true,
           CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
           CURLOPT_CUSTOMREQUEST => 'GET',
           CURLOPT_HTTPHEADER => array(
               'Authorization: Bearer ' . $request->authorization_token,
               'Cookie: PHPSESSID=ub0mpqgtmvauj6qjf90s74u6e9'
           ),
       ));

       $response = curl_exec($curl);

       curl_close($curl);
       $data = json_decode($response, true);


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
               $store_id = $vid;
               $description = $description;

               $tags = '';
               if (isset($row['extension_attributes']) && count($row['extension_attributes']) > 0) {
                   if (isset($row['extension_attributes']['category_links']) && count($row['extension_attributes']['category_links']) > 0) {

                       foreach ($row['extension_attributes']['category_links'] as $category_link) {
                           $get_tag = ThirdPartyAPICategory::where('category_id', $category_link['category_id'])->where('vendor_id', $vid)->first();
                           if ($get_tag) {
                               $tags = $tags . ',' . $get_tag->name;
                           }
                       }
                   }

               }


               $category_id = isset($row['extension_attributes']['category_links'])?$row['extension_attributes']['category_links'][0]['category_id']:null;

               $product_type = $this->GetProductType($category_id, $vid);
           $product_type_id=null;
               if($product_type){
                    $product_type_id=$product_type->id;
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
                   $product->save();
                   $product_id = $product->id;


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

               }
               else {

                   $product_check->title = $title;
                   $product_check->body_html = $description;
                   $product_check->is_updated_by_url = 1;
                   $product_check->product_type_id = $product_type_id;
                   $product_check->is_available = 1;
                   $product_check->tags = $tags;
                   $product_check->save();

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


                   $product_info = ProductInfo::where('product_id', $product_check->id)->where('sku', $row['sku'])->first();
                   if ($product_info == null) {
                       $product_info = new ProductInfo;
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

       $delete_products=Product::where('vendor', $vid)->whereNull('shopify_id')->where('is_available',0)->get();
       foreach ($delete_products as $delete_product){

           ProductInfo::where('product_id',$delete_product->id)->delete();
           $delete_product->delete();
       }

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

       $draft_products=Product::where('vendor',$vid)->whereNotNull('shopify_id')->where('is_updated_by_url',0)->get();
       $update_products=Product::where('vendor',$vid)->whereNotNull('shopify_id')->where('is_updated_by_url',1)->get();


       $data['product']=array(
           "status" =>'draft',
       );

       foreach ($draft_products as $draft_product){



           $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/products/$draft_product->shopify_id.json";
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

       foreach ($update_products as $update_product){
           $upload_product=0;
           $product_variants=ProductInfo::where('product_id',$update_product->id)->get();
           $variants=[];
           foreach ($product_variants as $product_variant){
               if($product_variant->stock) {
                   $upload_product = 1;
               }
               $variants[]=array(

                   "sku"     => $product_variant->sku,
                   "price"   => $product_variant->price_usd,
                   "grams"   => $product_variant->pricing_weight,
                   "taxable" => false,
                   "inventory_management" => ($product_variant->stock ? null :"shopify"),

               );
           }

           $products_array = array(
               "product" => array(
                   "status"=>'active',
                   "variants"=>$variants,
               )
           );
           if($upload_product) {

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

           }else{

               $products_array = array(
                   "product" => array(
                       "status"=>'draft',
                       "variants"=>$variants,

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


       }


       Product::where('vendor',$vid)->update(['is_updated_by_url'=>0]);


   }
//    function saveStoreFetchProductsFromJson($products,$vid,$tag_url=null)
//	{
//
//		//echo "<pre>"; print_r($products); die;
//		foreach($products as $index=> $row)
//		{
//			$pid=0;
//			foreach($row['variants'] as $var)
//			{
//
//				$check=ProductInfo::where('sku',$var['sku'])->first();
//
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
////								$img = "uploads/shopifyimages/".$img_val['id'].".jpg";
////								file_put_contents($img, file_get_contents($url));
////								$img_name=url($img);
//                                $img_name=$url;
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
////								$img = "uploads/shopifyimages/".$img_val['id'].".jpg";
////								file_put_contents($img, file_get_contents($url));
////								$img_name=url($img);
//                                $img_name=$url;
//								$product_img = new ProductImages;
//								$product_img->image = $img_name;
//								//$product_img->image_id = $img_val['id'];
//								$product_img->product_id = $product_id;
//								$product_img->save();
//							}
//                        }
//			}
//
//		}
//
//	}
    function saveStoreFetchProductsFromJson($products,$vid,$tag_url=null,$log_id=null)
	{


        //echo "<pre>"; print_r($products); die;
		foreach($products as $index=> $row)
		{

//            if($row['id']==8439899652389) {

                $html = $row['body_html'];

                //remove image
                $html = preg_replace('/<img[^>]*>/', '', $html);
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

                $product_check = Product::where('reference_shopify_id', $row['id'])->where('vendor', $vid)->first();

                $product_type = ProductType::where('product_type', $row['product_type'])->where('vendor_id', $vid)->first();
                if ($product_type == null) {
                    $product_type = new ProductType();
                }
                $product_type->product_type = $row['product_type'];
                $product_type->vendor_id = $vid;
                $product_type->save();


                //echo $pid; die;
                if ($product_check == null)  ////////New Product
                {
                    $cat = Category::where('category', $row['product_type'])->first();
                    if ($cat)
                        $category_id = $cat->id;
                    else {
                        $cate_que = new Category;
                        $cate_que->category = $row['product_type'];
                        $cate_que->save();
                        $category_id = $cate_que->id;
                    }


                    $shopify_id = $row['id'];
                    $title = $row['title'];
                    $description = $html;
                    $vendor = $row['vendor'];
                    $tags = implode(",", $row['tags']);
                    $handle = $row['handle'];
                    $store_id = $vid;

                    $product = new Product;
                    $product->title = $title;
                    $product->reference_shopify_id = $shopify_id;
                    $product->body_html = $description;
                    $product->vendor = $store_id;
                    $product->orignal_vendor = $vendor;
                    $product->tags = $tags;
                    $product->category = $category_id;
                    $product->product_type_id = $product_type->id;
                    $product->is_updated_by_url = 1;
                    $product->is_available = 1;
                    $product->save();
                    $product_id = $product->id;

                    $product_logs = new ProductLog();
                    $product_logs->title = 'Product Created';
                    $product_logs->date_time = now()->format('F j, Y H:i:s');
                    $product_logs->product_id = $product_id;
                    $product_logs->log_id = $log_id;
                    $product_logs->save();

                    $i = 0;


                    $grams = 0;
                    $store = Store::find($vid);
                    if ($store->base_weight) {
                        $grams = $store->base_weight;
                    }
                    if ($product_type && $product_type->base_weight) {
                        $grams = $product_type->base_weight;
                    }
                    $grams_selected = 0;
                    if ($row['variants'][0]['grams'] > 0) {
                        $grams_selected = 1;
                        $grams = $row['variants'][0]['grams'];
                    } else {
                        foreach ($row['variants'] as $var) {
                            if ($var['grams'] > 0 && $grams_selected == 0) {
                                $grams_selected = 1;
                                $grams = $var['grams'];
                            }
                        }
                    }

                    foreach ($row['variants'] as $var) {

                        $variant_grams = ($var['grams'] > 0) ? $var['grams'] : $grams;
                        $pricing_weight = $variant_grams;

                        if ($product_type && $product_type->base_weight) {
                            $pricing_weight = max($variant_grams, $product_type->base_weight);
                        }
                        $i++;
                        $check = ProductInfo::where('reference_shopify_id', $var['id'])->where('product_id', $product_id)->first();


                        if ($check == null) {

                            if ($var['sku']) {
                                $sku = $var['sku'];
                            } else {
                                if ($store->sku_count < 10) {
                                    $count = $store->sku_count + 1;
                                    if ($product_type && $product_type->product_type) {
                                        $sku = $store->name . '-' . $product_type->product_type . '-0' . $count;
                                    } else {
                                        $sku = $store->name . '-0' . $count;
                                    }
                                } else {
                                    $count = $store->sku_count + 1;
                                    if ($product_type && $product_type->product_type) {
                                        $sku = $store->name . '-' . $product_type->product_type . '-' . $count;
                                    } else {
                                        $sku = $store->name . '-' . $count;
                                    }
                                }
                                $store->sku_count = $store->sku_count + 1;
                                $store->save();
                            }

                            $prices = Helpers::calc_price_fetched_products_by_vendor($vid, $var['price'], $pricing_weight);
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
                            $product_info->vendor_id = $store_id;
                            $product_info->reference_shopify_id = $var['id'];
                            $product_info->dimensions = '0-0-0';
                            if (isset($row['options'])) {
                                $product_info->varient_name = $row['options'][0]['name'];
                            }

                            if (isset($row['options']) && isset($row['options'][1])) {
                                $product_info->varient1_name = $row['options'][1]['name'];
                            }
                            $product_info->varient_value = $var['option1'];
                            $product_info->varient1_value = $var['option2'];
                            $product_info->save();
                        }
                    }
                    if ($i > 1) {
                        Product::where('id', $product_id)->update(['is_variants' => 1]);
                    }
                    foreach ($row['images'] as $img_val) {
                        $imgCheck = ProductImages::where('image_id', $img_val['id'])->where('product_id',$product_id)->exists();
                        if (!$imgCheck) {
                            $url = $img_val['src'];
//								$img = "uploads/shopifyimages/".$img_val['id'].".jpg";
//								file_put_contents($img, file_get_contents($url));
//								$img_name=url($img);
                            $img_name = $url;
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
                } else  //Existing Product
                {
                    $vendor = $row['vendor'];
                    $data['title'] = $row['title'];
                    $data['body_html'] = $html;
                    $data['tags'] = implode(",", $row['tags']);
                    $data['product_type_id'] = $product_type->id;
                    $data['orignal_vendor'] = $vendor;
                    $data['is_updated_by_url'] = 1;
                    $data['is_available'] = 1;
                    Product::where('id', $product_check->id)->update($data);
                    $product_id = $product_check->id;


                    $product_logs = new ProductLog();
                    $product_logs->title = 'Product Update';
                    $product_logs->date_time = now()->format('F j, Y H:i:s');
                    $product_logs->product_id = $product_id;
                    $product_logs->log_id = $log_id;
                    $product_logs->save();
                    $i = 0;


                    $grams = 0;
                    $store = Store::find($vid);
                    if ($store->base_weight) {
                        $grams = $store->base_weight;
                    }
                    if ($product_type && $product_type->base_weight) {
                        $grams = $product_type->base_weight;
                    }
                    $grams_selected = 0;
                    if ($row['variants'][0]['grams'] > 0) {
                        $grams_selected = 1;
                        $grams = $row['variants'][0]['grams'];
                    } else {
                        foreach ($row['variants'] as $var) {
                            if ($var['grams'] > 0 && $grams_selected == 0) {
                                $grams_selected = 1;
                                $grams = $var['grams'];
                            }
                        }
                    }


                    $check_info_v = ProductInfo::where('product_id', $product_check->id)->get();
                    foreach ($check_info_v as $v_get) {

                        if ($v_get->inventory_id == null) {
                            if ($v_get->manual_weight == 0) {
                                $v_get->delete();
                            }
                        }
                    }

                    foreach ($row['variants'] as $var) {

                        $variant_grams = ($var['grams'] > 0) ? $var['grams'] : $grams;

                        $pricing_weight = $variant_grams;

                        if ($product_type && $product_type->base_weight) {
                            $pricing_weight = max($variant_grams, $product_type->base_weight);
                        }
                        $i++;
                        $check_info = ProductInfo::where('reference_shopify_id', $var['id'])->first();
                        if ($var['sku']) {
                            $sku = $var['sku'];
                        } else {
                            if ($store->sku_count < 10) {
                                $count = $store->sku_count + 1;
                                if ($product_type && $product_type->product_type) {
                                    $sku = $store->name . '-' . $product_type->product_type . '-0' . $count;
                                } else {
                                    $sku = $store->name . '-0' . $count;
                                }
                            } else {
                                $count = $store->sku_count + 1;
                                if ($product_type && $product_type->product_type) {
                                    $sku = $store->name . '-' . $product_type->product_type . '-' . $count;
                                } else {
                                    $sku = $store->name . '-' . $count;
                                }
                            }
                            $store->sku_count = $store->sku_count + 1;
                            $store->save();
                        }
                        if (!$check_info) {


                            $prices = Helpers::calc_price_fetched_products_by_vendor($vid, $var['price'], $pricing_weight);
                            $product_info = new ProductInfo;
                            $product_info->product_id = $product_id;
                            $product_info->sku = $sku;
                            $product_info->reference_shopify_id = $var['id'];
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
                            $product_info->vendor_id = $vid;
                            $product_info->dimensions = '0-0-0';
                            if (isset($row['options'])) {
                                $product_info->varient_name = $row['options'][0]['name'];
                            }

                            if (isset($row['options']) && isset($row['options'][1])) {
                                $product_info->varient1_name = $row['options'][1]['name'];
                            }
                            $product_info->varient_value = $var['option1'];
                            $product_info->varient1_value = $var['option2'];

                            $product_info->save();
                        } else   //update variants
                        {

                            if ($check_info->manual_weight == 1) {
                                $pricing_weight = $check_info->pricing_weight;
                            }
                            $prices = Helpers::calc_price_fetched_products_by_vendor($vid, $var['price'], $pricing_weight);
                            $info_id = $check_info->id;
                            $info['price'] = $prices['inr'];
                            $info['price_usd'] = $prices['usd'];
                            $info['price_nld'] = $prices['nld'];
                            $info['price_gbp'] = $prices['gbp'];
                            $info['price_cad'] = $prices['cad'];
                            $info['price_aud'] = $prices['aud'];
                            $info['price_irl'] = $prices['nld'];
                            $info['price_ger'] = $prices['nld'];
                            $info['base_price'] = $prices['base_price'];
                            $info['grams'] = $variant_grams;
                            $info['sku'] = $sku;
                            if ($check_info->manual_weight == 0) {
                                $info['pricing_weight'] = $pricing_weight;
                            }
                            $info['stock'] = $var['available'];


                            if (isset($row['options'])) {
                                $info['varient_name'] = $row['options'][0]['name'];
                            }
                            if (isset($row['options']) && isset($row['options'][1])) {
                                $info['varient1_name'] = $row['options'][1]['name'];
                            }
                            $info['varient_value'] = $var['option1'];
                            $info['varient1_value'] = $var['option2'];
                            ProductInfo::where('id', $info_id)->update($info);
                        }
                    }
                    if ($i > 1) {
                        Product::where('id', $product_id)->update(['is_variants' => 1]);
                    }
                    foreach ($row['images'] as $img_val) {
                        $imgCheck = ProductImages::where('image_id', $img_val['id'])->where('product_id',$product_id)->exists();
                        if (!$imgCheck) {
                            $url = $img_val['src'];
//								$img = "uploads/shopifyimages/".$img_val['id'].".jpg";
//								file_put_contents($img, file_get_contents($url));
//								$img_name=url($img);
                            $img_name = $url;
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

//        }

	}
	public function changestatus(){
		$data = ProductInfo::get();

		foreach($data as $key => $val) {
			$price = $val->price;
			$grams = $val->grams;
			$mul = 0;

			if($val->dimensions != '' && $val->dimensions != '--' && $val->dimensions != '---' && $val->dimensions != '0' && $val->dimensions != '2'   ){
				$d = explode('-',$val->dimensions);
				if(sizeof($d) == 3){
                 if($d[2]!=''){

                   $mul = $d[0] * $d[1] * $d[2] /5000;
                }
				}
			//	echo $mul;

			}

           $product = Product::where('id',$val->product_id)->first();
           if($product){
		    $db = Helpers::calc_price_new($price,$grams,$product->tags,$mul);


		    ProductInfo::where('id',$val->id)->update([
		    	'base_price'=>$db['inr'],
		    	'price_usd'=>$db['usd'],
		    	'price_aud'=>$db['aud'],
		    	'price_cad'=>$db['cad'],
		    	'price_gbp'=>$db['gbp'],
		    	'price_nld'=>$db['nld'],
				'price_irl'=>$db['irl'],
				'price_ger'=>$db['ger'],
		    ]);
		   ProductInfo::where('id',$val->id)->update(['price_status'=> 1]);
		 	}



}
}
   public function download($file){
	   $file_path='uploads/banner/'.$file;
		return response()->download($file_path);
	}
	public function download_logo($file){
	   $file_path='uploads/logo/'.$file;
		return response()->download($file_path);
	}
//	public function uploadeBulkProducts(Request $request)
//	{
//
//        $request->validate([
//            'file'=>'required|mimes:xlsx,csv',
//        ]);
//        $file = $request->file('file');
//
//        $name = str_replace(' ', '', $file->getClientOriginalName());
//        $name = "recent_". $name;
//        $file->move(public_path() . '/', $name);
//        $hashName =  $name;
//
//
//        // Store the file in the public folder (e.g., storage/app/public/uploads)
////        $filePath = $uploadedFile->store('public');
////        $hashName = $uploadedFile->hashName();
//
//        // Get the actual file path in the public folder
//
////dd(asset('storage/uplaods/'.$article->image));
//
//        $filePath = Storage::disk('public')->get($hashName);
////        dd($filePath);
//        $vid=2;
////$file2=file_get_contents(asset($hashName));
////dd($file);
//
//        $data=Excel::import(new BluckProductImport($vid),asset($hashName));
//        dd($data);
//        UploadBulkProducts::dispatch($request->username, $request->password, $filePath);
//
//
//        return redirect()->back()->with('success', 'uploaded Successfully');
//       // return redirect()->route('superadmin.allproduct')->with('success','Product Rejected Successfully.');
//	}
	public function uploadeBulkProducts(Request $request)
	{

        $request->validate([
            'file'=>'required|mimes:xlsx,csv',
        ]);


        $vendor = Store::where('name',$request->username)->first();
        if ($vendor) {
            $vid = $vendor->id;
        } else {
            $store = new Store;
            $store->name = $request->username;
            $store->email = $request->username . '@gmail.com';
            $store->role = 'Vendor';
            $store->username = $request->username;
            $store->password = \Illuminate\Support\Facades\Hash::make($request->password);
            $store->save();
            $vid = $store->id;
        }


        $file=request()->file('file');
        $name = str_replace(' ', '', $file->getClientOriginalName());
        $name = "recent_". $name;
        $file->move(public_path() . '/', $name);
        $hashName =  public_path($name);
//        dd($hashName);

        // Store the file in the public folder (e.g., storage/app/public/uploads)
//        $filePath = $uploadedFile->store('public');
//        $hashName = $uploadedFile->hashName();

        // Get the actual file path in the public folder



//        $filePath = Storage::disk('public')->get($hashName);
//        dd($filePath);


        UploadBulkProducts::dispatch($hashName,$vid);


//        $data=Excel::import(new BluckProductImport($vid),request()->file('file'));



        return redirect()->back()->with('success', 'Import In Progress');
       // return redirect()->route('superadmin.allproduct')->with('success','Product Rejected Successfully.');
	}

	public function variantDetailUpdate(Request $request){
		//echo "<pre>"; print_r($request->all()); die;
	 $id=$request->post('variant_id');
	$product_id=$request->post('product_id');
	$shipping_weight=$request->post('shipping_weight');

	$product = Product::find($product_id);

//	$product->title=$request->post('new_title');
//	$product->body_html=$request->post('new_body');
//	$product->tags=$request->post('tags');
//	$product->save();



	//dd($base_price);
	$check=ProductInfo::where('id',$id)->exists();

	if ($check)
	{

	        $product_info=ProductInfo::find($id);
	       // dd($product_info->base_price);
	        $prices=Helpers::calc_price_new($product_info->base_price,$shipping_weight,$request->post('tags'),'aaa',$product->vendor);
	        //dd($prices);
		//$prices=Helpers::calc_price_fetched_products($product_info->base_price,$shipping_weight);
	        $product_info->shipping_weight = $shipping_weight;
			$product_info->pricing_weight = $shipping_weight;
		$product_info->price = $prices['inr'];
		$product_info->price_usd = $prices['usd'];
		$product_info->price_nld = $prices['nld'];
		$product_info->price_gbp = $prices['gbp'];
		$product_info->price_cad = $prices['cad'];
		$product_info->price_aud = $prices['aud'];
		$product_info->price_irl = $prices['irl'];
		$product_info->price_ger = $prices['ger'];
        $product_info->manual_weight=1;
		$product_info->save();
	}

	 return redirect()->back();
	}

    public function Settings(Request $request){
        $setting=Setting::first();
        return view('superadmin.settings',compact('setting'));
    }

    public function SaveSettings(Request $request){

       $setting=Setting::first();
       if($setting==null){
           $setting=new Setting();
       }
       $setting->api_key=$request->api_key;
       $setting->password=$request->password;
       $setting->shop_url=$request->shop_url;
       $setting->save();
        return redirect()->back()->with('success', 'Setting Saved Successfully');
    }


    public function Logs(Request $request){

        $logs=Log::query();
        if($request->status!=""){
            $logs->where('status',$request->status);
        }

        if($request->date != "" && $request->date!='undefined'){
            $request->date = str_replace('/', '-', $request->date);
            $logs->whereDate('created_at' , date('Y-m-d',strtotime($request->date)));
        }
        $logs=$logs->orderBy('id','desc')->paginate(30)->appends($request->all());
        return view('superadmin.logs',compact('logs'));
    }


    public function orderlist(Request $request){


        $sql=Order::query();
        if($request->query('order') != ""){
            $sql->where('shopify_order_id' , 'like', '%' . $request->query('order') . '%');
        }
        if($request->query('sdate') != "" && $request->query('edate') != ""){
            $sql->where('order_date' , '>=', $request->query('sdate'));
            $sql->where('order_date' , '<=', $request->query('edate'));
        }
        if($request->query('flag') != "" && $request->query('flag') == "week"){
            $sql->whereBetween('order_date',[Carbon::now()->startOfWeek()->toDateString(), Carbon::now()->endOfWeek()->toDateString()]);
        }
        if($request->query('flag') != "" && $request->query('flag') == "month"){
            $sql->whereBetween('order_date',[Carbon::now()->startOfMonth()->toDateString(), Carbon::now()->lastOfMonth()->toDateString()]);
        }

        if($request->query('status') !=""){
            $sql->where('status',$request->query('status'));
        }
        $data=$sql->orderBy('shopify_order_id', 'desc')->paginate(30);
        return view('superadmin.orders',compact('data'));
    }


    public function vendors(){
        $vendorlist = Store::where('role','Vendor')->get();
        return view('superadmin.vendors',compact('vendorlist'));
    }


    public function vendorSetting($id){

        $vendor=Store::find($id);
        $vendor_product_types=ProductType::where('vendor_id',$vendor->id)->get();
        $payment = Payment::where('vendor_id',$id)->first();
        $markets=Markets::all();

        $thirdPartyApiCategories = ThirdPartyAPICategory::where('vendor_id', $vendor->id)->where('parent_id',1)
            ->with('childrenRecursive')
            ->get();

        return view('superadmin.vendor-setting',compact('vendor','vendor_product_types','markets','payment','thirdPartyApiCategories'));
    }



    public function updaterecord(Request $request){

        $id=$request->id;
        $weight=$request->weight;
        $hsn_code=$request->hsncode;
        ProductType::where('id',$id)->update(['base_weight' => $weight,'hsn_code'=>$hsn_code]);

        $currentTime = now();
        $log=new Log();
        $log->name='Update Products Weight by Product Type ('.$request->store.')';
        $log->date = $currentTime->format('F j, Y');
        $log->start_time = $currentTime->toTimeString();
        $log->status='Processing';
        $log->save();


        UpdateProductsWeight::dispatch($id,$log->id);
        return json_encode(array('status'=>'success'));


    }


    public function Vendorbaseweightupdate(Request $request){

        $store=Store::find($request->vendor_id);
        if($store){
            $store->base_weight=$request->base_weight;
            $store->hsn_code=$request->hsn_code;
            if ($request->hasFile('file')) {
                $file=$request->file('file');
                $name = str_replace(' ', '', $file->getClientOriginalName());
                $name = "size_chart_".time().'_'.$name;
                $file->move(public_path() . '/size-chart-images/', $name);
                $image = asset('/size-chart-images').'/' . $name;
                $store->size_chart_image=$image;
            }
            $store->size_chart_html=$request->html;
            $store->save();
            $this->CreateUpdateMetafield($store->id);
            return redirect()->back()->with([
                'success' => 'Setting Saved Successfully',
                'active_tab' => $request->active_tab, // Add more key-value pairs as needed
            ]);
//            return redirect()->back()->with('success', 'Setting Saved Successfully');
        }


    }


    public function updatemarketbulkprice(Request $request){



    $market_vendor=MarketVendor::where('market_id',$request->id)->where('vendor_id',$request->vendor_id)->first();
    if($market_vendor==null){
        $market_vendor=new MarketVendor();
    }


    $market_vendor->status=$request->status;
    $market_vendor->type=$request->type;
    $market_vendor->value=$request->value;
    $market_vendor->market_id=$request->id;
    $market_vendor->vendor_id=$request->vendor_id;
    $market_vendor->save();


        if($request->status==null){
            $market_vendor=MarketVendor::where('market_id',$request->id)->where('vendor_id',$request->vendor_id)->first();
            if($market_vendor){
                $market_vendor->delete();
            }
        }

        return json_encode(array('status'=>'success'));
    }


    public function updateproductdetail(Request $request){

        $id=$request->post('variant_id');
        $product_id=$request->post('product_id');

        $product = Product::find($product_id);


	$product->title=$request->post('new_title');
	$product->body_html=$request->post('description');
	$product->tags=$request->post('tags');
	$product->save();
        return redirect()->back()->with('success', 'Update Successfully');
    }


    public function updateProductTypeSizechart(Request $request){

        $product_type=ProductType::find($request->product_type_id);

        if($product_type){

            if ($request->hasFile('product_type_file')) {
                $file=$request->file('product_type_file');
                $name = str_replace(' ', '', $file->getClientOriginalName());
                $name = "size_chart_".time().'_'.$name;
                $file->move(public_path() . '/size-chart-images/', $name);
                $image = asset('/size-chart-images').'/' . $name;
                $product_type->size_chart_image=$image;
            }
            $product_type->size_chart_html=$request->product_type_html;
            $product_type->save();

            $this->CreateUpdateMetafield($product_type->vendor_id);
            return redirect()->back()->with('success', 'Setting Saved Successfully');
        }
    }



    public function CreateUpdateMetafield($id){
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

        $store=Store::find($id);
        $product_type_array=array();

        $product_types=ProductType::where('vendor_id',$id)->get();
        foreach ($product_types as $product_type) {
            $flag=0;
            if ($product_type->size_chart_html || $product_type->size_chart_image) {
                $flag=1;
            }

                $product_type_categories = ProductTypeSubCategory::where('product_type_id', $product_type->id)->get();
                $product_type_category_array = array();
                foreach ($product_type_categories as $product_type_category) {
                    if ($product_type_category->size_chart_html || $product_type_category->size_chart_image) {

                       $flag=1;
                        $data_type['tags'] = $product_type_category->tags;
                        $data_type['sizechart_html'] = $product_type_category->size_chart_html;
                        $data_type['sizechart_file'] = $product_type_category->size_chart_image;
                        array_push($product_type_category_array, $data_type);
                    }
                }

                usort($product_type_category_array, function ($a, $b) {
                    $tagsA = count(explode(',', $a['tags']));
                    $tagsB = count(explode(',', $b['tags']));

                    if ($tagsA == $tagsB) {
                        return strcmp($a['tags'], $b['tags']); // Sort alphabetically if tags count is the same
                    }

                    return $tagsB - $tagsA; // Sort by the number of tags in descending order
                });

                if($flag==1) {
                    $data['product_type'] = $product_type->product_type;
                    $data['sizechart_html'] = $product_type->size_chart_html;
                    $data['sizechart_file'] = $product_type->size_chart_image;
                    $data['product_type_tags'] = $product_type_category_array;

                    array_push($product_type_array, $data);
                }

        }



        $values = [
            'base_sizechart_html' => ($store->size_chart_html) ? $store->size_chart_html:'' ,
            'base_sizechart_file' => ($store->size_chart_image) ? $store->size_chart_image:'' ,
            'product_types' => $product_type_array
        ];

        $metafield_data=[
            "metafield" =>
                [
                    "key" => 'records',
                    "value" => json_encode($values),
                    "type" => "json",
                    "namespace" => "sizechart",

                ]
        ];


        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/smart_collections/$store->collections_ids/metafields.json";

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
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($metafield_data));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec ($curl);


        curl_close ($curl);

    }


    public function deleteProductTypeImage(Request $request){

       $product_type= ProductType::find($request->id);
       if($product_type) {
           $product_type->size_chart_image = null;
           $product_type->save();
           $this->CreateUpdateMetafield($product_type->vendor_id);
       }
        return json_encode(array('status'=>'success'));
    }

    public function deleteSettingImage(Request $request){

        $store= Store::find($request->id);
        if($store) {
            $store->size_chart_image = null;
            $store->save();
            $this->CreateUpdateMetafield($store->id);
        }
        return json_encode(array('status'=>'success'));
    }


    public function approveSelectedProducts(Request $request){

        $res = Product::whereNull('shopify_id');

        if($request->search != ""){
            $res->where('title' , 'LIKE', '%' . $request->search . '%');
        }
        if($request->vendor != ""){
            $res->where('vendor' , $request->vendor);
        }

        if($request->date != "" && $request->date!='undefined'){
            $request->date = str_replace('/', '-', $request->date);
            $res->whereDate('created_at' , date('Y-m-d',strtotime($request->date)));
        }
        if($request->status!=""){
            $res->where('status',$request->status);
        }

        if($request->shopify_status!=""){
            $res->where('shopify_status',$request->shopify_status);
        }

        if($request->product_type!=""){

            $ex_product_type=explode(',',$request->product_type);
            $res->whereIn('product_type_id',$ex_product_type);
        }

        $product_ids = $res->pluck('id')->toArray();



        $inStockProductIds = [];
        $outOfStockProductIds = [];


        Product::whereIn('id', $product_ids)
            ->with(['productInfos' => function ($query) {
                $query->select('product_id', 'stock');
            }])
            ->chunk(200, function ($products_get) use (&$total_products_in_stock, &$total_products_out_of_stock, &$inStockProductIds, &$outOfStockProductIds) {
                foreach ($products_get as $product_g) {
                    $stockCounts = $product_g->productInfos->pluck('stock')->toArray();

                    if (in_array(1, $stockCounts)) {
                        // If at least one variant has stock 1

                        $inStockProductIds[] = $product_g->id;
                    } elseif (count(array_unique($stockCounts)) === 1 && reset($stockCounts) === 0) {
                        // If all variants have stock 0

                        $outOfStockProductIds[] = $product_g->id;

                    }
                }
            });

        if($request->stock!="")
        {
            if($request->stock=='in-stock'){

                $res->whereIn('id', $inStockProductIds)->get();



            }elseif ($request->stock=='out-of-stock'){

                $res->whereIn('id', $outOfStockProductIds)->get();

            }

        }


        $products=$res->get();


    $log=new Log();
    $log->name='Approve Product Push';
    $log->date = now()->format('F j, Y');
    $log->start_time =now();
    $log->status='Processing';
    $log->filters=json_encode($request->all());
    $log->save();


        ApproveAllProducts::dispatch($products,$log->id);

        return json_encode(array('status'=>'success'));

    }

    public function denySelectedProducts(Request $request){

        $res = Product::whereNull('shopify_id')->where('in_queue',0);
        if($request->search != ""){
            $res->where('title' , 'LIKE', '%' . $request->search . '%');
        }
        if($request->vendor != ""){
            $res->where('vendor' , $request->vendor);
        }

        if($request->date != "" && $request->date!='undefined'){
            $request->date = str_replace('/', '-', $request->date);
            $res->whereDate('created_at' , date('Y-m-d',strtotime($request->date)));
        }
        if($request->status!=""){
            $res->where('status',$request->status);
        }
        if($request->shopify_status!=""){
            $res->where('shopify_status',$request->shopify_status);
        }

        if($request->product_type!=""){

            $ex_product_type=explode(',',$request->product_type);
            $res->whereIn('product_type_id',$ex_product_type);
        }

        $products=$res->get();

        DenyAllProducts::dispatch($products);

        return json_encode(array('status'=>'success'));
    }


    public function addProductTypeSizeChart($id){

        $product_type=ProductType::find($id);

        $product_tags=Product::where('product_type_id',$product_type->id)->pluck('tags');
$tag_array=array();
     foreach ($product_tags as $product_tag){

         $tags_data=explode(',',$product_tag);

         $tag_array = array_merge($tag_array, $tags_data);
     }
        $tags = array_unique($tag_array);

//        $tags_to_remove = array();
        $product_type_subcatgories=ProductTypeSubCategory::where('product_type_id',$id)->get();
//        foreach ($tags as $tag) {
//            foreach ($product_type_subcatgories as $subcategory) {
//                if (in_array($tag, explode(',', $subcategory->tags))) {
//                    $tags_to_remove[] = $tag;
//                    break;
//                }
//            }
//        }

// Remove the tags found in $tags_to_remove from $tags

//        $tags = array_diff($tags, $tags_to_remove);




        return view('superadmin.product-type-sizechart',compact('product_type','tags','product_type_subcatgories'));
    }



    public function saveProductTypeSubCategory(Request $request){


        $product_type_subcategory=new ProductTypeSubCategory();
        $product_type_subcategory->tags=implode(',',$request->tags);
        $product_type_subcategory->size_chart_html=$request->product_type_sub_html;

        if ($request->hasFile('product_type_sub_file')) {
            $file=$request->file('product_type_sub_file');
            $name = str_replace(' ', '', $file->getClientOriginalName());
            $name = "size_chart_".time().'_'.$name;
            $file->move(public_path() . '/size-chart-images/', $name);
            $image = asset('/size-chart-images').'/' . $name;
            $product_type_subcategory->size_chart_image=$image;
        }

        $product_type_subcategory->product_type_id=$request->product_type_id;
        $product_type_subcategory->vendor_id=$request->vendor_id;
        $product_type_subcategory->save();
        $this->CreateUpdateMetafield($product_type_subcategory->vendor_id);
        return redirect()->back()->with('success', 'Setting Saved Successfully');
    }


    public function deleteProductTypeSubCategoryImage(Request $request){

        $product_type_subcategory= ProductTypeSubCategory::find($request->id);
        if($product_type_subcategory) {
            $product_type_subcategory->size_chart_image = null;
            $product_type_subcategory->save();
            $this->CreateUpdateMetafield($product_type_subcategory->vendor_id);
        }
        return json_encode(array('status'=>'success'));
    }


    public function updateProductTypeSubCategory(Request $request){


        $product_type_subcategory=ProductTypeSubCategory::find($request->product_type_subcategory_id);
        $product_type_subcategory->tags=implode(',',$request->tags);
        $product_type_subcategory->size_chart_html=$request->product_type_sub_html;

        if ($request->hasFile('product_type_sub_file')) {
            $file=$request->file('product_type_sub_file');
            $name = str_replace(' ', '', $file->getClientOriginalName());
            $name = "size_chart_".time().'_'.$name;
            $file->move(public_path() . '/size-chart-images/', $name);
            $image = asset('/size-chart-images').'/' . $name;
            $product_type_subcategory->size_chart_image=$image;
        }

        $product_type_subcategory->product_type_id=$request->product_type_id;
        $product_type_subcategory->vendor_id=$request->vendor_id;
        $product_type_subcategory->save();
        $this->CreateUpdateMetafield($product_type_subcategory->vendor_id);
        return redirect()->back()->with('success', 'Setting Saved Successfully');
    }


    public function deleteProductTypeSubCategory($id){


        $product_type_subcategory=ProductTypeSubCategory::find($id);
        if($product_type_subcategory){

            $product_type_subcategory->delete();
        }
        $this->CreateUpdateMetafield($product_type_subcategory->vendor_id);
        return redirect()->back()->with('success', 'Setting Saved Successfully');
    }



    public function UpdatePricingWeight(){


        $products = Product::where('id', '>=', 168173)
            ->get();
        foreach ($products as $product){

            $product_type=ProductType::where('id',$product->product_type_id)->first();

            $variants=ProductInfo::where('product_id',$product->id)->whereNull('pricing_weight')->get();
            foreach ($variants as $variant){

                $pricing_weight=$variant->grams;

                if($product_type && $product_type->base_weight){
                    $pricing_weight=max($variant->grams, $product_type->base_weight);
                }

                $variant->pricing_weight=$pricing_weight;
                $variant->save();


            }

            dump($product->id);
        }

        dd('done');
    }


    public function updateProductPricesByProductType(Request $request){

        $currentTime = now();
        $log=new Log();
        $log->name='Update Product Price in Database ('.$request->store.')';
        $log->date = $currentTime->format('F j, Y');
        $log->start_time = $currentTime->toTimeString();
        $log->status='Processing';
        $log->save();

        UpdateProductPricesByProductType::dispatch($request->id,$log->id);
        return json_encode(array('status'=>'success'));
    }

    public function updateShopifyPricesByProductType(Request $request){
        $currentTime = now();
        $log=new Log();
        $log->name='Update Price in Shopify ('.$request->store.')';
        $log->date = $currentTime->format('F j, Y');
        $log->start_time = $currentTime->toTimeString();
        $log->status='Processing';
        $log->save();

        UpdateShopifyPricesByProductType::dispatch($request->id,$log->id);
        return json_encode(array('status'=>'success'));
    }


    public function UpdateProductShopifyStatus(){
        Product::where('shopify_status', 'In-Progress')->update(['shopify_status' => 'Pending']);
//        Product::where('vendor',66)->where('shopify_status', 'In-Progress')->update(['shopify_status' => 'Pending']);
        return redirect()->back()->with('success', 'Changed Successfully');
    }


    public function startShopifyPushCronjob($id){
        $log=Log::find($id);
        if($log){
            $log->is_enabled=1;
            $log->status='In-Progress';
            $log->is_running=1;
            $log->save();
        }
        return redirect()->back()->with('success', 'Changed Successfully');
    }

    public function pauseShopifyPushCronjob($id){
        $log=Log::find($id);
        if($log){
            $log->status='Paused';
            $log->is_enabled=0;
            $log->is_running=0;
            $log->running_at=now();
            $log->save();
        }

        $check_log=Log::where('name','Approve Product Push')->where('status','In-Queue')->first();
        if($check_log){
            $check_log->status='In-Progress';
            $check_log->is_running=1;
            $check_log->save();
        }
        return redirect()->back()->with('success', 'Changed Successfully');
    }


    public function syncApiData($id)
    {
        $vendor = Store::find($id);
        if($vendor){

            $log = new Log();
            $log->name = 'Fetch Product From Json ('.$vendor->name.')';
            $log->date = now()->format('F j, Y');
            $log->start_time = now()->toTimeString();
            $log->status = 'Processing';
            $log->save();
            ProductsSyncFromApi::dispatch($id,$log->id);
            return redirect()->back()->with('success', 'Products Sync In-Progress');
        }
    }


    public function LogsDetail($id)
    {

        $log = Log::find($id);
        if ($log) {
            $product_ids = explode(',', $log->product_ids);
            return view('superadmin.logs-detail', compact('log', 'product_ids'));
        }

    }

        public function updateSpecificShopifyProduct(){

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




        $products=Product::whereNotNull('shopify_id')->where('vendor',66)->get();
        foreach ($products as $product){

            $data['product']=array(
                "id" => $product->shopify_id,
                "body_html" => $product->body_html,
            );

            $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2022-10/products/$product->shopify_id.json";
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
        dd('done');
        }



//        public function SyncThirdPartyAPICategories(){
//
//
//        $store=Store::where('name','Kalamandir')->first();
//        if($store) {
//            $curl = curl_init();
//            curl_setopt_array($curl, array(
//                CURLOPT_URL => 'http://admin.kalamandir.com/rest/V1/categories',
//                CURLOPT_RETURNTRANSFER => true,
//                CURLOPT_ENCODING => '',
//                CURLOPT_MAXREDIRS => 10,
//                CURLOPT_TIMEOUT => 0,
//                CURLOPT_FOLLOWLOCATION => true,
//                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//                CURLOPT_CUSTOMREQUEST => 'GET',
//                CURLOPT_HTTPHEADER => array(
//                    'Authorization: Bearer dm8qawgncp0qr66kmk5o4azixm59qe9c',
//                    'Cookie: PHPSESSID=ub0mpqgtmvauj6qjf90s74u6e9'
//                ),
//            ));
//
//            $response = curl_exec($curl);
//
//            curl_close($curl);
//            $category = json_decode($response, true);
//
//            $category_data = ThirdPartyAPICategory::where('category_id', $category['id'])->where('level', 1)->first();
//            if ($category_data == null) {
//                $category_data = new ThirdPartyAPICategory();
//            }
//            $category_data->category_id = $category['id'];
//            $category_data->parent_id = $category['parent_id'];
//            $category_data->name = $category['name'];
//            $category_data->is_active = $category['is_active'];
//            $category_data->position = $category['position'];
//            $category_data->level = $category['level'];
//            $category_data->product_count = $category['product_count'];
//            $category_data->vendor_id = $store->id;
//            $category_data->save();
//
//
//            if (isset($category['children_data']) && count($category['children_data']) > 0) {
//
//                foreach ($category['children_data'] as $children_data) {
//
//                    $category_data = ThirdPartyAPICategory::where('category_id', $children_data['id'])->where('level', 2)->first();
//                    if ($category_data == null) {
//                        $category_data = new ThirdPartyAPICategory();
//                    }
//                    $category_data->category_id = $children_data['id'];
//                    $category_data->parent_id = $children_data['parent_id'];
//                    $category_data->name = $children_data['name'];
//                    $category_data->is_active = $children_data['is_active'];
//                    $category_data->position = $children_data['position'];
//                    $category_data->level = $children_data['level'];
//                    $category_data->product_count = $children_data['product_count'];
//                    $category_data->vendor_id = $store->id;
//                    $category_data->save();
//
//
//                    $product_type = ProductType::where('product_type', $children_data['name'])->where('vendor_id', $store->id)->first();
//                    if ($product_type == null) {
//                        $product_type = new ProductType();
//                    }
//                    $product_type->product_type = $children_data['name'];
//                    $product_type->vendor_id = $store->id;
//                    $product_type->save();
//
//
//
//                    if (isset($children_data['children_data']) && count($children_data['children_data']) > 0) {
//                        foreach ($children_data['children_data'] as $children_data_sub_category) {
//
//                            $category_data = ThirdPartyAPICategory::where('category_id', $children_data_sub_category['id'])->where('level', 3)->first();
//                            if ($category_data == null) {
//                                $category_data = new ThirdPartyAPICategory();
//                            }
//                            $category_data->category_id = $children_data_sub_category['id'];
//                            $category_data->parent_id = $children_data_sub_category['parent_id'];
//                            $category_data->name = $children_data_sub_category['name'];
//                            $category_data->is_active = $children_data_sub_category['is_active'];
//                            $category_data->position = $children_data_sub_category['position'];
//                            $category_data->level = $children_data_sub_category['level'];
//                            $category_data->product_count = $children_data_sub_category['product_count'];
//                            $category_data->vendor_id = $store->id;
//                            $category_data->save();
//
//                            if (isset($children_data_sub_category['children_data']) && count($children_data_sub_category['children_data']) > 0) {
//                                foreach ($children_data_sub_category['children_data'] as $children_data_sub_sub_category) {
//
//                                    $category_data = ThirdPartyAPICategory::where('category_id', $children_data_sub_sub_category['id'])->where('level', 4)->first();
//                                    if ($category_data == null) {
//                                        $category_data = new ThirdPartyAPICategory();
//                                    }
//                                    $category_data->category_id = $children_data_sub_sub_category['id'];
//                                    $category_data->parent_id = $children_data_sub_sub_category['parent_id'];
//                                    $category_data->name = $children_data_sub_sub_category['name'];
//                                    $category_data->is_active = $children_data_sub_sub_category['is_active'];
//                                    $category_data->position = $children_data_sub_sub_category['position'];
//                                    $category_data->level = $children_data_sub_sub_category['level'];
//                                    $category_data->product_count = $children_data_sub_sub_category['product_count'];
//                                    $category_data->vendor_id = $store->id;
//                                    $category_data->save();
//
//
//                                }
//                            }
//
//
//                        }
//
//                    }
//
//                }
//            }
//
//        }
//
//        }
        public function SyncThirdPartyAPICategories(){


        $store=Store::where('name','Kalamandir')->first();
        if($store) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'http://admin.kalamandir.com/rest/V1/categories',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer dm8qawgncp0qr66kmk5o4azixm59qe9c',
                    'Cookie: PHPSESSID=ub0mpqgtmvauj6qjf90s74u6e9'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            $category = json_decode($response, true);




            $category_data = ThirdPartyAPICategory::where('category_id', $category['id'])->where('level', 1)->first();
            if ($category_data == null) {
                $category_data = new ThirdPartyAPICategory();
            }
            $category_data->category_id = $category['id'];
            $category_data->parent_id = $category['parent_id'];
            $category_data->name = $category['name'];
            $category_data->is_active = $category['is_active'];
            $category_data->position = $category['position'];
            $category_data->level = $category['level'];
            $category_data->product_count = $category['product_count'];
            $category_data->vendor_id = $store->id;
            $category_data->save();




            if (isset($category['children_data']) && count($category['children_data']) > 0) {



                $sortedCategories = collect([]);

                // Get top-level categories (parents)

                $parents = json_decode(json_encode($category['children_data']), false);
                foreach ($parents as $parent) {
                    $this->sortChildrenRecursively($parent, $parent->children_data, $sortedCategories);
                }


                foreach ($sortedCategories as $children_data) {

                    $category_data = ThirdPartyAPICategory::where('category_id', $children_data->id)->first();
                    if ($category_data == null) {
                        $category_data = new ThirdPartyAPICategory();
                    }
                    $category_data->category_id = $children_data->id;
                    $category_data->parent_id = $children_data->parent_id;
                    $category_data->name = $children_data->name;
                    $category_data->is_active = $children_data->is_active;
                    $category_data->position = $children_data->position;
                    $category_data->level = $children_data->level;
                    $category_data->product_count = $children_data->product_count;
                    $category_data->vendor_id = $store->id;
                    $category_data->save();


                        if($category_data->level==2){
                            $product_type = ProductType::where('product_type', $children_data->name)->where('vendor_id', $store->id)->first();
                            if ($product_type == null) {
                                $product_type = new ProductType();
                            }
                            $product_type->product_type = $children_data->name;
                            $product_type->vendor_id = $store->id;
                            $product_type->save();
                        }
                }
            }

        }

        }



    private function sortChildrenRecursively($parent, $categories, &$sortedCategories)
    {

        $sortedCategories->push($parent);

//        // Get children of the parent category
//        $children = $categories->where('parent_id', $parent->id);

        // Sort and push children recursively
        foreach ($categories as $child) {
            $this->sortChildrenRecursively($child, $child->children_data, $sortedCategories);
        }
    }

        public function SyncThirdPartyAPIAttributes(){

            $store=Store::where('name','Kalamandir')->first();
            if($store) {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'http://admin.kalamandir.com/rest/V1/products/attributes?searchCriteria',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer dm8qawgncp0qr66kmk5o4azixm59qe9c',
                    'Cookie: PHPSESSID=ub0mpqgtmvauj6qjf90s74u6e9'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            $product_attributes = json_decode($response, true);

            if(count($product_attributes['items']) > 0) {
                foreach ($product_attributes['items'] as $product_attribute) {



                        $api_product_attribute = ThirdPartyAPIProductAttribute::where('attribute_id', $product_attribute['attribute_id'])->first();
                        if ($api_product_attribute == null) {
                            $api_product_attribute = new ThirdPartyAPIProductAttribute();
                        }
                        $api_product_attribute->attribute_id = $product_attribute['attribute_id'];
                        $api_product_attribute->default_frontend_label = isset($product_attribute['default_frontend_label']) ? $product_attribute['default_frontend_label'] : null;
                        $api_product_attribute->vendor_id = $store->id;
                        $api_product_attribute->save();

                        if (count($product_attribute['options']) > 0) {

                            foreach ($product_attribute['options'] as $option) {

                                $api_product_attribute_option = ThirdPartyAPIProductAttributeOptions::where('product_attribute_id', $product_attribute['attribute_id'])->where('value', $option['value'])->first();
                                if ($api_product_attribute_option == null) {
                                    $api_product_attribute_option = new ThirdPartyAPIProductAttributeOptions();
                                }
                                $api_product_attribute_option->product_attribute_id = $product_attribute['attribute_id'];
                                $api_product_attribute_option->label = $option['label'];
                                $api_product_attribute_option->value = $option['value'];
                                $api_product_attribute_option->vendor_id = $store->id;
                                $api_product_attribute_option->save();
                            }
                        }


                }
            }
        }





        }


        public function GetProductType($category_id,$vid){


            $check_category = ThirdPartyAPICategory::where('category_id', $category_id)->where('vendor_id', $vid)->first();
            if($check_category){
                if ($check_category->level == 2) {
                    $product_type = ProductType::where('product_type', $check_category->name)->where('vendor_id', $vid)->first();

                    return $product_type;
                }else{
                    return $this->GetProductType($check_category->parent_id, $vid);
                }
            }

        }


        public function getThirdPartyAPIInventory(){

            $store=Store::where('name','Kalamandir')->first();
            if($store) {

            $curl = curl_init();
            $vid=$store->id;
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'http://admin.kalamandir.com/rest/V1/products?searchCriteria[filter_groups][0][filters][0][field]=visibility&searchCriteria[filter_groups][0][filters][0][value]=4',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer dm8qawgncp0qr66kmk5o4azixm59qe9c',
                    'Cookie: PHPSESSID=ub0mpqgtmvauj6qjf90s74u6e9'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            $data = json_decode($response, true);
                foreach ($data['items'] as $index => $row) {

                    $stock = 0;
                    $qty = 0;
                    if (isset($row['extension_attributes']) && isset($row['extension_attributes']) && isset($row['extension_attributes']['stock_item'])) {
                        $stock = $row['extension_attributes']['stock_item']['is_in_stock'];
                        $qty = $row['extension_attributes']['stock_item']['qty'];
                    }

                    $product_check = Product::where('reference_shopify_id', $row['id'])->where('vendor', $vid)->first();
                    if($product_check){
                        $product_info = ProductInfo::where('product_id', $product_check->id)->where('sku', $row['sku'])->first();
                        if($product_info){
                            $category_id = isset($row['extension_attributes']['category_links'])?$row['extension_attributes']['category_links'][0]['category_id']:null;

                            $product_type = $this->GetProductType($category_id, $vid);
                            $product_type_id=null;
                            if($product_type){
                                $product_type_id=$product_type->id;
                            }

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
                            $product_info->save();

                        }
                    }

                }
        }
        }

}
