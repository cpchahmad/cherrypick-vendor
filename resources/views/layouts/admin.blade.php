<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Cherrypick</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="{{url('subadmin/assets/img/favicon1.png')}}" rel="icon">
  <link href="{{url('subadmin/assets/img/apple-touch-icon.png')}}" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="{{url('subadmin/assets/vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
  <link href="{{url('subadmin/assets/vendor/bootstrap-icons/bootstrap-icons.css')}}" rel="stylesheet">
  <link href="{{url('subadmin/assets/vendor/datepicker/datepicker.css')}}" rel="stylesheet">

  <link href="{{url('subadmin/assets/vendor/quill/quill.snow.css')}}" rel="stylesheet">
  <link href="{{url('subadmin/assets/vendor/quill/quill.bubble.css')}}" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="{{url('subadmin/assets/css/style.css')}}" rel="stylesheet">
  <link href="{{url('subadmin/assets/css/custom.css')}}" rel="stylesheet">

  <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>

    <link rel="stylesheet" href="{{asset('richtexteditor/rte_theme_default.css')}}">
</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="{{route('home')}}" class="logo d-flex align-items-center">
        <img src="{{url('subadmin/assets/img/logo-shopping.png')}}" alt="">
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
            @if(Auth::user()->role=='Vendor')
           <li>
              <a class="dropdown-item d-flex align-items-center" href="{{route('admin.editprofile')}}">
                <i class="bi bi-person"></i>
                <span>My Profile</span>
              </a>
            </li>
           <li>
                <hr class="dropdown-divider">
              </li>
             @endif
            <li>
              <a class="dropdown-item d-flex align-items-center" href="{{route('admin.logout')}}">
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
        <a class="nav-link " href="{{route('home')}}">
          <i class="bi bi-house-door"></i>
          <span>{{Auth::user()->name}}-Dashboard</span>
        </a>
      </li>
      @if(session('store_configuration')==1 || Auth::user()->role=='Vendor')
      <li class="nav-item">
        <a class="nav-link @if(request()->is('admin/general-config*') || request()->is('admin/store-front*') || request()->is('admin/payment-configuration*')) {{''}} @else {{'collapsed'}} @endif" data-bs-target="#setting-nav" data-bs-toggle="collapse" href="#" aria-expanded="true">
          <i class="bi bi-gear"></i><span>Store Configuration</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="setting-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="{{route('admin.generalconfig')}}" {{request()->is('admin/general-config*')?'active':''}}>
              <span>General Configuration</span>
            </a>
          </li>
          <li>
            <a href="{{route('admin.storefront')}}" {{request()->is('admin/store-front*')?'active':''}}>
              <span>Store Front</span>
            </a>
          </li>
          <li>
            <a href="{{route('admin.paymentconfig')}}" {{request()->is('admin/payment-configuration*')?'active':''}}>
              <span>Payment Configuration</span>
            </a>
          </li>
        </ul>
      </li>
      @endif
      @if(session('products')==1 || Auth::user()->role=='Vendor')
      <li class="nav-item">
        <a class="nav-link @if(request()->is('add-product*') || request()->is('edit-product/*') || request()->is('edit-variant/*') || request()->is('product-list*') || request()->is('out-of-stock*') || request()->is('importProduct*') || request()->is('exportProductView*') || request()->is('category*')) {{''}} @else {{'collapsed'}} @endif" data-bs-target="#setting-nav1" data-bs-toggle="collapse" href="#" aria-expanded="true">
          <i class="bi bi-archive"></i><span>Products</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="setting-nav1" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="{{route('add-product')}}">
              <span>Add Product</span>
            </a>
          </li>
          <li>
            <a href="{{route('product-list')}}">
              <span>View Products</span>
            </a>
          </li>
          <li>
            <a href="{{url('out-of-stock')}}">
              <span>Out of Stock Product</span>
            </a>
          </li>
          <li>
            <a href="{{url('importProduct')}}">
              <span>Bulk Import</span>
            </a>
          </li>
          <li>
            <a href="{{url('exportProductView')}}">
              <span>Bulk Export</span>
            </a>
          </li>
		   <li>
            <a href="{{url('category')}}">
              <span>Category</span>
            </a>
          </li>
        </ul>
      </li>
      @endif
      @if(session('orders')==1 || Auth::user()->role=='Vendor')
{{--      <li class="nav-item">--}}
{{--        <a class="nav-link  @if(request()->is('orders*') || request()->is('new-orders*') || request()->is('pickup-orders*') || request()->is('complete-orders*') || request()->is('order-details*')) {{''}} @else {{'collapsed'}} @endif" data-bs-target="#order-nav" data-bs-toggle="collapse" href="#">--}}
{{--          <i class="bi bi-cart3"></i><span>Orders</span><i class="bi bi-chevron-down ms-auto"></i>--}}
{{--        </a>--}}
{{--        <ul id="order-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">--}}
{{--          <li>--}}
{{--            <a href="{{url('orders')}}">--}}
{{--              <span>All Order</span>--}}
{{--            </a>--}}
{{--          </li>--}}
{{--          <li>--}}
{{--            <a href="{{url('new-orders')}}">--}}
{{--              <span>New Order</span>--}}
{{--            </a>--}}
{{--          </li>--}}
{{--          <li>--}}
{{--            <a href="{{url('pickup-orders')}}">--}}
{{--              <span>Orders ready for pickup</span>--}}
{{--            </a>--}}
{{--          </li>--}}
{{--          <li>--}}
{{--            <a href="{{url('complete-orders')}}">--}}
{{--              <span>Complete Orders</span>--}}
{{--            </a>--}}
{{--          </li>--}}
{{--        </ul>--}}
{{--      </li>--}}

            <li class="nav-item">
                <a class="nav-link collapsed" href="{{url('orders')}}">
                    <i class="bi bi-cart3"></i>
                    <span>Orders</span>
                </a>
            </li>
      @endif
      @if(session('marketing')==1 || Auth::user()->role=='Vendor')
      <li class="nav-item">
        <a class="nav-link  @if(request()->is('manage-discount*') || request()->is('manage-product-discount*') || request()->is('product-add-discount*') || request()->is('add-discount*') || request()->is('admin/banner*')) {{''}} @else {{'collapsed'}} @endif" data-bs-target="#marketing-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-megaphone"></i><span>Marketing</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="marketing-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
		<li>
            <a href="{{url('manage-product-discount')}}">
              <span>Products Discount</span>
            </a>
          </li>
          <!--<li>
            <a href="{{url('manage-discount')}}">
              <span>Discount Code</span>
            </a>
          </li>	-->
          <li>
            <a href="{{route('admin.banner')}}">
              <span>Manage Banner</span>
            </a>
          </li>
        </ul>
      </li>
      @endif
      @if(Auth::user()->role=='Vendor')
      <li class="nav-item">
         <a class="nav-link " href="{{route('admin.document')}}">
          <i class="bi bi-file-earmark-bar-graph"></i>
          <span>Documents</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link @if(request()->is('users/create*') || request()->is('users*') || request()->is('users-edit/*') || request()->is('user-role*') || request()->is('role-edit/*') || request()->is('user-role-create*')) {{''}} @else {{'collapsed'}} @endif" data-bs-target="#userrole-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-people"></i><span>User Roles</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="userrole-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="{{route('users.create')}}">
              <span>Add User</span>
            </a>
          </li>
          <li>
            <a href="{{route('users.index')}}">
              <span>View User</span>
            </a>
          </li>
          <li>
            <a href="{{route('user-role')}}">
              <span>User Role</span>
            </a>
          </li>
        </ul>
      </li>
      @endif
    </ul>

  </aside><!-- End Sidebar-->
   @yield('main')

  <!-- End #main -->
  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="copyright">
        Copyright © 2023,<a target="_blank" href="https://www.cherrypick.city">Cherrypick.city</a>
    </div>
  </footer>
  <!-- End Footer -->


  <!-- Vendor JS Files -->
  <script src="{{url('subadmin/assets/vendor/apexcharts/apexcharts.min.js')}}"></script>
  <script src="{{url('subadmin/assets/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
  <script src="{{url('subadmin/assets/vendor/datepicker/datepicker.js')}}"></script>
  <script src="{{url('subadmin/assets/vendor/chart.js/chart.min.js')}}"></script>
  <script src="{{url('subadmin/assets/vendor/echarts/echarts.min.js')}}"></script>
  <script src="{{url('subadmin/assets/vendor/quill/quill.min.js')}}"></script>

 <script src="{{url('subadmin/assets/vendor/tinymce/tinymce.min.js')}}"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
<script src="{{url('subadmin/assets/js/main.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>

  <script src="{{asset('richtexteditor/rte.js')}}"></script>

 <script>
      $(document).ready(function() {
      $("#e2").select2({
          theme: "classic"
      });
      $("#e3").select2({
         ajax: {
          type: 'get',
          url: "{{url('testevent')}}",
          delay: 300,
          dataType: 'json',
          data: function (params) {
            return {
              search: params.term
            };
          },
          processResults: function (data) {
              //alert(data);
            return {
              results: $.map(data, function (obj) {
                return {
                  id: obj.id,
                  text: obj.text,
                };
              })
            };
          }
        },
        minimumInputLength: 2,
        placeholder: "Please Select",
        escapeMarkup: function (m) {
          return m;
        }
      });
    });
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
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>


  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.js" integrity="sha512-VvWznBcyBJK71YKEKDMpZ0pCVxjNuKwApp4zLF3ul+CiflQi6aIJR+aZCP/qWsoFBA28avL5T5HA+RE+zrGQYg==" crossorigin="anonymous"></script>


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
