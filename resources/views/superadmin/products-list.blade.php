@extends('layouts.superadmin')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    .filters_div{
        justify-content: unset !important;

    }
    .sort-by{
        margin-bottom: unset !important;
    }
    span.select2.select2-container.select2-container--default {

        padding: 5px;
        background-color: #fff;
        border: 1px solid #dadcde;
    }
    .filter_btn{
        font-size:11px !important;
    }
</style>
@section('main')
  <main id="main" class="main">

    <div class="subpagetitle fit-title">

        @php

        @endphp
        <div class="row">
            <div class="col-6">
                <h1>Product List</h1>
                <div class="row">
                    <div class="col-6">
                        <p style="margin-bottom: 3px;">Total Products: ({{$total_products}})</p>
{{--                        <p>Total Variants: ({{$total_variants}})--}}

                    </div>
{{--              <div class="col-6">--}}
{{--                  <p style="margin-bottom: 3px;">In-Stock: ({{$total_products_in_stock}}/{{$total_variants_in_stock}})</p>--}}
{{--                <p style="margin-bottom: 3px;">Out-of-Stock: ({{$total_products_out_of_stock}}/{{$total_variants_out_of_stock}})</p>--}}
{{--              </div>--}}
                </div>
            </div>
            <div class="col-6" style="text-align: right;margin-bottom: 6px;">
                <button class="btn btn-success btn-sm approve_all submit_loader" href="">Approve All</button>
                <button class="btn btn-danger btn-sm deny_all" href="">Deny All</button>
            </div>

        </div>

    </div><!-- End Page Title -->




    <section class="section up-banner">
      <p><strong>Search and filter by products, vendor and date.</strong></p>
      <div class="sort-by">

        <div class="member-plan-search header onetime-search">
          <div class="search-bar">

              <form class="search-form d-flex align-items-center" method="get" action="">
                <input type="text" name="search" id='search' value='{{Request::get('search')}}' placeholder="Search products" title="Enter search keyword">
                <button type="button" title="Search" onclick="filterByName()"><i class="bi bi-search"></i></button>
              </form>
            </div>
         </div>


         <div class="label-area sort-area mx-2">
            <select class="form-select" aria-label="Default select example" onchange='filterByVendor(this.value)'>
              <option value='' selected="">Select Vendor</option>
              @foreach($vendorlist as $ven)
              <option value="{{$ven->id}}" {{Request::get('vendor') == $ven->id  ? 'selected' : ''}}>{{$ven->name}}</option>
              @endforeach
            </select>
         </div>

          <div class="label-area sort-area mx-2">
              <select class="form-select" aria-label="Default select example" onchange='filterByStatus(this.value)'>
                  <option value=''  selected="">Select App Status</option>
                  <option value="0" {{ Request::get('status') == "0" ? 'selected' : '' }}>Pending</option>
                  <option value="1" {{ Request::get('status') == "1" ? 'selected' : '' }}>Approved</option>
                  <option value="2" {{ Request::get('status') == "2" ? 'selected' : '' }}>Changes Pending</option>
                  <option value="3" {{ Request::get('status') == "3" ? 'selected' : '' }}>Deny</option>

              </select>
          </div>

          <div class="label-area sort-area mx-2">
              <select class="form-select" aria-label="Default select example" onchange='filterByShopifyStatus(this.value)'>
                  <option value=''  selected="">Select Shopify Status</option>
                  <option value="Pending" {{ Request::get('shopify_status') == "Pending" ? 'selected' : '' }}>Pending</option>
                  <option value="Complete" {{ Request::get('shopify_status') == "Complete" ? 'selected' : '' }}>Completed</option>
                  <option value="In-Progress" {{ Request::get('shopify_status') == "In-Progress" ? 'selected' : '' }}>In-Progress</option>
                  <option value="Failed" {{ Request::get('shopify_status') == "Failed" ? 'selected' : '' }}>Failed</option>

              </select>
          </div>

        <!--<div class="create-plan">
          <a href="#">Export Products</a>
        </div> -->
      </div>
      <div class="sort-by filters_div">


          <div class="sale-date mt-4 mx-2">
              <div class="input-group">
                  <input type="text" class="datepicker_input form-control datepicker-input" id="fil_date" placeholder="@if(Request::get('date')!='') {{Request::get('date')}} @else {{'Select Date'}} @endif" onblur='filterByDate(this.value)'  aria-label="Date and Month">
                  <i class="bi bi-calendar4 input-group-text"></i>
              </div>
          </div>


          <div class="label-area sort-area mx-2">
              @php

              $product_type=Request::get('product_type');
               $selectedProductTypes =explode(',',$product_type);
              @endphp

              <label>Select Product Type</label>
              <select class="js-example-basic-multiple form-control" onchange="filterByProductType(this)" multiple="multiple" name="product_type[]" >
                  <option value="" ></option>
                  @foreach($product_types as $type)
                      <option value="{{ $type->id }}" @if(in_array($type->id, $selectedProductTypes)) selected @endif>
                          {{ $type->product_type }}
                      </option>
                  @endforeach
              </select>
          </div>



          <div class="label-area mt-4 sort-area mx-2">
              <select class="form-select" aria-label="Default select example" onchange='filterByStock(this.value)'>
                  <option value=''  selected="">Select Stock Type</option>
                  <option value="in-stock" {{ Request::get('stock') == "in-stock" ? 'selected' : '' }}>In-Stock</option>
                  <option value="out-of-stock" {{ Request::get('stock') == "out-of-stock" ? 'selected' : '' }}>Out-of-Stock</option>
              </select>
          </div>


          <div class="label-area mt-4 sort-area mx-2" style="width: unset">


              <div class="dropdown">
                  <!-- Dropdown Button -->
                  <button class="btn btn-primary dropdown-toggle" data-filter="by_price" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    By Price
                  </button>

                  <!-- Dropdown Menu -->
                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                      <!-- Input Field within Dropdown -->
                      <div class="dropdown-item">
                          <input type="text" class="minimum_field_price form-control"  name="minimum_value_price" id='search' value='{{Request::get('minimum_value_price')}}' placeholder="Minimum" title="Enter search keyword">
                      </div>
                      <div class="dropdown-item">
                 <input type="text" class="maximum_field_price form-control"  name="maximum_value_price" id='search' value='{{Request::get('maximum_value_price')}}' placeholder="Maximum" title="Enter search keyword">
                      </div>
                      <div class="dropdown-item" style="text-align: center">
                      <button class="btn btn-primary btn-sm filter_btn by_price_search ">Search</button>
                      </div>
                  </div>
              </div>


{{--              <select class="form-select filter_weight_price" name="filter" aria-label="Default select example" >--}}
{{--                  <option value=''  selected="">Select Filter</option>--}}
{{--                  <option value="by_price" {{ Request::get('filter') == "by_price" ? 'selected' : '' }}>By Price</option>--}}
{{--                  <option value="by_weight" {{ Request::get('filter') == "by_weight" ? 'selected' : '' }}>By Weight</option>--}}
{{--              </select>--}}
          </div>

          <div class="label-area mt-4 sort-area mx-2" style="width: unset">


              <div class="dropdown">
                  <!-- Dropdown Button -->
                  <button class="btn btn-primary dropdown-toggle" data-filter="by_weight" type="button" id="dropdownMenuButton_weight" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      By Weight
                  </button>

                  <!-- Dropdown Menu -->
                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton_weight">
                      <!-- Input Field within Dropdown -->
                      <div class="dropdown-item">
                          <input type="text" class="minimum_field_weight form-control"  name="minimum_value_weight" id='search' value='{{Request::get('minimum_value_weight')}}' placeholder="Minimum" title="Enter search keyword">
                      </div>
                      <div class="dropdown-item">
                          <input type="text" class="maximum_field_weight form-control"  name="maximum_value_weight" id='search' value='{{Request::get('maximum_value_weight')}}' placeholder="Maximum" title="Enter search keyword">
                      </div>
                      <div class="dropdown-item" style="text-align: center">
                          <button class="btn btn-primary btn-sm filter_btn by_weight_search ">Search</button>
                      </div>
                  </div>
              </div>

          </div>





          {{--          <div class="label-area sort-area mx-2">--}}
{{--              @php--}}

{{--                  $product_tags=Request::get('tags');--}}
{{--                   $selectedProductTags =explode(',',$product_tags);--}}
{{--              @endphp--}}

{{--              <select class="js-example-basic-multiple1 form-control" onchange="filterByProductTags(this)" multiple="multiple" name="tags[]" >--}}
{{--                  <option value=""  ></option>--}}
{{--                  @foreach($tags as $tag)--}}
{{--                      <option value="{{ $tag }}"  @if(in_array($tag, $selectedProductTags)) selected @endif >--}}
{{--                          {{ $tag }}--}}
{{--                      </option>--}}
{{--                  @endforeach--}}
{{--              </select>--}}
{{--          </div>--}}


      </div>

{{--        <div class="sort-by filters_div weight_price_div" style="@if(Request::get('filter') == "by_price" || Request::get('filter') == "by_weight" ) display: flex @else display:none @endif">--}}
{{--            <div class="label-area sort-area mx-2">--}}
{{--            </div>--}}
{{--            <div class="label-area sort-area mx-2 ">--}}
{{--            </div>--}}
{{--            <div class="label-area sort-area mx-2">--}}
{{--            </div>--}}
{{--            <div class="member-plan-search header onetime-search">--}}
{{--                <div class="search-bar mt-4" style="min-width: unset;width: unset">--}}

{{--                    <form class="search-form d-flex align-items-center" method="get" action="">--}}
{{--                        <input type="text" class="minimum_field"  name="minimum_value" id='search' value='{{Request::get('minimum_value')}}' placeholder="Minimum" title="Enter search keyword">--}}
{{--                    </form>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="member-plan-search header onetime-search">--}}
{{--                <div class="search-bar mt-4" style="min-width: unset;width: unset">--}}

{{--                    <form class="search-form d-flex align-items-center" method="get" action="">--}}
{{--                        <input type="text" class="maximum_field"  name="maximum_value" id='search' value='{{Request::get('maximum_value')}}' placeholder="Maximum" title="Enter search keyword">--}}
{{--                    </form>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
        <form class="add-product-form mt-4">
                <div class="card table-card">
                  <div class="table-responsive">
                    <table class="table table-borderless view-productd">
                        <thead>
                          <tr>
                            <th scope="col" class="fl-input"><input class="form-check-input" type="checkbox" id="gridCheck" onchange="checkAll(this)"></th>
                            <th scope="col">Preview</th>
                            <th scope="col">Product</th>
                            <th scope="col">Date</th>
                            <th scope="col">Vendor Name</th>
                            <th scope="col">App Status</th>
                            <th scope="col">Shopify Status</th>
                            <th scope="col">Action</th>
                          </tr>
                        </thead>
                        <tbody>
                         @php $i=0; @endphp
                         @foreach($data as $row)
                         @php
                            $i++;
                            $image=\App\Models\ProductImages::where(['product_id' => $row->id])->pluck('image')->first();
                         @endphp
                          <tr>
                              <td><input class="form-check-input chk" type="checkbox"  name='products[]' value="{{$row->id}}"></td>
                            <th scope="row"><a href="#"><img src="{{$image}}" alt=""></a></th>
                            <td><a href="{{url('superadmin/products-details')}}/{{$row->id}}" class="text-primary fw-bold">{{$row->title}}</a></td>
                            <td>{{date('d-m-Y',strtotime($row->created_at))}}</td>
                            @php
                                $info_query=\App\Models\Store::where(['id' => $row->vendor])->pluck('name')->first();
                            @endphp
                            <td>{{ $info_query }}</td>
                            <td>@if($row->status==1) <span class="en-recovered"></span> Approved @elseif($row->status=='2') <span class="en-in-progress"></span>{{'Changes Pending'}} @elseif($row->status=='3') <span class="en-dismissed"></span>{{'Deny'}} @else <span class="en-dismissed"></span> Pending @endif</td>
                            <td>@if($row->shopify_status=='Complete') <span class="en-recovered"></span> Completed @elseif($row->shopify_status=='In-Progress') <span class="en-in-progress"></span>{{'In-Progress'}} @elseif($row->shopify_status=='Failed') <span class="en-dismissed"></span>{{'Failed'}} @else <span class="en-dismissed"></span> Pending @endif</td>


                            <!--<td><span class="form-switch">
                                    <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault_{{$row->id}}" onclick="approveProduct({{$row->id}})" checked="">
                               </span>
                            </td>-->

                            <td>
							<a class="btn btn-success btn-sm" href="{{url('superadmin/shopify-create')}}/{{$row->id}}">Approve</a>
							<a class="btn btn-danger btn-sm" href="{{url('superadmin/reject-product')}}/{{$row->id}}">Deny</a>
							<a class="btn btn-warning btn-sm" href="{{url('superadmin/products-details')}}/{{$row->id}}">View</a>
                            </td>
                          </tr>
                          @endforeach
                          @if($i!=0)
                          <tr>
                              <td colspan="8"><a class="btn btn-success btn-sm" href="javascript:void(0)" onclick="approveMultiple()">Approve</a>&nbsp;&nbsp;&nbsp;<a class="btn btn-danger btn-sm" onclick="rejectMultiple()" href="javascript:void(0)">Deny</a></td>
                          </tr>
                          @endif
                        </tbody>
                      </table>
                    </div>
                </div>
        </form>
      <nav class="mainpg timer-nav">
             {{ $data->links( "pagination::bootstrap-4") }}
        </nav>
    </section>
   </main>
@endsection

 <script>

     function approveProduct(id)
     {
         window.location.href='shopify-create/'+id;
     }
     function filterByVendor(id)
     {
          var search='{{Request::get('search')}}';
          var date='{{Request::get('date')}}';
         var status='{{Request::get('status')}}';
         var shopify_status='{{Request::get('shopify_status')}}';
         var productTypeSelect = document.querySelector('.js-example-basic-multiple');
         var selectedOptions = Array.from(productTypeSelect.selectedOptions).map(option => option.value);
         var productTypeParam = selectedOptions.join(',');
         var stock='{{Request::get('stock')}}';

         var selectedFilter = $('#dropdownMenuButton').data('filter');
         var selectedFilter_weight = $('#dropdownMenuButton_weight').data('filter');

         var minimum_value=$('.minimum_field_price').val();
         var maximum_value=$('.maximum_field_price').val();

         var maximum_value_weight=$('.maximum_field_weight').val();
         var minimum_value_weight=$('.minimum_field_weight').val();



          window.location.href='products?search='+search+'&vendor='+id+'&product_type=' + productTypeParam+'&date='+date+'&status='+status+'&shopify_status='+shopify_status+'&stock='+stock+'&filter_price='+selectedFilter+'&minimum_value_price='+minimum_value+'&maximum_value_price='+maximum_value+'&filter_weight='+selectedFilter_weight+'&minimum_value_weight='+minimum_value_weight+'&maximum_value_weight='+maximum_value_weight;
     }

     function filterByStatus(id)
     {

         var search='{{Request::get('search')}}';
         var vendor='{{Request::get('vendor')}}';
         var date='{{Request::get('date')}}';
         var shopify_status='{{Request::get('shopify_status')}}';
         var productTypeSelect = document.querySelector('.js-example-basic-multiple');
         var selectedOptions = Array.from(productTypeSelect.selectedOptions).map(option => option.value);
         var productTypeParam = selectedOptions.join(',');
         var stock='{{Request::get('stock')}}';

         var selectedFilter = $('#dropdownMenuButton').data('filter');
         var selectedFilter_weight = $('#dropdownMenuButton_weight').data('filter');

         var minimum_value=$('.minimum_field_price').val();
         var maximum_value=$('.maximum_field_price').val();

         var maximum_value_weight=$('.maximum_field_weight').val();
         var minimum_value_weight=$('.minimum_field_weight').val();

         window.location.href='products?search='+search+'&vendor='+vendor+'&product_type=' + productTypeParam+'&date='+date+'&status='+id+'&shopify_status='+shopify_status+'&stock='+stock+'&filter_price='+selectedFilter+'&minimum_value_price='+minimum_value+'&maximum_value_price='+maximum_value+'&filter_weight='+selectedFilter_weight+'&minimum_value_weight='+minimum_value_weight+'&maximum_value_weight='+maximum_value_weight;
     }

     function filterByShopifyStatus(val)
     {

         var search='{{Request::get('search')}}';
         var vendor='{{Request::get('vendor')}}';
         var date='{{Request::get('date')}}';
         var status='{{Request::get('status')}}';
         var productTypeSelect = document.querySelector('.js-example-basic-multiple');
         var selectedOptions = Array.from(productTypeSelect.selectedOptions).map(option => option.value);
         var productTypeParam = selectedOptions.join(',');
         var stock='{{Request::get('stock')}}';


         var selectedFilter = $('#dropdownMenuButton').data('filter');
         var selectedFilter_weight = $('#dropdownMenuButton_weight').data('filter');

         var minimum_value=$('.minimum_field_price').val();
         var maximum_value=$('.maximum_field_price').val();

         var maximum_value_weight=$('.maximum_field_weight').val();
         var minimum_value_weight=$('.minimum_field_weight').val();

         window.location.href='products?search='+search+'&vendor='+vendor+'&product_type=' + productTypeParam+'&date='+date+'&status='+status+'&shopify_status='+val+'&stock='+stock+'&filter_price='+selectedFilter+'&minimum_value_price='+minimum_value+'&maximum_value_price='+maximum_value+'&filter_weight='+selectedFilter_weight+'&minimum_value_weight='+minimum_value_weight+'&maximum_value_weight='+maximum_value_weight;
     }

     function filterByName(val)
     {
         var search=$('#search').val();
         if(search!='')
         {
             var productTypeSelect = document.querySelector('.js-example-basic-multiple');
             var selectedOptions = Array.from(productTypeSelect.selectedOptions).map(option => option.value);
             var productTypeParam = selectedOptions.join(',');
             var vendor='{{Request::get('vendor')}}';
             var date='{{Request::get('date')}}';
             var status='{{Request::get('status')}}';
             var shopify_status='{{Request::get('shopify_status')}}';
             var stock='{{Request::get('stock')}}';

             var selectedFilter = $('#dropdownMenuButton').data('filter');
             var selectedFilter_weight = $('#dropdownMenuButton_weight').data('filter');

             var minimum_value=$('.minimum_field_price').val();
             var maximum_value=$('.maximum_field_price').val();

             var maximum_value_weight=$('.maximum_field_weight').val();
             var minimum_value_weight=$('.minimum_field_weight').val();

             window.location.href='products?search='+search+'&vendor='+vendor+'&product_type=' + productTypeParam+'&date='+date+'&status='+status+'&shopify_status='+shopify_status+'&stock='+stock+'&filter_price='+selectedFilter+'&minimum_value_price='+minimum_value+'&maximum_value_price='+maximum_value+'&filter_weight='+selectedFilter_weight+'&minimum_value_weight='+minimum_value_weight+'&maximum_value_weight='+maximum_value_weight;
         }
     }
     function filterByDate(val)
     {
         if(val!='')
         {
             var productTypeSelect = document.querySelector('.js-example-basic-multiple');
             var selectedOptions = Array.from(productTypeSelect.selectedOptions).map(option => option.value);
             var productTypeParam = selectedOptions.join(',');
             var search='{{Request::get('search')}}';
             var vendor='{{Request::get('vendor')}}';
             var status='{{Request::get('status')}}';
             var shopify_status='{{Request::get('shopify_status')}}';
             var stock='{{Request::get('stock')}}';

             var selectedFilter = $('#dropdownMenuButton').data('filter');
             var selectedFilter_weight = $('#dropdownMenuButton_weight').data('filter');

             var minimum_value=$('.minimum_field_price').val();
             var maximum_value=$('.maximum_field_price').val();

             var maximum_value_weight=$('.maximum_field_weight').val();
             var minimum_value_weight=$('.minimum_field_weight').val();

             window.location.href='products?search='+search+'&vendor='+vendor+'&product_type=' + productTypeParam+'&date='+val+'&status='+status+'&shopify_status='+shopify_status+'&stock='+stock+'&filter_price='+selectedFilter+'&minimum_value_price='+minimum_value+'&maximum_value_price='+maximum_value+'&filter_weight='+selectedFilter_weight+'&minimum_value_weight='+minimum_value_weight+'&maximum_value_weight='+maximum_value_weight;
         }
     }

     function filterByProductType(val)
     {
         var productTypeSelect = document.querySelector('.js-example-basic-multiple');
         var selectedOptions = Array.from(productTypeSelect.selectedOptions).map(option => option.value);
         var productTypeParam = selectedOptions.join(',');


         var search='{{Request::get('search')}}';
         var date='{{Request::get('date')}}';
         var status='{{Request::get('status')}}';
         var shopify_status='{{Request::get('shopify_status')}}';
         var vendor='{{Request::get('vendor')}}';
         var stock='{{Request::get('stock')}}';

         var selectedFilter = $('#dropdownMenuButton').data('filter');
         var selectedFilter_weight = $('#dropdownMenuButton_weight').data('filter');

         var minimum_value=$('.minimum_field_price').val();
         var maximum_value=$('.maximum_field_price').val();

         var maximum_value_weight=$('.maximum_field_weight').val();
         var minimum_value_weight=$('.minimum_field_weight').val();

         window.location.href = 'products?search=' + search + '&vendor='+vendor+ '&product_type=' + productTypeParam + '&date=' + date + '&status=' + status+'&shopify_status='+shopify_status+'&stock='+stock+'&filter_price='+selectedFilter+'&minimum_value_price='+minimum_value+'&maximum_value_price='+maximum_value+'&filter_weight='+selectedFilter_weight+'&minimum_value_weight='+minimum_value_weight+'&maximum_value_weight='+maximum_value_weight;
     }


     function filterByProductTags(val)
     {
         var productTypeSelect = document.querySelector('.js-example-basic-multiple');
         var selectedOptions = Array.from(productTypeSelect.selectedOptions).map(option => option.value);
         var productTypeParam = selectedOptions.join(',');

         var productTagSelect = document.querySelector('.js-example-basic-multiple1');
         var selectedOptions_tags = Array.from(productTagSelect.selectedOptions).map(option => option.value);
         var productTagParam = selectedOptions_tags.join(',');


         var search='{{Request::get('search')}}';
         var date='{{Request::get('date')}}';
         var status='{{Request::get('status')}}';


         window.location.href = 'products?search=' + search + '&product_type=' + productTypeParam + '&tags=' +productTagParam+ '&date=' + date + '&status=' + status;
     }


     function filterByStock(val)
     {

         var search='{{Request::get('search')}}';
         var vendor='{{Request::get('vendor')}}';
         var date='{{Request::get('date')}}';
         var status='{{Request::get('status')}}';
         var shopify_status='{{Request::get('shopify_status')}}';
         var productTypeSelect = document.querySelector('.js-example-basic-multiple');
         var selectedOptions = Array.from(productTypeSelect.selectedOptions).map(option => option.value);
         var productTypeParam = selectedOptions.join(',');

         var selectedFilter = $('#dropdownMenuButton').data('filter');
         var selectedFilter_weight = $('#dropdownMenuButton_weight').data('filter');

         var minimum_value=$('.minimum_field_price').val();
         var maximum_value=$('.maximum_field_price').val();

         var maximum_value_weight=$('.maximum_field_weight').val();
         var minimum_value_weight=$('.minimum_field_weight').val();

         window.location.href='products?search='+search+'&vendor='+vendor+'&product_type=' + productTypeParam+'&date='+date+'&status='+status+'&shopify_status='+shopify_status+'&stock='+val+'&filter_price='+selectedFilter+'&minimum_value_price='+minimum_value+'&maximum_value_price='+maximum_value+'&filter_weight='+selectedFilter_weight+'&minimum_value_weight='+minimum_value_weight+'&maximum_value_weight='+maximum_value_weight;

     }




     function approveMultiple()
     {
        var array = $.map($('input[name="products[]"]:checked'), function(c){return c.value; });
        if(array!='')
        {

            var search='{{Request::get('search')}}';
            var vendor='{{Request::get('vendor')}}';
            console.log(vendor);
            var date='{{Request::get('date')}}';
            var status='{{Request::get('status')}}';
            var shopify_status='{{Request::get('shopify_status')}}';
            var stock='{{Request::get('stock')}}';

            var productTypeSelect = document.querySelector('.js-example-basic-multiple');
            var selectedOptions = Array.from(productTypeSelect.selectedOptions).map(option => option.value);
            var productTypeParam = selectedOptions.join(',');
                console.log(productTypeParam);

            {{--var v_token = "{{csrf_token()}}";--}}
            {{--var formData= new FormData();--}}
            {{--formData.append('ids' , array);--}}


            var selectedFilter = $('#dropdownMenuButton').data('filter');
            var selectedFilter_weight = $('#dropdownMenuButton_weight').data('filter');

            var minimum_value=$('.minimum_field_price').val();
            var maximum_value=$('.maximum_field_price').val();

            var maximum_value_weight=$('.maximum_field_weight').val();
            var minimum_value_weight=$('.minimum_field_weight').val();


            $.ajax({
                type: 'GET',
                url: "{{ route('superadmin.bulk-approve-product') }}",
                data: {
                    ids:array,
                    search: search,
                    vendor: vendor,
                    date: date,
                    status: status,
                    shopify_status:shopify_status,
                    product_type:productTypeParam,
                    stock:stock

                },
                success: function (response) {
                    var json = $.parseJSON(response);
                    if (json.status === 'success') {

                        toastr.success("Products are In-Progress for Approval");
                        // window.location.href = 'products';
                    }
                }
            });


            {{--$.ajax({--}}
            {{--        cache: false,--}}
            {{--        contentType: false,--}}
            {{--        processData: false,--}}
            {{--        type:'post',--}}

            {{--        data:formData,--}}
            {{--        headers: {'X-CSRF-Token': v_token},--}}
            {{--        url:"{{ route('superadmin.bulk-approve-product') }}",--}}
            {{--        success:function(response){--}}
            {{--            var json = $.parseJSON(response);--}}
            {{--            if(json.status=='success')--}}
            {{--            {--}}
            {{--                window.location.href='products';--}}
            {{--            }--}}
            {{--        }--}}
            {{--    });--}}
        }
     }
	 function rejectMultiple()
     {
        var array = $.map($('input[name="products[]"]:checked'), function(c){return c.value; });
        if(array!='')
        {
            var v_token = "{{csrf_token()}}";
            var formData= new FormData();
            formData.append('ids' , array);

            var search='{{Request::get('search')}}';
            var vendor='{{Request::get('vendor')}}';
            console.log(vendor);
            var date='{{Request::get('date')}}';
            var status='{{Request::get('status')}}';
            var shopify_status='{{Request::get('shopify_status')}}';
            var stock='{{Request::get('stock')}}';

            var productTypeSelect = document.querySelector('.js-example-basic-multiple');
            var selectedOptions = Array.from(productTypeSelect.selectedOptions).map(option => option.value);
            var productTypeParam = selectedOptions.join(',');
            console.log(productTypeParam);

            {{--var v_token = "{{csrf_token()}}";--}}
            {{--var formData= new FormData();--}}
            {{--formData.append('ids' , array);--}}


            var selectedFilter = $('#dropdownMenuButton').data('filter');
            var selectedFilter_weight = $('#dropdownMenuButton_weight').data('filter');

            var minimum_value=$('.minimum_field_price').val();
            var maximum_value=$('.maximum_field_price').val();

            var maximum_value_weight=$('.maximum_field_weight').val();
            var minimum_value_weight=$('.minimum_field_weight').val();


            $.ajax({
                type: 'GET',
                url: "{{ route('superadmin.bulk-reject-product') }}",
                data: {
                    ids:array,
                    search: search,
                    vendor: vendor,
                    date: date,
                    status: status,
                    shopify_status:shopify_status,
                    product_type:productTypeParam,
                    stock:stock

                },
                success: function (response) {
                    var json = $.parseJSON(response);
                    if (json.status === 'success') {

                        toastr.success("Products are In-Progress for Deny");
                        // window.location.href = 'products';
                    }
                }
            });


        }
     }
    function checkAll(ele) {
         var checkboxes = document.getElementsByName('products[]');
         if (ele.checked) {
             for (var i = 0; i < checkboxes.length; i++) {
                 if (checkboxes[i].type == 'checkbox') {
                     checkboxes[i].checked = true;
                 }
             }
         } else {
             for (var i = 0; i < checkboxes.length; i++) {
                 console.log(i)
                 if (checkboxes[i].type == 'checkbox') {
                     checkboxes[i].checked = false;
                 }
             }
         }
     }
    $('.sidebar-nav .nav-link:not(.collapsed) ~ .nav-content').addClass('show');

    jQuery(function($) {
     var path = window.location.href; // because the 'href' property of the DOM element is the absolute path
     $('ul a').each(function() {
      if (this.href === path) {
       $(this).addClass('active');
      }
     });
    });
  </script>

  <script>
    window.pressed = function(){
    var a = document.getElementById('upfile');
    if(a.value == "")
    {
        fileLabel.innerHTML = "Choose file";
    }
    else
    {
        var theSplit = a.value.split('\\');
        fileLabel.innerHTML = theSplit[theSplit.length-1];
    }
};

</script>

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>

    $(document).ready(function () {



        $('.js-example-basic-multiple').select2();
        $('.js-example-basic-multiple1').select2();

        $('.approve_all').click(function (){

                var search='{{Request::get('search')}}';
                var vendor='{{Request::get('vendor')}}';
                console.log(vendor);
                var date='{{Request::get('date')}}';
                var status='{{Request::get('status')}}';
            var shopify_status='{{Request::get('shopify_status')}}';
            var stock='{{Request::get('stock')}}';

            var productTypeSelect = document.querySelector('.js-example-basic-multiple');
            var selectedOptions = Array.from(productTypeSelect.selectedOptions).map(option => option.value);
            var productTypeParam = selectedOptions.join(',');

            var selectedFilter = $('#dropdownMenuButton').data('filter');
            var selectedFilter_weight = $('#dropdownMenuButton_weight').data('filter');

            var minimum_value=$('.minimum_field_price').val();
            var maximum_value=$('.maximum_field_price').val();

            var maximum_value_weight=$('.maximum_field_weight').val();
            var minimum_value_weight=$('.minimum_field_weight').val();
            $.ajax({
                type: 'GET',
                url: "{{ route('superadmin.approve-selected-products') }}",
                data: {
                    search: search,
                    vendor: vendor,
                    date: date,
                    status: status,
                    shopify_status:shopify_status,
                    product_type:productTypeParam,
                    stock:stock,
                    filter_price:selectedFilter,
                    filter_weight:selectedFilter_weight,
                    minimum_value_price:minimum_value,
                    maximum_value_price:maximum_value,
                    minimum_value_weight:minimum_value_weight,
                    maximum_value_weight:maximum_value_weight,

                },
                success: function (response) {
                    var json = $.parseJSON(response);
                    if (json.status === 'success') {

                        toastr.success("Products are In-Progress for Approval");
                        // window.location.href = 'products';
                    }
                }
            });

        });

        $('.deny_all').click(function (){

            var search='{{Request::get('search')}}';
            var vendor='{{Request::get('vendor')}}';
            console.log(vendor);
            var date='{{Request::get('date')}}';
            var status='{{Request::get('status')}}';

            var shopify_status='{{Request::get('shopify_status')}}';
            var stock='{{Request::get('stock')}}';
            var productTypeSelect = document.querySelector('.js-example-basic-multiple');
            var selectedOptions = Array.from(productTypeSelect.selectedOptions).map(option => option.value);
            var productTypeParam = selectedOptions.join(',');



            var selectedFilter = $('#dropdownMenuButton').data('filter');
            var selectedFilter_weight = $('#dropdownMenuButton_weight').data('filter');

            var minimum_value=$('.minimum_field_price').val();
            var maximum_value=$('.maximum_field_price').val();

            var maximum_value_weight=$('.maximum_field_weight').val();
            var minimum_value_weight=$('.minimum_field_weight').val();

            $.ajax({
                type: 'GET',
                url: "{{ route('superadmin.deny-selected-products') }}",
                data: {
                    search: search,
                    vendor: vendor,
                    date: date,
                    status: status,
                    shopify_status:shopify_status,
                    product_type:productTypeParam,
                    stock:stock,
                    filter_price:selectedFilter,
                    filter_weight:selectedFilter_weight,
                    minimum_value_price:minimum_value,
                    maximum_value_price:maximum_value,
                    minimum_value_weight:minimum_value_weight,
                    maximum_value_weight:maximum_value_weight,
                },
                success: function (response) {
                    var json = $.parseJSON(response);
                    if (json.status === 'success') {
                        toastr.success("Products are In-Progress for Deny");
                        // window.location.href = 'products';
                    }
                }
            });

        });


        $('.filter_weight_price').on('change', function () {
            var filter = $(this).val();
            if(filter){
                $('.weight_price_div').show();
            }else{
                $('.weight_price_div').hide();
            }

        });

        $('.by_price_search').on('click', function() {

            var maximum_value=$('.maximum_field_price').val();
            var minimum_value=$('.minimum_field_price').val();

            var maximum_value_weight=$('.maximum_field_weight').val();
            var minimum_value_weight=$('.minimum_field_weight').val();

            var search='{{Request::get('search')}}';
            var vendor='{{Request::get('vendor')}}';
            var date='{{Request::get('date')}}';
            var status='{{Request::get('status')}}';
            var shopify_status='{{Request::get('shopify_status')}}';
            var productTypeSelect = document.querySelector('.js-example-basic-multiple');
            var selectedOptions = Array.from(productTypeSelect.selectedOptions).map(option => option.value);
            var productTypeParam = selectedOptions.join(',');
            var stock='{{Request::get('stock')}}';

            var selectedFilter = $('#dropdownMenuButton').data('filter');
            var selectedFilter_weight = $('#dropdownMenuButton_weight').data('filter');

            window.location.href='products?search='+search+'&vendor='+vendor+'&product_type=' + productTypeParam+'&date='+date+'&status='+status+'&shopify_status='+shopify_status+'&stock='+stock+'&filter_price='+selectedFilter+'&minimum_value_price='+minimum_value+'&maximum_value_price='+maximum_value+'&filter_weight='+selectedFilter_weight+'&minimum_value_weight='+minimum_value_weight+'&maximum_value_weight='+maximum_value_weight;

        });


        $('.by_weight_search').on('click', function() {

            var maximum_value=$('.maximum_field_price').val();
            var minimum_value=$('.minimum_field_price').val();

            var maximum_value_weight=$('.maximum_field_weight').val();
            var minimum_value_weight=$('.minimum_field_weight').val();

            var search='{{Request::get('search')}}';
            var vendor='{{Request::get('vendor')}}';
            var date='{{Request::get('date')}}';
            var status='{{Request::get('status')}}';
            var shopify_status='{{Request::get('shopify_status')}}';
            var productTypeSelect = document.querySelector('.js-example-basic-multiple');
            var selectedOptions = Array.from(productTypeSelect.selectedOptions).map(option => option.value);
            var productTypeParam = selectedOptions.join(',');
            var stock='{{Request::get('stock')}}';

            var selectedFilter = $('#dropdownMenuButton').data('filter');
            var selectedFilter_weight = $('#dropdownMenuButton_weight').data('filter');

            window.location.href='products?search='+search+'&vendor='+vendor+'&product_type=' + productTypeParam+'&date='+date+'&status='+status+'&shopify_status='+shopify_status+'&stock='+stock+'&filter_price='+selectedFilter+'&minimum_value_price='+minimum_value+'&maximum_value_price='+maximum_value+'&filter_weight='+selectedFilter_weight+'&minimum_value_weight='+minimum_value_weight+'&maximum_value_weight='+maximum_value_weight;

        });
    });
</script>
@endsection
