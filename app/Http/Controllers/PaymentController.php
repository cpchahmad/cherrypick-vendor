<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use Auth;

class PaymentController extends Controller
{
    
    public function paymentconfig(){ 
        if(Auth::user()->role=='Vendor')
            $vendor_id=Auth::user()->id;
       else
           $vendor_id=Auth::user()->vendor_id;
        $data = Payment::where('vendor_id',$vendor_id)->get();
    	return view('subadmin.payment-configuration',compact('data'));
    }
    public function editPaymentconfig(){
        if(Auth::user()->role=='Vendor')
            $vendor_id=Auth::user()->id;
       else
           $vendor_id=Auth::user()->vendor_id;
        $details = Payment::where('vendor_id',$vendor_id)->first();
    	return view('subadmin.payment-configuration-edit',compact('details'));
    }
    public function submitPaymentconfig(Request $request){
        //echo "<pre>"; print_r($request->all()); die();
    	$request->validate([
            'account_no'=>'required|min:8',
            'name'=>'required',
            'ifsc'=>'required|min:11|max:11|regex:/^[A-Za-z]{4}\d{7}$/',
            //'gst'=>'required',
            'address'=>'required',
        ]);
        if(Auth::user()->role=='Vendor')
            $vendor_id=Auth::user()->id;
       else
           $vendor_id=Auth::user()->vendor_id; 
        $payment = Payment::updateOrCreate(
            ['vendor_id' => $vendor_id],
            [
                'account_no'=>$request->account_no,
                'bank_name'=>$request->name,
                'ifsc'=>$request->ifsc,
                'gst'=>$request->gst,
                'address'=>$request->address,
                'vendor_id' => $vendor_id,
                'account_type' => $request->account_type
            ]
       );
       return redirect()->route('admin.editpaymentconfig')->with('success','Bank detail updated');
    }
}
