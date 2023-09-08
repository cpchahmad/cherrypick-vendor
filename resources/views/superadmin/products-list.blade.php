@extends('layouts.superadmin')
@section('main')
  <main id="main" class="main">
   <div class="home-flex">
    <div class="pagetitle">
       <h1>Product List</h1>
    </div><!-- End Page Title -->
   </div>
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
         <div class="label-area sort-area">
            <select class="form-select" aria-label="Default select example" onchange='filterByVendor(this.value)'>
              <option value='' selected="">Select Vendor</option>
              @foreach($vendorlist as $ven)
              <option value="{{$ven->id}}" {{Request::get('vendor') == $ven->id  ? 'selected' : ''}}>{{$ven->name}}</option>
              @endforeach
            </select>
         </div>
        <div class="sale-date">
          <div class="input-group">
              <input type="text" class="datepicker_input form-control datepicker-input" id="fil_date" placeholder="@if(Request::get('date')!='') {{Request::get('date')}} @else {{'Select Date'}} @endif" onblur='filterByDate(this.value)'  aria-label="Date and Month">
            <i class="bi bi-calendar4 input-group-text"></i>
          </div>
        </div>
        <!--<div class="create-plan">
          <a href="#">Export Products</a>
        </div> -->
      </div>
        <form class="add-product-form">
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
                            <th scope="col">Status</th>
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
                            <td>@if($row->status==1) <span class="en-recovered"></span> Approved @else <span class="en-dismissed"></span> Pending @endif</td>
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
          window.location.href='products?search='+search+'&vendor='+id+'&date='+date;
     }
     function filterByName(val)
     {
         var search=$('#search').val();
         if(search!='')
         {
             var vendor='{{Request::get('vendor')}}';
             var date='{{Request::get('date')}}';
             window.location.href='products?search='+search+'&vendor='+vendor+'&date='+date;
         }
     }
     function filterByDate(val)
     {
         if(val!='')
         {
             var search='{{Request::get('search')}}';
             var vendor='{{Request::get('vendor')}}';
             window.location.href='products?search='+search+'&venodr='+vendor+'&date='+val;
         }
     }
     function approveMultiple()
     {       
        var array = $.map($('input[name="products[]"]:checked'), function(c){return c.value; });
        if(array!='')
        {
            var v_token = "{{csrf_token()}}";
            var formData= new FormData();
            formData.append('ids' , array);
            $.ajax({
                    cache: false,
                    contentType: false,
                    processData: false,
                    type:'post',
                    data:formData,
                    headers: {'X-CSRF-Token': v_token},
                    url:"{{ route('superadmin.bulk-approve-product') }}",
                    success:function(response){
                        var json = $.parseJSON(response);
                        if(json.status=='success')
                        {
                            window.location.href='products';
                        }
                    }
                });
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
            $.ajax({
                    cache: false,
                    contentType: false,
                    processData: false,
                    type:'post',
                    data:formData,
                    headers: {'X-CSRF-Token': v_token},
                    url:"{{ route('superadmin.bulk-reject-product') }}",
                    success:function(response){
                        var json = $.parseJSON(response);
                        if(json.status=='success')
                        {
                            window.location.href='products';
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
