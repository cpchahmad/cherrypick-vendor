@extends('layouts.superadmin')

  <main id="main" class="main">
   <div class="home-flex">
    <div class="pagetitle">
       <h1>Out of Stock Items</h1>
    </div><!-- End Page Title -->
   </div>
   
    <section class="section up-banner">
      <div class="row">
        <P><b>Out of Stock Items</b></P>
       <div class="col-md-12">
        <div class="card-body show-plan collections">
          <!-- Bordered Table -->
          <div class="table-responsive">
          <table class="table table-bordered table-white">
            <thead>
              <tr>
                <th scope="col">S.No</th>
                <th scope="col">Vendor Name</th>
                <th scope="col">Vendor Email</th>
                <th scope="col">Store URL</th>
                <th scope="col">Number of Out of Stock Items</th>
                <th scope="col">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>1</td>
                <td>Rohnl Dolly</td>
                <td>customer@gmail.com</td>
                <td><a href="#">wwww.storeurl.com</a></td>
                <td class="cnt-stock">23</td>
                <td class="icon-action">
                  <a href="view-vendor.html"><i class="bi bi-eye"></i></a>
                 
               </td>
              </tr>
              <tr>
                <td>2</td>
                <td>Sumit Jhon</td>
                <td>customer@gmail.com</td>
                <td><a href="#">wwww.storeurl.com</a></td>
                <td class="cnt-stock">03</td>
                <td class="icon-action">
                  <a href="view-vendor.html"><i class="bi bi-eye"></i></a>
               </td>
              </tr>
              <tr>
                <td>3</td>
                <td>Rohnl Dolly</td>
                <td>customer@gmail.com</td>
                <td><a href="#">wwww.storeurl.com</a></td>
                <td class="cnt-stock">22</td>
                <td class="icon-action">
                  <a href="view-vendor.html"><i class="bi bi-eye"></i></a>
               </td>
              </tr>
              <tr>
                <td>4</td>
                <td>Eleea Dolly</td>
                <td>customer@gmail.com</td>
                <td><a href="#">wwww.storeurl.com</a></td>
                <td class="cnt-stock">17</td>
                <td class="icon-action">
                  <a href="view-vendor.html"><i class="bi bi-eye"></i></a>
               </td>
              </tr>
            </tbody>
          </table>
          </div>
          <!-- End Bordered Table -->
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
