@extends('layouts.admin')
@section('main')
<main id="main" class="main">
   <div class="home-flex">
    <div class="pagetitle">
       <h1>Orders</h1>
    </div><!-- End Page Title -->
   </div>
   
    <section class="section dashboard">
      <div class="row">
        <!-- Left side columns -->
        <div class="col-lg-12 dash-text">
         
          <div class="row">
            <p><b>All Orders</b></p>
            <div class="sort-by">
              <div class="member-plan-search header onetime-search">
                <div class="search-bar">
                    <form class="search-form d-flex align-items-center" method="POST" action="#">
                      <input type="text" name="query" placeholder="Search Orders" title="Enter search keyword">
                      <button type="submit" title="Search"><i class="bi bi-search"></i></button>
                    </form>
                  </div>
                 
               </div>
              <div class="sale-date">
                <div class="input-group">
                  <input type="text" class="datepicker_input form-control datepicker-input" placeholder="Select date" required="" aria-label="Date and Month">
                  <i class="bi bi-calendar4 input-group-text"></i>
                </div>
              </div>
              <div class="create-plan">
                <a href="#">Export Order</a>
              </div>
            </div>
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
                               <th scope="col" data-sortable="" style="width: 9.77778%;"><a href="#" class="dataTable-sorter">Date</a></th>
                               <th scope="col" data-sortable="" style="width: 16.7778%;"><a href="#" class="dataTable-sorter">Payment Status</a></th>
                               <th scope="col" data-sortable="" style="width: 8.22222%;"><a href="#" class="dataTable-sorter">Total</a></th>
                               <th scope="col" data-sortable="" style="width: 19.2222%;"><a href="#" class="dataTable-sorter">Fulfillment Status</a></th>
                               <th scope="col" data-sortable="" style="width: 17.4444%;"><a href="#" class="dataTable-sorter">Items</a></th>
                               <th scope="col" data-sortable="" style="width: 11.5556%;"><a href="#" class="dataTable-sorter">Action</a></th>
                            </tr>
                         </thead>
                         <tbody>
                             @foreach($data as $row)
                            <tr>
                               <td>#{{$row['order_number']}}</td>
                               <td>{{date('d-m-Y', strtotime($row['created_at']))}}</td>
                               <td>{{$row['financial_status']}}</td>
                               <td>{{$row['presentment_currency']}} {{$row['current_total_price']}}</td>
                               <td><span class="fulfill">{{$row['fulfillment_status']}}</span></td>
                               <td>{{count($row['line_items'])}} items</td>
                               <td class="icon-action">
                                  <a href="view-order.html"><i class="bi bi-eye"></i></a>
                                  <a href="change-order-status.html"><i class="bi bi-pencil-fill"></i></a>
                               </td>
                            </tr>
                            @endforeach                
                         </tbody>
                      </table>
                   </div>
                   <div class="dataTable-bottom">
                      <div class="dataTable-info">Showing 1 to 5 of 5 entries</div>
                      <nav class="dataTable-pagination">
                         <ul class="dataTable-pagination-list"></ul>
                      </nav>
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
        <ul class="pagination">
          <li class="page-item disabled">
            <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
          </li>
          <li class="page-item">
            <a class="page-link" href="{{url('orders')}}?page=1">Next</a>
          </li>
        </ul>
      </nav>
    </section>
   </main>
@stop
  