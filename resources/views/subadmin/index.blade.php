@extends('layouts.admin')
@section('main')
  <main id="main" class="main">
   <div class="home-flex">
    <div class="pagetitle">
       <h1>Dashboard</h1>
    </div><!-- End Page Title -->
   </div>
    <section class="section dashboard">
      <div class="row">
        <!-- Left side columns -->
        <div class="col-lg-12 dash-text">
          <div class="row">
            <div class="col-md-5">
              <p><b>Sales Summary</b></p>
              <div class="row">
              <div class="col-xxl-12 col-md-12">
			  <a href="{{url('orders')}}">
                <div class="card info-card revenue-card">
                  <div class="card-body card-custom">
                  <div class="d-flex align-items-center">
                      <div class="ps-3">
                        <h5><b>{{$total_items_sale}}</b></h5>
                        <p>Total Number of Sales</p>
                      </div>
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-bar-chart"></i>
                      </div>
                    </div>
                  </div>
                </div>
				</a>
              </div>
              <div class="col-xxl-12 col-md-12">
			  <a href="{{url('orders')}}">
                <div class="card info-card revenue-card">
                  <div class="card-body card-custom">
                  <div class="d-flex align-items-center">
                      <div class="ps-3">
                        <h5><b>{{$total_price}}</b></h5>
                        <p>Total Sales</p>
                      </div>
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                       
                      </div>
                    </div>
                  </div>
  
                </div>
				</a>
              </div>
             </div>
             </div>
             <div class="col-md-7">
              <p><b>Orders Summary</b></p>
              <div class="row">
              <div class="col-xxl-12 col-md-12">
			  <a href="{{url('orders')}}">
                <div class="card info-card revenue-card">
                  <div class="card-body card-custom">
                  <div class="d-flex align-items-center">
                    <div class="ps-3">
                      <h5><b>{{$total_order}}</b></h5>
                      <p>Total's Orders</p>
                    </div>
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-calendar4"></i>
                    </div>
                    </div>
                  </div>
                </div>
				</a>
              </div>
              <div class="col-xxl-6 col-md-6">
			  <a href="{{url('orders')}}?flag=week">
                <div class="card info-card revenue-card">
                  <div class="card-body card-custom">
                  <div class="d-flex align-items-center">
                      <div class="ps-3">
                        <h5><b>{{$total_order_week}}</b></h5>
                        <p>Weekly Orders</p>
                      </div>
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-border-width"></i>
                      </div>
                    </div>
                  </div>
  
                </div>
				</a>
              </div>
              <div class="col-xxl-6 col-md-6">
			  <a href="{{url('orders')}}?flag=month">
                <div class="card info-card revenue-card">
                  <div class="card-body card-custom">
                  <div class="d-flex align-items-center">
                      <div class="ps-3">
                        <h5><b>{{$total_order_month}}</b></h5>
                        <p>Monthly Orders</p>
                      </div>
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-border-width"></i>
                      </div>
                    </div>
                  </div>
  
                </div>
              </div>
			  </a>
            </div>
          </div>
          </div>
          <div class="row">
            <div class="col-lg-6">
              <P><b>Sales Analytics</b></P>
             <div class="card">
               <div class="card-body">
                 <!-- Bar Chart -->
                 <canvas id="barChartmem" style="max-height: 400px; padding: 120xp;"></canvas>
                 <script>
                   document.addEventListener("DOMContentLoaded", () => {
                     new Chart(document.querySelector('#barChartmem'), {
                       type: 'bar',
                       data: {
                         labels: ['Jan', 'Feb', 'March', 'April', 'May', 'June', 'July', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' ],
                         datasets: [{
                           label: 'Total Sales',
                           data: [{{$data_month_arr[0]}}, {{$data_month_arr[1]}}, {{$data_month_arr[2]}}, {{$data_month_arr[3]}}, {{$data_month_arr[4]}}, {{$data_month_arr[5]}}, {{$data_month_arr[6]}}, {{$data_month_arr[7]}}, {{$data_month_arr[8]}}, {{$data_month_arr[9]}}, {{$data_month_arr[10]}}, {{$data_month_arr[11]}}],
                           backgroundColor: [
                             'rgba(224 224 224)'
                           ],
                           borderColor: [
                             'rgba(224 224 224)',
                           ],
                           borderWidth: 0
                         }]
                       },
                       options: {
                         scales: {
                           y: {
                             beginAtZero: true
                           }
                         }
                       }
                     });
                   });
                 </script>
                 <!-- End Bar CHart -->
   
               </div>
             </div>
           </div>
            <div class="col-md-6">
                <P><b>Order Analytics</b></P>
               <div class="card">
                 <div class="card-body">
                   <!-- Bar Chart -->
                   <canvas id="barChart" style="max-height: 400px; padding: 120xp;"></canvas>
                   <script>
                     document.addEventListener("DOMContentLoaded", () => {
                       new Chart(document.querySelector('#barChart'), {
                         type: 'bar',
                         data: {
                           labels: ['Today Orders', 'Weekly Orders', 'Monthly Orders' ],
                           datasets: [{
                             label: 'Total Orders',
                             data: [{{$total_order_today}}, {{$total_order_week}}, {{$total_order_month}}],
                             backgroundColor: [
                             'rgba(224 224 224)',
                             'rgba(224 224 224)',
                             'rgba(224 224 224)'
                             ],
                             borderColor: [
                             'rgba(224 224 224)',
                             'rgba(224 224 224)',
                             'rgba(224 224 224)'
                             ],
                             borderWidth: 1,
                             innerWidth: 1,
                             barThickness: 30,
                           }
                          ],
                           
                         },
                         options: {
                           scales: {
                             y: {
                               beginAtZero: true,
                               barThickness: 1,
                               maxBarThickness: 1
                             }
                           }
                         
                         }
                       });
                     });
                   </script>
                   <!-- End Bar CHart -->
     
                 </div>
             </div>
            </div>
          </div>
          <div class="row">
            <P><b>Out of Stock Items </b></P>
           <div class="col-md-12">
            <div class="card-body show-plan collections">
              <!-- Bordered Table -->
              <div class="table-responsive">
              <table class="table table-bordered table-white">
                <thead>
                  <tr>
                    <th scope="col">S.No</th>
                    <th scope="col">SKU</th>
                    <th scope="col">Product Name</th>
                    <th scope="col">Product Image</th>
                    <th scope="col">Price</th>
                    <!--<th scope="col">Quantity</th>-->
                  </tr>
                </thead>
                <tbody>
                  @foreach($out_of_stock as $k=>$row)  
                  <tr>
                    <th scope="row">{{$k+1}}</th>
                    <td>#{{$row->sku}}</td>
                    <td>{{$row->title}}</td>
                    @php 
                        $image=\App\Models\ProductImages::where(['product_id' => $row->pid])->pluck('image')->first();
                    @endphp
                    <td class="p-img"><img src="{{$image}}"></td>
                    <td>{{$row->base_price}}</td>
                    <!--<td>{{$row->stock}}</td>-->
                  </tr>
                  @endforeach
                  
                </tbody>
              </table>
              </div>
              <!-- End Bordered Table -->
            </div>
           </div>
          </div>
          <div class="row">
            <P><b>Open Orders</b></P>
           <div class="col-md-12">
            <div class="card-body show-plan collections">
              <!-- Bordered Table -->
              <div class="table-responsive">
              <table class="table table-bordered table-white">
                <thead>
                  <tr>
                    <th scope="col">S.No</th>
                    <th scope="col">Order ID</th>
                    <th scope="col">Date</th>
                    <th scope="col">Order Items</th>
                    <th scope="col">Status</th>
                    <th scope="col">Action</th>
                  </tr>
                </thead>
                <tbody>
                @foreach($new_orders as $k=>$row)    
                  <tr>
                    <th scope="row">{{$k+1}}</th>
                    <td>{{$row->shopify_order_id}}</td>
                    <td>{{$row->order_date}}</td>
                    @php
                    $product_count=\App\Models\Orderitem::where(['shopify_orders_id' =>$row->shopify_order_id, 'vendor_id' =>App\Helpers\Helpers::VendorID()])->count();
                   @endphp
                    <td>{{$product_count}} items</td>
                    <td>@if($row->status=='0') <span class="en-dismissed"></span>{{'New'}} @elseif($row->status=='1') <span class="en-in-progress"></span>{{'Ready For Pickup'}} @else <span class="en-recovered"></span>{{'Completed'}} @endif</td>
                    <td><a href="{{url('order-details')}}/{{$row->id}}"><i class="bi bi-eye"></i></a></td>
                  </tr>
                @endforeach  
                 
                </tbody>
              </table>
              </div>
              <!-- End Bordered Table -->
            </div>
           </div>
          </div>
        </div>
      </div>
    </section>
   </main>
  <!-- End #main -->
  <!-- ======= Footer ======= -->
  @endsection