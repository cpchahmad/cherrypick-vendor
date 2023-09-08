@extends('layouts.superadmin')
@section('main')
  <main id="main" class="main">
    <div class="subpagetitle fit-title">
        <h1>View Product</h1>
         <p><a href="{{url('superadmin/products')}}">Product List</a> / <b>View Product</b></p>
      </div>
    <section class="section up-banner">
        <div class="row">
            <div class="col-12 ">
                <div class="card fullorders">
                  <div class="row ">
                  <div class="m-3">
                           
                            <div style="float:right;">
                            @if($data->status != 1)
                              <a class="btn btn-success btn-sm" href="{{url('superadmin/shopify-create')}}/{{$data->id}}">Approve</a>
                            @endif
                            @if($data->status != 3 && $data->status != 1)
                              <a class="btn btn-danger btn-sm" href="{{url('superadmin/reject-product')}}/{{$data->id}}">Deny</a>
                            @endif
                            </div>
                            
                            <div style="float:left;">
                              <a class="btn btn-warning btn-sm" href="{{url()->previous()}}">Back</a>
                            </div>
                          </div>
                      <div class="col-12">
                        <hr>
                         <!-- <h4>Product Details</h4> -->
                         <div class="order-summry">
                        <div class="order-items">
                        <input type="hidden" id="product_id" value="{{$data->id}}" > 
                        <input type="hidden" id="title" value="{{$data->title}}" > 
                        <input type="hidden" id="body_html" value="{{$data->body_html}}" > 
                         <p><b>Ttile</b> <span>{{$data->title}}</span></p>
						 <p><b>Body HTML</b> <span>{{$data->body_html}}</span></p>
                         <p><b>Tags</b> <span>{{$data->tags}}</span></p>
						 @php 
                                $info_query=\App\Models\Category::where(['id' => $data->category])->pluck('category')->first();
                            @endphp
						 <p><b>Type</b> <span>{{ $info_query }}</span></p>
            
                        </div>
                        <div class="order-items">
                          <p><b>Vendor Name</b> <span>{{$vendor->name}}</span></p>
                          <p><b>Vendor Email</b> <span>{{$vendor->email}}</span></p>
                        </div>
                        </div>
                         
						 <div class="card table-card">
                  <div class="table-responsive">
                    <table class="table table-borderless view-productd">
                        <thead>
                          <tr>
			@if($data->is_variants==1)
			<th scope="col">Variant Name</th>
			<th scope="col">Variant Value</th>
			@endif
                            <th scope="col">Price</th>
                            <th scope="col">SKU</th>
                            <th scope="col">Weight(GM)</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Dimensions(H-W-L)</th>
                             <th scope="col">Shelf life</th>
                            <th scope="col">Temp requirements</th>
                             <th scope="col">Action</th>
                          </tr>
                        </thead>
                        <tbody>
						@foreach($items as $row)
                         <tr>
							@if($data->is_variants==1)
							<td>{{$row->varient_name}}</td>
							<td>{{$row->varient_value}}</td>
							@endif
							<td>
							INR-{{$row->price}}<br>
							USD-{{$row->price_usd}}<br>
							GBP-{{$row->price_gbp}}<br>
							NLD-{{$row->price_nld}}<br>
							AUD-{{$row->price_aud}}<br>
							CAD-{{$row->price_cad}}
							</td>
							<td>{{$row->sku}}</td>
							<td>{{$row->grams}}</td>
							<td>{{$row->stock}}</td>
							<td>{{$row->dimensions}}</td>
							<td>{{$row->shelf_life}}</td>
							<td>{{$row->temp_require}}</td>
							<td><button class="btn btn-warning btn-sm submit" id="{{$row->id}}" shipping="{{$row->shipping_weight}}">Update</button></td>
						 </tr>
						 @endforeach
                        </tbody>
                      </table>
                    </div>
                </div>
				<div class="row">
				<h4>Product Images</h4>
				  @php
                   // $images=\App\Models\ProductImages::where(['product_id' =>$data->id])->whereNOTNull('image_id')->get();
					$images=\App\Models\ProductImages::where(['product_id' =>$data->id])->get();
                    //whereNull('variant_ids')
		    //echo "<pre>"; print_r($images);
                  @endphp
                  @foreach($images as $prd)
                  <div id='img_{{$prd->id}}' class="col-3"><img src="{{$prd->image}}" height="120px" width='120px'></div>
                  @endforeach
                  </div>
                      </div>
                    </div>     
                 </div>
                
            </div>
          </div>
    </section>
   </main>
   
   
   <div id="updateModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-xs-center">Update Shipping Weight</h4>
                    <button type="button" class="close" data-dismiss="modal" onclick="dismissModal();">&times;</button>
            </div>
            <div class="modal-body">
            
                <form role="form" method="POST" action="{{route('superadmin.variantdetails')}}">
                
                 @csrf
               @method('post')
                    <input type="hidden" name="variant_id" id="variant_id" >
                    <input type="hidden" name="product_id" id="product_val" >
                    <div class="form-group">
                        <label class="control-label">Title</label>
                        <div>
                        <input type="text"  class="form-control input-lg"  name="new_title" id="title_val" placeholder="title" required>
                          
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Body</label>
                        <div>
                          <input type="text"  class="form-control input-lg" name="new_body" id="body_val" placeholder="body" required >
                        </div>
                    </div>
					<div class="form-group">
                        <label class="control-label">Tags</label>
                        <div>
                          <input type="text"  class="form-control input-lg" name="tags" value="{{$data->tags}}" id="tags" placeholder="Tags" required >
                        </div>
                    </div>
                    
                      <div class="form-group">
                        <label class="control-label">Shipping Weight</label>
                        <div>
                          <input type="text"  class="form-control input-lg decimal" name="shipping_weight" id="shipping_weight" placeholder="Shipping Wieght" required>
                        </div>
                    </div><br>
                    
                 
                    
                    <div class="modal-footer">
                    
                    <div class="form-group">
                        <div>
                           
                            <button type="submit" class="btn btn-info btn-block">Update</button>
                        </div>
                    </div>
                  
      </div>
                </form>
            </div>
           
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->





   
@endsection

  
