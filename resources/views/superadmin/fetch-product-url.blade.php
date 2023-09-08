@extends('layouts.superadmin')
@section('main')
  <main id="main" class="main">
   <div class="home-flex">
    <div class="pagetitle">
       <h1>Product Fetch Form Url</h1>
    </div><!-- End Page Title -->
   </div>
   @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                       <h5>{{ $message }}</h5>
                     </div>
                 @endif
				 @if ($message = Session::get('error'))
                    <div class="alert alert-danger">
                       <h5>{{ $message }}</h5>
                     </div>
                 @endif
     
    <section class="section up-banner">
        <form class="add-product-form" method="post" id="yourFormId" action="{{route('fetch-product-url')}}" enctype="multipart/form-data" >
          @csrf
            <div class="card store-config">
                     <div class="row">
                        <div class="col-12">
                            <label for="inputNanme4" class="form-label">URL</label>
                            <input type="text" class="form-control" placeholder="Enter Url" name="url" required>
                           </div>
                    </div>
                     <div class="col-12">
                            <label for="inputNanme4" class="form-label">Venodr Name</label>
                            <input type="text" class="form-control" placeholder="Venodr Name" name="username" required>
							<span style="color:red;">
                      @error('username')
                      {{$message}}
                       @enderror
                     </span>
                           </div>
                   
					<div class="col-12">
                            <label for="inputNanme4" class="form-label">Passwor</label>
                            <input type="text" class="form-control" placeholder="Password" name="password" required>
							<span style="color:red;">
                      @error('password')
                      {{$message}}
                       @enderror
                     </span>
                           </div>
                    </div>
					 </div>
            </div>
            <div class="timer-btns pro-submit">
              <button type="submit" class="btn btn-primary submitBtn">Submit</button>
           </div>
        </form>
    </section>
	</div>
   </main>
   @endsection
   @section('js')
   <script>
   $(document).ready(function () {
    $("#yourFormId").submit(function () {
        $(".submitBtn").attr("disabled", true);
        return true;
    });
});
</script>
   @endsection
