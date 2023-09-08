@extends('layouts.superadmin')

  <main id="main" class="main">
    <div class="subpagetitle fit-title">
        <h1>View Product</h1>
         <p><a href="products-list.html">Product List</a> / <b>View Product</b></p>
      </div>
    <section class="section up-banner">
        <div class="row">
            <div class="col-12 ">
                <div class="card fullorders">
                  <div class="row ">
                      <div class="col-12">
                         <h4>Product Details <span class="full-status">Pending</span></h4>
                         <div class="order-summry">
                        <div class="order-items">
                         <p><b>Product Name</b> <span>Ut inventore ipsa voluptas nulla</span></p>
                         <p><b>Product SKU</b> <span>SP001</span></p>
                         <p><b>Product Price</b> <span>₹ 634</span></p>
                         <p><b>Product Quantity</b> <span>2</span></p>
                         <p><b>Product Dimensions</b> <span>23, 30, 22</span></p>
                         <p><b>Product Variants</b> <span>Black, SL</span></p>
                         <p><b>Product Category</b> <span>Shopify category</span></p>
                         <p><b>Product Tags</b> <span>Best, New</span></p>
						 <p><b>HSN Code</b> <span>0123456789</span></p>
                         <p><b>Date</b> <span>02/05/22</span></p>
                        </div>
                        <div class="order-items">
                          <p><b>Vendor Name</b> <span>Sumit jhon</span></p>
                          <p><b>Vendor Email</b> <span>customer@gmail.com</span></p>
                          <p><b>Store URL</b> <span>www.shopifyurl.com</span></p>
                        </div>
                        </div>
                         
                      </div>
                    </div>     
                 </div>
                
            </div>
          </div>
    </section>
   </main>
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