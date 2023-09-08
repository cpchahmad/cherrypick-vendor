@extends('layouts.admin')
@section('main')
  <main id="main" class="main">
    <div class="subpagetitle fit-title">
      <h1>Payment Configuration</h1>
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
                        <input type="text" class="form-control" id="account" value="@if(isset($details->account_no)){{$details->account_no}} @else {{old('account_no')}} @endif" placeholder="Enter account No" name="account_no">
                        <span style="color:red;">
                         @error('account_no')
						 {{$message}}
                        @enderror
                     </span>
                       </div>
                    <div class="col-6">
                        <label for="inputNanme4" class="form-label">Bank Name</label>
                        <input type="text" class="form-control" id="name" placeholder="Enter bank name" name="name" value="@if(isset($details->bank_name)){{$details->bank_name}} @else {{old('name')}} @endif">
                        <span style="color:red;">
                         @error('name')
                          {{$message}}
                        @enderror
                     </span>
                       </div>
                       <div class="col-6">
                        <label for="inputNanme4" class="form-label">IFSC</label>
                        <input type="text" class="form-control" id="" placeholder="Enter IFSC" name="ifsc" value="@if(isset($details->ifsc)){{$details->ifsc}}@endif">
                        <span style="color:red;">
                         @error('ifsc')
                          {{$message}}
                        @enderror
                     </span>
                       </div>
                       <div class="col-6">
                        <label for="inputNanme4" class="form-label">GST</label>
                        <input type="text" class="form-control" id="" placeholder="Enter GST" name="gst" value="@if(isset($details->gst)){{$details->gst}} @else {{old('gst')}} @endif">
                        <span style="color:red;">
                         @error('gst')
                          {{$message}}
                        @enderror
                     </span>
                       </div>
                         <div class="col-6">
                        <label for="inputNanme4" class="form-label">Account Type</label>
                        <select class="form-control" name='account_type'>
                            <option value="current"@if(isset($details->account_type) && $details->account_type=='current'){{'selected'}}@endif>Current</option>
                            <option value="saving"@if(isset($details->account_type) && $details->account_type=='saving'){{'selected'}}@endif>Saving</option>
                        </select>
                        <span style="color:red;">
                         @error('account_type')
                          {{$message}}
                        @enderror
                     </span>
                       </div>
                       <div class="col-12">
                        <label for="inputNanme4" class="form-label">Address</label>
                        <textarea name="address" class="form-control" id="" value="" placeholder="Address">@if(isset($details->address)){{$details->address}} @else {{old('address')}} @endif</textarea>
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
@endsection
@section('js')
<script type="text/javascript">
 jQuery(document).ready(function() {
    $('#account').keypress(function(event) {
        if ((event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });
});
 jQuery(document).ready(function() {
    $('#name').keydown(function (e) {
  
    if (e.shiftKey || e.ctrlKey || e.altKey) {
    
      e.preventDefault();
      
    } else {
    
      var key = e.keyCode;
      
      if (!((key == 8) || (key == 32) || (key == 46) || (key >= 35 && key <= 40) || (key >= 65 && key <= 90))) {
      
        e.preventDefault();
        
      }

    }
    
  });
});
</script>
@stop
  