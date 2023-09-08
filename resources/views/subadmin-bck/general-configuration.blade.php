@extends('layouts.admin')

  <main id="main" class="main">
   <div class="home-flex">
    <div class="pagetitle">
       <h1>General Configuration</h1>
    </div><!-- End Page Title -->
   </div>
   @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                       <h5>{{ $message }}</h5>
                     </div>
                 @endif
     
    <section class="section up-banner">
        <form class="add-product-form" method="post" action="{{route('post.generalconfig')}}">
          @csrf
            <div class="card">
                <div class="row">
                    <div class="col-12">
                     <div class="row">
                    <div class="col-6">
                        <label for="inputNanme4" class="form-label">Email Id</label>
                        <input type="email" class="form-control" id="" value="" placeholder="enter email Id" name="emailid">
                        <span style="color:red;">
                         @error('emailid')
                          {{$message}}
                        @enderror
                     </span>
                       </div>
                    <div class="col-6">
                        <label for="inputNanme4" class="form-label">Contact Number</label>
                        <input type="text" class="form-control" id="" placeholder="enter mobile number" name="mobile">
                        <span style="color:red;">
                         @error('mobile')
                          {{$message}}
                        @enderror
                     </span>
                       </div>
                    </div>
                </div>
               </div>
            </div>
            <div class="timer-btns pro-submit">
              <button type="submit" class="btn btn-primary">Submit</button>
           </div>
        </form>
    </section>
   </main>
  <!-- End #main -->
  <!-- ======= Footer ======= -->
  