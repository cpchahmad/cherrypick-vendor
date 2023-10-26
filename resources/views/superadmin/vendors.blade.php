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
                                    <th scope="col">Total Products</th>
                                    <th scope="col">Pending Products</th>
                                    <th scope="col">Changes Pending Products</th>
                                    <th scope="col">Approved Products</th>
                                    <th scope="col">Deny Products</th>
                                    <th scope="col">Shopify Pushed Products</th>
                                    <th scope="col">Shopify Pending Products</th>
                                    <th scope="col">Shopify In-Progress Products</th>
                                    <th scope="col">Shopify Failed Products</th>
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
                                    $pending_products=\App\Models\Product::where('vendor',$vendor->id)->where('status',0)->count();
                                    $changes_pending_products=\App\Models\Product::where('vendor',$vendor->id)->where('status',2)->count();
                                    $approved_products=\App\Models\Product::where('vendor',$vendor->id)->where('status',1)->count();
                                    $deny_products=\App\Models\Product::where('vendor',$vendor->id)->where('status',3)->count();
                                    $shopify_pushed_products=\App\Models\Product::where('vendor',$vendor->id)->where('shopify_status','Complete')->count();
                                    $shopify_pending_products=\App\Models\Product::where('vendor',$vendor->id)->where('shopify_status','Pending')->count();
                                    $shopify_inprogress_products=\App\Models\Product::where('vendor',$vendor->id)->where('shopify_status','In-Progress')->count();
                                    $shopify_failed_products=\App\Models\Product::where('vendor',$vendor->id)->where('shopify_status','Failed')->count();
                                    @endphp
                                    <tr>

                                        <td>
                                            <span><a style="font-weight: 600;" href="{{route('vendor.setting',$vendor->id)}}">{{$vendor->name}}</a></span>
                                            <span>{{$vendor->email}}</span>
                                        </td>

                                        <td>{{$total_products}}</td>
                                        <td>{{$pending_products}}</td>
                                        <td>{{$changes_pending_products}}</td>
                                        <td>{{$approved_products}}</td>
                                        <td>{{$deny_products}}</td>
                                        <td>{{$shopify_pushed_products}}</td>
                                        <td>{{$shopify_pending_products}}</td>
                                        <td>{{$shopify_inprogress_products}}</td>
                                        <td>{{$shopify_failed_products}}</td>
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
