<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Pages / Login - Cherrypick</title>
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

  <link href="{{url('subadmin/assets/vendor/quill/quill.snow.css')}}" rel="stylesheet">
  <link href="{{url('subadmin/assets/vendor/quill/quill.bubble.css')}}" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="{{url('subadmin/assets/css/style.css')}}" rel="stylesheet">
  <link href="{{url('subadmin/assets/css/custom.css')}}" rel="stylesheet">


</head>

<body>

  <main>
    <div class="container">

      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

              <div class="d-flex justify-content-center py-4">
                <a href="index.html" class="logo d-flex align-items-center w-auto">
                   <img src="assets/img/logo-shopping.png" alt="">

                </a>
              </div><!-- End Logo -->

              <div class="card mb-3">

                <div class="card-body">

                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Login to Your Account</h5>
                    <p class="text-center small">Enter your username & password to login</p>
                  </div>
                   @if ($message = Session::get('error'))
                    <div class="alert alert-danger">
                       <h5>{{ $message }}</h5>
                     </div>
                 @endif

                  <form class="row g-3 needs-validation" novalidate method="post" action="{{route('post.login')}}">
                    @csrf

                    <div class="col-12">
                      <label for="yourUsername" class="form-label">Username</label>
                      <div class="input-group has-validation">
                        <span class="input-group-text" id="inputGroupPrepend">@</span>
                        <input type="text" name="username" class="form-control" id="yourUsername" required>
                        <div class="invalid-feedback">Please enter your username.</div>
                      </div>
                      <span style="color:red;">
                      @error('username')
                      {{$message}}
                       @enderror
                     </span>
                    </div>

                    <div class="col-12">
                      <label for="yourPassword" class="form-label">Password</label>
                      <input type="password" name="password" class="form-control" id="yourPassword" required>
                      <div class="invalid-feedback">Please enter your password!</div>
                       <span style="color:red;">
                      @error('password')
                      {{$message}}
                       @enderror
                     </span>
                    </div>

                    <div class="col-12">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember_me" value="true" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">Remember me</label>
                      </div>
                    </div>
                    <div class="col-12">
                      <button class="btn btn-primary w-100" type="submit">Login</button>
                      <p class="small mb-0"><a href="{{route('forget.password.get')}}">Forgot Password</a></p>
                    </div>
                    <div class="col-12">
                      <p class="small mb-0">Don't have account? <a href="{{route('admin.register')}}">Create an account</a></p>
                    </div>
                  </form>

                </div>
              </div>

              <div class="credits">
                <!-- All the links in the footer should remain intact. -->
                <!-- You can delete the links only if you purchased the pro version. -->
                <!-- Licensing information: https://bootstrapmade.com/license/ -->
                <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
                  Copyright © 2023,<a target="_blank" href="https://www.cherrypick.city">Cherrypick.city</a>
              </div>

            </div>
          </div>
        </div>

      </section>

    </div>
  </main><!-- End #main -->


  <!-- Vendor JS Files -->
  <script src="{{url('subadmin/assets/vendor/apexcharts/apexcharts.min.js')}}"></script>
  <script src="{{url('subadmin/assets/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
  <script src="{{url('subadmin/assets/vendor/chart.js/chart.min.js')}}"></script>
  <script src="{{url('subadmin/assets/vendor/echarts/echarts.min.js')}}"></script>
  <script src="{{url('subadmin/assets/vendor/quill/quill.min.js')}}"></script>

 <script src="{{url('subadmin/assets/vendor/tinymce/tinymce.min.js')}}"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
  <script src="{{url('subadmin/assets/vendor/php-email-form/validate.js')}}"></script>

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

</body>

</html>
