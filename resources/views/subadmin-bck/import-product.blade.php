@extends('layouts.admin')

  <main id="main" class="main">
   <div class="home-flex">
    <div class="pagetitle">
       <h1>Import Bulk Products</h1>
    </div><!-- End Page Title -->
   </div>
    @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                       <h5>{{ $message }}</h5>
                     </div>
                 @endif
     
    <section class="section up-banner">
        <form class="add-product-form" method="post" action="{{url('import')}}" enctype="multipart/form-data">
          @csrf
            <div class="card store-config">
                     <div class="row">
                    <div class="col-12">
                        <label for="inputNanme4" class="form-label">Select Excel File</label>
                        <input type="file" class="form-control" name="file">
                        <span style="color:red;">
                         @error('logo')
                          {{$message}}
                        @enderror
                     </span>
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
  