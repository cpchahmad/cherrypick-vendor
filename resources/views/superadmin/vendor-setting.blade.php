@extends('layouts.superadmin')

<style>
    .form-select{
        width: auto !important;
    }
</style>
@section('main')
    <main id="main" class="main">
        <div class="subpagetitle fit-title">
            <h1>{{$vendor->name}}</h1>
            <p><a href="{{url('vendors')}}">Vendor</a> / <b>Vendor Setting</b></p>
        </div>
        <section class="section up-banner">
            <div class="row">
                <div class="col-12 ">
                    <div class="card fullorders">
                        <ul class="nav nav-tabs" data-bs-toggle="tabs">
                            <li class="nav-item">
                                <a href="#tabs-home-7" class="nav-link active" data-bs-toggle="tab">Bulk Price Update</a>
                            </li>
                            <li class="nav-item">
                                <a href="#tabs-profile-7" class="nav-link" data-bs-toggle="tab">HSN and Weight</a>
                            </li>
                            <li class="nav-item">
                                <a href="#tabs-profile-8" class="nav-link" data-bs-toggle="tab">Setting</a>
                            </li>


                        </ul>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane active show" id="tabs-home-7">
                                    @if(count($vendor_product_types) > 0)
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
                                                <th scope="col">S.No</th>
                                                <th scope="col">Product Type</th>
                                                <th scope="col">HSN Code</th>
                                                <th scope="col">Base Weight</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $i=1; ?>

                                            @foreach($vendor_product_types as $vendor_product_type)
                                                <tr>
                                                    <td>{{$i++}}</td>
                                                    <td>{{$vendor_product_type->product_type}}</td>

                                                    <td><input type="text" class="float-number" id="hsn_{{$vendor_product_type->id}}" value="{{$vendor_product_type->hsn_code}}"></td>
                                                    <td><input type="text" class="float-number" id="weight_{{$vendor_product_type->id}}" value="{{$vendor_product_type->base_weight}}"></td>
                                                    <td><button class="btn btn-primary" onclick="updateRecord({{$vendor_product_type->id}})">Save</button></td>
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
                                    <form class="add-product-form" method="post" action="{{url('superadmin/vendor-setting')}}" >
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

        if(status_val && type_val && value) {
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
        }
    }
</script>


