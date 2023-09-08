@extends('layouts.superadmin')
@section('main')
<main id="main" class="main">
    <div class="subpagetitle fit-title">
        <h1>Order Details</h1>
      </div>
        <section class="section up-banner">
      <div class="row">
       <div class="col-md-12">
        <div class="card-body show-plan collections">
          <!-- Bordered Table -->
          <div class="table-responsive">
          <table class="table table-bordered table-white">
            <thead>
              <tr>
                <th scope="col">S.No</th>
                <th scope="col">Product</th>
                <th scope="col">Quantity</th>
                <th scope="col">Amount</th>
              </tr>
            </thead>
            <tbody>
                @php $i=1; $price=0; @endphp
                @foreach($items_data as $item)
				@php $price=$price+$item->price; @endphp
              <tr>
                <td>{{$i++}}</td>
                <td>{{$item->product_name}}</td>
                <td>{{$item->quantity}}</td>
				<td>{{$item->price}}</td>
              </tr>
              @endforeach
              <tr>
                <td colspan="3"><b>Total Amount</b></td>
				<td><b>{{$price}}</b></td>
              </tr>
            </tbody>
          </table>
          </div>
          <!-- End Bordered Table -->
        </div>
       </div>
      </div>
    </section>
   </main>
@stop
  