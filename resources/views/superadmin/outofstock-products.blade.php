@extends('layouts.superadmin')
@section('main')
  <main id="main" class="main">
   <div class="home-flex">
    <div class="pagetitle">
       <h1>Product - Out of Stock</h1>
    </div><!-- End Page Title -->
   </div>
   <div class="mt-3 mb-3">
                <a class="btn btn-warning btn-sm" href="{{route('superadmin.out-of-stock')}}">Back</a>
            </div>
    <section class="section up-banner">
        <form class="add-product-form">
                <div class="card table-card">
                  <div class="table-responsive">
                    <table class="table table-borderless view-productd">
                        <thead>
                          <tr>
                            <th scope="col" width="30%">Preview</th>
                            <th scope="col" width="70%">Product Title</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($product as $row)
                          <tr>
                            @php 
                                $image=\App\Models\ProductImages::where(['product_id' => $row->pid])->pluck('image')->first();
                            @endphp
                            <th scope="row" width="30%"><a href="#"><img src="{{$image}}" alt=""></a></th>
                            <td width="70%">{{$row->title}}@if($row->varient_name!='') {{'('.$row->varient_name."-".$row->varient_value.')'}}@endif</td>
                          </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                </div>
        </form>
        <nav class="mainpg timer-nav">
             {{ $product->links( "pagination::bootstrap-4") }}
        </nav>
    </section>
   </main>
@endsection
