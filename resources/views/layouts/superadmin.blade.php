<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Cherrypick</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="{{url('superadmin/assets/img/favicon1.png')}}" rel="icon">
  <link href="{{url('superadmin/assets/img/apple-touch-icon.png')}}" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="{{url('superadmin/assets/vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
  <link href="{{url('superadmin/assets/vendor/bootstrap-icons/bootstrap-icons.css')}}" rel="stylesheet">
  <link href="{{url('superadmin/assets/vendor/datepicker/datepicker.css')}}" rel="stylesheet">

  <link href="{{url('superadmin/assets/vendor/quill/quill.snow.css')}}" rel="stylesheet">
  <link href="{{url('superadmin/assets/vendor/quill/quill.bubble.css')}}" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="{{url('superadmin/assets/css/style.css')}}" rel="stylesheet">
  <link href="{{url('superadmin/assets/css/custom.css')}}" rel="stylesheet">


    <link rel="stylesheet" href="{{asset('dist/css/dropify.min.css')}}">
</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="index.html" class="logo d-flex align-items-center">
        <img src="{{url('superadmin/assets/img/logo-shopping.png')}}" alt="">
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <nav class="header-nav ms-auto">
     <ul class="d-flex align-items-center">
        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile dropdown-toggle d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
           <i class="front-user bi bi-person-circle"></i>
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
           <li>
                <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="{{url('logout')}}">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sign Out</span>
              </a>
            </li>

          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->
      </ul>
    </nav><!-- End Icons Navigation -->
  </header><!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link " href="{{route('superadmin.home')}}">
          <i class="bi bi-house-door"></i>
          <span>Dashboard</span>
        </a>
      </li>
      <!--<a href="{{route('change-status')}}">change</a>-->
      <li class="nav-item">
        <a class="nav-link @if(request()->is('superadmin/products*') || request()->is('superadmin/products-details/*') || request()->is('superadmin/stores-products')  || request()->is('superadmin/banner*') || request()->is('superadmin/store-configuration*') || request()->is('superadmin/documents*') || request()->is('superadmin/store-amount*')) {{''}} @else {{'collapsed'}} @endif" data-bs-target="#setting-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-archive"></i><span>Approval Requests</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="setting-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="{{url('superadmin/products')}}">
              <span>Catalog</span>
            </a>
          </li>
          <li>
            <a href="{{url('superadmin/banner')}}">
              <span>Banner</span>
            </a>
          </li>
			<li>
            <a href="{{url('superadmin/stores-products')}}">
              <span>Store Products</span>
            </a>
          </li>
{{--          <li>--}}
{{--            <a href="{{url('superadmin/store-configuration')}}">--}}
{{--              <span>Store Configuration</span>--}}
{{--            </a>--}}
{{--          </li>--}}
		  <li>
            <a href="{{url('superadmin/store-amount')}}">
              <span>Store/Vendors Payment</span>
            </a>
          </li>
          <li>
            <a href="{{route('documents')}}">
              <span>Signed Documents</span>
            </a>
          </li>
        </ul>
      </li>
      <li class="nav-item">
         <a class="nav-link collapsed" href="{{url('superadmin/out-of-stock')}}">
          <i class="bi bi-bag"></i>
          <span>Out of Stock Items</span>
        </a>
      </li>

        <li class="nav-item">
            <a class="nav-link collapsed" href="{{url('superadmin/orders')}}">
                <i class="bi bi-cart3"></i>
                <span>Orders</span>
            </a>
        </li>
	  <li class="nav-item">
        <a class="nav-link @if(request()->is('superadmin/conversion-rate*') || request()->is('superadmin/shipingchagres/*')) {{''}} @else {{'collapsed'}} @endif" data-bs-target="#setting-nav1" data-bs-toggle="collapse" href="#">
          <i class="bi bi-archive"></i><span>Price&Shiping Setting</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="setting-nav1" class="nav-content collapse " data-bs-parent="#sidebar-nav1">
          <li>
            <a href="{{url('superadmin/conversion-rate')}}">
              <span>Price Conversion Rate</span>
            </a>
          </li>
          <li>
            <a href="{{url('superadmin/shipingchagres/1')}}">
              <span>USA Market</span>
            </a>
          </li>
			<li>
            <a href="{{url('superadmin/shipingchagres/2')}}">
              <span>UK Market</span>
            </a>
          </li>
		<li>
            <a href="{{url('superadmin/shipingchagres/3')}}">
              <span>NLD Market</span>
            </a>
          </li>
		<li>
            <a href="{{url('superadmin/shipingchagres/4')}}">
              <span>IND Market</span>
            </a>
          </li>
		<li>
            <a href="{{url('superadmin/shipingchagres/5')}}">
              <span>CAD Market</span>
            </a>
          </li>
		<li>
            <a href="{{url('superadmin/shipingchagres/6')}}">
              <span>AUD Market</span>
            </a>
          </li>
		<li>
            <a href="{{url('superadmin/shipingchagres/7')}}">
              <span>Ireland Market</span>
            </a>
          </li>
		<li>
            <a href="{{url('superadmin/shipingchagres/8')}}">
              <span>Germany Market</span>
            </a>
          </li>
        </ul>
      </li>


        <li class="nav-item @if(request()->is('superadmin/vendors')) active @endif">
            <a class="nav-link collapsed" href="{{url('superadmin/vendors')}}">
                <i class="bi bi-bag"></i>
                <span>Stores</span>
            </a>
        </li>

        <li class="nav-item @if(request()->is('superadmin/logs')) active @endif">
            <a class="nav-link collapsed" href="{{url('superadmin/logs')}}">
                <i class="bi bi-bag"></i>
                <span>Logs</span>
            </a>
        </li>
        <li class="nav-item @if(request()->is('superadmin/settings')) active @endif">
            <a class="nav-link collapsed" href="{{url('superadmin/settings')}}">
                <i class="bi bi-bag"></i>
                <span>Settings</span>
            </a>
        </li>
    </ul>

  </aside><!-- End Sidebar-->
  @yield('main')


  <!-- End #main -->
  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>Cherrypick</span></strong>. All Rights Reserved
    </div>
  </footer>
  <!-- End Footer -->


  <!-- Vendor JS Files -->
  <script src="{{url('superadmin/assets/vendor/apexcharts/apexcharts.min.js')}}"></script>
  <script src="{{url('superadmin/assets/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
  <script src="{{url('superadmin/assets/vendor/datepicker/datepicker.js')}}"></script>
  <script src="{{url('superadmin/assets/vendor/chart.js/chart.min.js')}}"></script>
  <script src="{{url('superadmin/assets/vendor/echarts/echarts.min.js')}}"></script>
  <script src="{{url('superadmin/assets/vendor/quill/quill.min.js')}}"></script>

  <script src="{{url('superadmin/assets/vendor/tinymce/tinymce.min.js')}}"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
  <script src="{{asset('dist/js/dropify.min.js')}}"></script>

  <!-- Template Main JS File -->
 <script src="{{url('superadmin/assets/js/main.js')}}"></script>
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




<script>


$('.decimal').keyup(function(){
    var val = $(this).val();
    if(isNaN(val)){
         val = val.replace(/[^0-9\.]/g,'');
         if(val.split('.').length>2)
             val =val.replace(/\.+$/,"");
    }
    $(this).val(val);
});

function dismissModal(){
$('#updateModal').modal('hide');

}
$(document).ready(function(){

$(".submit").click(function(){
var id= $(this).attr("id");
var shipping_weight=$(this).attr("shipping");

var title= $('#title').val();
var body= $('#body_html').val();
var product_id= $('#product_id').val();


$('#updateModal').modal('show');
$('#variant_id').val(id);
$('#shipping_weight').val(shipping_weight);
$('#title_val').val(title);
$('#body_val').val(body);
$('#product_val').val(product_id);


});
});
</script>


  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
  <script>
      toastr.options =
          {
              "closeButton" : true,
              "progressBar" : false,
              "positionClass": "toast-bottom-center",

          }
      @if(Session::has('success'))

      toastr.success("{{ session('success') }}");
      @endif

          @if(Session::has('error'))

      toastr.error("{{ session('error') }}");
      @endif

          @if(Session::has('info'))

      toastr.info("{{ session('info') }}");
      @endif

          @if(Session::has('warning'))

      toastr.warning("{{ session('warning') }}");
      @endif
  </script>
@yield('js')
</body>

</html>
