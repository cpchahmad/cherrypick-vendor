@extends('layouts.superadmin')

<style>
    .table-responsive {
        overflow-x: auto;
    }

    .table-bordered th,
    .table-bordered td {
        border: 1px solid #dee2e6;
    }

    .table-responsive thead th:first-child,
    .table-responsive tbody td:first-child {
        position: sticky;
        left: 0;
        background-color: #fff; /* Adjust background color as needed */
        z-index: 2;
    }

    .table-responsive thead th:first-child::after,
    .table-responsive tbody td:first-child::after {
        content: '\00a0'; /* Add a non-breaking space to ensure content is visible */
    }
</style>


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

                                    <th scope="col" style="width: 200px; background-color: #E0E0E0;">Store Detail</th>
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
                                @foreach($vendorData as $vendor)


                                    <tr>

                                        <td>
                                            <span><a style="font-weight: 600;" href="{{route('vendor.setting',$vendor->id)}}">{{$vendor->name}}</a></span>
                                            <br>
                                            <span>{{$vendor->email}}</span>
                                        </td>


                                        {{-- <td>{{$vendor->total_products}}/{{$vendor->total_variants}}</td>
                                        <td>{{$vendor->pending_products}}/{{$vendor->total_pending_products_variants}}</td>
                                        <td>{{$vendor->changes_pending_products}}/{{$vendor->total_changes_pending_products_variants}}</td>
                                        <td>{{$vendor->approved_products}}/{{$vendor->total_approved_products_variants}}</td>
                                        <td>{{$vendor->deny_products}}/{{$vendor->total_deny_products_variants}}</td>
                                        <td>{{$vendor->shopify_pushed_products}}/{{$vendor->total_shopify_pushed_products_variants}}</td>
                                        <td>{{$vendor->shopify_pending_products}}/{{$vendor->total_shopify_pending_products_variants}}</td>
                                        <td>{{$vendor->shopify_inprogress_products}}/{{$vendor->total_shopify_inprogress_products_variants}}</td>
                                        <td>{{$vendor->shopify_failed_products}}/{{$vendor->total_shopify_failed_products_variants}}</td> --}}


                                        <td>{{$vendor->total_products}}</td>
                                        <td>{{$vendor->pending_products}}</td>
                                        <td>{{$vendor->changes_pending_products}}</td>
                                        <td>{{$vendor->approved_products}}</td>
                                        <td>{{$vendor->deny_products}}</td>
                                        <td>{{$vendor->shopify_pushed_products}}</td>
                                        <td>{{$vendor->shopify_pending_products}}</td>
                                        <td>{{$vendor->shopify_inprogress_products}}</td>
                                        <td>{{$vendor->shopify_failed_products}}</td>


                                        <td><input type="text" class="float-number" id="dis_{{$vendor->id}}" value="{{$vendor->vendor_discount}}"><button onclick="updateDiscount({{$vendor->id}})">Save</button></td>
                                        <td class="icon-action">
                  <span class="form-switch">
                      <input class="form-check-input" type="checkbox" id="premium_{{$vendor->id}}" onclick="premiumStatus({{$vendor->id}})" @if($vendor->premium=='1') {{'checked'}} @endif>
                     </span>
                                        </td>
                                        <td><span class="@if($vendor->status=='Active') {{'en-recovered'}} @else {{'en-dismissed'}} @endif"></span> {{$vendor->status}}</td>

                                        @php
                                            $json_url=\Illuminate\Support\Facades\DB::table('cron_json_url')->where('vendor_id',$vendor->id)->first();
                                        @endphp
                                        <td class="icon-action">
                                            @if($json_url)
                                                <a href="{{route('sync.from.api',$vendor->id)}}" style="font-size: 10px;color:white;" class="btn btn-primary btn-sm">Sync from Api</a>
                                            @endif
                                            <a  href="{{route('vendor.setting',$vendor->id)}}"><i class="bi bi-eye"></i></a>
                                            <span class="form-switch">
                      <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault_{{$vendor->id}}" onclick="changeStoreStatus({{$vendor->id}})" @if($vendor->status=='Active') {{'checked'}} @endif>
                     </span>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <nav class="mainpg timer-nav">
                                {{ $vendors->links( "pagination::bootstrap-4") }}
                            </nav>
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
