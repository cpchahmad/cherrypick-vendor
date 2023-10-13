@extends('layouts.superadmin')

@section('main')
    <main id="main" class="main">
        <div class="home-flex">
            <div class="pagetitle">
                <h1>Vendors</h1>
            </div><!-- End Page Title -->
            <a class="btn btn-primary" href="{{url('superadmin/updateprice')}}">Update Product Prices</a>
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
                                    <th scope="col">Vendor Name</th>
                                    <th scope="col">Vendor Email</th>
                                    <th scope="col">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $i=1; ?>
                                @foreach($vendorlist as $vendor)
                                    <tr>
                                        <td>{{$i++}}</td>
                                        <td>{{$vendor->name}}</td>
                                        <td>{{$vendor->email}}</td>
                                        <td class="icon-action">
                                            <a href="{{route('vendor.setting',$vendor->id)}}"><i class="bi bi-eye"></i></a>
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
                    alert("Premium Status Updated Successfully!!")
                    window.location.href="store-configuration";
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
                    alert("Status Updated Successfully!!")
                    window.location.href="store-configuration";
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
                        alert("Discount Updated Successfully!!")
                    }
                }
            });
        }
    }

</script>
