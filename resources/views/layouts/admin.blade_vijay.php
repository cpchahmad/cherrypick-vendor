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
</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="index.html" class="logo d-flex align-items-center">
        <img src="{{url('subadmin/assets/img/logo-shopping.png')}}" alt="">
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->
  
    <nav class="header-nav ms-auto">
     <ul class="d-flex align-items-center">

        <li class="nav-item dropdown">

          <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
            <i class="bi bi-bell-fill"></i>
            <span class="badge bg-primary badge-number">0</span>
          </a><!-- End Notification Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
            <li class="dropdown-header">
            <b>Notifications</b>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="notification-item">
              <p>Quae dolorem earum veritatis oditseno</p>
                <p><span class="badge bg-primary badge-number show-number">0</span> 30 min. ago</p>
            </li>

            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="notification-item">
              <p>Quae dolorem earum veritatis oditseno</p>
                <p><span class="badge bg-primary badge-number show-number">0</span> 1 hr. ago</p>
            </li>

            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="notification-item">
              <p>Quae dolorem earum veritatis oditseno</p>
                <p><span class="badge bg-primary badge-number">0</span> 2 hr. ago</p>
            </li>

            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="notification-item">
              <p>Quae dolorem earum veritatis oditseno</p>
                <p><span class="badge bg-primary badge-number">0</span> 4 hr. ago</p>
            </li>

            <li>
              <hr class="dropdown-divider">
            </li>
            <li class="dropdown-footer">
              <a href="#">View More</a>
            </li>

          </ul><!-- End Notification Dropdown Items -->

        </li><!-- End Notification Nav -->

        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile dropdown-toggle d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
           <i class="front-user bi bi-person-circle"></i>
          </a><!-- End Profile Iamge Icon -->
           @php $vendorrole = session()->get('vendorrole');
           @endphp
          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            @if($vendorrole=='Vendor')
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
          @if($vendorrole=='Vendor')
            <li>
              <a class="dropdown-item d-flex align-items-center" href="{{route('admin.logout')}}">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sign Out</span>
              </a>
            </li>
            @else
            <li>
              <a class="dropdown-item d-flex align-items-center" href="{{route('post.userlogout')}}">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sign Out</span>
              </a>
            </li>
            @endif

          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->
      </ul>
    </nav><!-- End Icons Navigation -->
  </header><!-- End Header -->

  <!-- ======= Sidebar ======= -->
   @php $vendorrole = session()->get('vendorrole');
   @endphp
   @php $role = session()->get('role');

   @endphp
  <aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
      <li class="nav-item">
        <a class="nav-link " href="{{route('home')}}">
          <i class="bi bi-house-door"></i>
          <span>Dashboard</span>
        </a>
      </li>
      @if($vendorrole=='Vendor')
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#setting-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-gear"></i><span>Store Configuration</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="setting-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="{{route('admin.generalconfig')}}">
              <span>General Configuration</span>
            </a>
          </li>
          <li>
            <a href="{{route('admin.storefront')}}">
              <span>Store Front</span>
            </a>
          </li>
          <li>
            <a href="{{route('admin.paymentconfig')}}">
              <span>Payment Configuration</span>
            </a>
          </li>
        </ul>
      </li>
      @elseif($role->store_configuration==1)
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#setting-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-gear"></i><span>Store Configuration</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="setting-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="{{route('admin.generalconfig')}}">
              <span>General Configuration</span>
            </a>
          </li>
          <li>
            <a href="{{route('admin.storefront')}}">
              <span>Store Front</span>
            </a>
          </li>
          <li>
            <a href="{{route('admin.paymentconfig')}}">
              <span>Payment Configuration</span>
            </a>
          </li>
        </ul>
      </li>

      @endif
       @if($vendorrole=='Vendor')
        <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#setting-nav1" data-bs-toggle="collapse" href="#">
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
            <a href="out-of-stock-items.html">
              <span>Out of Stock Product</span>
            </a>
          </li>
          <li>
            <a href="bulk-import.html">
              <span>Bulk Import</span>
            </a>
          </li>
          <li>
            <a href="bulk-export.html">
              <span>Bulk Export</span>
            </a>
          </li>
        </ul>
      </li>
       @elseif($role->products ==1)
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#setting-nav1" data-bs-toggle="collapse" href="#">
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
            <a href="out-of-stock-items.html">
              <span>Out of Stock Product</span>
            </a>
          </li>
          <li>
            <a href="bulk-import.html">
              <span>Bulk Import</span>
            </a>
          </li>
          <li>
            <a href="bulk-export.html">
              <span>Bulk Export</span>
            </a>
          </li>
        </ul>
      </li>
      @endif
       @if($vendorrole=='Vendor')
       <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#order-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-cart3"></i><span>Orders</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="order-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="all-orders.html">
              <span>All Order</span>
            </a>
          </li>
          <li>
            <a href="new-order.html">
              <span>New Order</span>
            </a>
          </li>
          <li>
            <a href="orders-in-progress.html">
              <span>Orders in progress</span>
            </a>
          </li>
          <li>
            <a href="orders-ready-for-pickup.html">
              <span>Orders ready for pickup</span>
            </a>
          </li>
        </ul>
      </li>
       @elseif($role->orders==1)
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#order-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-cart3"></i><span>Orders</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="order-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="all-orders.html">
              <span>All Order</span>
            </a>
          </li>
          <li>
            <a href="new-order.html">
              <span>New Order</span>
            </a>
          </li>
          <li>
            <a href="orders-in-progress.html">
              <span>Orders in progress</span>
            </a>
          </li>
          <li>
            <a href="orders-ready-for-pickup.html">
              <span>Orders ready for pickup</span>
            </a>
          </li>
        </ul>
      </li>
      @endif
       @if($vendorrole=='Vendor')
       <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#marketing-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-megaphone"></i><span>Marketing</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="marketing-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="discount.html">
              <span>Discount</span>
            </a>
          </li>
          <li>
            <a href="{{route('admin.banner')}}">
              <span>Manage Banner</span>
            </a>
          </li>
        </ul>
      </li>

       @elseif($role->marketing ==1)
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#marketing-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-megaphone"></i><span>Marketing</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="marketing-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="discount.html">
              <span>Discount</span>
            </a>
          </li>
          <li>
            <a href="{{route('admin.banner')}}">
              <span>Manage Banner</span>
            </a>
          </li>
        </ul>
      </li>
      @endif
      @if($vendorrole=='Vendor')
      <li class="nav-item">
         <a class="nav-link collapsed" href="{{route('admin.document')}}">
          <i class="bi bi-file-earmark-bar-graph"></i>
          <span>Documents</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#userrole-nav" data-bs-toggle="collapse" href="#">
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

      @elseif($role=='')
      <li class="nav-item">
         <a class="nav-link collapsed" href="{{route('admin.document')}}">
          <i class="bi bi-file-earmark-bar-graph"></i>
          <span>Documents</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#userrole-nav" data-bs-toggle="collapse" href="#">
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

  </aside>
  <!-- End Sidebar-->
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
  <script src="{{url('subadmin/assets/vendor/apexcharts/apexcharts.min.js')}}"></script>
  <script src="{{url('subadmin/assets/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
  <script src="{{url('subadmin/assets/vendor/datepicker/datepicker.js')}}"></script>
  <script src="{{url('subadmin/assets/vendor/chart.js/chart.min.js')}}"></script>
  <script src="{{url('subadmin/assets/vendor/echarts/echarts.min.js')}}"></script>
  <script src="{{url('subadmin/assets/vendor/quill/quill.min.js')}}"></script>
  
 <script src="{{url('subadmin/assets/vendor/tinymce/tinymce.min.js')}}"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>

  <!-- Template Main JS File -->
   <script src="{{url('subadmin/assets/js/main.js')}}"></script>
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
 

</body>

</html>