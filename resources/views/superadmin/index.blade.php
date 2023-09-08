@extends('layouts.superadmin')
@section('main')
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
          <p><b>Approval Summary</b></p>
          <div class="row">
              <div class="col-xxl-4 col-md-4">
			  <a href="{{url('superadmin/approved-products')}}/all">
                <div class="card info-card revenue-card">
                  <div class="card-body card-custom">
                  <div class="d-flex align-items-center">
                      <div class="ps-3">
                        <h5><b>{{$total_approval}}</b></h5>
                        <p>Total Approval</p>
                      </div>
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-bar-chart"></i>
                      </div>
                    </div>
                  </div>
                </div>
				</a>
              </div>
			  
              <div class="col-xxl-4 col-md-4">
			  <a href="{{url('superadmin/products')}}">
                <div class="card info-card revenue-card">
                  <div class="card-body card-custom">
                  <div class="d-flex align-items-center">
                      <div class="ps-3">
                        <h5><b>{{$total_pending_approval}}</b></h5>
                        <p>Pending Approval</p>
                      </div>
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-calendar3-range"></i>
                      </div>
                    </div>
                  </div>
                </div>
				</a>
              </div>
			  <div class="col-xxl-4 col-md-4">
			  <a href="{{url('superadmin/reject-products')}}">
                <div class="card info-card revenue-card">
                  <div class="card-body card-custom">
                  <div class="d-flex align-items-center">
                      <div class="ps-3">
                        <h5><b>{{$total_deny}}</b></h5>
                        <p>Total Deny</p>
                      </div>
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-bar-chart"></i>
                      </div>
                    </div>
                  </div>
                </div>
				</a>
              </div>
			  
              </div>
              <div class="row">
              <div class="col-xxl-4 col-md-4">
			  <a href="{{url('superadmin/approved-products')}}/today">
                <div class="card info-card revenue-card">
                  <div class="card-body card-custom">
                  <div class="d-flex align-items-center">
                    <div class="ps-3">
                      <h5><b>{{$total_today_approval}}</b></h5>
                      <p>Today's Approval</p>
                    </div>
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-calendar4"></i>
                    </div>
                    </div>
                  </div>
                </div>
				</a>
              </div>
              <div class="col-xxl-4 col-md-4">
			  <a href="{{url('superadmin/approved-products')}}/week">
                <div class="card info-card revenue-card">
                  <div class="card-body card-custom">
                  <div class="d-flex align-items-center">
                      <div class="ps-3">
                        <h5><b>{{$total_weekly_approval}}</b></h5>
                        <p>Weekly Approval</p>
                      </div>
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-border-width"></i>
                      </div>
                    </div>
                  </div>
                </div>
				</a>
              </div>
              <div class="col-xxl-4 col-md-4">
			  <a href="{{url('superadmin/approved-products')}}/month">
                <div class="card info-card revenue-card">
                  <div class="card-body card-custom">
                  <div class="d-flex align-items-center">
                      <div class="ps-3">
                        <h5><b>{{$total_month_approval}}</b></h5>
                        <p>Monthly Approval</p>
                      </div>
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-border-width"></i>
                      </div>
                    </div>
                  </div>
                </div>
				</a>
              </div>
          </div>

          <div class="row">
            <p><b>Stock Summary</b></p>
            <div class="col-xxl-6 col-md-6">
			<a href="{{url('superadmin/out-of-stock')}}">
              <div class="card info-card revenue-card">
                <div class="card-body card-custom">
                <div class="d-flex align-items-center">
                  <div class="ps-3">
                    <h5><b>{{$total_out_of_stock}}</b></h5>
                    <p>Total Out of Stock Items</p>
                  </div>
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-clock-history"></i>
                  </div>
                  </div>
                </div>
              </div>
			  </a>
            </div>
          </div>
          </div>
           
           <div class="col-md-12">
            <P><b>Out of Stock Items</b></P>
            <div class="card-body show-plan collections">
              <!-- Bordered Table -->
              <div class="table-responsive">
              <table class="table table-bordered table-white">
                <thead>
                  <tr>
                    <th scope="col">S.No</th>
                    <th scope="col">Vendor Name</th>
                    <th scope="col">Vendor Email</th>
                    <th scope="col">Total Products</th>
                    <th scope="col">Store Status</th>
                    <th scope="col">Action</th>
                  </tr>
                </thead>
                <tbody>
                @foreach($data as $k=>$row)
                  <tr>
                    <td>{{$k+1}}</td>
                    <td>{{$row->name}}</td>
                    <td>{{$row->email}}</td>
                    <td>{{$row->products}}</td>
                    <td>{{$row->status}}</td>
                    <td>
                      <span class="form-switch">
                      <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault_{{$row->id}}" onclick="changeStoreStatus({{$row->id}})" @if($row->status=='Active') {{'checked'}} @endif>
                     </span>
                    </td>
                  </tr>
                @endforeach  
                
                </tbody>
              </table>
              </div>
              <!-- End Bordered Table -->
            </div>
           </div>
       
        </div>
      </div>
    </section>
   </main>
  <!-- End #main -->
  <!-- ======= Footer ======= -->
  @endsection
 <script>
     function changeStoreStatus(id)
     {
        var v_token = "{{csrf_token()}}";       
        $.ajax({
                type:'post',
                data:{id : id},
                headers: {'X-CSRF-Token': v_token},
                url:"{{ route('superadmin.change-vendor-status') }}",
                success:function(response){
                    var json = $.parseJSON(response);
                    if(json.status=='success')
                    {
                        alert("Status Updated Successfully!!")
                    }
                }
            });
     }
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