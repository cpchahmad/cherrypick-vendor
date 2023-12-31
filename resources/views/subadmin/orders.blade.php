@extends('layouts.admin')
<style>
    .seprate input, select.form-select{
        height:38px !important;
    }
</style>
@section('main')
<main id="main" class="main">
   <div class="home-flex">
    <div class="pagetitle">
       <h1>All Orders</h1>
    </div><!-- End Page Title -->
   </div>

    <section class="section dashboard">
      <div class="row">
        <!-- Left side columns -->
        <div class="col-lg-12 dash-text">

          <div class="row">
              <form class="search-form" method="get" action="">
                  <div class="row align-items-center">
                      <div class="col-md-3">
                          <div class="mb-3">
                              <label for="order" class="form-label">Search Orders</label>
                              <input type="text" name="order" id="order" value="{{ request()->get('order') }}" class="form-control" placeholder="Search Orders" title="Enter search keyword">
                          </div>
                      </div>

                      <div class="col-md-3">
                          <div class="mb-3">
                              <label for="status" class="form-label">Select Status</label>
                              <select class="form-select" id="status" aria-label="Select Status" name="status">
                                  <option value='' selected="">Select Status</option>
                                  <option value="0" {{ Request::get('status') == "0" ? 'selected' : '' }}>New</option>
                                  <option value="1" {{ Request::get('status') == "1" ? 'selected' : '' }}>Ready for Pickup</option>
                                  <option value="2" {{ Request::get('status') == "2" ? 'selected' : '' }}>Completed</option>
                              </select>
                          </div>
                      </div>

                      <div class="col-md-3">
                          <div class="mb-3">
                              <label for="sdate" class="form-label">Start Date</label>
                              <input type="date" name="sdate" id="sdate" value="{{ request()->get('sdate') }}" class="form-control" placeholder="YYYY-MM-DD" aria-label="Start Date">
                          </div>
                      </div>

                      <div class="col-md-3">
                          <div class="mb-3">
                              <label for="edate" class="form-label">End Date</label>
                              <input type="date" name="edate" id="edate" value="{{ request()->get('edate') }}" class="form-control" placeholder="YYYY-MM-DD" aria-label="End Date">
                          </div>
                      </div>

                      <div class="col-md-3">
                          <div class="mb-3">
                              <button class="btn btn-primary" type="submit">Submit</button>
                          </div>
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
                               <td>@if($row['status']=='0') <span class="en-dismissed"></span>{{'New'}} @elseif($row['status']=='1') <span class="en-in-progress"></span>{{'Ready For Pickup'}} @else <span class="en-recovered"></span>{{'Completed'}} @endif</td>
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
