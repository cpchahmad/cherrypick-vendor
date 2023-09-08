@extends('layouts.superadmin')
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
           <div class="col-md-12">
            <div class="card-body show-plan collections">
              <!-- Bordered Table -->
              <div class="table-responsive">
			  <form action="{{url('superadmin/store-amount-history')}}/{{$id}}" method="get">
                                <table border="0" cellspacing="5" cellpadding="5">
                                  <tbody>
                                    <tr>
                                      <td>
                                        <input type="date" id="min" name="min" @if(Request::get('min')) value="{{Request::get('min')}}" @endif>
                                      </td>
                                      <td>
                                        <input type="date" id="max" name="max" @if(Request::get('max')) value="{{Request::get('max')}}" @endif>
                                      </td>
                                      <td>
                                          <button type="submit" id="filtrar_fecha">Filter</button>
                                      </td>
                                    </tr>
                                  </tbody>
                                </table>
                                </form>   
                                <div class="m-3 mb-1">
              <a class="btn btn-warning btn-sm" href="{{route('superadmin.store-amount')}}">Back</a>
            </div> 
                                <br><br>
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
                               <th scope="col" data-sortable="" style="width: 19.2222%;"><a href="#" class="dataTable-sorter">Amount</a></th>
                               <th scope="col" data-sortable="" style="width: 11.5556%;"><a href="#" class="dataTable-sorter">Action</a></th>
                            </tr>
                         </thead>
                         <tbody>
                             @foreach($data as $row)
                            <tr>
                               <td>#{{$row['shopify_order_id']}}</td>
                               <td>{{$row['order_date']}}</td>
                               <td>
									{{round($row['price'],2)}}
							   </td>
                               <td class="icon-action">
                                  <a href="{{url('superadmin/order-details')}}/{{$row['id']}}" target="_blank"><i class="bi bi-eye"></i></a>
                                  
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
  