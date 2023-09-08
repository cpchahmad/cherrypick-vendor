@extends('layouts.superadmin')

  <main id="main" class="main">
   <div class="home-flex">
    <div class="pagetitle">
       <h1>Add Product</h1>
    </div><!-- End Page Title -->
   </div>
    <section class="section up-banner">
        <form class="add-product-form">
            <div class="card">
                <div class="row">
                   <div class="col-12">
                    <div class="col-add-product">
                     <label for="inputNanme4" class="form-label">Name</label>
                     <input type="text" class="form-control" id="" placeholder="Product Name">
                     </div>
                     <div class="col-add-product">
                     <label for="inputNanme4" class="form-label">Short Description</label>
                     <textarea name="addition" id="" class="form-control" placeholder="Short Description"></textarea>
                  </div>
                  </div>
               </div>
            </div>
              <div class="col-md-12">
                <div class="card">
                  <div class="container">
                    <fieldset class="form-group">
                        <a href="javascript:void(0)" onclick="$('#pro-image').click()" class="fileLabel">Add File</a>
                        <input type="file" id="pro-image" name="pro-image" style="display: none;" class="form-control" multiple>
                    </fieldset>
                    <div class="preview-images-zone"></div>
                </div>
                 </div>
               </div>
               <span class="file0-size">Suggestion Size - 1080px * 1080xp </span>
               <div class="card">
                <div class="row">
                   <div class="col-4">
                    <div class="col-add-product product-flex">
                     <label for="inputNanme4" class="form-label">Price</label>
                     <input type="text" class="form-control" id="" placeholder="Product Price">
                     </div>
                   
                  </div>
                  <div class="col-4">
                    <div class="col-add-product product-flex">
                     <label for="inputNanme4" class="form-label">Compare at price</label>
                     <input type="text" class="form-control" id="" placeholder="Compare at price">
                     </div>
                  </div>
                  <div class="col-4">
                    <div class="col-add-product product-flex">
                     <label for="inputNanme4" class="form-label">Weight</label>
                     <input type="text" class="form-control" id="" placeholder="Product Weight">
                     </div>
                  </div>
                  <div class="col-4">
                    <div class="col-add-product product-flex">
                     <label for="inputNanme4" class="form-label">Quantity</label>
                     <input type="text" class="form-control" id="" placeholder="Quantity available">
                     </div>
                  </div>
                  <div class="col-4">
                    <div class="col-add-product product-flex">
                     <label for="inputNanme4" class="form-label">Dimensions</label>
                     <input type="text" class="form-control" id="" placeholder="Dimensions">
                     </div>
                  </div>
                  <div class="col-4">
                    <div class="col-add-product product-flex">
                     <label for="inputNanme4" class="form-label">Shelf life</label>
                     <input type="text" class="form-control" id="" placeholder="Shelf life">
                     </div>
                  </div>
                  <div class="col-12">
                    <div class="col-add-product product-flex">
                     <label for="inputNanme4" class="form-label">Temp requirements (If any)</label>
                     <input type="text" class="form-control" id="" placeholder="Temp requirements">
                     </div>
                  </div>
               </div>
            </div>
            <div class="card">
                <div class="row">
                   <div class="col-12 multiple-vriant">
                        <label for="" class="form-label"><b>If the product has variants, Please Select Option</b></label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="payradious" id="payradious" value="">
                            <label class="form-check-label" for="payradious">
                              This product has variants, like size or color</label>
                        
                        <div class="row show-options">
                          <div class="customer_records_dynamic"></div>
                          <div class="customer_records">
                            <div class="row">
                              <div class="col-3">
                               <div class="col-add-product product-flex">
                                <label for="inputNanme4" class="form-label">Variant Name</label>
                                <input type="text" class="form-control" id="" placeholder="Variant Name">
                                </div>
                              
                             </div>
                             <div class="col-3">
                               <div class="col-add-product product-flex">
                                <label for="inputNanme4" class="form-label">Variant Value</label>
                                <input type="text" class="form-control" id="" placeholder="Variant Value">
                                </div>
                             </div>
                             <div class="col-3">
                              <div class="col-add-product product-flex">
                               <label for="inputNanme4" class="form-label">Price</label>
                               <input type="text" class="form-control" id="" placeholder="Price">
                               </div>
                            </div>
                            <div class="col-3">
                              <div class="col-add-product product-flex">
                               <label for="inputNanme4" class="form-label">SKU</label>
                               <input type="text" class="form-control" id="" placeholder="SKU">
                               </div>
                            </div>
                             <div class="col-3">
                               <div class="col-add-product product-flex">
                                <label for="inputNanme4" class="form-label">Weight</label>
                                <input type="text" class="form-control" id="" placeholder="Product Weight">
                                </div>
                             </div>
                             <div class="col-3">
                               <div class="col-add-product product-flex">
                                <label for="inputNanme4" class="form-label">Quantity</label>
                                <input type="text" class="form-control" id="" placeholder="Quantity">
                                </div>
                             </div>
                             <div class="col-3">
                               <div class="col-add-product product-flex">
                                <label for="inputNanme4" class="form-label">Dimensions</label>
                                <input type="text" class="form-control" id="" placeholder="Dimensions">
                                </div>
                             </div>
                             <div class="col-3">
                               <div class="col-add-product product-flex">
                                <label for="inputNanme4" class="form-label">Shelf life</label>
                                <input type="text" class="form-control" id="" placeholder="Shelf life">
                                </div>
                             </div>
                             <div class="col-12">
                               <div class="col-add-product product-flex">
                                <label for="inputNanme4" class="form-label">Temp requirements (If any)</label>
                                <input type="text" class="form-control" id="" placeholder="Temp requirements">
                                </div>
                             </div>
                          </div>
                          </div>
                          <button class="extra-fields-customer" type="button">Add More</button>
                        </div>
                        </div>
                  </div>
               </div>
            </div>
            <div class="card">
              <div class="row">
                 <div class="col-12">
                  <div class="col-add-product product-flex">
                   <label for="inputNanme4" class="form-label">Category</label>
                   <select class="form-select" aria-label="Default select example">
                      <option selected="">Open this select Category</option>
                      <option value="1">One</option>
                      <option value="2">Two</option>
                      <option value="3">Three</option>
                    </select>
                   </div>
                </div>
             </div>
          </div>
            <div class="card">
                <div class="row">
                    <div class="col-6">
                        <div class="col-add-product product-flex">
                         <label for="inputNanme4" class="form-label">SKU</label>
                         <input type="text" class="form-control" id="" placeholder="Product SKU">
                         </div>
                      </div>
                      <div class="col-6">
                        <div class="col-add-product product-flex">
                         <label for="inputNanme4" class="form-label">Tag</label>
                         <input type="text" class="form-control" id="" placeholder="Product Tag">
                         </div>
                      </div>
               </div>
            </div>
            <div class="timer-btns pro-submit">
              <a href="{{url('product-list')}}" class="btn btn-light">Back</a>
                <button type="submit" class="btn btn-primary">Submit</button>
             </div>
        </form>
    </section>
   </main>
  <!-- End #main -->
  <!-- ======= Footer ======= -->
  
 <script>
    $('.sidebar-nav .nav-link:not(.collapsed) ~ .nav-content').addClass('show');
  
    jQuery(function($) {
     var path = window.location.href; // because the 'href' property of the DOM element is the absolute path
     $('ul a').each(function() {
      if (this.href === path) {
       $(this).addClass('active');
      }
     });
    });
  </script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
 
 <script>
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
