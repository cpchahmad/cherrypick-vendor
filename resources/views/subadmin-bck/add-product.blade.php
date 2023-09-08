@extends('layouts.admin')
@section('main')
  <main id="main" class="main">
   <div class="home-flex">
    <div class="pagetitle">
       <h1>Add Product</h1>
    </div><!-- End Page Title -->
   </div>
   @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                       <h5>{{ $message }}</h5>
                     </div>
                 @endif
    <section class="section up-banner">
        <form class="add-product-form" method="post" action="{{route('save-products')}}" enctype="multipart/form-data" id="form_data">
          @csrf
            <div class="card">
                <div class="row">
                   <div class="col-12">
                    <div class="col-add-product">
                     <label for="inputNanme4" class="form-label">Name</label>
                     <input type="text" class="form-control" id="" placeholder="Product Name" name="name">
                     <span style="color:red;">
                      @error('name')
                      {{$message}}
                       @enderror
                     </span>
                     </div>
                     <div class="col-add-product">
                     <label for="inputNanme4" class="form-label">Short Description</label>
                     <textarea  id="" class="form-control" placeholder="Short Description" name="description"></textarea>
                     <span style="color:red;">
                      @error('description')
                      {{$message}}
                       @enderror
                     </span>
                  </div>
                    <div class="col-add-product">
                     <label for="inputNanme4" class="form-label">Tags</label>
                     <input type="text" class="form-control" id="tags" placeholder="Tags" name="tags">
                     <span style="color:red;">
                      @error('name')
                      {{$message}}
                       @enderror
                     </span>
                     </div>
                  </div>
               </div>
            </div>
              <div class="col-md-12">
                <div class="card">
                  <div class="container">
                     <fieldset class="form-group">
                        <a href="javascript:void(0)" onclick="$('#pro-image').click()" class="fileLabel">Add File</a>
                        <input type="file" id="pro-image" name="pro_image" style="display: none;" class="form-control" multiple>
                    </fieldset>
                    <input type="hidden" id="img" name="images">
                    <span style="color:red;">
                      @error('profile')
                      {{$message}}
                       @enderror
                     </span>

                    <div class="preview-images-zone"></div>
                </div>
                 </div>
               </div>
               <span class="file0-size">Suggestion Size - 1080px * 1080xp </span>
               
                <div class="card">
                <div class="row">
                   <div class="col-12 multiple-vriant">
                        <label for="" class="form-label"><b>If the product has variants, Please Select Option</b></label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="payradious" id="payradious" value="1" onchange="showHideDiv()">
                            <label class="form-check-label" for="payradious">
                              This product has variants, like size or color</label>
                        
                        <div class="row show-options" id="multivarient" style="display: none">
                          <div class="customer_records_dynamic"></div>
                          <div class="customer_records">
                            <div class="row">
                              <div class="col-3">
                               <div class="col-add-product product-flex">
                                <label for="inputNanme4" class="form-label">Variant Name</label>
                                <input type="text" class="form-control" id="" placeholder="Variant Name" name="varient_name[]">
                                <span style="color:red;">
                                   @error('varient_name')
                                     {{$message}}
                                   @enderror
                                </span>
                               </div>
                              
                             </div>
                             <div class="col-3">
                               <div class="col-add-product product-flex">
                                <label for="inputNanme4" class="form-label">Variant Value</label>
                                <input type="text" class="form-control" id="" placeholder="Variant Value" name="varient_value[]">
                                <span style="color:red;">
                                   @error('varient_value')
                                     {{$message}}
                                   @enderror
                                </span>
                                </div>
                             </div>
                             <div class="col-3">
                              <div class="col-add-product product-flex">
                               <label for="inputNanme4" class="form-label">Price</label>
                               <input type="text" class="form-control" id="" placeholder="Price" name="varient_price[]">
                               <span style="color:red;">
                                   @error('product_price')
                                     {{$message}}
                                   @enderror
                                </span>
                               </div>
                            </div>
                            <div class="col-3">
                              <div class="col-add-product product-flex">
                               <label for="inputNanme4" class="form-label">SKU</label>
                               <input type="text" class="form-control" id="" placeholder="SKU" name="varient_sku[]">
                               <span style="color:red;">
                                   @error('product_sku')
                                     {{$message}}
                                   @enderror
                                </span>
                               </div>
                            </div>
                             <div class="col-3">
                               <div class="col-add-product product-flex">
                                <label for="inputNanme4" class="form-label">Weight</label>
                                <input type="text" class="form-control" id="" placeholder="Product Weight" name="varient_grams[]">
                                <span style="color:red;">
                                   @error('product_weight')
                                     {{$message}}
                                   @enderror
                                </span>
                                </div>
                             </div>
                             <div class="col-3">
                               <div class="col-add-product product-flex">
                                <label for="inputNanme4" class="form-label">Quantity</label>
                                <input type="text" class="form-control" id="" placeholder="Quantity" name="varient_quantity[]">
                                <span style="color:red;">
                                   @error('product_quantity')
                                     {{$message}}
                                   @enderror
                                </span>
                                </div>
                             </div>
                           
                             <div class="col-3">
                               <div class="col-add-product product-flex">
                                <label for="inputNanme4" class="form-label">Dimensions</label>
                                <div class="dimensions-input">
                                  <input type="text" name="varient_height[]" class="form-control" id="" placeholder="H">
                                  <input type="text" name="varient_width[]" class="form-control" id="" placeholder="W">
                                  <input type="text" name="varient_length[]" class="form-control" id="" placeholder="L">
                                 </div>
                                </div>
                             </div>
                             <div class="col-3">
                               <div class="col-add-product product-flex">
                                <label for="inputNanme4" class="form-label">Shelf life</label>
                                <input type="text" class="form-control" id="" placeholder="Shelf life" name="varient_shelf_life[]">
                                <span style="color:red;">
                                   @error('product_shelf_life')
                                     {{$message}}
                                   @enderror
                                </span>

                                </div>
                             </div>
                             <div class="col-12">
                               <div class="col-add-product product-flex">
                                <label for="inputNanme4" class="form-label">Temp requirements (If any)</label>
                                <input type="text" class="form-control" id="" placeholder="Temp requirements" name="varient_temp[]">
                                <span style="color:red;">
                                   @error('product_temp')
                                     {{$message}}
                                   @enderror
                                </span>


                                </div>
                             </div>
                          </div>
                          </div>
                          <button class="extra-fields-customer" type="button" id='add'>Add More</button>
                        </div>
                            
                        </div>
                  </div>
               </div>
            </div>
               
               <div class="card" id="single_varient">
                <div class="row">
                   <div class="col-4">
                    <div class="col-add-product product-flex">
                     <label for="inputNanme4" class="form-label">Price</label>
                     <input type="text" class="form-control" id="" placeholder="Product Price" name="price">
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
                     <input type="text" class="form-control" id="" placeholder="SKU" name="sku">
                     <span style="color:red;">
                      @error('sku')
                      {{$message}}
                       @enderror
                     </span>
                     </div>
                  </div>
                  <div class="col-4">
                    <div class="col-add-product product-flex">
                     <label for="inputNanme4" class="form-label">Weight</label>
                     <input type="text" class="form-control" name="grams" id="" placeholder="Product Weight">
                     <span style="color:red;">
                      @error('grams')
                      {{$message}}
                       @enderror
                     </span>
                     </div>
                  </div>
                  <div class="col-4">
                    <div class="col-add-product product-flex">
                     <label for="inputNanme4" class="form-label">Quantity</label>
                     <input type="text" class="form-control" id="" placeholder="Quantity available" name="quantity">
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
                    <div class="dimensions-input">
                      <input type="text" name="height" class="form-control" id="" placeholder="H">
                      <input type="text" name="width" class="form-control" id="" placeholder="W">
                      <input type="text" name="length" class="form-control" id="" placeholder="L">
                     </div>
                    </div>
                 </div>
                  <div class="col-4">
                    <div class="col-add-product product-flex">
                     <label for="inputNanme4" class="form-label">Shelf life</label>
                     <input type="text" class="form-control" id="" name="shelf_life" placeholder="Shelf life">
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
                     <input type="text" class="form-control" id="" placeholder="Temp requirements" name="temp">
                     <span style="color:red;">
                      @error('temp')
                      {{$message}}
                       @enderror
                     </span>
                     </div>
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
                      <option value="{{$data->id}}">{{$data->category}}</option>
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
                <button type="submit" class="btn btn-primary">Submit</button>
             </div>
        </form>
    </section>
   </main>

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
@stop


  
  