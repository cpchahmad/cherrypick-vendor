@extends('layouts.admin')
@section('main')
  <main id="main" class="main">
   <div class="home-flex">
    <div class="pagetitle">
       <h1>Edit Product</h1>
    </div><!-- End Page Title -->
   </div>
   @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                       <h5>{{ $message }}</h5>
                     </div>
                 @endif
    <section class="section up-banner">
        <form class="add-product-form" method="post" action="{{url('update-products')}}" enctype="multipart/form-data" id="form_data">
          @csrf
            <div class="card">
                <div class="row">
                   <div class="col-12">
                    <div class="col-add-product">
                     <label for="inputNanme4" class="form-label">Name</label>
                     <input type="text" class="form-control" id="" placeholder="Product Name" name="name" value='{{$product->title}}'>
                     <span style="color:red;">
                      @error('name')
                      {{$message}}
                       @enderror
                     </span>
                     </div>
                     <div class="col-add-product">
                     <label for="inputNanme4" class="form-label">Short Description</label>
                     <textarea  id="" class="form-control" placeholder="Short Description" name="description">{{$product->body_html}}</textarea>
                     <span style="color:red;">
                      @error('description')
                      {{$message}}
                       @enderror
                     </span>
                  </div>
                    <div class="col-add-product">
                     <label for="inputNanme4" class="form-label">Tags</label>
                     <input type="text" class="form-control" id="tags" placeholder="Tags" name="tags" value="{{$product->tags}}">
                     <span style="color:red;">
                      @error('tags')
                      {{$message}}
                       @enderror
                     </span>
                     </div>
                  </div>
               </div>
            </div>
               
             <div class="card">
                  <div class="row prod-img">
				  @php
                    $images=\App\Models\ProductImages::where(['product_id' =>$product->id])->whereNull('variant_ids')->get();
					//echo "<pre>"; print_r($images);
                  @endphp
                  @foreach($images as $prd)
                  <div id='img_{{$prd->id}}' class="col-3"><img src="{{$prd->image}}" height="120px" width='120px'><br><a href="javascript:void(0)" onclick="deleteImage({{$prd->id}})">Delete</a></div>
                  @endforeach
                  </div>
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
			
			 
            </div>

             <meta name="csrf-token" content="{{ csrf_token() }}" />

            <div class="card">
              <div class="row">
                 <div class="col-12">
                  <div class="col-add-product product-flex">
                   <label for="inputNanme4" class="form-label">Category</label>
                   <select class="form-select" aria-label="Default select example" name="category">
                      <option>Select Category</option>
                      @foreach($category as $data)
                      <option value="{{$data->id}}"@if($product->category==$data->id) {{'selected'}} @endif>{{$data->category}}</option>
                      @endforeach
                    </select>
                   </div>
                </div>
                <span style="color:red;">
                         @error('category')
                          {{$message}}
                         @enderror
                     </span>
             </div>
          </div>

            <div class="timer-btns pro-submit">
                <a href="{{url('product-list')}}" class="btn btn-light">Back</a>
                <input type="hidden" name="pid" value="{{$product->id}}">
                <button type="submit" class="btn btn-primary">Submit</button>
             </div>
        </form>
    </section>
   </main>
@endsection
@section('js')
<script type="text/javascript">
function hideAddfile()
{
	$('#pro-image').click();
}
</script>
@stop

  <!-- Template Main JS File -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script>
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

  //$(".preview-images-zone").sortable();

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
    //$("#pro-image").val("");
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




  
  