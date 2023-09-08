@extends('layouts.superadmin')
@section('main')
  <main id="main" class="main">
    <div class="home-flex">
        <div class="pagetitle fit-title">
          <h1>Price Conversion Rate</h1>
		 
        </div><!-- End Page Title -->
		 <a class="btn btn-primary" href="{{url('superadmin/updateprice')}}">Update Product Prices</a>
       </div>
     
    <section class="section up-banner">
	@if ($message = Session::get('success'))
                    <div class="alert alert-success">{{ $message }}</div>
                   @endif
        <form class="add-product-form" method="post" action="{{url('superadmin/update-conversion-rate')}}" enctype="multipart/form-data">
          @csrf
            <div class="card">
                     <div class="row">
                    <div class="col-4">
                        <label for="inputNanme4" class="form-label">INR-USD</label>
                        <input type="text" class="form-control" id="" value="{{$data->usd_inr}}"  name="usd_inr" required="true">
                        <span style="color:red;">
                         @error('usd_inr')
                           {{$message}}
                         @enderror
                     </span> 
                       </div>
                    <div class="col-4">
                        <label for="inputNanme4" class="form-label">INR-EURO</label>
                        <input type="text" class="form-control" id="" value="{{$data->euro_inr}}"  name="euro_inr" required="true">
                        <span style="color:red;">
                         @error('euro_inr')
                           {{$message}}
                         @enderror
                     </span> 

                       </div>
					   <div class="col-4">
                        <label for="inputNanme4" class="form-label">INR-GBP</label>
                        <input type="text" class="form-control" id="" name="gbp_inr" value="{{$data->gbp_inr}}"  required="true">
                        <span style="color:red;">
                         @error('gbp_inr')
                           {{$message}}
                         @enderror
                     </span> 

                       </div>
					   <div class="col-4">
                        <label for="inputNanme4" class="form-label">INR-Dirham</label>
                        <input type="text" class="form-control" id="" name="dirham_inr" value="{{$data->dirham_inr}}" required="true">
                        <span style="color:red;">
                         @error('dirham_inr')
                           {{$message}}
                         @enderror
                     </span> 

                       </div>
					   <div class="col-4">
                        <label for="inputNanme4" class="form-label">INR-CAD</label>
                        <input type="text" class="form-control" id="" name="cad_inr" value="{{$data->cad_inr}}" required="true">
                        <span style="color:red;">
                         @error('cad_inr')
                           {{$message}}
                         @enderror
                     </span> 

                       </div>
					   <div class="col-4">
                        <label for="inputNanme4" class="form-label">INR-AUD</label>
                        <input type="text" class="form-control" id="" name="aud_inr" value="{{$data->aud_inr}}" required="true">
                        <span style="color:red;">
                         @error('aud_inr')
                           {{$message}}
                         @enderror
                     </span> 

                       </div>
                    </div>
            </div>
            <div class="timer-btns">
              <button type="submit" class="btn btn-primary">Submit</button>
           </div>
        </form>
    </section>
   </main>
@endsection
  