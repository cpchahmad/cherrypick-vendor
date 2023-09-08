@extends('layouts.superadmin')
@section('main')
  <main id="main" class="main">
    <div class="home-flex">
        <div class="pagetitle fit-title">
          <h1>@if(Request::segment(3)=='1') USA @elseif(Request::segment(3)=='2') UK @elseif(Request::segment(3)=='3') NLD @elseif(Request::segment(3)=='4') IND @elseif(Request::segment(3)=='5') CAD @elseif(Request::segment(3)=='6') AUD @endif Market Shiping Charges in INR</h1>
        </div><!-- End Page Title -->
       </div>
     
    <section class="section up-banner">
	@if ($message = Session::get('success'))
                    <div class="alert alert-success">{{ $message }}</div>
                   @endif
        <form class="add-product-form" method="post" action="{{url('superadmin/update-shiping-charges')}}" enctype="multipart/form-data">
          @csrf
            <div class="card">
                     <div class="row">
                    <div class="col-4">
                        <label for="inputNanme4" class="form-label">< 50Gms</label>
                        <input type="text" class="form-control shiping" id="" value="@if(isset($data->gms_50_inr)){{$data->gms_50_inr}}@endif"  name="gms_50" required="true"> 
                       </div>
                    <div class="col-4">
                        <label for="inputNanme4" class="form-label">50Gms > && <= 100Gms</label>
                        <input type="text" class="form-control shiping" id="" value="@if(isset($data->gms_100_inr)){{$data->gms_100_inr}}@endif"  name="gms_100" required="true">
                       </div>
					   <div class="col-4">
                        <label for="inputNanme4" class="form-label">100Gms > && <= 150Gms</label>
                        <input type="text" class="form-control shiping" id="" name="gms_150" value="@if(isset($data->gms_150_inr)){{$data->gms_150_inr}}@endif"  required="true">
                       </div>
					   <div class="col-4">
                        <label for="inputNanme4" class="form-label">150Gms > && <= 200Gms</label>
                        <input type="text" class="form-control shiping" id="" name="gms_200" value="@if(isset($data->gms_200_inr)){{$data->gms_200_inr}}@endif" required="true">
                       </div>
					   <div class="col-4">
                        <label for="inputNanme4" class="form-label">200Gms > && <= 250Gms</label>
                        <input type="text" class="form-control shiping" id="" name="gms_250" value="@if(isset($data->gms_250_inr)){{$data->gms_250_inr}}@endif" required="true">
                       </div>
					   <div class="col-4">
                        <label for="inputNanme4" class="form-label">250Gms > && <= 300Gms</label>
                        <input type="text" class="form-control shiping" id="" name="gms_300" value="@if(isset($data->gms_300_inr)){{$data->gms_300_inr}}@endif" required="true">
                       </div>
					   <div class="col-4">
                        <label for="inputNanme4" class="form-label">300Gms > && <= 400Gms</label>
                        <input type="text" class="form-control shiping" id="" name="gms_400" value="@if(isset($data->gms_400_inr)){{$data->gms_400_inr}}@endif" required="true">
                       </div>
					   <div class="col-4">
                        <label for="inputNanme4" class="form-label">400Gms > && <= 500Gms</label>
                        <input type="text" class="form-control shiping" id="" name="gms_500" value="@if(isset($data->gms_500_inr)){{$data->gms_500_inr}}@endif" required="true">
                       </div>
					   <div class="col-4">
                        <label for="inputNanme4" class="form-label">500Gms > && <= 750Gms</label>
                        <input type="text" class="form-control shiping" id="" name="gms_750" value="@if(isset($data->gms_750_inr)){{$data->gms_750_inr}}@endif" required="true">
                       </div>
					 <div class="col-4">
                        <label for="inputNanme4" class="form-label">750Gms > && <= 1000Gms</label>
                        <input type="text" class="form-control shiping" id="" name="gms_1000" value="@if(isset($data->gms_1000_inr)){{$data->gms_1000_inr}}@endif" required="true">
                       </div>
                       
                       <div class="col-4">
                        <label for="inputNanme4" class="form-label"> > 1000Gms</label>
                        <input type="text" class="form-control shiping" id="" name="gms_5000" value="@if(isset($data->gms_5000_inr)){{$data->gms_5000_inr}}@endif" required="true">
                       </div>
                       
                    </div>
					<hr>
					<div class="row">
					<h5><b>Savory Product Charges</b></h5>
                    <div class="col-4">
                        <label for="inputNanme4" class="form-label"> < 50Gms</label>
                        <input type="text" class="form-control shiping" id="" value="@if(isset($data->savory_gms_50_inr)){{$data->savory_gms_50_inr}}@endif"  name="savory_gms_50" required="true"> 
                       </div>
                    <div class="col-4">
                        <label for="inputNanme4" class="form-label">50Gms > && <= 100Gms</label>
                        <input type="text" class="form-control shiping" id="" value="@if(isset($data->savory_gms_100_inr)){{$data->savory_gms_100_inr}}@endif"  name="savory_gms_100" required="true">
                       </div>
					   <div class="col-4">
                        <label for="inputNanme4" class="form-label">100Gms > && <= 150Gms</label>
                        <input type="text" class="form-control shiping" id="" name="savory_gms_150" value="@if(isset($data->savory_gms_150_inr)){{$data->savory_gms_150_inr}}@endif"  required="true">
                       </div>
					   <div class="col-4">
                        <label for="inputNanme4" class="form-label">150Gms > && <= 200Gms</label>
                        <input type="text" class="form-control shiping" id="" name="savory_gms_200" value="@if(isset($data->savory_gms_200_inr)){{$data->savory_gms_200_inr}}@endif" required="true">
                       </div>
					   <div class="col-4">
                        <label for="inputNanme4" class="form-label">200Gms > && <= 250Gms</label>
                        <input type="text" class="form-control shiping" id="" name="savory_gms_250" value="@if(isset($data->savory_gms_250_inr)){{$data->savory_gms_250_inr}}@endif" required="true">
                       </div>
					   <div class="col-4">
                        <label for="inputNanme4" class="form-label">250Gms > && <= 300Gms</label>
                        <input type="text" class="form-control shiping" id="" name="savory_gms_300" value="@if(isset($data->savory_gms_300_inr)){{$data->savory_gms_300_inr}}@endif" required="true">
                       </div>
					   <div class="col-4">
                        <label for="inputNanme4" class="form-label">300Gms > && <= 400Gms</label>
                        <input type="text" class="form-control shiping" id="" name="savory_gms_400" value="@if(isset($data->savory_gms_400_inr)){{$data->savory_gms_400_inr}}@endif" required="true">
                       </div>
					   <div class="col-4">
                        <label for="inputNanme4" class="form-label">400Gms > && <= 500Gms</label>
                        <input type="text" class="form-control shiping" id="" name="savory_gms_500" value="@if(isset($data->savory_gms_500_inr)){{$data->savory_gms_500_inr}}@endif" required="true">
                       </div>
					   <div class="col-4">
                        <label for="inputNanme4" class="form-label">500Gms > && <= 750Gms</label>
                        <input type="text" class="form-control shiping" id="" name="savory_gms_750" value="@if(isset($data->savory_gms_750_inr)){{$data->savory_gms_750_inr}}@endif" required="true">
                       </div>
					    <div class="col-4">
                        <label for="inputNanme4" class="form-label">750Gms > && <= 1000Gms</label>
                        <input type="text" class="form-control shiping" id="" name="savory_gms_1000" value="@if(isset($data->savory_gms_1000_inr)){{$data->savory_gms_1000_inr}}@endif" required="true">
                       </div>
                        <div class="col-4">
                        <label for="inputNanme4" class="form-label"> > 1000Gms</label>
                        <input type="text" class="form-control shiping" id="" name="savory_gms_5000" value="@if(isset($data->savory_gms_5000_inr)){{$data->savory_gms_5000_inr}}@endif" required="true">
                       </div>
                    </div>
                    <br>
					<hr>
                    <h5><b>Saree/Furniture</b></h5>
					<div class="row">
                    <div class="col-6">
                        <label for="inputNanme4" class="form-label">Saree</label>
                        <input type="text" class="form-control shiping" id="" name="saree" required="true" value="@if(isset($data->saree_inr)){{$data->saree_inr}}@endif" required="true">
                       </div>
                       <div class="col-6">
                        <label for="inputNanme4" class="form-label">Furniture</label>
                        <input type="text" class="form-control shiping" id="" name="furniture" value="@if(isset($data->furniture_inr)){{$data->furniture_inr}}@endif" required="true">
                       </div>
					   </div>
            </div>


            <div class="timer-btns">
			<input type="hidden" name="market" value="{{$id}}">
              <button type="submit" class="btn btn-primary">Submit</button>
           </div>
        </form>
    </section>
   </main>
@endsection
@section('js')
<script type="text/javascript">
 jQuery(document).ready(function() {
    $('.shiping').keypress(function(event) {
        if ((event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });
});
</script>
@stop
