@extends('layouts.admin')

  <main id="main" class="main">
    <div class="subpagetitle fit-title">
      <h1>Payment Configuration</h1>
       <p><a href="payment-configuration.html">Payment Configuration</a> / <b>Edit Bank Info</b></p>
    </div>
    @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                       <h5>{{ $message }}</h5>
                     </div>
                 @endif
     
    <section class="section up-banner">
        <form class="add-product-form edit-back-info" method="post" action="{{route('post.editpaymentconfig')}}">
          @csrf
            <div class="card">
              <h5>Edit Bank Info</h5>
                     <div class="row">
                    <div class="col-6">
                        <label for="inputNanme4" class="form-label">Account No</label>
                        <input type="text" class="form-control" id="" value="" placeholder="Enter account No" name="account">
                        <span style="color:red;">
                         @error('account')
                          {{$message}}
                        @enderror
                     </span>
                       </div>
                    <div class="col-6">
                        <label for="inputNanme4" class="form-label">Bank Name</label>
                        <input type="text" class="form-control" id="" placeholder="Enter bank name" name="name" value="">
                        <span style="color:red;">
                         @error('name')
                          {{$message}}
                        @enderror
                     </span>
                       </div>
                       <div class="col-6">
                        <label for="inputNanme4" class="form-label">IFSC</label>
                        <input type="text" class="form-control" id="" placeholder="Enter IFSC" name="ifsc" value="">
                        <span style="color:red;">
                         @error('ifsc')
                          {{$message}}
                        @enderror
                     </span>
                       </div>
                       <div class="col-6">
                        <label for="inputNanme4" class="form-label">GST</label>
                        <input type="text" class="form-control" id="" placeholder="Enter GST" name="gst" value="">
                        <span style="color:red;">
                         @error('gst')
                          {{$message}}
                        @enderror
                     </span>
                       </div>
                       <div class="col-12">
                        <label for="inputNanme4" class="form-label">Address</label>
                        <textarea name="address" class="form-control" id="" value="" placeholder="Address"></textarea>
                        <span style="color:red;">
                         @error('address')
                          {{$message}}
                        @enderror
                     </span>
                       </div>
                    </div>
            </div>
            <div class="timer-btns">
              <button type="reset" class="btn btn-secondary">Cancel</button>
              <button type="submit" class="btn btn-primary">Update</button>
           </div>
        </form>
    </section>
   </main>
  <!-- End #main -->
  <!-- ======= Footer ======= -->
  