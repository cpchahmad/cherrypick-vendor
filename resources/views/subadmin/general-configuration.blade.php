@extends('layouts.admin')
@section('main')
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
            <div class="card gen-config">
                <div class="row">
                    <div class="col-12">
                     <div class="row">
                    <div class="col-6 field">
                        <label for="inputNanme4" class="form-label">Email Id</label>
                        <input type="email" class="form-control" id="" value="{{$data->email}}" placeholder="Enter Email" name="emailid">
                        <span style="color:red;">
                         @error('emailid')
                          <span style="color:red;">Please enter valid email address.</span>
                        @enderror
                     </span>
                       </div>
                    <div class="col-6 field">
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
					<div class="col-6 field">
                        <label for="inputNanme4" class="form-label">Collections Id</label>
                        <input type="text" class="form-control" id="" placeholder="Enter Collections Ids" name="collections_ids" value="{{$data->collections_ids}}" readonly>
                        <span style="color:red;">
                         @error('collections_ids')
                          {{$message}}
                        @enderror
                     </span>
                       </div>
					   
					</div>
					<div class="row">
					<div class="col-2">
                        <label class="form-label"><br>Monday</label>
                    </div>
					<div class="col-2">
                        <label class="form-label">Status</label>
						<select class="form-control" name="status[1]">
							<option value="1"@if(isset($status[1]) && $status[1]=='1') {{'selected'}} @endif>Open</option>
							<option value="0"@if(isset($status[1]) && $status[1]=='0') {{'selected'}} @endif>Close</option>
						</select>
                    </div>
					<div class="col-4">
                        <label class="form-label">Opening Time</label>
                        <input type="text" class="form-control" name="open[1]" value="@if(isset($open[1])) {{$open[1]}} @endif" >
                    </div>
					<div class="col-4">
                        <label class="form-label">Closing Time</label>
                        <input type="text" class="form-control" name="close[1]" value="@if(isset($close[1])) {{$close[1]}} @endif" >
                    </div>
					   
					</div>
					<div class="row">
					<div class="col-2">
                        <label class="form-label"><br>Tuesday</label>
                    </div>
					<div class="col-2 w-label">
                        <label class="form-label">&nbsp;</label>
						<select class="form-control" name="status[2]">
							<option value="1"@if(isset($status[2]) && $status[2]=='1') {{'selected'}} @endif>Open</option>
							<option value="0"@if(isset($status[2]) && $status[2]=='0') {{'selected'}} @endif>Close</option>
						</select>
                    </div>
					<div class="col-4 w-label">
                        <label class="form-label">&nbsp;</label>
                        <input type="text" class="form-control" name="open[2]" value="@if(isset($open[2])) {{$open[2]}} @endif">
                    </div>
					<div class="col-4 w-label">
                        <label class="form-label">&nbsp;</label>
                        <input type="text" class="form-control" name="close[2]" value="@if(isset($close[2])) {{$close[2]}} @endif">
                    </div>
					   
					</div>
					<div class="row">
					<div class="col-2">
                        <label class="form-label"><br>Wednesday</label>
                    </div>
					<div class="col-2 w-label">
                        <label class="form-label">&nbsp;</label>
						<select class="form-control" name="status[3]">
							<option value="1"@if(isset($status[3]) && $status[3]=='1') {{'selected'}} @endif>Open</option>
							<option value="0"@if(isset($status[3]) && $status[3]=='0') {{'selected'}} @endif>Close</option>
						</select>
                    </div>
					<div class="col-4 w-label">
                        <label class="form-label">&nbsp;</label>
                        <input type="text" class="form-control" name="open[3]" value="@if(isset($open[3])) {{$open[3]}} @endif">
                    </div>
					<div class="col-4 w-label">
                        <label class="form-label">&nbsp;</label>
                        <input type="text" class="form-control" name="close[3]" value="@if(isset($close[3])) {{$close[3]}} @endif">
                    </div>
					   
					</div>
					<div class="row">
					<div class="col-2">
                        <label class="form-label"><br>Thursday</label>
                    </div>
					<div class="col-2 w-label">
                        <label class="form-label">&nbsp;</label>
						<select class="form-control" name="status[4]">
							<option value="1"@if(isset($status[4]) && $status[4]=='1') {{'selected'}} @endif>Open</option>
							<option value="0"@if(isset($status[4]) && $status[4]=='0') {{'selected'}} @endif>Close</option>
						</select>
                    </div>
					<div class="col-4 w-label">
                        <label class="form-label">&nbsp;</label>
                        <input type="text" class="form-control" name="open[4]" value="@if(isset($open[4])) {{$open[4]}} @endif">
                    </div>
					<div class="col-4 w-label">
                        <label class="form-label">&nbsp;</label>
                        <input type="text" class="form-control" name="close[4]" value="@if(isset($close[4])) {{$close[4]}} @endif">
                    </div>
					   
					</div>
					<div class="row">
					<div class="col-2">
                        <label class="form-label"><br>Friday</label>
                    </div>
					<div class="col-2 w-label">
                        <label class="form-label">&nbsp;</label>
						<select class="form-control" name="status[5]">
							<option value="1"@if(isset($status[5]) && $status[5]=='1') {{'selected'}} @endif>Open</option>
							<option value="0"@if(isset($status[5]) && $status[5]=='0') {{'selected'}} @endif>Close</option>
						</select>
                    </div>
					<div class="col-4 w-label">
                        <label class="form-label">&nbsp;</label>
                        <input type="text" class="form-control" name="open[5]" value="@if(isset($open[5])) {{$open[5]}} @endif">
                    </div>
					<div class="col-4 w-label">
                        <label class="form-label">&nbsp;</label>
                        <input type="text" class="form-control" name="close[5]" value="@if(isset($close[5])) {{$close[5]}} @endif">
                    </div>
					   
					</div>
					<div class="row">
					<div class="col-2">
                        <label class="form-label"><br>Saturday</label>
                    </div>
					<div class="col-2 w-label">
                        <label class="form-label">&nbsp;</label>
						<select class="form-control" name="status[6]">
							<option value="1"@if(isset($status[6]) && $status[6]=='1') {{'selected'}} @endif>Open</option>
							<option value="0"@if(isset($status[6]) && $status[6]=='0') {{'selected'}} @endif>Close</option>
						</select>
                    </div>
					<div class="col-4 w-label">
                        <label class="form-label">&nbsp;</label>
                        <input type="text" class="form-control" name="open[6]" value="@if(isset($open[6])) {{$open[6]}} @endif">
                    </div>
					<div class="col-4 w-label">
                        <label class="form-label">&nbsp;</label>
                        <input type="text" class="form-control" name="close[6]" value="@if(isset($close[6])) {{$close[6]}} @endif">
                    </div>
					   
					</div>
					<div class="row">
					<div class="col-2">
                        <label class="form-label"><br>Sunday</label>
                    </div>
					<div class="col-2 w-label">
                        <label class="form-label">&nbsp;</label>
						<select class="form-control" name="status[7]">
							<option value="1"@if(isset($status[7]) && $status[7]=='1') {{'selected'}} @endif>Open</option>
							<option value="0"@if(isset($status[7]) && $status[7]=='0') {{'selected'}} @endif>Close</option>
						</select>
                    </div>
					<div class="col-4 w-label">
                        <label class="form-label">&nbsp;</label>
                        <input type="text" class="form-control" name="open[7]" value="@if(isset($open[7])) {{$open[7]}} @endif">
                    </div>
					<div class="col-4 w-label">
                        <label class="form-label">&nbsp;</label>
                        <input type="text" class="form-control" name="close[7]" value="@if(isset($close[7])) {{$close[7]}} @endif">
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
@endsection
@section('js')
<script type="text/javascript">
 jQuery(document).ready(function() {
    $('#mobile').keypress(function(event) {
        if ((event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });
});
</script>
@stop
  