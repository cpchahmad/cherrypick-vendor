@extends('layouts.admin')

  <main id="main" class="main">
   <div class="home-flex">
    <div class="pagetitle">
       <h1>Store Front</h1>
    </div><!-- End Page Title -->
   </div>
    @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                       <h5>{{ $message }}</h5>
                     </div>
                 @endif
     
    <section class="section up-banner">
        <form class="add-product-form" method="post" action="{{route('post.storefront')}}" enctype="multipart/form-data">
          @csrf
            <div class="card store-config">
                     <div class="row">
                    <div class="col-12">
                        <label for="inputNanme4" class="form-label">Store Logo</label>
                        <input type="file" class="form-control" id="" value="" placeholder="enter email Id" name="logo">
                        <span style="color:red;">
                         @error('logo')
                          {{$message}}
                        @enderror
                     </span>
                       </div>
                    <div class="col-12">
                        <label for="inputNanme4" class="form-label">About the Store </label>
                        <textarea class="form-control" name="about_store" id="" placeholder="About the Store "></textarea>
                        <span style="color:red;">
                         @error('about_store')
                          {{$message}}
                        @enderror
                     </span>
                       </div>
                       <div class="col-12">
                        <label for="inputNanme4" class="form-label">What products does the store carry?</label>
                        <textarea name="store_carry" class="form-control" id="" placeholder="What products does the store carry?"></textarea>
                       </div>
                       <span style="color:red;">
                         @error('store_carry')
                          {{$message}}
                        @enderror
                     </span>
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
  