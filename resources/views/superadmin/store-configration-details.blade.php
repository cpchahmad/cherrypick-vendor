@extends('layouts.superadmin')

  <main id="main" class="main">
    <div class="subpagetitle fit-title">
        <h1>Store Configuration</h1>
         <p><a href="{{url('superadmin/store-configuration')}}">Store Configuration</a> / <b>Store Configuration Details</b></p>
      </div>
   
    <section class="section dashboard">
      <div class="row">
        <div class="col-12 ">
            <div class="card fullorders">
              <div class="row ">
                  <div class="col-12">
                    <div class="mb-3">
                      <a class="btn btn-warning btn-sm" href="{{route('superadmin.store-configuration')}}">Back</a>
                    </div>
<!--                    <div class="details-approved">
                        <a href="#" class="checka"><i class="bi bi-check2"></i></a>
                        <a href="#" class="removex"><i class="bi bi-x"></i></a>
                    </div>-->
                     <h4>General Configuration</h4>
                     <div class="order-summry ">
                    <div class="order-items">
                      <form class="add-product-form" method="post" action="{{route('genralconfiguration.update',$data->id)}}">
                          @csrf
                          <div class="gen-config">
                            <div class="row">
                              <div class="col-12">
                                <div class="row">
                                  <div class="col-12 field">
                                    <label for="inputNanme4" class="form-label">Email Id</label>
                                    <input type="email" class="form-control" id="" value="{{$data->email}}" placeholder="Enter Email" name="email">
                                    <span style="color:red;">
                                      @error('email')
                                        <span style="color:red;">Please enter valid email address.</span>
                                      @enderror
                                    </span>
                                  </div>
                                  <div class="col-12 field">
                                    <label for="inputNanme4" class="form-label">Contact Number</label>
                                    <input type="text" class="form-control" id="mobile" placeholder="Enter Mobile Number" name="mobile" value="{{$data->mobile}}">
                                  <span style="color:red;">
                                    @error('mobile')
                                      {{$message}}
                                    @enderror
                                  </span>
                                </div>
                              </div>
					                    <div class="row">
                                <div class="col-12 field">
                                  <label for="inputNanme4" class="form-label">Address </label>
                                  <input type="text" class="form-control" id="" placeholder="Enter Address" name="address" value="{{$data->address}}">
                                  <span style="color:red;">
                                    @error('address')
                                      {{$message}}
                                    @enderror
                                  </span>
                                </div>
                              </div>
                              <div class="mt-3">
                                  <button type="submit" class="btn btn-primary">Update</button>
                              </div>
                            </div>
                          </div>
                        <!-- <p><b>Email Id</b> <span>{{$data->email}}</span></p>
                        <p><b>Contact Number</b> <span>{{$data->mobile}}</span></p>
                        <p><b>Address</b> <span>{{$data->address}}</span></p> -->
                      </form>
                    </div>
                    </div>
                  </div>
                </div>     
             </div>
              <div class="card fullorders">
              <div class="row ">
                  <div class="col-12">
                      <h4>Update Product Tags</h4>
                      <div class="order-summry">
                        <form action="{{route('update.vendor.tag',$data->id)}}" method="POST">
                          @csrf
                          <div class="mb-2">
                            <input type="text" class="form-control" value="" name="tags" placeholder="Add Tags">
                            @error('tags')
                                <div class="error text-danger">{{ $message }}</div>
                            @enderror
                          </div>
                          <button type="submit" class="btn btn-danger btn-sm">Add Tag</button>
                        </form>
                      </div>
                  </div>
                </div>     
             </div>
             <div class="card fullorders">
                <div class="row ">
                    <div class="col-12">
<!--                        <div class="details-approved">
                            <a href="#" class="checka"><i class="bi bi-check2"></i></a>
                            <a href="#" class="removex"><i class="bi bi-x"></i></a>
                        </div>-->
                       <h4>Store Front Configuration</h4>
                       <div class="order-summry">
                       <form class="add-product-form" method="post" action="{{route('storeFront.update',$data->id)}}" enctype="multipart/form-data">
                        @csrf
                          <div class="store-config">
                                  <div class="row">
                                  <div class="col-12">
                        @if($data->logo!='')
                          <div class="col-3"><img src="{{asset('uploads/logo/'.$data->logo)}}" height="120px" width='120px'></div>
                        @endif	
                        <label for="inputNanme4" class="form-label">Store Logo</label>
                          <input type="file" class="form-control" accept="image/png, image/gif, image/jpeg, image/jpg" name="logo">
                          <span style="color:red;">
                          @error('logo')
                            {{$message}}
                          @enderror
                          </span>
                        </div>
                        <div class="col-12">
                          <label for="inputNanme4" class="form-label">About the Store </label>
                          <textarea class="form-control" name="about_store" id="" placeholder="About the Store ">{{$data->about_store}}</textarea>
                          <span style="color:red;">
                            @error('about_store')
                              {{$message}}
                            @enderror
                          </span>
                       </div>
                       <div class="col-12">
                        <label for="inputNanme4" class="form-label">What products does the store carry?</label>
                        <textarea name="store_carry" class="form-control" id="" placeholder="What products does the store carry?">{{$data->store_carry}}</textarea>
                       </div>
                       <span style="color:red;">
                         @error('store_carry')
                          {{$message}}
                        @enderror
                     </span>
                    </div>
                  </div>
                  <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Update</button>
                  </div>
                </form>
                      <!-- <div class="order-items">
                       <p><b>Store Logo </b> <span><img src="{{asset('/uploads/logo/'.$data->logo)}}"></span></p>
                       <p><b>About the Store</b> <span>{{$data->about_store}}.</span></p>
                       <p><b>What products does the store carry?</b> <span>{{$data->store_carry}}</span></p>
                      </div> -->
                      </div>
                    </div>
                  </div>     
               </div>
               <div class="card fullorders">
                <div class="row ">
                    <div class="col-12">
<!--                        <div class="details-approved">
                            <a href="#" class="checka"><i class="bi bi-check2"></i></a>
                            <a href="#" class="removex"><i class="bi bi-x"></i></a>
                        </div>-->
                       <h4>Payment Configuration</h4>
                       <div class="order-summry">
                       @if($payment)
                       <form class="add-product-form edit-back-info" method="post" action="{{route('paymentDetails.update',$payment->id)}}">
                          @csrf
                          <div class="">
              <h5>Edit Bank Info</h5>
                     <div class="row">
                    <div class="col-6">
                        <label for="inputNanme4" class="form-label">Account No</label>
                        <input type="text" class="form-control" id="account" value="@if(isset($payment->account_no)){{$payment->account_no}} @else {{old('account_no')}} @endif" placeholder="Enter account No" name="account_no">
                        <span style="color:red;">
                         @error('account_no')
						 {{$message}}
                        @enderror
                     </span>
                       </div>
                    <div class="col-6">
                        <label for="inputNanme4" class="form-label">Bank Name</label>
                        <input type="text" class="form-control" id="name" placeholder="Enter bank name" name="bank_name" value="@if(isset($payment->bank_name)){{$payment->bank_name}} @else {{old('name')}} @endif">
                        <span style="color:red;">
                         @error('bank_name')
                          {{$message}}
                        @enderror
                     </span>
                       </div>
                       <div class="col-6">
                        <label for="inputNanme4" class="form-label">IFSC</label>
                        <input type="text" class="form-control" id="" placeholder="Enter IFSC" name="ifsc" value="@if(isset($payment->ifsc)){{$payment->ifsc}}@endif">
                        <span style="color:red;">
                         @error('ifsc')
                          {{$message}}
                        @enderror
                     </span>
                       </div>
                       <div class="col-6">
                        <label for="inputNanme4" class="form-label">GST</label>
                        <input type="text" class="form-control" id="" placeholder="Enter GST" name="gst" value="@if(isset($payment->gst)){{$payment->gst}} @else {{old('gst')}} @endif">
                        <span style="color:red;">
                         @error('gst')
                          {{$message}}
                        @enderror
                     </span>
                       </div>
                         <div class="col-6">
                        <label for="inputNanme4" class="form-label">Account Type</label>
                        <select class="form-control" name='account_type'>
                            <option value="current"@if(isset($payment->account_type) && $payment->account_type=='current'){{'selected'}}@endif>Current</option>
                            <option value="saving"@if(isset($payment->account_type) && $payment->account_type=='saving'){{'selected'}}@endif>Saving</option>
                        </select>
                        <span style="color:red;">
                         @error('account_type')
                          {{$message}}
                        @enderror
                     </span>
                       </div>
                       <div class="col-12">
                        <label for="inputNanme4" class="form-label">Address</label>
                        <textarea name="address" class="form-control" id="" value="" placeholder="Address">@if(isset($payment->address)){{$payment->address}} @else {{old('address')}} @endif</textarea>
                        <span style="color:red;">
                         @error('address')
                          {{$message}}
                        @enderror
                     </span>
                       </div>
                    </div>
            </div>
            <div class="mt-3">
              <!-- <button type="reset" class="btn btn-secondary">Cancel</button> -->
              <button type="submit" class="btn btn-primary">Update</button>
           </div>
</form>
                       <!-- <div class="order-items">
                        <p><b>Account No  </b><span>{{$payment->account_no}}</span></p>
                        <p><b>Bank Name  </b><span>{{$payment->bank_name}}</span></p>
                        <p><b>IFSC  </b><span>{{$payment->ifsc}}</span></p>
                        <p><b>GST  </b><span>{{$payment->gst}}</span></p>
                        <p><b>Bank Address  </b><span>{{$payment->address}}</span></p>
                      </div> -->
                      @elseif($payment=='')
                       <div class="order-items">
                        <p><b>Account No  </b><span></span></p>
                        <p><b>Bank Name  </b><span></span></p>
                        <p><b>IFSC </b><span></span></p>
                        <p><b>GST  </b><span></span></p>
                        <p><b>Bank Address  </b><span></span></p>
                      </div>

                      @endif
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
