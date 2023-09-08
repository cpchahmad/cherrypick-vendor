@extends('layouts.admin')
@section('main')
  <main id="main" class="main">
   <div class="home-flex">
    <div class="pagetitle">
        <h1>Add Variant</h1>
    </div><!-- End Page Title -->
   </div>
    <section class="section up-banner">
             <form action="{{url('save-new-variant')}}" method="post" class="add-product-form"  enctype="multipart/form-data">
            <div class="card">
              @csrf
                <div class="row">
                    @if($data->is_variants==1)
                    <div class="col-4">
                   <div class="col-add-product product-flex">
                    <label for="inputNanme4" class="form-label">Variant Name</label>
                    <input type="text" class="form-control" id="" placeholder="Variant Name" name="varient_name">
                   </div>

                 </div>
                 <div class="col-4">
                   <div class="col-add-product product-flex">
                    <label for="inputNanme4" class="form-label">Variant Value</label>
                    <input type="text" class="form-control" id="" placeholder="Variant Value" name="varient_value">
                    </div>
                 </div>
                    @endif
                   <div class="col-4">
                    <div class="col-add-product product-flex">
                     <label for="inputNanme4" class="form-label">Price</label>
                     <input type="text" class="form-control price" id="" placeholder="Product Price" name="price">
                     </div>
                   
                  </div>
                  <div class="col-4">
                    <div class="col-add-product product-flex">
                     <label for="inputNanme4" class="form-label">SKU</label>
                     <input type="text" class="form-control" id="" placeholder="SKU" name="sku">
                     </div>
                  </div>
                  <div class="col-4">
                    <div class="col-add-product product-flex">
                     <label for="inputNanme4" class="form-label">Weight</label>
                     <input type="text" class="form-control gm" name="grams" id="" placeholder="Product Weight">
                     </div>
                  </div>
                  <div class="col-4">
                    <div class="col-add-product product-flex">
                     <label for="inputNanme4" class="form-label">Quantity</label>
                     <input type="text" class="form-control qty" id="" placeholder="Quantity available" name="quantity">
                     </div>
                  </div>
                 <div class="col-4">
                   <div class="col-add-product product-flex">
                    <label for="inputNanme4" class="form-label">Dimensions</label>
                    <div class="dimensions-input">
                      <input type="text" name="height" class="form-control" id="" placeholder="H" >
                      <input type="text" name="width" class="form-control" id="" placeholder="W" >
                      <input type="text" name="length" class="form-control" id="" placeholder="L" >
                     </div>
                    </div>
                 </div>
                  <div class="col-4">
                    <div class="col-add-product product-flex">
                     <label for="inputNanme4" class="form-label">Shelf life</label>
                     <input type="text" class="form-control" id="" name="shelf_life" placeholder="Shelf life">
                     </div>
                  </div>
                  <div class="col-12">
                    <div class="col-add-product product-flex">
                     <label for="inputNanme4" class="form-label">Temp requirements (If any)</label>
                     <input type="text" class="form-control" id="" placeholder="Temp requirements" name="temp">
                     </div>
                  </div>
				  <div class="col-12">
					<div class="col-add-product product-flex">
                        <label for="inputNanme4" class="form-label">Upload Image</label>
                        <input type="file" class="form-control" name="image">
						</div>
                 </div>
               </div>
                <div class="timer-btns pro-submit">
                    <input type="hidden" name="pid" value="{{$data->id}}">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            
            </div>
            </form>
             
    
    </section>
   </main>
@endsection
@section('js')
<script type="text/javascript">
 jQuery(document).ready(function() {
    $('.price').keypress(function(event) {
        if ((event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });
	$('.gm').keypress(function(event) {
        if ((event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });
	$('.qty').keypress(function(event) {
        if ((event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });
});
</script>
@stop