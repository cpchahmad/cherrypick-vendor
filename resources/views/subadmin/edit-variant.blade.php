@extends('layouts.admin')
@section('main')
  <main id="main" class="main">
   <div class="home-flex">
    <div class="pagetitle editwrcls">
        <h1>Edit Product Variant</h1>@if($is_variants==1) <a class="btn btn-primary" href='{{url('add-new-variant')}}/{{$pid}}'>Add New</a> @endif
    </div><!-- End Page Title -->
   </div>
                    @if ($message = Session::get('success'))
                    <div class="alert alert-success">{{ $message }}</div>
                   @endif
    <section class="section up-banner">
            @php $i=1; @endphp
            @foreach($prodcut_info as $row)
             <form action="{{url('update-variant')}}" method="post" class="add-product-form"  enctype="multipart/form-data">
            <div class="card" id='card_{{$row->id}}'>
               
                    @csrf
                <div class="row">
                    @if($is_variants==1)
                    <div class="col-4">
                   <div class="col-add-product product-flex">
                    <label for="inputNanme4" class="form-label">Variant Name</label>
                    <input type="text" class="form-control" id="" placeholder="Variant Name" name="varient_name"  value="{{$row->varient_name}}">
                    <span style="color:red;">
                       @error('varient_name')
                         {{$message}}
                       @enderror
                    </span>
                   </div>

                 </div>
                 <div class="col-4">
                   <div class="col-add-product product-flex">
                    <label for="inputNanme4" class="form-label">Variant Value</label>
                    <input type="text" class="form-control" id="" placeholder="Variant Value" name="varient_value"  value="{{$row->varient_value}}">
                    <span style="color:red;">
                       @error('varient_value')
                         {{$message}}
                       @enderror
                    </span>
                    </div>
                 </div>
                    @endif
                   <div class="col-4">
                    <div class="col-add-product product-flex">
                     <label for="inputNanme4" class="form-label">Price</label>
                     <input type="text" class="form-control price" id="" placeholder="Product Price" name="price" value="{{$row->base_price}}">
                     <span style="color:red;">
                      @error('price')
                      {{$message}}
                       @enderror
                     </span>
                     </div>
                   
                  </div>
                  <div class="col-4">
                    <div class="col-add-product product-flex">
                     <label for="inputNanme4" class="form-label">SKU</label>
                     <input type="text" class="form-control" id="" placeholder="SKU" name="sku" value="{{$row->sku}}">
                     <span style="color:red;">
                      @error('compare_price')
                      {{$message}}
                       @enderror
                     </span>
                     </div>
                  </div>
                  <div class="col-4">
                    <div class="col-add-product product-flex">
                     <label for="inputNanme4" class="form-label">Weight</label>
                     <input type="text" class="form-control gm" name="grams" id="" placeholder="Product Weight"  value="{{$row->grams}}">
                     <span style="color:red;">
                      @error('weight')
                      {{$message}}
                       @enderror
                     </span>
                     </div>
                  </div>
                  <div class="col-4">
                    <div class="col-add-product product-flex">
                     <label for="inputNanme4" class="form-label">Quantity</label>
                     <input type="text" class="form-control qty" id="" placeholder="Quantity available" name="quantity" value="{{$row->stock}}">
                     <span style="color:red;">
                      @error('quantity')
                      {{$message}}
                       @enderror
                     </span>
                     </div>
                  </div>
                 <div class="col-4">
                   <div class="col-add-product product-flex">
                    <label for="inputNanme4" class="form-label">Dimensions</label>
                    @php 
                    $dimensions=explode("-",$row->dimensions);                   
                    @endphp
                    <div class="dimensions-input">
                      <input type="text" name="height" class="form-control" id="" placeholder="H" value="{{$dimensions[0]}}">
                      <input type="text" name="width" class="form-control" id="" placeholder="W" value="{{$dimensions[1]}}">
                      <input type="text" name="length" class="form-control" id="" placeholder="L" value="{{$dimensions[2]}}">
                     </div>
                    </div>
                 </div>
                  <div class="col-4">
                    <div class="col-add-product product-flex">
                     <label for="inputNanme4" class="form-label">Shelf life</label>
                     <input type="text" class="form-control" id="" name="shelf_life" placeholder="Shelf life" value="{{$row->shelf_life}}">
                     <span style="color:red;">
                      @error('shelf_life')
                      {{$message}}
                       @enderror
                     </span>
                     </div>
                  </div>
                  <div class="col-12">
                    <div class="col-add-product product-flex">
                     <label for="inputNanme4" class="form-label">Temp requirements (If any)</label>
                     <input type="text" class="form-control" id="" placeholder="Temp requirements" name="temp" value="{{$row->temp_require}}">
                     <span style="color:red;">
                      @error('temp')
                      {{$message}}
                       @enderror
                     </span>
                     </div>
                  </div>
				  @php
                    $images=\App\Models\ProductImages::where(['variant_ids' =>$row->id])->get();
                  @endphp
				  @foreach($images as $prd)
                  <div id='img_{{$prd->id}}' class="col-3"><img src="{{$prd->image}}" height="120px" width='120px'><br><a href="javascript:void(0)" onclick="deleteImage({{$prd->id}})">Delete</a></div>
				  @endforeach
				  <div class="col-12">
                        <label for="inputNanme4" class="form-label">Upload Image</label>
                        <input type="file" class="form-control" name="image">
                        <span style="color:red;">
                         @error('image')
                          {{$message}}
                        @enderror
                      </span>
                 </div>
               </div>
			   <br>
                <div class="timer-btns pro-submit">
                    <input type="hidden" name="id" value="{{$row->id}}">
                    <input type="hidden" name="pid" value="{{$pid}}">
                    <input type="hidden" name="is_variants" value="{{$is_variants}}">
                    @if($i!=1)
                        <a href="javascript:void(0)" onclick='deleteVarient({{$row->id}})'><i class="bi bi-trash"></i></a>
                    @endif
					<a href="{{url('product-list')}}" class="btn btn-light">Back</a>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            
            </div>
            </form>
            @php $i++; @endphp
            @endforeach
             <meta name="csrf-token" content="{{ csrf_token() }}" />
             
    
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
  <!-- Template Main JS File -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script>
      function deleteVarient(id)
      {
          var v_token = "{{csrf_token()}}";
           $.ajax({
               type: 'POST',    
                url:"{{url('delete-variant')}}",
                data:'id='+ id,
                headers: {'X-CSRF-Token': v_token},
                success:function(response){
                    $('#card_'+id).hide();
                }
            });
      }
function showHideDiv()
{
    if($("#payradious").prop('checked') == true){
        $("#multivarient").show();
        $("#single_varient").hide();
    }
    else
    {
        $("#single_varient").show();
        $("#multivarient").hide();
    }
}
function deleteImage(id)
{
    $.ajax({
        url: "{{url('delete-image')}}/"+id,
        type: 'GET',
        success: function(response)
        {
            $('#img_'+id).hide();
        }
    });
}
      
      
   $(document).ready(function () {
  document
    .getElementById("pro-image")
    .addEventListener("change", readImage, false);

  $(".preview-images-zone").sortable();

  $(document).on("click", ".image-cancel", function () {
    let no = $(this).data("no");
    $(".preview-image.preview-show-" + no).remove();
  });
});

var num = 0;
function readImage() {
  if (window.File && window.FileList && window.FileReader) {
    var files = event.target.files; //FileList object
    var output = $(".preview-images-zone");

    for (let i = 0; i < files.length; i++) {
      var file = files[i];
      var checkdiv = $("div.preview-image").length;
      // lemit line
      if (num <= 5 || checkdiv <= 5) {
        var num = checkdiv;
        if (!file.type.match("image")) continue;

        var picReader = new FileReader();

        picReader.addEventListener("load", function (event) {
          var picFile = event.target;
          var html =
            '<div class="preview-image preview-show-' +
            num +
            '">' +
            '<div class="image-cancel" data-no="' +
            num +
            '">x</div>' +
            '<div class="image-zone"><img id="pro-img-' +
            num +
            '" src="' +
            picFile.result +
            '"></div>' +
            "</div>";

          output.append(html);
          num = num + 1;
        });
      }
      picReader.readAsDataURL(file);
    }
    $("#pro-image").val("");
  } else {
    console.log("Browser not support");
  }
}

</script>
<script>
  $(".extra-fields-customer").click(function () {
  $(".customer_records").clone().appendTo(".customer_records_dynamic");
  $(".customer_records_dynamic .customer_records").addClass("single remove");
  $(".single .extra-fields-customer").remove();
  $(".single").append(
    '<a href="#" class="remove-field btn-remove-customer">Remove Fields</a>'
  );
  $(".customer_records_dynamic > .single").attr("class", "remove");

  $(".customer_records_dynamic input").each(function () {
    var count = 0;
    var fieldname = $(this).attr("name");
    $(this).attr("name", fieldname + count);
    count++;
  });
});

$(document).on("click", ".remove-field", function (e) {
  $(this).parent(".remove").remove();
  e.preventDefault();
});

</script>
<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
<script >
   $('#pro-image').change(function(e){
          let TotalFiles = $('#pro-image')[0].files.length;
            profile = e.target.files[0];
            var formData= new FormData();
            formData.append('profile' , profile);
            $.ajax({
                cache: false,
                contentType: false,
                processData: false,
                type:'post',
                data:formData,
                headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')},
                url:"{{ route('save-product') }}",
                success:function(response){
                    var img_val=$('#img').val();
                    var json = $.parseJSON(response);
                    if(img_val=='')
                        $('#img').val(json.message);
                    else
                    {
                         $('#img').val(img_val+","+json.message);
                    }
                }
            });
        });
  
</script>



  
  