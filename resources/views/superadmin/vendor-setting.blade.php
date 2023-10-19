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
        color: white;
        padding: 0px 0px;
        cursor: pointer;
    }

    .bi-x-circle{
        font-size:20px;
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
        <section class="section up-banner">
            <div class="row">
                <div class="col-12 ">
                    <div class="card fullorders">
                        <ul class="nav nav-tabs" data-bs-toggle="tabs">

                            <li class="nav-item">
                                <a href="#tabs-profile-6" class="nav-link active" data-bs-toggle="tab">General Configuration</a>
                            </li>

                            <li class="nav-item">
                                <a href="#tabs-profile-9" class="nav-link " data-bs-toggle="tab">Update Product Tags</a>
                            </li>

                            <li class="nav-item">
                                <a href="#tabs-profile-10" class="nav-link " data-bs-toggle="tab">Store Front Configuration</a>
                            </li>

                            <li class="nav-item">
                                <a href="#tabs-profile-11" class="nav-link " data-bs-toggle="tab">Payment Configuration</a>
                            </li>

                            <li class="nav-item">
                                <a href="#tabs-home-7" class="nav-link " data-bs-toggle="tab">Bulk Price Update</a>
                            </li>
                            <li class="nav-item">
                                <a href="#tabs-profile-7" class="nav-link" data-bs-toggle="tab">HSN and Weight</a>
                            </li>
                            <li class="nav-item">
                                <a href="#tabs-profile-8" class="nav-link" data-bs-toggle="tab">Settings</a>
                            </li>


                        </ul>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane active show" id="tabs-profile-6">

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
                                <div class="tab-pane" id="tabs-profile-9">
                                    <form action="{{route('update.vendor.tag',$vendor->id)}}" method="POST">
                                        @csrf
                                        <div class="mb-2">
                                            <input type="text" class="form-control" value="" name="tags" placeholder="Add Tags">
                                            @error('tags')
                                            <div class="error text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <button type="submit" class="btn btn-danger btn-sm">Add Tag</button>
                                    </form>
                                </div>

                                <div class="tab-pane" id="tabs-profile-10">
                                    <form class="add-product-form" method="post" action="{{route('storeFront.update',$vendor->id)}}" enctype="multipart/form-data">
                                        @csrf
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

                                <div class="tab-pane" id="tabs-profile-11">
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

                                <div class="tab-pane " id="tabs-home-7">
                                    @if(count($markets) > 0)
                                        <table class="table table-bordered table-white">
                                            <thead>
                                            <tr>
                                                <th scope="col">S.No</th>
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
                                                    <td>{{$i++}}</td>
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

                                                    <td><button class="btn btn-primary" onclick="updateMarketPrice({{$market->id}})">Save</button></td>

                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                        @else
                                        <p>No Market Found</p>

                                    @endif

                                </div>
                                <div class="tab-pane" id="tabs-profile-7">

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
                                                  <td><a href="#" data-bs-toggle="modal" data-bs-target="#basicModal_{{$vendor_product_type->id}}">Add Size Chart</a></td>
                                                    <td><button class="btn btn-primary" onclick="updateRecord({{$vendor_product_type->id}})">Save</button></td>

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
{{--                                                                                <input name="product_type_file" type="file" data-default-file="@if(isset($vendor_product_type->size_chart_image)){{$vendor_product_type->size_chart_image}}@endif" class="dropify_{{$vendor_product_type->id}}" data-height="100" />--}}
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

                                <div class="tab-pane" id="tabs-profile-8">
                                    <form class="add-product-form" method="post" action="{{url('superadmin/vendor-setting')}}"  enctype="multipart/form-data" >
                                        @csrf
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

                                            </div>
                                        </div>

                                        <div class="card">
                                            <div class="row">
                                                <h4>Size Chart</h4>


                                                <div class="col-12 mt-2">
                                                    <label for="inputNanme4" class="form-label">HTML</label>

                                                    <div>
                                                        <textarea style="width: 100% !important;" name="html" id="editor1" class="form-control tooltip_logo_text" rows="3">@if(isset($vendor->size_chart_html)){{$vendor->size_chart_html}}@endif </textarea>

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




<script>





    function updateRecord(id)
    {
        var weight_val=$('#weight_'+id).val();
        var hsn_val=$('#hsn_'+id).val();
        var v_token = "{{csrf_token()}}";

            $.ajax({
                type:'post',
                data:{id : id, weight : weight_val,hsncode:hsn_val},
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
    $(document).ready(function(){

        // $('.dropify').dropify();


        {{--$('#basicModal_{{$vendor_product_types[0]->id}}').on('shown.bs.modal', function () {--}}
        {{--    $('.dropify_{{$vendor_product_types[0]->id}}').dropify();--}}
        {{--});--}}

        {{--$('.dropify{{$vendor_product_types[0]->id}}').dropify({--}}
        {{--    Parent: $('#basicModal_{{$vendor_product_types[0]->id}}')--}}
        {{--});--}}

        $('.tooltip_logo_text').each(function () {
            CKEDITOR.replace($(this).prop('id'));

        });

        @foreach($vendor_product_types as $index=> $vendor_product_type)
        {{--$('.dropify_{{$vendor_product_type->id}}').dropify();--}}

        $('.editor_{{$vendor_product_type->id}}').each(function () {
            CKEDITOR.replace($(this).prop('id'));
        });
        @endforeach



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
    });
</script>

@endsection
