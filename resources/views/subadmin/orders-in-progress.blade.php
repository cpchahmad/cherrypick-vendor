@extends('layouts.admin')

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
            <P><b>Orders in Progress</b></P>
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
                  <input type="text" class="datepicker_input form-control" placeholder="Select date" required aria-label="Date and Month">
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
              <table class="table table-bordered datatable table-white">
                <thead>
                  <tr>
                    <th scope="col" class="fl-input"><input class="form-check-input" type="checkbox" id="gridCheck"></th>
                    <th scope="col">Order ID</th>
                    <th scope="col">Date</th>
                    <th scope="col">Customer Email</th>
                    <th scope="col">Payment Status</th>
                    <th scope="col">Fulfillment Status</th>
                    <th scope="col">Items</th>
                    <th scope="col">Order Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td scope="row"><input class="form-check-input" type="checkbox" id="gridCheck"></td>
                    <td>#3456</td>
                    <td>04-10-22</td>
                    <td>customer@gmail.com</td>
                    <td>Paid</td>
                    <td><span class="fulfill">Fulfillment</span></td>
                    <td>2</td>
                    <td><span class="en-in-progress"></span>In Progress</td>
                  </tr>
                  <tr>
                    <td scope="row"><input class="form-check-input" type="checkbox" id="gridCheck"></td>
                    <td>#3458</td>
                    <td>07-10-22</td>
                    <td>customer@gmail.com</td>
                    <td>Unpaid</td>
                    <td><span class="fulfill">Fulfillment</span></td>
                    <td>2</td>
                    <td><span class="en-in-progress"></span>In Progress</td>
                  </tr>
                  <tr>
                    <td scope="row"><input class="form-check-input" type="checkbox" id="gridCheck"></td>
                    <td>#3456</td>
                    <td>04-10-22</td>
                    <td>customer@gmail.com</td>
                    <td>COD</td>
                    <td><span class="fulfill">Fulfillment</span></td>
                    <td>1</td>
                    <td><span class="en-in-progress"></span>In Progress</td>
                  </tr>
                  <tr>
                    <td scope="row"><input class="form-check-input" type="checkbox" id="gridCheck"></td>
                    <td>#3456</td>
                    <td>04-10-22</td>
                    <td>customer@gmail.com</td>
                    <td>Paid</td>
                    <td><span class="fulfill">Fulfillment</span></td>
                    <td>5</td>
                    <td><span class="en-in-progress"></span>In Progress</td>
                  </tr>
                  <tr>
                    <td scope="row"><input class="form-check-input" type="checkbox" id="gridCheck"></td>
                    <td>#3456</td>
                    <td>04-10-22</td>
                    <td>customer@gmail.com</td>
                    <td>Paid</td>
                    <td><span class="fulfill">Fulfillment</span></td>
                    <td>3</td>
                    <td><span class="en-in-progress"></span>In Progress</td>
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
      <nav class="mainpg timer-nav">
        <ul class="pagination">
          <li class="page-item disabled">
            <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
          </li>
          <li class="page-item"><a class="page-link" href="#">1</a></li>
          <li class="page-item active" aria-current="page">
            <a class="page-link" href="#">2</a>
          </li>
          <li class="page-item"><a class="page-link" href="#">3</a></li>
          <li class="page-item">
            <a class="page-link" href="#">Next</a>
          </li>
        </ul>
      </nav>
    </section>
   </main>
   <div class="modal fade" id="verticalycentered" tabindex="-1" data-bs-backdrop="false">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><b><i class="bi bi-trash"></i></b> Delete</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to Delete ?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
          <button type="button" class="btn btn-primary">Delete!</button>
        </div>
      </div>
    </div>
  </div>
  <!-- End #main -->
  <!-- ======= Footer ======= -->
  