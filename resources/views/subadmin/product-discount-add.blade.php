@extends('layouts.admin')
@section('main')
<main id="main" class="main">
    <div class="subpagetitle fit-title">
        <h1>Discount Settings</h1>
         <p><a href="{{url('manage-discount')}}">Discount</a> / <b>Discount Settings</b></p>
      </div>
      
   <section class="section dashboard">
    <div class="row">
      <div class="col-lg-12">
	@if ($message = Session::get('error'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
  @endif
          <form class="edit-timer-form cp-setting" method="post" action="{{url('save-product-discount')}}">
              @csrf
            <div class="card">
                <div class="seprate row g-3">
                    <div class="col-12">
                     <label for="inputNanme4" class="form-label"><b>Discount(%)</b></label>
                     <input type="text" class="form-control" id="discount" name="discount" placeholder="Discount(%)" required="true">
					 <span style="color:red;" id="err_name">
                      @error('discount')
                      {{$message}}
                       @enderror
                     </span>
                    </div>
               </div>
            </div>

           <div class="card">
            <div class="seprate row g-3">
                <div class="col-12">
                    <label for="" class="form-label"><b>Applies To</b></label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="applies_to" id="gridAT1" value="all" checked>
                        <label class="form-check-label" for="gridAT1">
                            All Products
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="applies_to" id="gridAT2" value="products">
                        <label class="form-check-label" for="gridAT2">
                            Specific Products
                        </label>
                        <span style="color:red;">
                        @if($errors->has('products_ids'))
                            <div class="error">{{ $errors->first('products_ids') }}</div>
                         @endif
                        </span>
                        <div class="member-plan-search header browser-search">
                          <div class="">
                                     <select name="products_ids[]" multiple="multiple" id="e2" style="width: 75%">
                                         @if(count($products) > 1)
                                             @foreach($products as $row)
                                                <option value="{{$row['id']}}">{{$row['title']}}</option>
                                             @endforeach   
                                         @endif
                                    </select>                                   
                            </div>
                         </div>
                    </div>
                   
                </div>
              </div>     
           </div>
 
    <div class="timer-btns">
         <a href="{{url('manage-product-discount')}}" class="btn btn-light">Back</a>
        <button type="submit" class="btn btn-primary">Save</button>
     </div>
 </form>
      </div>
    </div>
  </section>
  </main><!-- End #main -->

@endsection
@section('js')
<script type="text/javascript">
 jQuery(document).ready(function() {
    $('#discount').keypress(function(event) {
        if ((event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });
});
</script>
@stop

