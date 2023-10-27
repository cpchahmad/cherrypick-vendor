@extends('layouts.superadmin')

@section('main')
    <main id="main" class="main">
        <div class="home-flex">
            <div class="pagetitle">
                <h1>Stores</h1>
            </div><!-- End Page Title -->
{{--            <a class="btn btn-primary" href="{{url('superadmin/updateprice')}}">Update Product Prices</a>--}}
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

                                    <th scope="col">Store Detail</th>
                                    <th scope="col">Total Products/Total Variants</th>
                                    <th scope="col">Pending Products/Pending Variants</th>
                                    <th scope="col">Changes Pending Products/Changes Pending Variants</th>
                                    <th scope="col">Approved Products/Approved Variants</th>
                                    <th scope="col">Deny Products/Deny Variants</th>
                                    <th scope="col">Shopify Pushed Products/Variants</th>
                                    <th scope="col">Shopify Pending Products/Variants</th>
                                    <th scope="col">Shopify In-Progress Products/Variants</th>
                                    <th scope="col">Shopify Failed Products/Variants</th>
                                    <th scope="col">Store Discount</th>
                                    <th scope="col">Premium Status</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $i=1; ?>
                                @foreach($vendorlist as $vendor)


                                    @php
                                    $total_products=\App\Models\Product::where('vendor',$vendor->id)->count();
                                   $total_variants=\App\Models\ProductInfo::where('vendor_id',$vendor->id)->count();
                                    $pending_products_ids=\App\Models\Product::where('vendor',$vendor->id)->where('status',0)->pluck('id');
                                    $pending_products=count($pending_products_ids);
                                    $total_pending_variants=\App\Models\ProductInfo::whereIn('product_id',$pending_products_ids)->count();


                                    $changes_pending_products_ids=\App\Models\Product::where('vendor',$vendor->id)->where('status',2)->pluck('id');
                                    $changes_pending_products=count($changes_pending_products_ids);
                                    $total_changes_pending_variants=\App\Models\ProductInfo::whereIn('product_id',$changes_pending_products_ids)->count();


                                    $approved_products_ids=\App\Models\Product::where('vendor',$vendor->id)->where('status',1)->pluck('id');
                                    $approved_products=count($approved_products_ids);
                                    $total_approved_variants=\App\Models\ProductInfo::whereIn('product_id',$approved_products_ids)->count();



                                    $deny_products_ids=\App\Models\Product::where('vendor',$vendor->id)->where('status',3)->pluck('id');
                                    $deny_products=count($deny_products_ids);
                                     $total_deny_variants=\App\Models\ProductInfo::whereIn('product_id',$deny_products_ids)->count();


                                    $shopify_pushed_products_ids=\App\Models\Product::where('vendor',$vendor->id)->where('shopify_status','Complete')->pluck('id');
                                    $shopify_pushed_products=count($shopify_pushed_products_ids);
                                    $total_shopify_pushed_variants=\App\Models\ProductInfo::whereIn('product_id',$shopify_pushed_products_ids)->count();


                                    $shopify_pending_products_ids=\App\Models\Product::where('vendor',$vendor->id)->where('shopify_status','Pending')->pluck('id');
                                    $shopify_pending_products=count($shopify_pushed_products_ids);
                                    $total_shopify_pending_variants=\App\Models\ProductInfo::whereIn('product_id',$shopify_pending_products_ids)->count();


                                    $shopify_inprogress_products_ids=\App\Models\Product::where('vendor',$vendor->id)->where('shopify_status','In-Progress')->pluck('id');
                                    $shopify_inprogress_products=count($shopify_inprogress_products_ids);
                                     $total_shopify_inprogress_variants=\App\Models\ProductInfo::whereIn('product_id',$shopify_inprogress_products_ids)->count();




                                    $shopify_failed_products_ids=\App\Models\Product::where('vendor',$vendor->id)->where('shopify_status','Failed')->pluck('id');
                                    $shopify_failed_products=count($shopify_failed_products_ids);
                                     $total_shopify_failed_variants=\App\Models\ProductInfo::whereIn('product_id',$shopify_failed_products_ids)->count();



                                    @endphp
                                    <tr>

                                        <td>
                                            <span><a style="font-weight: 600;" href="{{route('vendor.setting',$vendor->id)}}">{{$vendor->name}}</a></span>
                                            <span>{{$vendor->email}}</span>
                                        </td>

                                        <td>{{$total_products}}/{{$total_variants}}</td>
                                        <td>{{$pending_products}}/{{$total_pending_variants}}</td>
                                        <td>{{$changes_pending_products}}/{{$total_changes_pending_variants}}</td>
                                        <td>{{$approved_products}}/{{$total_approved_variants}}</td>
                                        <td>{{$deny_products}}/{{$total_deny_variants}}</td>
                                        <td>{{$shopify_pushed_products}}/{{$total_shopify_pushed_variants}}</td>
                                        <td>{{$shopify_pending_products}}/{{$total_shopify_pending_variants}}</td>
                                        <td>{{$shopify_inprogress_products}}/{{$total_shopify_inprogress_variants}}</td>
                                        <td>{{$shopify_failed_products}}/{{$total_shopify_failed_variants}}</td>
                                        <td><input type="text" class="float-number" id="dis_{{$vendor->id}}" value="{{$vendor->vendor_discount}}"><button onclick="updateDiscount({{$vendor->id}})">Save</button></td>
                                        <td class="icon-action">
                  <span class="form-switch">
                      <input class="form-check-input" type="checkbox" id="premium_{{$vendor->id}}" onclick="premiumStatus({{$vendor->id}})" @if($vendor->premium=='1') {{'checked'}} @endif>
                     </span>
                                        </td>
                                        <td><span class="@if($vendor->status=='Active') {{'en-recovered'}} @else {{'en-dismissed'}} @endif"></span> {{$vendor->status}}</td>
                                        <td class="icon-action">
                                            <a href="{{route('vendor.setting',$vendor->id)}}"><i class="bi bi-eye"></i></a>
                                            <span class="form-switch">
                      <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault_{{$vendor->id}}" onclick="changeStoreStatus({{$vendor->id}})" @if($vendor->status=='Active') {{'checked'}} @endif>
                     </span>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- End Bordered Table -->
                    </div>
                </div>
            </div>
        </section>
    </main>
    <!-- End #main -->
    <!-- ======= Footer ======= -->
@endsection
@section('js')
    <script type="text/javascript">
        jQuery(document).ready(function() {
            $('.float-number').keypress(function(event) {
                if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                    event.preventDefault();
                }
            });
        });
    </script>
@stop
<script>
    function premiumStatus(id)
    {
        var v_token = "{{csrf_token()}}";
        $.ajax({
            type:'post',
            data:{id : id},
            headers: {'X-CSRF-Token': v_token},
            url:"{{ route('superadmin.change-premium-status') }}",
            success:function(response){
                var json = $.parseJSON(response);
                if(json.status=='success')
                {
                    // alert("Premium Status Updated Successfully!!")
                    toastr.success("Premium Status Updated Successfully!!");
                    window.location.href="vendors";
                }
            }
        });
    }
    function changeStoreStatus(id)
    {
        var v_token = "{{csrf_token()}}";
        $.ajax({
            type:'post',
            data:{id : id},
            headers: {'X-CSRF-Token': v_token},
            url:"{{ route('superadmin.change-vendor-status') }}",
            success:function(response){
                var json = $.parseJSON(response);
                if(json.status=='success')
                {
                    toastr.success("Status Updated Successfully!!");
                    // alert("Status Updated Successfully!!")
                    window.location.href="vendors";
                }
            }
        });
    }
    function updateDiscount(id)
    {
        var dis_val=$('#dis_'+id).val();
        var v_token = "{{csrf_token()}}";
        if(dis_val!='')
        {
            $.ajax({
                type:'post',
                data:{id : id, discount : dis_val},
                headers: {'X-CSRF-Token': v_token},
                url:"{{ route('superadmin.change-vendor-discount') }}",
                success:function(response){
                    var json = $.parseJSON(response);
                    if(json.status=='success')
                    {
                        toastr.success("Discount Updated Successfully!!");
                        // alert("Discount Updated Successfully!!")
                    }
                }
            });
        }
    }

</script>
