@extends('layouts.admin')
@section('main')
<main id="main" class="main">
   <div class="home-flex">
    <div class="pagetitle">
       <h1>New Orders</h1>
    </div><!-- End Page Title -->
   </div>
   
    <section class="section dashboard">
      <div class="row">
        <!-- Left side columns -->
        <div class="col-lg-12 dash-text">
         
          <div class="row">
			<form class="search-form d-flex align-items-center" method="get" action="">
            <div class="sort-by">
              <div class="member-plan-search header onetime-search">
                <div class="search-bar search-form d-flex align-items-center">
                    
                      <input type="text" name="order" id="order" value="{{ request()->get('order') }}" placeholder="Search Orders" title="Enter search keyword">
                      <button type="submit" title="Search"><i class="bi bi-search"></i></button>
                    
                  </div>
                 
               </div>
              <div class="sale-date">
                <div class="input-group">
                  <input type="date" name="date" value="{{ request()->get('date') }}" class="datepicker_input form-control datepicker-input" placeholder="YYYY-MM-DD" aria-label="Date">
                  <!--<i class="bi bi-calendar4 input-group-text"></i>-->
                </div>
              </div>
              <div class="create-plan">
                <button class="btn btn-primary" type="submit">Submit</button>
              </div>
            </div>
            </form>
           <div class="col-md-12">
            <div class="card-body show-plan collections">
              <!-- Bordered Table -->
              <div class="table-responsive">
                <div class="dataTable-wrapper dataTable-loading no-footer sortable searchable fixed-columns">
                   <div class="dataTable-top">
                      <div class="dataTable-dropdown">
                         <label>
                            <select class="dataTable-selector">
                               <option value="5">5</option>
                               <option value="10" selected="">10</option>
                               <option value="15">15</option>
                               <option value="20">20</option>
                               <option value="25">25</option>
                            </select>
                            entries per page
                         </label>
                      </div>
                      <div class="dataTable-search"><input class="dataTable-input" placeholder="Search..." type="text"></div>
                   </div>
                   <div class="dataTable-container">
                      <table class="table table-bordered datatable table-white dataTable-table">
                         <thead>
                            <tr>
                               <th scope="col" data-sortable="" style="width: 10.6667%;"><a href="#" class="dataTable-sorter">Order ID</a></th>
                               <th scope="col" data-sortable="" style="width: 9.77778%;"><a href="#" class="dataTable-sorter">Order Date</a></th>
                               <th scope="col" data-sortable="" style="width: 17.4444%;"><a href="#" class="dataTable-sorter">Items</a></th>
                               <th scope="col" data-sortable="" style="width: 19.2222%;"><a href="#" class="dataTable-sorter">OTP</a></th>
                               <th scope="col" data-sortable="" style="width: 19.2222%;"><a href="#" class="dataTable-sorter">Status</a></th>
                               <th scope="col" data-sortable="" style="width: 11.5556%;"><a href="#" class="dataTable-sorter">Action</a></th>
                            </tr>
                         </thead>
                         <tbody>
                             @foreach($data as $row)
                            <tr>
                               <td>#{{$row['shopify_order_id']}}</td>
                               <td>{{$row['order_date']}}</td>
                               @php
                                $product_count=\App\Models\Orderitem::where(['shopify_orders_id' =>$row['shopify_order_id'], 'vendor_id' =>App\Helpers\Helpers::VendorID()])->count();
                               @endphp
                               <td>{{$product_count}} items</td>
                               <td><span class="fulfill">{{$row['otp']}}</span></td> 
                               <td><span class="en-dismissed"></span>New</td>
                               <td class="icon-action">
                                  <a href="{{url('order-details')}}/{{$row['id']}}"><i class="bi bi-eye"></i></a>
                                  
                               </td>
                            </tr>
                            @endforeach                     
                         </tbody>
                      </table>
                   </div>
                </div>
              </div>
              <!-- End Bordered Table -->
            </div>
           </div>
          </div>
        </div>
      </div>
      <nav class="mainpg timer-nav">
        {{ $data->links( "pagination::bootstrap-4") }}
      </nav>
    </section>
   </main>
@stop
@section('js')
<script type="text/javascript">
 jQuery(document).ready(function() {
    $('#order').keypress(function(event) {
        if (event.which === 32) {
            event.preventDefault();
        }
    });
});
</script>
@stop
  