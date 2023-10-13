@extends('layouts.superadmin')
@section('main')
  <main id="main" class="main">
   <div class="home-flex">
    <div class="pagetitle">
       <h1>Import Bulk Products</h1>
    </div><!-- End Page Title -->
   </div>
		@if (count($errors) > 0)
		<div class="row">
		<div class="col-md-8 col-md-offset-1">
		<div class="alert alert-danger alert-dismissible">
		<h4><i class="icon fa fa-ban"></i> Error!</h4>
		@foreach($errors->all() as $error)
		{{ $error }} <br>
		@endforeach
		</div>
		</div>
		</div>
		@endif
		@if ($message = Session::get('success'))
		<div class="alert alert-success">
		<h5>{{ $message }}</h5>
		</div>
		@endif

    <section class="section up-banner">
        <form class="add-product-form" method="post" action="{{url('superadmin/uploade-bulk-products')}}" enctype="multipart/form-data">
          @csrf
            <div class="card store-config">
                     <div class="row">

                         <div class="col-12">
                             <label for="inputNanme4" class="form-label">Vendor Name</label>
                             <input type="text" class="form-control" placeholder="Venodr Name" name="username" required>
                             <span style="color:red;">
                      @error('username')
                                 {{$message}}
                                 @enderror
                     </span>
                         </div>

                         <div class="col-12">
                             <label for="inputNanme4" class="form-label">Password</label>
                             <input type="text" class="form-control" placeholder="Password" name="password" required>
                             <span style="color:red;">
                      @error('password')
                                 {{$message}}
                                 @enderror
                     </span>
                         </div>
                     </div>
                    <div class="col-12">
                        <label for="inputNanme4" class="form-label">Select Excel File</label>
                        <input type="file" class="form-control" name="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                        <span style="color:red;">
                         @error('file')
                          {{$message}}
                        @enderror
                     </span>
                        <a href="{{url('uploads/sample_file.xlsx')}}">Download Sample File.</a>
                       </div>
                    </div>
            </div>
            <div class="timer-btns pro-submit">
              <button type="submit" class="btn btn-primary">Submit</button>
           </div>
        </form>
    </section>
   </main>
@endsection


