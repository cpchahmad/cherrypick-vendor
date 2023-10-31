@extends('layouts.superadmin')
<link
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css"
    rel="stylesheet"
/>


<style>
    .bg-cyan-lt{
        color: black !important;
        background: #e3e3e3;
        font-weight: 500 !important;
    }
    .font_size{
        font-size: 15px;
    }

    .bootstrap-tagsinput .tag {
        margin: 3px;
        color: white !important;
        background-color: #9c9eff;
        padding: 0.2rem;
        display: inline-block;
    }


</style>
@section('main')
    <main id="main" class="main">
        <div class="subpagetitle fit-title">
            <h1>{{$data->title}}</h1>
            <p><a href="{{url('superadmin/products')}}">Product List</a> / <b>View Product</b></p>
        </div>
        <section class="section up-banner">

            <div class="row">
                <div class="col-12" style="text-align: right">

                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#basicModal">
                        Edit
                    </button>




                </div>
            </div>
            <div class="row">

                <div class="row row-cards">
                    <div class="col-lg-12 col-md-12">
                        <div class="">



                            <div class="row">
                                <div class="col-sm-8" style="padding-right: 0">

                                    <div class="card bg-white border-0 mt-3 mb-3 shadow-sm">
                                        <div class="card-body bg-white border-light">

                                            <input type="hidden" id="product_id" value="{{$data->id}}" >
                                            <input type="hidden" id="title" value="{{$data->title}}" >
                                            <input type="hidden" id="body_html" value="{{$data->body_html}}" >
                                            <div class="form-group">
                                                <label class="col-form-label" for="formGroupExampleInput">Title</label>
                                                <input type="text" class="form-control" id="formGroupExampleInput" value="{{$data->title}}">
                                            </div>
                                            <div class="form-group">
                                                <label class="col-form-label" for="description">Description</label>
                                                <textarea class="form-control" name="description" id="description"  rows="10">{{strip_tags($data->body_html)}}</textarea>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="card bg-white border-0 mt-3 mb-3 shadow-sm">
                                        <div class="card-body bg-white border-light">
                                            <strong>Media</strong>
                                            <div class="row">

                                                @php
                                                                                        // $images=\App\Models\ProductImages::where(['product_id' =>$data->id])->whereNOTNull('image_id')->get();
                                             $images=\App\Models\ProductImages::where(['product_id' =>$data->id])->get();
                                                                                         //whereNull('variant_ids')
                                                                                 //echo "<pre>"; print_r($images);
                                                                                    @endphp
                                                @foreach($images as $prd)
                                                    <div class="col-3 mb-3">
                                                        <img src="{{$prd->image}}" class="img-fluid" alt="Image 1">
                                                    </div>
                                                    @if($prd->image2)
                                                        <div class="col-3 mb-3">
                                                            <img src="{{$prd->image2}}" class="img-fluid" alt="Image 2">
                                                        </div>
                                                    @endif
                                                    @if($prd->image3)
                                                        <div class="col-3 mb-3">
                                                            <img src="{{$prd->image3}}" class="img-fluid" alt="Image 3">
                                                        </div>
                                                    @endif
                                                    @if($prd->image4)
                                                        <div class="col-3 mb-3">
                                                            <img src="{{$prd->image4}}" class="img-fluid" alt="Image 4">
                                                        </div>
                                                    @endif
                                                    @if($prd->image5)
                                                        <div class="col-3 mb-3">
                                                            <img src="{{$prd->image5}}" class="img-fluid" alt="Image 5">
                                                        </div>
                                                    @endif
                                                @endforeach


                                            </div>
                                        </div>

                                    </div>
                                    <strong>Variants</strong>
                                    @foreach($items as $row)
                                    <div class="card bg-white border-0 mt-3 mb-3 shadow-sm">

                                        <div class="card-body bg-white border-light">

                                            <div class="row">
                                                <div class="col-12" style="text-align:end">
                                                    <button class="btn btn-warning btn-sm submit"  id="{{$row->id}}" shipping="{{$row->pricing_weight}}">Update</button>
                                                </div>
                                            </div>
                                            <div class="row mt-3" style="border-bottom: 2px solid black">

                                                     <div class="col-6">
                                                         @if($data->is_variants==1)
                                                         {{$row->varient_value}} / {{$row->varient1_value}}
                                                             @endif
                                                     </div>

                                                      <div class="col-6" style="text-align:right">
                                                          {{$row->sku}}
                                                      </div>
                                                  </div>

                                            <div class="row mt-3">
                                                @if($data->is_variants==1)
                                                <div class="col-6 font_size">
                                                    <p><b>Varaint 1 Name:</b> {{$row->varient_name}}</p>

                                                    <p><b>Varaint 1 Value:</b> {{$row->varient_value}}</p>


                                                    <p><b>Varaint 2 Name:</b> {{$row->varient1_name}}</p>
                                                    <p><b>Varaint 2 Value:</b> {{$row->varient1_value}}</p>
                                                </div>
                                                @endif
                                                <div class="col-6 font_size">

                                                    <p><b>Weight(GM):</b> {{$row->grams}}</p>
                                                    <p><b>Pricing Weight(GM):</b> {{$row->pricing_weight}}</p>

                                                    <p><b>Quantity:</b> {{$row->stock}}</p>


                                                    <p><b>Dimensions(H-W-L):</b> {{$row->dimensions}}</p>
                                                    <p><b>Shelf life:</b> {{$row->shelf_life}}</p>
                                                    <p><b>Temp requirements:</b> {{$row->temp_require}}</p>

                                                </div>
                                            </div>

                                            <div class="row mt-3">

                                                <h5>Price</h5>

                                                <div class="col-12">

                                                <p><b>Base Price:</b> {{number_format($row->base_price,2)}}</p>
                                                </div>
                                                    <div class="col-4" >
                                                    <p><b>INR-</b>{{number_format($row->price,2)}}</p>
                                                    </div>

                                                    <div class="col-4" >
                                                        <p><b>USD-</b>{{number_format($row->price_usd,2)}}</p>
                                                    </div>

                                                    <div class="col-4" >
                                                        <p><b>GBP-</b>{{number_format($row->price_gbp,2)}}</p>
                                                    </div>

                                                    <div class="col-4" >
                                                        <p><b>NLD-</b>{{number_format($row->price_nld,2)}}</p>
                                                    </div>

                                                <div class="col-4" >
                                                    <p><b>AUD-</b>{{number_format($row->price_aud,2)}}</p>
                                                </div>

                                                <div class="col-4" >
                                                    <p><b>CAD-</b>{{number_format($row->price_cad,2)}}</p>
                                                </div>


                                                </div>


                                                </div>


                                        </div>
                                    @endforeach







                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4">
                                    <div class="card border-light border-0 mt-3  shadow-sm">
                                        <div class="card-header  text-dark">
                                            <strong>Vendor Detail</strong>
                                        </div>

                                        <div class="card-body bg-white">
                                      <div class="row">

                                          <div style="text-align:end">
                                              @if($data->status != 1)
                                                  <a class="btn btn-success btn-sm" href="{{url('superadmin/shopify-create')}}/{{$data->id}}">Approve</a>
                                              @endif
                                              @if($data->status != 3 && $data->status != 1)
                                                  <a class="btn btn-danger btn-sm" href="{{url('superadmin/reject-product')}}/{{$data->id}}">Deny</a>
                                              @endif
                                          </div>
                                      </div>

                                          <div class=" row mt-4 order-items">
                                              <p><b>Vendor Name</b> <span>{{$vendor->name}}</span></p>
                                              <p><b>Vendor Email</b> <span>{{$vendor->email}}</span></p>
                                          </div>

                                        </div>
                                    </div>

                                    <div class="mt-1">
                                        <div class="card border-light border-0 mt-3  shadow-sm">
                                            <div class="card-header  text-dark">
                                                <strong>Product organization</strong>
                                            </div>

                                            <div class="card-body bg-white">
                                                <h5>Tags</h5>
                                                @if(isset($data->tags))
                                                    @php
                                                    $tags=explode(',',$data->tags);
                                                    @endphp
                                                    @foreach($tags as $tag)
                                                        <span  class="badge bg-cyan-lt ">{{$tag}}</span>
                                                    @endforeach
                                                @else
                                                    <span>No Tags</span>
                                                @endif
                                                <br>


                                                @php
                                                    $info_query=\App\Models\Category::where(['id' => $data->category])->pluck('category')->first();
                                                @endphp
                                                <div class="form-group mt-2">
                                                    <label class="col-form-label" for="formGroupExampleInput">Product Type</label>
                                                    <input type="text" class="form-control" id="formGroupExampleInput" value="{{ $info_query }}">
                                                </div>



                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
{{--                <div class="col-10 ">--}}
{{--                    <div class="card fullorders">--}}
{{--                        <div class="row ">--}}
{{--                            <div class="m-3">--}}

{{--                                <div style="float:right;">--}}
{{--                                    @if($data->status != 1)--}}
{{--                                        <a class="btn btn-success btn-sm" href="{{url('superadmin/shopify-create')}}/{{$data->id}}">Approve</a>--}}
{{--                                    @endif--}}
{{--                                    @if($data->status != 3 && $data->status != 1)--}}
{{--                                        <a class="btn btn-danger btn-sm" href="{{url('superadmin/reject-product')}}/{{$data->id}}">Deny</a>--}}
{{--                                    @endif--}}
{{--                                </div>--}}

{{--                                <div style="float:left;">--}}
{{--                                    <a class="btn btn-warning btn-sm" href="{{url()->previous()}}">Back</a>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="col-12">--}}
{{--                                <hr>--}}
{{--                                <!-- <h4>Product Details</h4> -->--}}


{{--                                --}}{{--                          <h3>Current Data</h3>--}}

{{--                                <div class="order-summry">--}}

{{--                                    <div class="order-items">--}}
{{--                                        <input type="hidden" id="product_id" value="{{$data->id}}" >--}}
{{--                                        <input type="hidden" id="title" value="{{$data->title}}" >--}}
{{--                                        <input type="hidden" id="body_html" value="{{$data->body_html}}" >--}}
{{--                                        <p><b>Title</b> <span>{{$data->title}}</span></p>--}}
{{--                                        <p><b>Body HTML</b> <span>{{$data->body_html}}</span></p>--}}
{{--                                        <p><b>Tags</b> <span>{{$data->tags}}</span></p>--}}
{{--                                        @php--}}
{{--                                            $info_query=\App\Models\Category::where(['id' => $data->category])->pluck('category')->first();--}}
{{--                                        @endphp--}}
{{--                                        <p><b>Type</b> <span>{{ $info_query }}</span></p>--}}

{{--                                    </div>--}}
{{--                                    <div class="order-items">--}}
{{--                                        <p><b>Vendor Name</b> <span>{{$vendor->name}}</span></p>--}}
{{--                                        <p><b>Vendor Email</b> <span>{{$vendor->email}}</span></p>--}}
{{--                                    </div>--}}
{{--                                </div>--}}

{{--                                <div class="card table-card">--}}
{{--                                    <div class="table-responsive">--}}
{{--                                        <table class="table table-borderless view-productd">--}}
{{--                                            <thead>--}}
{{--                                            <tr>--}}
{{--                                                @if($data->is_variants==1)--}}
{{--                                                    <th scope="col">Variant Name</th>--}}
{{--                                                    <th scope="col">Variant Value</th>--}}
{{--                                                @endif--}}
{{--                                                <th scope="col">Price</th>--}}
{{--                                                <th scope="col">SKU</th>--}}
{{--                                                <th scope="col">Weight(GM)</th>--}}
{{--                                                <th scope="col">Quantity</th>--}}
{{--                                                <th scope="col">Dimensions(H-W-L)</th>--}}
{{--                                                <th scope="col">Shelf life</th>--}}
{{--                                                <th scope="col">Temp requirements</th>--}}
{{--                                                <th scope="col">Action</th>--}}
{{--                                            </tr>--}}
{{--                                            </thead>--}}
{{--                                            <tbody>--}}
{{--                                            @foreach($items as $row)--}}
{{--                                                <tr>--}}
{{--                                                    @if($data->is_variants==1)--}}
{{--                                                        <td>{{$row->varient_name}}</td>--}}
{{--                                                        <td>{{$row->varient_value}}</td>--}}
{{--                                                    @endif--}}
{{--                                                    <td>--}}
{{--                                                        INR-{{$row->price}}<br>--}}
{{--                                                        USD-{{$row->price_usd}}<br>--}}
{{--                                                        GBP-{{$row->price_gbp}}<br>--}}
{{--                                                        NLD-{{$row->price_nld}}<br>--}}
{{--                                                        AUD-{{$row->price_aud}}<br>--}}
{{--                                                        CAD-{{$row->price_cad}}--}}
{{--                                                    </td>--}}
{{--                                                    <td>{{$row->sku}}</td>--}}
{{--                                                    <td>{{$row->grams}}</td>--}}
{{--                                                    <td>{{$row->stock}}</td>--}}
{{--                                                    <td>{{$row->dimensions}}</td>--}}
{{--                                                    <td>{{$row->shelf_life}}</td>--}}
{{--                                                    <td>{{$row->temp_require}}</td>--}}
{{--                                                    <td><button class="btn btn-warning btn-sm submit" id="{{$row->id}}" shipping="{{$row->shipping_weight}}">Update</button></td>--}}
{{--                                                </tr>--}}
{{--                                            @endforeach--}}
{{--                                            </tbody>--}}
{{--                                        </table>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                                <div class="row">--}}
{{--                                    <h4>Product Images</h4>--}}
{{--                                    @php--}}
{{--                                        // $images=\App\Models\ProductImages::where(['product_id' =>$data->id])->whereNOTNull('image_id')->get();--}}
{{--                                         $images=\App\Models\ProductImages::where(['product_id' =>$data->id])->get();--}}
{{--                                         //whereNull('variant_ids')--}}
{{--                                 //echo "<pre>"; print_r($images);--}}
{{--                                    @endphp--}}
{{--                                    --}}{{--                  @foreach($images as $prd)--}}

{{--                                    --}}{{--                  <div id='img_{{$prd->id}}' class="col-3"><img src="{{$prd->image}}" height="120px" width='120px'></div>--}}
{{--                                    --}}{{--                    @if($prd->image2)--}}
{{--                                    --}}{{--                  <div id='img_{{$prd->id}}' class="col-3"><img src="{{$prd->image2}}" height="120px" width='120px'></div>--}}
{{--                                    --}}{{--                        @endif--}}
{{--                                    --}}{{--                        @if($prd->image3)--}}
{{--                                    --}}{{--                  <div id='img_{{$prd->id}}' class="col-3"><img src="{{$prd->image3}}" height="120px" width='120px'></div>--}}
{{--                                    --}}{{--                  @endif--}}
{{--                                    --}}{{--                        @if($prd->image4)--}}
{{--                                    --}}{{--                            <div id='img_{{$prd->id}}' class="col-3"><img src="{{$prd->image4}}" height="120px" width='120px'></div>--}}
{{--                                    --}}{{--                  @endif--}}
{{--                                    --}}{{--                        @if($prd->image5)--}}
{{--                                    --}}{{--                            <div id='img_{{$prd->id}}' class="col-3"><img src="{{$prd->image5}}" height="120px" width='120px'></div>--}}
{{--                                    --}}{{--                    @endif--}}
{{--                                    --}}{{--                  @endforeach--}}


{{--                                    <div class="row">--}}
{{--                                        @foreach($images as $prd)--}}
{{--                                            <div class="col-3 mb-3">--}}
{{--                                                <img src="{{$prd->image}}" class="img-fluid" alt="Image 1">--}}
{{--                                            </div>--}}
{{--                                            @if($prd->image2)--}}
{{--                                                <div class="col-3 mb-3">--}}
{{--                                                    <img src="{{$prd->image2}}" class="img-fluid" alt="Image 2">--}}
{{--                                                </div>--}}
{{--                                            @endif--}}
{{--                                            @if($prd->image3)--}}
{{--                                                <div class="col-3 mb-3">--}}
{{--                                                    <img src="{{$prd->image3}}" class="img-fluid" alt="Image 3">--}}
{{--                                                </div>--}}
{{--                                            @endif--}}
{{--                                            @if($prd->image4)--}}
{{--                                                <div class="col-3 mb-3">--}}
{{--                                                    <img src="{{$prd->image4}}" class="img-fluid" alt="Image 4">--}}
{{--                                                </div>--}}
{{--                                            @endif--}}
{{--                                            @if($prd->image5)--}}
{{--                                                <div class="col-3 mb-3">--}}
{{--                                                    <img src="{{$prd->image5}}" class="img-fluid" alt="Image 5">--}}
{{--                                                </div>--}}
{{--                                            @endif--}}
{{--                                        @endforeach--}}
{{--                                    </div>--}}


{{--                                </div>--}}



{{--                                --}}{{--                          <h3>Previous Data</h3>--}}
{{--                                --}}{{--                          @foreach($change_products as $change_product)--}}
{{--                                --}}{{--                              <div class="order-summry">--}}

{{--                                --}}{{--                                  <div class="order-items">--}}
{{--                                --}}{{--                                      <p><b>Title</b> <span>{{$change_product->title}}</span></p>--}}
{{--                                --}}{{--                                      <p><b>Body HTML</b> <span>{{$change_product->body_html}}</span></p>--}}
{{--                                --}}{{--                                      <p><b>Tags</b> <span>{{$change_product->tags}}</span></p>--}}
{{--                                --}}{{--                                      @php--}}
{{--                                --}}{{--                                          $info_query=\App\Models\Category::where(['id' => $change_product->category])->pluck('category')->first();--}}
{{--                                --}}{{--                                      @endphp--}}
{{--                                --}}{{--                                      <p><b>Type</b> <span>{{ $info_query }}</span></p>--}}
{{--                                --}}{{--                                  </div>--}}

{{--                                --}}{{--                              </div>--}}

{{--                                --}}{{--                              <div class="card table-card">--}}
{{--                                --}}{{--                                  <div class="table-responsive">--}}
{{--                                --}}{{--                                      <table class="table table-borderless view-productd">--}}
{{--                                --}}{{--                                          <thead>--}}
{{--                                --}}{{--                                          <tr>--}}
{{--                                --}}{{--                                              @if($change_product->is_variants==1)--}}
{{--                                --}}{{--                                                  <th scope="col">Variant Name</th>--}}
{{--                                --}}{{--                                                  <th scope="col">Variant Value</th>--}}
{{--                                --}}{{--                                              @endif--}}
{{--                                --}}{{--                                              <th scope="col">Price</th>--}}
{{--                                --}}{{--                                              <th scope="col">SKU</th>--}}
{{--                                --}}{{--                                              <th scope="col">Weight(GM)</th>--}}
{{--                                --}}{{--                                              <th scope="col">Quantity</th>--}}
{{--                                --}}{{--                                              <th scope="col">Dimensions(H-W-L)</th>--}}
{{--                                --}}{{--                                              <th scope="col">Shelf life</th>--}}
{{--                                --}}{{--                                              <th scope="col">Temp requirements</th>--}}

{{--                                --}}{{--                                          </tr>--}}
{{--                                --}}{{--                                          </thead>--}}
{{--                                --}}{{--                                          <tbody>--}}
{{--                                --}}{{--                                          @foreach($change_variants as $change_variant)--}}
{{--                                --}}{{--                                              <tr>--}}
{{--                                --}}{{--                                                  @if($data->is_variants==1)--}}
{{--                                --}}{{--                                                      <td>{{$change_variant->varient_name}}</td>--}}
{{--                                --}}{{--                                                      <td>{{$change_variant->varient_value}}</td>--}}
{{--                                --}}{{--                                                  @endif--}}
{{--                                --}}{{--                                                  <td>--}}
{{--                                --}}{{--                                                      INR-{{$change_variant->price}}<br>--}}
{{--                                --}}{{--                                                      USD-{{$change_variant->price_usd}}<br>--}}
{{--                                --}}{{--                                                      GBP-{{$change_variant->price_gbp}}<br>--}}
{{--                                --}}{{--                                                      NLD-{{$change_variant->price_nld}}<br>--}}
{{--                                --}}{{--                                                      AUD-{{$change_variant->price_aud}}<br>--}}
{{--                                --}}{{--                                                      CAD-{{$change_variant->price_cad}}--}}
{{--                                --}}{{--                                                  </td>--}}
{{--                                --}}{{--                                                  <td>{{$change_variant->sku}}</td>--}}
{{--                                --}}{{--                                                  <td>{{$change_variant->grams}}</td>--}}
{{--                                --}}{{--                                                  <td>{{$change_variant->stock}}</td>--}}
{{--                                --}}{{--                                                  <td>{{$change_variant->dimensions}}</td>--}}
{{--                                --}}{{--                                                  <td>{{$change_variant->shelf_life}}</td>--}}
{{--                                --}}{{--                                                  <td>{{$change_variant->temp_require}}</td>--}}
{{--                                --}}{{--                                              </tr>--}}
{{--                                --}}{{--                                          @endforeach--}}
{{--                                --}}{{--                                          </tbody>--}}
{{--                                --}}{{--                                      </table>--}}
{{--                                --}}{{--                                  </div>--}}
{{--                                --}}{{--                              </div>--}}
{{--                                --}}{{--                          @endforeach--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}

{{--                </div>--}}


            </div>
        </section>
    </main>


    <div id="updateModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-xs-center">Update Pricing Weight</h4>
                    <button type="button" class="close" data-dismiss="modal" onclick="dismissModal();">&times;</button>
                </div>
                <div class="modal-body">

                    <form role="form" method="POST" action="{{route('superadmin.variantdetails')}}">

                        @csrf
                        @method('post')
                        <input type="hidden" name="variant_id" id="variant_id" >
                        <input type="hidden" name="product_id" id="product_val" >
{{--                        <div class="form-group">--}}
{{--                            <label class="control-label">Title</label>--}}
{{--                            <div>--}}
{{--                                <input type="text"  class="form-control input-lg"  name="new_title" id="title_val" placeholder="title" required>--}}

{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="form-group">--}}
{{--                            <label class="control-label">Body</label>--}}
{{--                            <div>--}}
{{--                                <input type="text"  class="form-control input-lg" name="new_body" id="body_val" placeholder="body" required >--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="form-group">--}}
{{--                            <label class="control-label">Tags</label>--}}
{{--                            <div>--}}
{{--                                <input type="text"  class="form-control input-lg" name="tags" value="{{$data->tags}}" id="tags" placeholder="Tags" required >--}}
{{--                            </div>--}}
{{--                        </div>--}}

                        <div class="form-group">
                            <label class="control-label">Pricing Weight</label>
                            <div>
                                <input type="text"  class="form-control input-lg decimal" name="shipping_weight" id="shipping_weight" placeholder="Shipping Weight" required>
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

    <div class="modal fade" id="basicModal" tabindex="-1" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <form role="form" method="POST" action="{{route('superadmin.updateproductdetails')}}">

                        @csrf
                        @method('post')
                        <input type="hidden" name="variant_id" id="variant_id" >
                        <input type="hidden" name="product_id" value="{{$data->id}}" id="product_val" >
                        <div class="form-group">
                            <label class="control-label">Title</label>
                            <div>
                                <input type="text"  class="form-control input-lg"  name="new_title" value="{{$data->title}}" id="title_val" placeholder="title" required>

                            </div>
                        </div>
                        <div class="form-group mt-2">
                            <label class="control-label">Description</label>
                            <div>
                                <textarea style="width: 100% !important;" name="description" id="editor1" class="form-control tooltip_logo_text" rows="3"> {{$data->body_html}} </textarea>

                            </div>
                        </div>
                        <div class="form-group mt-2">
                            <label class="control-label">Tags</label>

                            <div>
                                <input type="text" data-role="tagsinput"
                                       @if($data->tags) value="{{$data->tags}}" id="tagsInput"
                                       @endif name="tags" class="form-control tag_input">


                            </div>
                        </div>

                        <br>



                        <div class="modal-footer">

                            <div class="form-group">
                                <div>

                                    <button type="submit" class="btn btn-info btn-block">Edit</button>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>


    </div>

@endsection


<script src="https://cdn.ckeditor.com/4.11.3/standard/ckeditor.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.min.js"></script>


<script>
    $(function () {
        $('input')
            .on('change', function (event) {


                var $element = $(event.target);
                var $container = $element.closest('.example');

                if (!$element.data('tagsinput')) return;

                var val = $element.val();
                if (val === null) val = 'null';
                var items = $element.tagsinput('items');

                $('code', $('pre.val', $container)).html(
                    $.isArray(val)
                        ? JSON.stringify(val)
                        : '"' + val.replace('"', '\\"') + '"'
                );
                $('code', $('pre.items', $container)).html(
                    JSON.stringify($element.tagsinput('items'))
                );
            })
            .trigger('change');




    });

</script>


<script>
    $(document).ready(function(){

        $('.tooltip_logo_text').each(function () {
        CKEDITOR.replace($(this).prop('id'));

    });


    });
</script>


