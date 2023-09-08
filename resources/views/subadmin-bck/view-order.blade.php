@extends('layouts.admin')

  <main id="main" class="main">
    <div class="subpagetitle fit-title">
        <h1>View Order</h1>
         <p><a href="all-orders.html">Order</a> / <b>View Order</b></p>
      </div>
   
    <section class="section dashboard">
      <div class="row">
        <div class="col-12 ">
            <div class="card fullorders">
              <div class="row ">
                  <div class="col-12">
                     <h4>Order Details <span class="full-status">In Progress</span></h4>
                     <div class="order-summry">
                    <div class="order-items">
                     <p><b>Order Id</b> <span>#3213123</span></p>
                     <p><b>Date</b> <span>02/05/22</span></p>
                     <p><b>Fulfillment Status</b> <span>Fulfillment</span></p>
                     <p><b>Payment Status</b> <span>Paid</span></p>
                     <p><b>Items</b> <span>2</span></p>
                     <p><b>Price</b> <span>₹ 634</span></p>
                     <p><b>Discount</b> <span>0.00</span></p>
                     <p><b>OTP</b> <span>32434</span></p>
                    </div>
                    <div class="order-items">
                      <p><b>Product Name</b> <span>Luja Floral Vase Antique Bronze</span></p>
                      <p><b>SKU</b> <span>CH001</span></p>
                      <p><b>Variant</b> <span>Small, Red</span></p>
                    </div>
                    </div>
                     
                  </div>
                </div>     
             </div>
            
        </div>
      </div>
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
 