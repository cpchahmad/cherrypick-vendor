@extends('layouts.superadmin')

<style>
    .form-select{
        width: auto !important;
    }

    .image-container {
        position: relative;
        display: inline-block;
    }

    .delete-button {
        position: absolute;
        top: 0;
        right: 0;
        /*color: white;*/
        padding: 0px 0px;
        cursor: pointer;
    }

    .bi-x-circle{
        font-size:20px;
    }
    .btn_size{
        font-size: 12px !important;
        margin-right: 10px;
    }
    .rte-modern.rte-desktop.rte-toolbar-default{
        min-width: unset !important;
    }

    .card.fullorders{
        padding: 0px !important;
    }
    .nav-link{
        text-align: left;
    }
</style>
@section('main')
    <main id="main" class="main">
        <div class="subpagetitle fit-title">
            <div class="row">
                <div class="col-6">
            <h1>{{$vendor->name}}</h1>
            <p><a href="{{url('superadmin/vendors')}}">Store</a> / <b>Store Setting</b></p>
        </div>

                <div class="col-6" style="text-align: right">
                    <a class="btn btn-primary" href="{{route('update-price-by-vendor',$vendor->id)}}">Update Prices in Database</a>
                    <a class="btn btn-primary" href="{{route('update-price-by-vendor-inshopify',$vendor->id)}}">Update Prices in Shopify</a>

                </div>
            </div>
        </div>

        <input type="hidden" class="vendor_name" name="vendor_name" value="{{$vendor->name}}" >
        <section class="section up-banner">
            <div class="row">
                <div class="col-3 ">


                    <div class="card fullorders">
                        <div class="nav flex-column nav-pills " id="v-pills-tab" role="tablist" aria-orientation="vertical">

                            <button class="nav-link active" id="tabs-profile-6" data-bs-toggle="pill" data-bs-target="#v-pills-home" type="button" role="tab" aria-controls="v-pills-home" aria-selected="true">General Configuration</button>
                            <button class="nav-link" id="v-pills-profile-tab" data-bs-toggle="pill" data-bs-target="#v-pills-profile" type="button" role="tab" aria-controls="v-pills-profile" aria-selected="false">Update Product Tags</button>
                            <button class="nav-link" id="v-pills-messages-tab" data-bs-toggle="pill" data-bs-target="#v-pills-messages" type="button" role="tab" aria-controls="v-pills-messages" aria-selected="false">Store Front Configuration</button>
                            <button class="nav-link" id="v-pills-messages1-tab" data-bs-toggle="pill" data-bs-target="#v-pills-messages1" type="button" role="tab" aria-controls="v-pills-messages1" aria-selected="false">Payment Configuration</button>
                            <button class="nav-link" id="v-pills-messages2-tab" data-bs-toggle="pill" data-bs-target="#v-pills-messages2" type="button" role="tab" aria-controls="v-pills-messages2" aria-selected="false">Bulk Price Update</button>
                            <button class="nav-link" id="v-pills-messages3-tab" data-bs-toggle="pill" data-bs-target="#v-pills-messages3" type="button" role="tab" aria-controls="v-pills-messages3" aria-selected="false">HSN and Weight</button>
                            @if($vendor->name=='Kalamandir')
                            <button class="nav-link" id="v-pills-messages5-tab" data-bs-toggle="pill" data-bs-target="#v-pills-messages5" type="button" role="tab" aria-controls="v-pills-messages3" aria-selected="false">Categories (API)</button>
                                @endif
                            <button class="nav-link" id="v-pills-messages4-tab" data-bs-toggle="pill" data-bs-target="#v-pills-messages4" type="button" role="tab" aria-controls="v-pills-messages4" aria-selected="false">Settings</button>

{{--                            <ul class="nav nav-tabs flex-column" id="v-pills-tab" role="tablist" aria-orientation="vertical">--}}

{{--                                <li class="nav-item">--}}
{{--                                    <a href="#tabs-profile-6" class="nav-link active" data-bs-toggle="tab">General Configuration</a>--}}
{{--                                </li>--}}

{{--                                <li class="nav-item">--}}
{{--                                    <a href="#tabs-profile-9" class="nav-link " data-bs-toggle="tab">Update Product Tags</a>--}}
{{--                                </li>--}}

{{--                                <li class="nav-item">--}}
{{--                                    <a href="#tabs-profile-10" class="nav-link " data-bs-toggle="tab">Store Front Configuration</a>--}}
{{--                                </li>--}}

{{--                                <li class="nav-item">--}}
{{--                                    <a href="#tabs-profile-11" class="nav-link " data-bs-toggle="tab">Payment Configuration</a>--}}
{{--                                </li>--}}

{{--                                <li class="nav-item">--}}
{{--                                    <a href="#tabs-home-7" class="nav-link " data-bs-toggle="tab">Bulk Price Update</a>--}}
{{--                                </li>--}}
{{--                                <li class="nav-item">--}}
{{--                                    <a href="#tabs-profile-7" class="nav-link" data-bs-toggle="tab">HSN and Weight</a>--}}
{{--                                </li>--}}
{{--                                <li class="nav-item">--}}
{{--                                    <a href="#tabs-profile-8" class="nav-link" data-bs-toggle="tab">Settings</a>--}}
{{--                                </li>--}}


{{--                            </ul>--}}

                        </div>
                    </div>
                </div>

                    <div class="col-9">
                    <div class="card">
                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab">

                                        <form class="add-product-form" method="post" action="{{route('genralconfiguration.update',$vendor->id)}}">
                                            @csrf
                                            <div class="gen-config">
                                                <div class="row">
                                                    <div class="col-12">

                                                        <div class="row">
                                                            <div class="col-6 field">
                                                                <label for="inputNanme4" class="form-label">Email</label>
                                                                <input type="email" class="form-control" id="" value="{{$vendor->email}}" placeholder="Enter Email" name="email">
                                                                <span style="color:red;">
                                      @error('email')
                                        <span style="color:red;">Please enter valid email address.</span>
                                      @enderror
                                    </span>
                                                            </div>
                                                            <div class="col-6 field">
                                                                <label for="inputNanme4" class="form-label">Contact Number</label>
                                                                <input type="text" class="form-control" id="mobile" placeholder="Enter Mobile Number" name="mobile" value="{{$vendor->mobile}}">
                                                                <span style="color:red;">
                                    @error('mobile')
                                                                    {{$message}}
                                                                    @enderror
                                  </span>
                                                            </div>
                                                        </div>
                                                        <div class="row mt-3">
                                                            <div class="col-6 field">
                                                                <label for="inputNanme4" class="form-label">Address </label>
                                                                <input type="text" class="form-control" id="" placeholder="Enter Address" name="address" value="{{$vendor->address}}">
                                                                <span style="color:red;">
                                    @error('address')
                                                                    {{$message}}
                                                                    @enderror
                                  </span>
                                                            </div>
                                                        </div>
                                                        <div class="mt-3">
                                                            <button type="submit" style="float:right" class="btn btn-primary">Update</button>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </form>
                                    </div>
                                    <div class="tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
                                        <form action="{{route('update.vendor.tag',$vendor->id)}}" method="POST">
                                            @csrf

                                            <input type="hidden" name="active_tab" value="v-pills-profile">
                                            <div class="mb-2">
                                                <input type="text" class="form-control" value="" name="tags" placeholder="Add Tags">
                                                @error('tags')
                                                <div class="error text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <button type="submit" class="btn btn-danger btn-sm">Add Tag</button>
                                        </form>
                                    </div>

                                    <div class="tab-pane fade" id="v-pills-messages" role="tabpanel" aria-labelledby="v-pills-messages-tab">
                                        <form class="add-product-form" method="post" action="{{route('storeFront.update',$vendor->id)}}" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="active_tab" value="v-pills-messages">
                                            <div class="store-config">
                                                <div class="row">
                                                    <div class="col-12">
                                                        @if($vendor->logo!='')
                                                            <div class="col-3"><img src="{{asset('uploads/logo/'.$vendor->logo)}}" height="120px" width='120px'></div>
                                                        @endif
                                                        <label for="inputNanme4" class="form-label">Store Logo</label>
                                                        <input type="file" class="form-control" accept="image/png, image/gif, image/jpeg, image/jpg" name="logo">
                                                        <span style="color:red;">
                          @error('logo')
                                                            {{$message}}
                                                            @enderror
                          </span>
                                                    </div>
                                                    <div class="col-12 mt-3">
                                                        <label for="inputNanme4" class="form-label">About the Store </label>
                                                        <textarea class="form-control" name="about_store" id="" placeholder="About the Store ">{{$vendor->about_store}}</textarea>
                                                        <span style="color:red;">
                            @error('about_store')
                                                            {{$message}}
                                                            @enderror
                          </span>
                                                    </div>
                                                    <div class="col-12 mt-3">
                                                        <label for="inputNanme4" class="form-label">What products does the store carry?</label>
                                                        <textarea name="store_carry" class="form-control" id="" placeholder="What products does the store carry?">{{$vendor->store_carry}}</textarea>
                                                    </div>
                                                    <span style="color:red;">
                         @error('store_carry')
                                                        {{$message}}
                                                        @enderror
                     </span>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <button type="submit" style="float:right" class="btn btn-primary">Update</button>
                                            </div>
                                        </form>
                                    </div>

                                    <div class="tab-pane fade" id="v-pills-messages1" role="tabpanel" aria-labelledby="v-pills-messages1-tab">
                                        @if($payment)
                                            <form class="add-product-form edit-back-info" method="post" action="{{route('paymentDetails.update',$payment->id)}}">
                                                @csrf
                                                <div class="">
                                                    <h5>Edit Bank Info</h5>

                                                    <div class="row">
                                                        <div class="col-6">
                                                            <label for="inputNanme4" class="form-label">Account No</label>
                                                            <input type="text" class="form-control" id="account" value="@if(isset($payment->account_no)){{$payment->account_no}} @else {{old('account_no')}} @endif" placeholder="Enter account No" name="account_no">
                                                            <span style="color:red;">
                         @error('account_no')
                                                                {{$message}}
                                                                @enderror
                     </span>
                                                        </div>
                                                        <div class="col-6">
                                                            <label for="inputNanme4" class="form-label">Bank Name</label>
                                                            <input type="text" class="form-control" id="name" placeholder="Enter bank name" name="bank_name" value="@if(isset($payment->bank_name)){{$payment->bank_name}} @else {{old('name')}} @endif">
                                                            <span style="color:red;">
                         @error('bank_name')
                                                                {{$message}}
                                                                @enderror
                     </span>
                                                        </div>
                                                        <div class="col-6 mt-3">
                                                            <label for="inputNanme4" class="form-label">IFSC</label>
                                                            <input type="text" class="form-control" id="" placeholder="Enter IFSC" name="ifsc" value="@if(isset($payment->ifsc)){{$payment->ifsc}}@endif">
                                                            <span style="color:red;">
                         @error('ifsc')
                                                                {{$message}}
                                                                @enderror
                     </span>
                                                        </div>
                                                        <div class="col-6 mt-3">
                                                            <label for="inputNanme4" class="form-label">GST</label>
                                                            <input type="text" class="form-control" id="" placeholder="Enter GST" name="gst" value="@if(isset($payment->gst)){{$payment->gst}} @else {{old('gst')}} @endif">
                                                            <span style="color:red;">
                         @error('gst')
                                                                {{$message}}
                                                                @enderror
                     </span>
                                                        </div>
                                                        <div class="col-6 mt-3">
                                                            <label for="inputNanme4" class="form-label">Account Type</label>
                                                            <select class="form-control" name='account_type'>
                                                                <option value="current"@if(isset($payment->account_type) && $payment->account_type=='current'){{'selected'}}@endif>Current</option>
                                                                <option value="saving"@if(isset($payment->account_type) && $payment->account_type=='saving'){{'selected'}}@endif>Saving</option>
                                                            </select>
                                                            <span style="color:red;">
                         @error('account_type')
                                                                {{$message}}
                                                                @enderror
                     </span>
                                                        </div>
                                                        <div class="col-12 mt-3">
                                                            <label for="inputNanme4" class="form-label">Address</label>
                                                            <textarea name="address" class="form-control" id="" value="" placeholder="Address">@if(isset($payment->address)){{$payment->address}} @else {{old('address')}} @endif</textarea>
                                                            <span style="color:red;">
                         @error('address')
                                                                {{$message}}
                                                                @enderror
                     </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mt-3">
                                                    <!-- <button type="reset" class="btn btn-secondary">Cancel</button> -->
                                                    <button type="submit" style="float:right" class="btn btn-primary">Update</button>
                                                </div>
                                            </form>

                                        @elseif($payment=='')
                                            <div class="order-items">
                                                <p><b>Account No  </b><span></span></p>
                                                <p><b>Bank Name  </b><span></span></p>
                                                <p><b>IFSC </b><span></span></p>
                                                <p><b>GST  </b><span></span></p>
                                                <p><b>Bank Address  </b><span></span></p>
                                            </div>

                                        @endif
                                    </div>

                                    <div class="tab-pane fade" id="v-pills-messages2" role="tabpanel" aria-labelledby="v-pills-messages2-tab">
                                        @if(count($markets) > 0)
                                            <div class="table-responsive">
                                            <table class="table table-bordered table-white">
                                                <thead>
                                                <tr>
{{--                                                    <th scope="col">S.No</th>--}}
                                                    <th scope="col">Market</th>
                                                    <th scope="col">Increase/Decrease</th>
                                                    <th scope="col">Type</th>
                                                    <th scope="col">Value</th>
                                                    <th scope="col">Action</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php $i=1; ?>
                                                @foreach($markets as $market)
                                                    <tr>
{{--                                                        <td>{{$i++}}</td>--}}
                                                        <td>{{$market->name}}</td>
                                                        <td>

                                                            @php
                                                                $market_vendor=\App\Models\MarketVendor::where('market_id',$market->id)->where('vendor_id',$vendor->id)->first();
                                                            @endphp
                                                            <select class="form-select" id="status_{{$market->id}}" aria-label="Select Status" name="price_status">
                                                                <option value='' selected="">Select Status</option>
                                                                <option value="increase"  @if($market_vendor && $market_vendor->status == 'increase') selected @endif   >Increase</option>
                                                                <option value="decrease"  @if($market_vendor && $market_vendor->status == 'decrease') selected @endif >Decrease</option>
                                                            </select>


                                                        </td>



                                                        <td>
                                                            <select class="form-select" id="type_{{$market->id}}" aria-label="Select Status" name="type">
                                                                <option value='' selected="">Select Type</option>
                                                                <option value="fixed"  @if($market_vendor && $market_vendor->type == 'fixed') selected @endif >Fixed</option>
                                                                <option value="percentage"  @if($market_vendor && $market_vendor->type == 'percentage') selected @endif >Percentage</option>
                                                            </select>
                                                        </td>


                                                        <td><input type="text" class="float-number" id="value_{{$market->id}}" value=" @if($market_vendor) {{$market_vendor->value}} @endif"></td>

                                                        <td><button class="btn btn-primary btn_size" onclick="updateMarketPrice({{$market->id}})">Save</button></td>

                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                            </div>
                                        @else
                                            <p>No Market Found</p>

                                        @endif

                                    </div>
                                    <div class="tab-pane fade" id="v-pills-messages3" role="tabpanel" aria-labelledby="v-pills-messages3-tab">

                                        <div class="table-responsive">
                                            @if(count($vendor_product_types) > 0)
                                                <table class="table table-bordered table-white">
                                                    <thead>
                                                    <tr>
                                                        <th scope="col">Product Type</th>
                                                        <th scope="col">HSN Code</th>
                                                        <th scope="col">Base Weight</th>
                                                        <th scope="col">Size Chart</th>
                                                        <th scope="col">Action</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php $i=1; ?>

                                                    @foreach($vendor_product_types as $vendor_product_type)
                                                        <tr>

                                                            <td>{{$vendor_product_type->product_type}}</td>

                                                            <td><input type="text" class="float-number" id="hsn_{{$vendor_product_type->id}}" value="{{$vendor_product_type->hsn_code}}"></td>
                                                            <td><input type="text" class="float-number" id="weight_{{$vendor_product_type->id}}" value="{{$vendor_product_type->base_weight}}"></td>
{{--                                                            <td><a href="#" data-bs-toggle="modal" data-bs-target="#basicModal_{{$vendor_product_type->id}}">Add Size Chart</a></td>--}}
                                                            <td><a target="_blank" href="{{route('superadmin.add-product-type-sizechart',$vendor_product_type->id)}}" >Add Size Chart</a></td>
                                                            <td>

                                                                <div class="btn-list flex-nowrap">
                                                                    <div class="dropdown">
                                                                        <button class="btn dropdown-toggle align-text-top" data-bs-toggle="dropdown">
                                                                            Actions
                                                                        </button>
                                                                        <div class="dropdown-menu dropdown-menu-end" style="cursor: pointer">
                                                                            <a onclick="updateRecord({{$vendor_product_type->id}})"  class="dropdown-item" >
                                                                                Save
                                                                            </a>
                                                                            <a class="dropdown-item update_database_price" data-store="{{$vendor->name}}" data-id="{{$vendor_product_type->id}}"  >
                                                                                Update Prices in Database
                                                                            </a>

                                                                            <a class="dropdown-item update_shopify_price" data-store="{{$vendor->name}}"  data-id="{{$vendor_product_type->id}}" >
                                                                                Update Prices in Shopify
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>


                                                            </td>

                                                            <div class="modal fade" id="basicModal_{{$vendor_product_type->id}}" tabindex="-1" aria-hidden="true" style="display: none;">
                                                                <div class="modal-dialog modal-lg">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title">Size Chart</h5>
                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                        </div>
                                                                        <div class="modal-body">

                                                                            <form role="form" method="POST" action="{{route('superadmin.update-product-type-sizechart')}}" enctype="multipart/form-data">

                                                                                @csrf
                                                                                @method('post')

                                                                                <input type="hidden" name="product_type_id" value="{{$vendor_product_type->id}}">
                                                                                <div class="col-12 mt-2">
                                                                                    <label for="inputNanme4" class="form-label">HTML</label>

                                                                                    <div>
                                                                                        <textarea style="width: 100% !important;" name="product_type_html" id="editor_id_{{$vendor_product_type->id}}" class="form-control editor_{{$vendor_product_type->id}}" rows="3">@if(isset($vendor_product_type->size_chart_html)){{$vendor_product_type->size_chart_html}}@endif </textarea>

                                                                                    </div>
                                                                                </div>

                                                                                <div class="col-12 mt-2">

                                                                                    @if(isset($vendor_product_type->size_chart_image))
                                                                                        <div class="col-3 mt-2">
                                                                                            <div class="image-container">
                                                                                                <img src="{{$vendor_product_type->size_chart_image}}" height="120px" width="120px">
                                                                                                <div class="delete-button">

                                                                                                    <i class="bi bi-x-circle product_type_setting" data-id="{{$vendor_product_type->id}}"></i>
                                                                                                </div>
                                                                                            </div>

                                                                                        </div>
                                                                                    @endif

                                                                                    <label for="inputNanme4" class="form-label mt-2">Image</label>
                                                                                    <div>
                                                                                        <input name="product_type_file" type="file" data-default-file="@if(isset($vendor_product_type->size_chart_image)){{$vendor_product_type->size_chart_image}}@endif" class="dropify_{{$vendor_product_type->id}}" data-height="100" />
                                                                                        <input name="product_type_file" type="file" src="@if(isset($vendor_product_type->size_chart_image)){{$vendor_product_type->size_chart_image}}@endif" class="form-control" data-height="100" />
                                                                                    </div>
                                                                                </div>

                                                                                <div class="modal-footer">

                                                                                    <div class="form-group">
                                                                                        <div>

                                                                                            <button type="submit" class="btn btn-info btn-block">Add</button>
                                                                                        </div>
                                                                                    </div>

                                                                                </div>
                                                                            </form>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </tr>
                                                    @endforeach

                                                    </tbody>
                                                </table>
                                            @else
                                                <p>No Product Type Found</p>
                                            @endif
                                        </div>

                                    </div>

                                    <div class="tab-pane fade" id="v-pills-messages5" role="tabpanel" aria-labelledby="v-pills-messages5-tab">
                                        <label for="inputNanme4" class="form-label">Categories</label>

                                        @if(count($thirdPartyApiCategories) > 0)
                                            <ul>
                                                @foreach($thirdPartyApiCategories as $category)
                                                    <li>
                                                        {{ $category->name }} {{-- Assuming your category model has a 'name' attribute --}}
                                                        @if(count($category->childrenRecursive) > 0)
                                                            @php
                                                                $renderChildren = function ($categories) use (&$renderChildren) {
                                                                    echo '<ul>';
                                                                    foreach ($categories as $childCategory) {
                                                                        echo '<li>' . $childCategory->name; // Assuming your category model has a 'name' attribute
                                                                        if (count($childCategory->childrenRecursive) > 0) {
                                                                            $renderChildren($childCategory->childrenRecursive);
                                                                        }
                                                                        echo '</li>';
                                                                    }
                                                                    echo '</ul>';
                                                                };
                                                                $renderChildren($category->childrenRecursive);
                                                            @endphp
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>

                                    <div class="tab-pane fade" id="v-pills-messages4" role="tabpanel" aria-labelledby="v-pills-messages4-tab">
                                        <form class="add-product-form" method="post" action="{{url('superadmin/vendor-setting')}}"  enctype="multipart/form-data" >
                                            @csrf

                                            <input type="hidden" name="active_tab" value="v-pills-messages4">
                                            <div class="card">
                                                <div class="row">

                                                    <input type="hidden" name="vendor_id" value="{{$vendor->id}}" >

                                                    <div class="col-6">
                                                        <label for="inputNanme4" class="form-label">Base Weight</label>
                                                        <input type="text" class="form-control" id="" name="base_weight" value="@if(isset($vendor->base_weight)) {{$vendor->base_weight}} @endif" required="true">
                                                        <span style="color:red;">
                         @error('dirham_inr')
                                                            {{$message}}
                                                            @enderror
                     </span>

                                                    </div>
                                                    <div class="col-6">
                                                        <label for="inputNanme4" class="form-label">HSN Code</label>
                                                        <input type="text" class="form-control" id="" name="hsn_code" value="@if(isset($vendor->hsn_code)) {{$vendor->hsn_code}} @endif">
                                                        <span style="color:red;">
                         @error('dirham_inr')
                                                            {{$message}}
                                                            @enderror
                     </span>

                                                    </div>

                                                </div>
                                            </div>

                                            <div class="card">
                                                <div class="row">
                                                    <h4>Size Chart</h4>


                                                    <div class="col-12 mt-2">
                                                        <label for="inputNanme4" class="form-label">HTML</label>

                                                        <div>
                                                            <textarea style="width: 100% !important;" name="html" id="editor1" class="form-control " rows="3">@if(isset($vendor->size_chart_html)){{$vendor->size_chart_html}}@endif </textarea>

                                                        </div>
                                                    </div>

                                                    <div class="col-12 mt-2">

                                                        @if(isset($vendor->size_chart_image))
                                                            <div class="col-3 mt-2">
                                                                <div class="image-container setting_sizechart_img ">
                                                                    <img src="{{$vendor->size_chart_image}}" height="120px" width="120px">
                                                                    <div class="delete-button">

                                                                        <i class="bi bi-x-circle setting_sizechart" data-id="{{$vendor->id}}"></i>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        @endif

                                                        <label for="inputNanme4" class="form-label mt-2">Image</label>
                                                        <div>
                                                            <input name="file" type="file" src="@if(isset($vendor->size_chart_image)){{$vendor->size_chart_image}}@endif" class="form-control" data-height="100" />
                                                        </div>
                                                    </div>
                                                </div>




                                            </div>

                                            <div class="timer-btns">
                                                <button type="submit" class="btn btn-primary">Submit</button>
                                            </div>
                                        </form>


                                    </div>

                                </div>
                            </div>

                    </div>
                    </div>


            </div>
        </section>
    </main>
@endsection

@section('js')
<script src="https://cdn.ckeditor.com/4.11.3/standard/ckeditor.js"></script>

@if(Session::has('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var activeTabId = "{{ Session::get('active_tab') }}";
            var activeButton = document.querySelector('[data-bs-target="#' + activeTabId + '"]');

            if (activeButton) {
                activeButton.click();
            }
        });
    </script>
@endif

<script>





    function updateRecord(id)
    {
        var weight_val=$('#weight_'+id).val();
        var hsn_val=$('#hsn_'+id).val();
        var v_token = "{{csrf_token()}}";
        var store=$('.vendor_name').val();
        console.log(store);

            $.ajax({
                type:'post',
                data:{id : id, weight : weight_val,hsncode:hsn_val,store:store},
                headers: {'X-CSRF-Token': v_token},
                url:"{{ route('superadmin.update.record') }}",
                success:function(response){
                    var json = $.parseJSON(response);
                    if(json.status=='success')
                    {
                        toastr.success("Record Updated Successfully!!");
                        // alert("Record Updated Successfully!!")
                    }
                }
            });

    }


    function updateMarketPrice(id)
    {
        var status_val=$('#status_'+id).val();
        var type_val=$('#type_'+id).val();
        var value=$('#value_'+id).val();
        var vendor_id={{$vendor->id}};
        var v_token = "{{csrf_token()}}";

        // if(status_val && type_val && value) {
            $.ajax({
                type: 'post',
                data: {id: id, status: status_val, type: type_val, value: value, vendor_id: vendor_id},
                headers: {'X-CSRF-Token': v_token},
                url: "{{ route('superadmin.update.market-bulkprice') }}",
                success: function (response) {
                    var json = $.parseJSON(response);
                    if (json.status == 'success') {

                        toastr.success("Record Updated Successfully!!");
                    }
                }
            });
        // }
    }
</script>

<script>


        var editor1 = new RichTextEditor("#editor1", { editorResizeMode: "none" });
</script>

<script>
    $(document).ready(function(){

        $('.product_type_setting').click(function (){

        var id=$(this).data('id');
    $(this).parents('.modal-body').find('.image-container').css('display', 'none');


    $.ajax({
        type: 'get',
        data: {id: id},
        url: "{{ route('superadmin.delete.product-type-img') }}",
        success: function (response) {
            var json = $.parseJSON(response);
            if (json.status == 'success') {

                toastr.success("Image Deleted Successfully!!");
            }
        }
    });
})


        $('.setting_sizechart').click(function (){

            var id=$(this).data('id');
            $('.setting_sizechart_img').css('display', 'none');


            $.ajax({
                type: 'get',
                data: {id: id},
                url: "{{ route('superadmin.delete.setting-img') }}",
                success: function (response) {
                    var json = $.parseJSON(response);
                    if (json.status == 'success') {

                        toastr.success("Image Deleted Successfully!!");
                    }
                }
            });
        })


        $('.update_database_price').click(function (){

            var id=$(this).data('id');
            var store=$(this).data('store');
            $.ajax({
                type: 'get',
                data: {id: id,store:store},
                url: "{{ route('superadmin.update.database-price-by-producttype') }}",
                success: function (response) {
                    var json = $.parseJSON(response);
                    if (json.status == 'success') {

                        toastr.success("Updating Database Price is In-Progress!!");
                    }
                }
            });
        });

        $('.update_shopify_price').click(function (){

            var id=$(this).data('id');
            var store=$(this).data('store');
            $.ajax({
                type: 'get',
                data: {id: id,store:store},
                url: "{{ route('superadmin.update.shopify-price-by-producttype') }}",
                success: function (response) {
                    var json = $.parseJSON(response);
                    if (json.status == 'success') {

                        toastr.success("Updating Shopify Price is In-Progress!!");
                    }
                }
            });
        });
    });
</script>

@endsection
