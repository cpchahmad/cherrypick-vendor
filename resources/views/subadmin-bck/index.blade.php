@extends('layouts.admin')

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
                <div class="card info-card revenue-card">
                  <div class="card-body card-custom">
                  <div class="d-flex align-items-center">
                      <div class="ps-3">
                        <h5><b>22</b></h5>
                        <p>Total Number of Sales</p>
                      </div>
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-bar-chart"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-xxl-12 col-md-12">
                <div class="card info-card revenue-card">
                  <div class="card-body card-custom">
                  <div class="d-flex align-items-center">
                      <div class="ps-3">
                        <h5><b>3200.56</b></h5>
                        <p>Total Sales</p>
                      </div>
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-currency-dollar"></i>
                      </div>
                    </div>
                  </div>
  
                </div>
              </div>
             </div>
             </div>
             <div class="col-md-7">
              <p><b>Orders Summary</b></p>
              <div class="row">
              <div class="col-xxl-12 col-md-12">
                <div class="card info-card revenue-card">
                  <div class="card-body card-custom">
                  <div class="d-flex align-items-center">
                    <div class="ps-3">
                      <h5><b>23</b></h5>
                      <p>Total's Orders</p>
                    </div>
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-calendar4"></i>
                    </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-xxl-6 col-md-6">
                <div class="card info-card revenue-card">
                  <div class="card-body card-custom">
                  <div class="d-flex align-items-center">
                      <div class="ps-3">
                        <h5><b>833</b></h5>
                        <p>Weekly Orders</p>
                      </div>
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-border-width"></i>
                      </div>
                    </div>
                  </div>
  
                </div>
              </div>
              <div class="col-xxl-6 col-md-6">
                <div class="card info-card revenue-card">
                  <div class="card-body card-custom">
                  <div class="d-flex align-items-center">
                      <div class="ps-3">
                        <h5><b>2313</b></h5>
                        <p>Monthly Orders</p>
                      </div>
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-border-width"></i>
                      </div>
                    </div>
                  </div>
  
                </div>
              </div>
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
                           data: [32, 18, 8, 13, 23, 41, 14, 23, 33, 48, 28, 45],
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
                             data: [40, 13, 23, 50],
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
                    <th scope="col">Quantity</th>
                    <th scope="col">Update Quantity</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <th scope="row">1</th>
                    <td>#2344</td>
                    <td>ProductA</td>
                    <td class="p-img"><img src="{{url('subadmin/assets/img/card.jpg')}}"></td>
                    <td>₹ 234</td>
                    <td>0</td>
                    <td class="up-quantity">
                        <input type="text" name="qtn">
                        <button type="button">Save</button>
                    </td>
                  </tr>
                  <tr>
                    <th scope="row">2</th>
                    <td>#2344</td>
                    <td>ProductB</td>
                    <td class="p-img"><img src="{{url('subadmin/assets/img/card.jpg')}}"></td>
                    <td>₹ 234</td>
                    <td>-1</td>
                    <td class="up-quantity">
                        <input type="text" name="qtn">
                        <button type="button">Save</button>
                    </td>
                  </tr>
                  <tr>
                    <th scope="row">3</th>
                    <td>#2344</td>
                    <td>ProductC</td>
                    <td class="p-img"><img src="{{url('subadmin/assets/img/card.jpg')}}"></td>
                    <td>₹ 234</td>
                    <td>0</td>
                    <td class="up-quantity">
                        <input type="text" name="qtn">
                        <button type="button">Save</button>
                    </td>
                  </tr>
                  <tr>
                    <th scope="row">4</th>
                    <td>#2344</td>
                    <td>ProductD</td>
                    <td class="p-img"><img src="{{url('subadmin/assets/img/card.jpg')}}"></td>
                    <td>₹ 234</td>
                    <td>-1</td>
                    <td class="up-quantity">
                        <input type="text" name="qtn">
                        <button type="button">Save</button>
                    </td>
                  </tr>
                  <tr>
                    <th scope="row">5</th>
                    <td>#2344</td>
                    <td>ProductE</td>
                    <td class="p-img"><img src="{{url('subadmin/assets/img/card.jpg')}}"></td>
                    <td>₹ 234</td>
                    <td>0</td>
                    <td class="up-quantity">
                        <input type="text" name="qtn">
                        <button type="button">Save</button>
                    </td>
                  </tr>
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
                    <th scope="col">Customer Email</th>
                    <th scope="col">Payment Status</th>
                    <th scope="col">Fulfillment Status</th>
                    <th scope="col">Total</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <th scope="row">1</th>
                    <td>ProductA</td>
                    <td>#3456</td>
                    <td>customer@gmail.com</td>
                    <td>Paid</td>
                    <td>Fulfillment</td>
                    <td>₹ 234</td>
                  </tr>
                  <tr>
                    <th scope="row">2</th>
                    <td>ProductB</td>
                    <td>#3456</td>
                    <td>customer@gmail.com</td>
                    <td>Paid</td>
                    <td>Partial Fulfillment</td>
                    <td>₹ 234</td>
                  </tr>
                  <tr>
                    <th scope="row">3</th>
                    <td>ProductC</td>
                    <td>#3456</td>
                    <td>customer@gmail.com</td>
                    <td>Paid</td>
                    <td>Fulfillment</td>
                    <td>₹ 234</td>
                  </tr>
                  <tr>
                    <th scope="row">4</th>
                    <td>ProductD</td>
                    <td>#3456</td>
                    <td>customer@gmail.com</td>
                    <td>Paid</td>
                    <td>Partial Fulfillment</td>
                    <td>₹ 234</td>
                  </tr>
                  <tr>
                    <th scope="row">5</th>
                    <td>ProductE</td>
                    <td>#3456</td>
                    <td>customer@gmail.com</td>
                    <td>Paid</td>
                    <td>Fulfillment</td>
                    <td>₹ 234</td>
                  </tr>
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
  