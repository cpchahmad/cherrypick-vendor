@extends('layouts.superadmin')

  <main id="main" class="main">
    <div class="subpagetitle fit-title">
        <h1>Vendor Details</h1>
         <p><a href="out-of-stock-items.html">Out of Stock Items</a> / <b>Vendor Details</b></p>
      </div>
   
    <section class="section dashboard">
      <div class="row">
        <div class="col-12 ">
            <div class="card fullorders">
              <div class="row ">
                  <div class="col-12">
                     <h4>Vendor Details <span class="full-status">Out of Stock Items</span></h4>
                     <div class="order-summry">
                    <div class="order-items">
                     <p><b>Vendor Name</b> <span>Name of vendor</span></p>
                     <p><b>Vendor Email</b> <span>vendor@gmail.com</span></p>
                     <p><b>Store URL</b> <span>www.vendor.com</span></p>
                    </div>
                   
                    </div>
                     
                  </div>
                  <div class="order-items vendor-item-list">
                    <h6 class="ostock-list"><b>Out of Stock Items Lists</b></h6>
                    <div class="table-responsive">
                      <table class="table table-bordered table-white">
                        <thead>
                          <tr>
                            <th scope="col">S.No</th>
                            <th scope="col">Product Name</th>
                            <th scope="col">SKU</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>1</td>
                            <td>Luja Floral Vase Antique Bronze</td>
                            <td>CH001</td>
                          </tr>
                          <tr>
                            <td>2</td>
                            <td>Luja Floral Vase Antique Bronze</td>
                            <td>CH002</td>
                          </tr>
                          <tr>
                            <td>3</td>
                            <td>Luja Floral Vase Antique Bronze</td>
                            <td>CH003</td>
                          </tr>
                          <tr>
                            <td>4</td>
                            <td>Luja Floral Vase Antique Bronze</td>
                            <td>CH004</td>
                          </tr>
                        </tbody>
                      </table>
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
 <script>
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
    document.querySelector('.chatbot-right').style.display = 'none';
    let chatfill = document.querySelector('.btm-chaticon');
    let chathide = document.querySelector('.bi-x');

    chatfill.addEventListener("click", () => {
      document.querySelector('.chatbot-right').style.display = 'block';
    });

    chathide.addEventListener('click', function(){
      document.querySelector('.chatbot-right').style.display = 'none';
    });

  </script>