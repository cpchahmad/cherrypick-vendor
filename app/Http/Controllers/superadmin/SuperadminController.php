<?php

namespace App\Http\Controllers\superadmin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ProductController;
use App\Models\ProductChange;
use App\Models\Setting;
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
use Session;
use DB;
use App\Helpers\Helpers;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PriceExport;
use App\Imports\BluckProductImport;
use App\Models\SareeFurniture;
use Validator;

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
        $sql=Product::where('status', 1)->get()->toArray();
        $total_approval=count($sql);
        $sql_pending=Product::whereIn('status', [0,2])->get()->toArray();
        $total_pending_approval=count($sql_pending);
		$sql_pending_reject=Product::where('status', 3)->get()->toArray();
        $total_deny=count($sql_pending_reject);
        $sql_today=Product::where('status', 1)->where('approve_date', Carbon::now()->format('Y-m-d'))->get()->toArray();
        $total_today_approval=count($sql_today);
        $sql_weekly=Product::where('status', 1)->whereBetween('approve_date',[Carbon::now()->startOfWeek()->toDateString(), Carbon::now()->endOfWeek()->toDateString()])->get()->toArray();
        $total_weekly_approval=count($sql_weekly);
        $sql_month=Product::where('status', 1)->whereBetween('approve_date',[Carbon::now()->startOfMonth()->toDateString(), Carbon::now()->lastOfMonth()->toDateString()])->get()->toArray();
        $total_month_approval=count($sql_month);

        $sql_out_of_stock=ProductInfo::where('stock', 0)->get()->toArray();
        $total_out_of_stock=count($sql_out_of_stock);

        $data = DB::table('products_variants')
                ->select(array(DB::raw('COUNT(products_variants.id) as products'),'stores.name','stores.email','stores.status','stores.id'))
                ->where('products_variants.stock', '=', 0)
                ->join('stores', 'products_variants.vendor_id', '=', 'stores.id')
                ->groupBy('products_variants.vendor_id')
                ->get();


    	return view('superadmin.index', compact('total_approval','total_pending_approval','total_today_approval','total_weekly_approval','total_month_approval','total_out_of_stock','data','total_deny'));
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
		return redirect()->back();
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

		return redirect()->back();
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
		return redirect()->back();
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
   	//return $request->all();
       //echo "<pre>"; print_r($request->all()); die();

     $res = Product::whereIn('status',[0,2]);
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

      $data = $res->orderBy('updated_at', 'DESC')->paginate(30)->appends($request->all());
      $vendorlist = Store::where('role','Vendor')->get();
      //dd($data);
     return view('superadmin.products-list',compact('data','vendorlist'));
    }
	public function updateAllProductPrices()
	{
		ProductInfo::where('price_conversion_update_status', 0)->update(['price_conversion_update_status' => 1]);
		return back()->with('success','Product price updated successfully');
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
     $change_products=ProductChange::where('product_id',$id)->get();
     return view('superadmin.products-details',compact('data','items','vendor'));
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
        $ids=explode(",",$request->ids);
        foreach($ids as $v)
        {
            Product::where('id', $v)->update(['status' => '1', 'approve_date' => Carbon::now()]);
        }
        return json_encode(array('status'=>'success'));
    }
	public function bulkRejectProduct(Request $request)
    {
        $ids=explode(",",$request->ids);
        foreach($ids as $v)
        {
            Product::where('id', $v)->update(['status' => '3', 'approve_date' => Carbon::now()]);
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

        if($product->status==0 || $product->status==3)
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
                    "price"   => $v->price_usd,
                    "grams"   => $v->grams,
                    "taxable" => false,
                    "inventory_management" => ($v->stock ? "shopify" :null),
                    "inventory_quantity" => $v->stock
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
                "vendor"       =>  $store->name,
                //"product_type" => $category->category,
				"product_type" => $category->category??'',
                "published"    => true ,
                "tags"         => explode(",",$product->tags),
                "variants"     =>$variants,
				"options"     =>  $opt
            )
        );

        //echo "<pre>"; print_r($products_array); die();

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


        $SHOPIFY_API = "https://$API_KEY:$PASSWORD@$SHOP_URL/admin/api/2020-04/products.json";
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
		$shopify_handle=$result['product']['handle'];
        Product::where('id', $product->id)->update(['shopify_id' => $shopify_product_id, 'handle' => $shopify_handle, 'status' => '1', 'approve_date' => Carbon::now()]);
        foreach($result['product']['variants'] as $prd)
        {
            ProductInfo::where('sku', $prd['sku'])->update(['inventory_item_id' => $prd['inventory_item_id'], 'inventory_id' => $prd['id'], 'price_status' => '0']);
            $location_id=Helpers::DiffalultLocation();
            ProductInventoryLocation::updateOrCreate(
                                ['items_id' => $prd['inventory_item_id'], 'location_id' => $location_id],
                                ['items_id' => $prd['inventory_item_id'], 'stock' => $prd['inventory_quantity'], 'location_id' => $location_id]
                            );

        }
        $this->shopifyUploadeImage($product->id,$shopify_product_id);
		//$this->linkProductToCollection($shopify_product_id,$store->collections_ids);
		ProductInfo::where('product_id', $product->id)->update(['price_status' => '0']);
        }
        else if($product->status==2)
        {
            $category=Category::find($product->category);
            $shopify_id=$product->shopify_id;

            $data['product']=array(
                    "id" => $shopify_id,
                    "title" => $product->title,
                    "tags"   => $product->tags,
                    "product_type" => $category->category,
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



//            $API_KEY = 'fd46f1bf9baedd514ed7075097c53995';
//            $PASSWORD = 'shpua_daf4f90db21249801ebf3d93bdfd0335';
//            $SHOP_URL = 'cherrpick-zain.myshopify.com';
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

            $product_infos =ProductInfo::where('product_id',$product['id'])->get();

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
                            $data['variant'] = array(
                                "id" => $invid,
                                "title" => $product_info->varient_name,
                                "option1" => $product_info->varient_value,
                                "sku" => $product_info->sku,
                                "price" => $product_info->price_usd,
                                "compare_at_price" => $product_info->price_usd,
                                "grams" => $product_info->grams,
                                "taxable" => false,
                                "inventory_management" => ($product_info->stock) ? "shopify" : null,
                            );
                        } else {
                            $data['variant'] = array(
                                "id" => $invid,
                                "sku" => $product_info->sku,
                                "price" => $product_info->price_usd,
                                "compare_at_price" => $product_info->price_usd,
                                "grams" => $product_info->grams,
                                "taxable" => false,
                                "inventory_management" => ($product_info->stock) ? "shopify" : null,
                            );
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

		$check=DB::table('cron_json_url')->where('url',$request->url)->get();

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
			$vid=$store->id;
		}


		if(sizeof($check) == 0){
		DB::table('cron_json_url')->insert(['vendor_id' =>$vid , 'url' => $request->url]);

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
					$product_info->price_irl = $prices['nld'];
					$product_info->price_ger = $prices['nld'];
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
					$product_info->price_irl = $prices['nld'];
					$product_info->price_ger = $prices['nld'];
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
					$info['price_irl']=$prices['nld'];
					$info['price_ger']=$prices['nld'];
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
	public function uploadeBulkProducts(Request $request)
	{
        $request->validate([
            'file'=>'required|mimes:xlsx,csv',
        ]);

        $data=Excel::import(new BluckProductImport,request()->file('file'));

         return redirect()->back()->with('success', 'uploaded Successfully');
       // return redirect()->route('superadmin.allproduct')->with('success','Product Rejected Successfully.');
	}

	public function variantDetailUpdate(Request $request){
		//echo "<pre>"; print_r($request->all()); die;
	 $id=$request->post('variant_id');
	$product_id=$request->post('product_id');
	$shipping_weight=$request->post('shipping_weight');

	$product = Product::find($product_id);

	$product->title=$request->post('new_title');
	$product->body_html=$request->post('new_body');
	$product->tags=$request->post('tags');
	$product->save();



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
			$product_info->grams = $shipping_weight;
		$product_info->price = $prices['inr'];
		$product_info->price_usd = $prices['usd'];
		$product_info->price_nld = $prices['nld'];
		$product_info->price_gbp = $prices['gbp'];
		$product_info->price_cad = $prices['cad'];
		$product_info->price_aud = $prices['aud'];
		$product_info->price_irl = $prices['irl'];
		$product_info->price_ger = $prices['ger'];
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

}
