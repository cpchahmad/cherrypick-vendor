@extends('layouts.superadmin')
<style>

    #main{
        margin-top: 40px !important;
    }
</style>
@section('main')
    <main id="main" class="main">
        <div class="subpagetitle fit-title">
            <h1>View Order</h1>
            <p><a href="{{route('superadmin.allorders')}}">Order</a> / <b>View Order</b></p>
        </div>

        <section class="section dashboard">
            <div class="row">
                <div class="col-12 ">
                    <div class="card fullorders">
                        @php
                            $status=$data->status;
                        @endphp
                        <h4><span>#{{$data->shopify_order_id}}</span><span class="full-status">@if($status==0) New Order @elseif($status==1) Ready for Pickup @else Completed @endif</span><span>{{$data->fulfillment_status}}</span><span class="full-status">{{$data->payment_status}}</span>

                            <br>

                            <p style="margin-top:6px"> <span style="font-weight: 200;
    font-size: 20px;">{{$data->order_date}}</span></p>
                        </h4>


                        <div class="row ">

                            <div class="row">
                                <div class="col-sm-12" style="padding-right: 0">

                                    <div class="card bg-white border-0 mt-3 mb-3 shadow-sm">
                                        <div class="card-body bg-white border-light">
                                            <div class="row">

                                                @php $price=0; $items=0; $discount=0;
                                      $usd_price_total=0;
                                                @endphp
                                                @foreach($items_data as $item)
                                                    @php $price=$price+$item->price;
                                                if($item->has_variant){
                                              $usd_price_total=$item->has_variant->price_usd +$usd_price_total;
                                                }
                                                    @endphp
                                                    @php $items++; @endphp
                                                    @php $discount=$discount+$item->discount; @endphp


                                                    <div class="col-md-1">
                                                        @if(isset($item->has_variant->has_image->image) && $item->has_variant->has_image->image!='')
                                                            <img src="{{$item->has_variant->has_image->image}}" style="width: 100%">
                                                        @else
                                                            <img src="{{asset('public/empty.jpg')}}" style="width: 100%">
                                                        @endif
                                                    </div>



                                                    <div class="col-md-3">
                                                        <strong> {{$item->product_name}}</strong>
                                                        <br>
                                                        @if($item->has_variant)
                                                            {{$item->has_variant->varient_value}}
                                                            <br>
                                                        @endif
                                                        @if($item->sku!=null)
                                                            <b>SKU:</b>{{$item->sku}}
                                                        @endif


                                                    </div>
                                                    @php
                                                        $lineitem_total=$item->price*$item->quantity;
                                                        $usd_lineitem_total=0;
                                                          if($item->has_variant){
                                                        $usd_lineitem_total=$item->has_variant->price_usd*$item->quantity;
                                                      }
                                                    @endphp


                                                    <div class="col-md-2">
                                                        INR {{$item->price}}x{{$item->quantity}}



                                                    </div>
                                                    <div class="col-md-2">

                                                        @if($item->has_variant)
                                                            ${{number_format($item->has_variant->price_usd, 2)}}x{{$item->quantity}}
                                                        @endif


                                                    </div>

                                                    <div class="col-md-2 text-right"> INR {{$lineitem_total}}</div>
                                                    <div class="col-md-2 text-right"> ${{number_format($usd_lineitem_total,2)}}</div>
                                                    <hr>
                                                    <br>
                                                @endforeach



                                            </div>
                                        </div>

                                    </div>
                                    <div class="card bg-white border-0 mt-3 mb-3 shadow-sm">
                                        <div class="card-body bg-white border-light">
                                            <div class="row">
                                                <div class="col-md-3">Subtotal</div>
                                                <div class="col-md-3" style="text-align:right">{{count($items_data)}} Items</div>
                                                <div class="col-md-3 text-right" style="text-align:right"> INR {{$price}}</div>
                                                <div class="col-md-3 text-right" style="text-align:center"> ${{number_format($usd_price_total,2)}}</div>




                                                <div class="col-md-6 mt-2" ><strong>Total</strong></div>
                                                <div class="col-md-3 mt-2 text-right" style="text-align:right">INR{{$price}} </div>
                                                <div class="col-md-3 mt-2 text-right" style="text-align:center">${{number_format($usd_price_total,2)}} </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>

                </div>


                <div class="col-4">
                    <div class="card fullorders">
                        <p><b>OTP</b> <span>{{$data->otp}}</span></p>
                        <p><b>Discount</b> <span>{{$data->vendor_discount}}%</span></p>
{{--                        @if($data->status==0)--}}
{{--                            <div class="bulk-example mt-2">--}}
{{--                                <p><a href="{{url('change-status')}}/{{$data->shopify_order_id}}/1">Mark Ready For Pickup</a></p>--}}
{{--                            </div>--}}
{{--                        @elseif($data->status==1)--}}
{{--                            <div class="bulk-example mt-2">--}}
{{--                                <p><a href="{{url('change-status')}}/{{$data->shopify_order_id}}/2">Mark Completed</a></p>--}}
{{--                            </div>--}}
{{--                        @endif--}}
                    </div>

                </div>
            </div>
        </section>
    </main>
@stop
