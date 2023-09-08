@extends('layouts.admin')
@section('main')
<main id="main" class="main">
    <div class="subpagetitle fit-title">
        <h1>View Order</h1>
         <p><a href="{{url('orders')}}">Order</a> / <b>View Order</b></p>
      </div>
   
    <section class="section dashboard">
      <div class="row">
        <div class="col-12 ">
            <div class="card fullorders">
                
              <div class="row ">
                  @php $price=0; $items=0; $discount=0; @endphp
                  @foreach($items_data as $item)
                    @php $price=$price+$item->price; @endphp
                    @php $items++; @endphp
                    @php $discount=$discount+$item->discount; @endphp
                  @endforeach
                  <div class="col-12">
                      @php
                        $status=$data->status;
                      @endphp
                      <h4>Order Details <span class="full-status">@if($status==0) New Order @elseif($status==1) Ready for Pickup @else Completed @endif</span></h4>
                     <div class="order-summry">
                    <div class="order-items">
                     <p><b>Order Id</b> <span>#{{$data->shopify_order_id}}</span></p>
                     <p><b>Date</b> <span>{{$data->order_date}}</span></p>
                     <p><b>Fulfillment Status</b> <span>{{$data->fulfillment_status}}</span></p>
                     <p><b>Payment Status</b> <span>{{$data->payment_status}}</span></p>
                     <p><b>Items</b> <span>{{count($items_data)}}</span></p>
                     
					 <p><b>Price</b> <span>{{$price}}</span></p>
                     <!--<p><b>Items</b> <span>{{$items}}</span></p>-->
					 <!--<p><b>Discount</b> <span>0</span></p>-->
                     <p><b>Discount</b> <span>{{$data->vendor_discount}}%</span></p>
                     <p><b>OTP</b> <span>{{$data->otp}}</span></p>
                     @if($data->status==0)
                     <div class="bulk-example">
                        <p><a href="{{url('change-status')}}/{{$data->shopify_order_id}}/1">Mark Ready For Pickup</a></p>
                    </div>
                     @elseif($data->status==1)
                     <div class="bulk-example">
                        <p><a href="{{url('change-status')}}/{{$data->shopify_order_id}}/2">Mark Completed</a></p>
                    </div>
                     @endif
                    </div>
                         
                    <div class="order-items">
                        @foreach($items_data as $item)
                      <p><b>Product Name</b> <span>{{$item->product_name}}</span></p>
                      <p><b>SKU</b> <span>{{$item->sku}}</span></p>
                      <p><b>Quantity</b> <span>{{$item->quantity}}</span></p>
                      ----------------------------------------------------------------------
                      @endforeach
                    </div>
                         
                    </div>
                     
                  </div>
                  <a href="{{url('orders')}}" class="btn btn-light">Back</a>
                </div>     
             </div>
            
        </div>
      </div>
    </section>
   </main>
@stop
  