@extends('layouts.admin')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-tagsinput/1.3.6/jquery.tagsinput.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .variant-image-preview {
        width: 50px; /* Set width as needed */
        /*height: 100px; !* Set height as needed *!*/
        object-fit: cover;
        cursor: pointer; /* Optional: to indicate it's clickable */
        border: 1px solid #ccc; /* Optional: a border for visual distinction */
        padding: 5px; /* Optional: some padding for better appearance */
    }
    .rte-modern.rte-desktop.rte-toolbar-default{
        min-width: unset !important;
    }

    #product-meta-keywords_tagsinput{
        height:45px !important;
    }
    #product-meta-keywords_tag{
        width:70px !important;
    }


    .tagsinput{
        height: 45px !important;
        width: 100% !important;
        min-height:unset !important;
        /*margin-top:5px !important;*/

    }
    .tagsinput div input{
        width: 90px !important;
    }

    .preview-images-zone > .preview-image{
        height: 185px !important;
        width: 185px !important;
    }

    .category-tree ul, .category-tree li {
        list-style-type: none;
        padding-left: 0;
    }
    .toggle-icon {
        margin-right: 10px;
        cursor: pointer;
    }
    .sub-categories {
        display: none; /* Initially hide all sub-categories */
        padding-left: 20px;
    }
    .open {
        display: block !important; /* Force show when class is 'open' */
    }
    .image-container_edit {
        position: relative;
        display: inline-block;
    }
    .delete-button {
        position: absolute;
        top: 0;
        right: 0;
        color: white;
        padding: 0px 0px;
        cursor: pointer;
    }

    .bi-x-circle{
        font-size:20px;
    }

    .delete-button_single{
        position: absolute;
        top: 0;
        right: 0;
        color: white;
        padding: 0px 0px;
        cursor: pointer;
    }
</style>
@section('main')
    <main id="main" class="main">
        <div class="home-flex">
            <div class="pagetitle">
                <h1>Edit Product ({{$product->title}})</h1>
            </div><!-- End Page Title -->
        </div>
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <h5>{{ $message }}</h5>
            </div>
        @endif
        <section class="section up-banner">
            <form class="add-product-form" method="post" action="{{route('update-product',$product->id)}}" enctype="multipart/form-data" id="form_data" name="form_data" onsubmit = "return(validate());">
                @csrf
                <div class="row">
                    <div class="col-8">
                        <div class="card">
                            <div class="row">
                                <div class="col-12">
                                    <div class="col-add-product">
                                        <label for="inputNanme4" class="form-label">Title</label>
                                        <input type="text" class="form-control" id="" value="{{$product->title}}" placeholder="Product Name" name="name">
                                        <span style="color:red;" id="err_name">
                      @error('name')
                                            {{$message}}
                                            @enderror
                     </span>
                                    </div>
                                    <div class="col-add-product">
                                        <label for="inputNanme4" class="form-label">Description</label>
                                        <textarea style="width: 100% !important;" name="description" id="editor1" class="form-control " rows="3">{{$product->body_html}}</textarea>
                                        <span style="color:red;"  id="err_description">
                      @error('description')
                                            {{$message}}
                                            @enderror
                     </span>
                                    </div>
                                    {{--                            <div class="col-add-product">--}}
                                    {{--                                <label for="inputNanme4" class="form-label">Tags</label>--}}
                                    {{--                                <input type="text" class="form-control" id="tags" placeholder="Tags" name="tags" >--}}
                                    {{--                                <span style="color:red;" id="err_tags">--}}
                                    {{--                      @error('tags')--}}
                                    {{--                                    {{$message}}--}}
                                    {{--                                    @enderror--}}
                                    {{--                     </span>--}}
                                    {{--                            </div>--}}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="card">
                            <label for="inputNanme4" class="form-label">Status</label>
                            <select class="form-select" name="product_status">
                                <option @if($product->product_status=='active') selected @endif value="active">Active</option>
                                <option @if($product->product_status=='draft') selected @endif value="draft">Draft</option>
                            </select>
                        </div>
                        <div class="card">
                            <label for="inputNanme4" class="form-label">Product Organization</label>

                            <label >Product Category</label>

                            <?php
                            // Convert the string of category IDs to an array
                            $productCategoryIds = explode(',', $product->category);
                            ?>
                            <div class="category-tree">
                                <ul>
                                    @foreach($categories_data as $category)
                                        <li class="category-item">
                                            @if($category->childrenRecursive->count() > 0)
                                                <span class="toggle-icon">▶</span>
                                            @endif
                                            <input type="checkbox" name="selected_categories[]" value="{{$category->id}}" class="category-checkbox parent" data-id="{{ $category->id }}"
                                                {{ in_array($category->id, $productCategoryIds) ? 'checked' : '' }} />
                                            <label>{{ $category->name }}</label>
                                            @if($category->childrenRecursive->count() > 0)
                                                <ul class="sub-categories">
                                                    @include('subadmin.partials.child-categories-edit', ['children' => $category->childrenRecursive, 'parentId' => $category->id, 'level' => 1, 'productCategoryIds' => $productCategoryIds])
                                                </ul>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>



                            <label class="mt-3" >Product Type</label>

                            <select style="width:100%"   class="js-example-basic-multiple " required name="product_type" >

                                <option value="">Select Product Type</option>
                                @foreach($product_types as $product_type)
                                    <option @if($product_type->id==$product->product_type_id) selected @endif value="{{$product_type->id}}">{{$product_type->product_type}}</option>
                                @endforeach
                            </select>


                            <label class="mt-3" >Brand</label>
                            <input type="text" value="{{$product->brand}}" class="form-control mt-2" name="brand">

                            <label class="mt-3" >Tags</label>
                            <input type="text" class="form-control mt-2 tags" value="{{$product->orignal_tags}}" placeholder="Tags" name="tags">
                            <span style="color:red;" id="err_tags">
                      @error('tags')
                                {{$message}}
                                @enderror
                     </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card">
                        <div class="container">
                            <label for="inputNanme4" class="form-label">Media</label>
                            <fieldset class="form-group" style="text-align: center">
                                <a href="javascript:void(0)" onclick="$('#pro-image').click()" class="fileLabel">Upload image</a>
                                <input type="file" id="pro-image" name="pro_image[]" style="display: none;" class="form-control" multiple>
                            </fieldset>
                            <input type="hidden" id="img" value="{{implode(',',$prodcut_images)}}" name="images">
                            <span style="color:red;">
                      @error('profile')
                                {{$message}}
                                @enderror
                     </span>

                            <div class="preview-images-zone"></div>
                            <div class="row">
                            @if($product->ProductImages)
                                @foreach($product->ProductImages as $image)
                                <div class="col-2 mt-2 existing_img_{{$image->id}}">
                                    <div class="image-container_edit">
                                        <img src="{{$image->image}}" height="120px" width="120px">
                                        <div class="delete-button">
                                            <i class="bi bi-x-circle product_images_del" data-id="{{$image->id}}"></i>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                                    @endif
                            </div>
                        </div>
                    </div>
                </div>
                <span class="file0-size">Suggestion Size - 1080px * 1080xp </span>

                <div class="card">
                    <div class="row">
                        <div class="col-12 multiple-vriant">
                            <label for="" class="form-label"><b>If the product has variants, Please Select Option</b></label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="payradious" id="payradious" value="1" onchange="showHideDiv()"  @if($product->is_variants==1) checked @endif >
                                <label class="form-check-label" for="payradious">
                                    This product has variants, like size or color</label>


                                <div class="row show-options" id="multivarient" >
                                    <div class="variant_options"    >
                                        <hr>
                                        @php
                                            $options = json_decode($product->options); // Decode the JSON string to PHP array
                                        @endphp

                                        <h3 class="font-w300">Options</h3>
                                        <br>

                                        <div class="form-group">
                                            <div class="col-xs-12 push-10">
                                                <h5>Option 1</h5>
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <input type="text" class="form-control" value="@if(isset($options[0])){{$options[0]->name}}@endif" placeholder="Attribute Name" name="attribute1">
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <input class="js-tags-options options-preview form-control" type="text"
                                                               id="product-meta-keywords" name="option1" value="@if(isset($options[0])){{ implode(',', $options[0]->values) }}@endif">
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-light btn-square option_btn_1 mt-2">
                                                    Add another option
                                                </button>
                                            </div>
                                        </div>
                                        <div class="option_2" style="@if(isset($options[1]))display: block;@else display:none @endif">
                                            <hr>
                                            <div class="form-group">
                                                <div class="col-xs-12 push-10">
                                                    <h5>Option 2</h5>
                                                    <div class="row">
                                                        <div class="col-sm-3">
                                                            <input type="text" value="@if(isset($options[1])){{$options[1]->name}}@endif" class="form-control" placeholder="Attribute Name" name="attribute2">
                                                        </div>
                                                        <div class="col-sm-9">
                                                            <input class="js-tags-options options-preview form-control" value="@if(isset($options[1])){{ implode(',', $options[1]->values) }}@endif" type="text"
                                                                   id="product-meta-keywords" name="option2">
                                                        </div>
                                                    </div>
                                                    <button type="button"
                                                            class="btn btn-light btn-square option_btn_2 mt-2">Add another
                                                        option
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="option_3" style="@if(isset($options[2]))display: block;@else display:none @endif">
                                            <hr>
                                            <div class="form-group">
                                                <div class="col-xs-12 push-10">
                                                    <h5>Option 3</h5>
                                                    <div class="row">
                                                        <div class="col-sm-3">
                                                            <input type="text" value="@if(isset($options[2])){{$options[2]->name}}@endif" class="form-control" placeholder="Attribute Name" name="attribute3">
                                                        </div>
                                                        <div class="col-sm-9">
                                                            <input class="js-tags-options options-preview form-control" value="@if(isset($options[2])){{ implode(',', $options[2]->values) }}@endif" type="text"
                                                                   id="product-meta-keywords" name="option3">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="variants_table" style="display: none;">
                                            <hr>
                                            <h3 class="block-title">Preview</h3>
                                            <br>
                                            <div class="form-group">
                                                <div class="col-xs-12 push-10">
                                                    <table class="table table-hover table-responsive">
                                                        <thead>
                                                        <tr>
                                                            <th style="width: 15%;">Image</th>
                                                            <th style="width: 10%;">Title</th>
                                                            <th style="width: 12%;">Price</th>
                                                            <th style="width: 10%;">Weight(GM)</th>
                                                            <th style="width: 10%;">Quantity</th>
                                                            <th style="width: 15%;">SKU</th>
                                                            <th style="width: 15%;">Dimensions</th>
                                                            <th style="width: 12%;">Shelf life</th>
                                                            <th style="width: 10%;">Temp requirements</th>

                                                        </tr>
                                                        </thead>
                                                        <tbody>
{{--                                                        @foreach($product->variants as $variant)--}}
{{--                                                            @php--}}
{{--                                                            $dimensions=explode('-',$variant->dimensions);--}}
{{--                                                            $height=isset($dimensions[0])?$dimensions[0]:null;--}}
{{--                                                            $width=isset($dimensions[1])?$dimensions[1]:null;--}}
{{--                                                            $length=isset($dimensions[2])?$dimensions[2]:null;--}}
{{--                                                            @endphp--}}
{{--                                                        <tr>--}}
{{--                                                            <td class="variant_title">{{$variant->title}} <input type="hidden" name="varient_value[]" value="{{$variant->title}}"></td>--}}
{{--                                                            <td><input type="number" step="any" class="form-control" name="varient_price[]" placeholder="$0.00" value="{{$variant->base_price}}"></td>--}}
{{--                                                            <td><input type="number" step="any" class="form-control" name="varient_grams[]" value="{{$variant->grams}}" placeholder=""></td>--}}
{{--                                                            <td><input type="number" step="any" class="form-control" name="varient_quantity[]" value="{{$variant->qty}}" placeholder="0"></td>--}}
{{--                                                            <td><input type="text" class="form-control" name="varient_sku[]" value="{{$variant->sku}}"></td>--}}
{{--                                                            <td class="dimensions-input">--}}
{{--                                                                <input type="text" name="varient_height[]" value="{{$height}}" class="form-control qty" placeholder="H">--}}
{{--                                                               <input type="text" name="varient_width[]" value="{{$width}}" class="form-control qty" placeholder="W">--}}
{{--                                                                   <input type="text" name="varient_length[]" value="{{$length}}" class="form-control qty" placeholder="L">--}}
{{--                                                            </td>--}}
{{--                                                            <td><input type="text" class="form-control" name="varient_shelf_life[]" value="{{$variant->shelf_life}}"></td>--}}
{{--                                                            <td><input type="text" class="form-control" name="varient_temp[]" value="{{$variant->temp_require}}"></td>--}}

{{--                                                            <td><input type="file" class="form-control" name="varient_image[]" placeholder=""></td>--}}
{{--                                                        </tr>--}}
{{--                                                        @endforeach--}}
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>



                                    </div>
                                    {{--                                    <button class="extra-fields-customer" type="button" id='add'>Add More</button>--}}
                                </div>


                            </div>
                        </div>
                    </div>
                </div>

                <div class="card" id="single_varient" @if($product->is_variants==0) style="display: block" @else style="display: none"  @endif >
                    @if($product->variants)
                   @foreach($product->variants as $s_varaint)
                            @php
                           $dimensions=explode('-',$s_varaint->dimensions);
                             $height=isset($dimensions[0])?$dimensions[0]:null;
                       $width=isset($dimensions[1])?$dimensions[1]:null;
                      $length=isset($dimensions[2])?$dimensions[2]:null;
                                                                                        @endphp
                    <div class="row">
                        <div class="col-4">
                            <div class="col-add-product product-flex">
                                <label for="inputNanme4" class="form-label">Price</label>
                                <input type="text" class="form-control price" id="" value="{{$s_varaint->base_price}}" placeholder="Product Price" name="price">
                                <span style="color:red;" id="err_price">
                      @error('price')
                                    {{$message}}
                                    @enderror
                     </span>
                            </div>

                        </div>
                        <div class="col-4">
                            <div class="col-add-product product-flex">
                                <label for="inputNanme4" class="form-label">SKU</label>
                                <input type="text" class="form-control" value="{{$s_varaint->sku}}" id="" placeholder="SKU" name="sku">
                                <span style="color:red;" id="err_sku">
                      @error('sku')
                                    {{$message}}
                                    @enderror
                     </span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="col-add-product product-flex">
                                <label for="inputNanme4" class="form-label">Weight(GM)</label>
                                <input type="text" class="form-control gm" name="grams" value="{{$s_varaint->grams}}" id="" placeholder="Product Weight">
                                <span style="color:red;" id="err_grams">
                      @error('grams')
                      <span style="color:red;">The weight(GM) field is required.</span>
                       @enderror
                     </span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="col-add-product product-flex">
                                <label for="inputNanme4" class="form-label">Quantity</label>
                                <input type="text" class="form-control qty" id="" value="{{$s_varaint->qty}}" placeholder="Quantity available" name="quantity">
                                <span style="color:red;" id="err_quantity">
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
                                    <input type="text" name="height" value="{{$height}}" class="form-control qty" id="" placeholder="H">
                                    <input type="text" name="width" value="{{$width}}" class="form-control qty" id="" placeholder="W">
                                    <input type="text" name="length" value="{{$length}}" class="form-control qty" id="" placeholder="L">
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="col-add-product product-flex">
                                <label for="inputNanme4" class="form-label">Shelf life</label>
                                <input type="text" value="{{$s_varaint->shelf_life}}" class="form-control" id="" name="shelf_life" placeholder="Shelf life">
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
                                <input type="text" value="{{$s_varaint->temp_require}}" class="form-control" id="" placeholder="Temp requirements" name="temp">
                                <span style="color:red;">
                      @error('temp')
                                    {{$message}}
                                    @enderror
                     </span>
                            </div>
                        </div>

                        @php
                        $s_image=\App\Models\ProductImages::where('variant_ids',$s_varaint->id)->first();

                        @endphp
                        @if($s_image)
                            <div class="col-2 mt-2 existing_img_{{$s_image->id}}">
                                <input type="hidden" name="existing_single_img" id="s_image_data" value="{{$s_image->image}}">
                                <div class="image-container_edit setting_sizechart_img ">
                                    <img src="{{$s_image->image}}" height="120px" width="120px">
                                    <div class="delete-button_single">
                                        <i class="bi bi-x-circle product_images_del" data-id="{{$s_image->id}}"></i>
                                    </div>
                                </div>

                            </div>
                        @endif
                        <div class="col-12">
                            <label for="inputNanme4" class="form-label">Upload image</label>
                            <input type="file" class="form-control" name="image" accept="image/*">
                            <span style="color:red;">
                         @error('image')
                                {{$message}}
                                @enderror
                      </span>
                        </div>
                    </div>
                    @endforeach
                        @endif
                </div>
                <meta name="csrf-token" content="{{ csrf_token() }}" />



                <div class="timer-btns pro-submit">
                    <a href="{{url('home')}}" class="btn btn-light">Back</a>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </section>
    </main>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <script>
        function validate()
        {
            document.getElementById('err_name').innerHTML="";
            document.getElementById('err_description').innerHTML="";
            document.getElementById('err_tags').innerHTML="";
            document.getElementById('err_category').innerHTML="";
            document.getElementById('err_price').innerHTML="";
            document.getElementById('err_sku').innerHTML="";
            document.getElementById('err_grams').innerHTML="";
            document.getElementById('err_quantity').innerHTML="";
            document.getElementById('err_var').innerHTML="";
            if( document.form_data.name.value == "" ) {
                document.getElementById('err_name').innerHTML="Please Enter Product Name";
                document.form_data.name.focus() ;
                return false;
            }
            if( document.form_data.description.value == "" ) {
                document.getElementById('err_description').innerHTML="Please Enter Description";
                document.form_data.description.focus() ;
                return false;
            }
            if( document.form_data.tags.value == "" ) {
                document.getElementById('err_tags').innerHTML="Please Enter Tags";
                document.form_data.tags.focus() ;
                return false;
            }
            if (document.getElementById('payradious').checked) {
                var varient_name = document.getElementsByName('varient_name[]');
                for (var i = 0, iLen = varient_name.length; i < iLen; i++) {
                    if(varient_name[i].value=="")
                    {
                        document.getElementById('err_var').innerHTML="The varient name field is required.";
                        return false;
                    }
                    if(varient_name[i].value!=varient_name[0].value)
                    {
                        document.getElementById('err_var').innerHTML="The varient name same is required.";
                        return false;
                    }
                }
                var varient_value = document.getElementsByName('varient_value[]');
                for (var i = 0, iLen = varient_value.length; i < iLen; i++) {
                    if(varient_value[i].value=="")
                    {
                        document.getElementById('err_var').innerHTML="The varient value field is required.";
                        return false;
                    }
                }
                var varient_price = document.getElementsByName('varient_price[]');
                for (var i = 0, iLen = varient_price.length; i < iLen; i++) {
                    if(varient_price[i].value=="")
                    {
                        document.getElementById('err_var').innerHTML="The varient price field is required.";
                        return false;
                    }
                }
                var varient_sku = document.getElementsByName('varient_sku[]');
                for (var i = 0, iLen = varient_sku.length; i < iLen; i++) {
                    if(varient_sku[i].value=="")
                    {
                        document.getElementById('err_var').innerHTML="The varient sku field is required.";
                        return false;
                    }
                }
                var varient_grams = document.getElementsByName('varient_grams[]');
                for (var i = 0, iLen = varient_grams.length; i < iLen; i++) {
                    if(varient_grams[i].value=="")
                    {
                        document.getElementById('err_var').innerHTML="The varient weight field is required.";
                        return false;
                    }
                }
                var varient_quantity = document.getElementsByName('varient_quantity[]');
                // for (var i = 0, iLen = varient_quantity.length; i < iLen; i++) {
                // 	if(varient_quantity[i].value=="")
                // 	{
                // 		document.getElementById('err_var').innerHTML="The varient quantity field is required.";
                // 		return false;
                // 	}
                // }


            } else {
                if( document.form_data.price.value == "" ) {
                    document.getElementById('err_price').innerHTML="Please Enter Product Price";
                    document.form_data.price.focus() ;
                    return false;
                }
                if( document.form_data.sku.value == "" ) {
                    document.getElementById('err_sku').innerHTML="Please Enter SKU";
                    document.form_data.sku.focus() ;
                    return false;
                }
                if( document.form_data.grams.value == "" ) {
                    document.getElementById('err_grams').innerHTML="Please Enter Product Weight";
                    document.form_data.grams.focus() ;
                    return false;
                }
                // if( document.form_data.quantity.value == "" ) {
                // 	document.getElementById('err_quantity').innerHTML="Please Enter Product Quantity";
                // 	document.form_data.quantity.focus() ;
                // 	return false;
                // }
            }
            if( document.form_data.category.value == "" ) {
                document.getElementById('err_category').innerHTML="The category field is required.";
                document.form_data.category.focus() ;
                return false;
            }
            return( true );
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



        $(document).ready(function () {
            $(document).on('click', '.variant-image-preview', function() {
                $(this).next('.image-input').click(); // Simulate a click on the hidden file input
            });

// This is to capture the change event when a file is selected through the hidden input
            $(document).on('change', '.image-input', function(e) {
                var input=$(this);
                var file = e.target.files[0];
                var reader = new FileReader();

                reader.onload = function(e) {
                    $(input).parents('.image-preview-cell').find('.variant-image-preview').attr('src', e.target.result);
                };

                reader.readAsDataURL(file);
            });

            $('.product_images_del').click(function (){

                var id=$(this).data('id');
                $('.existing_img_'+id).css('display', 'none');

                product_id={{$product->id}}
                $.ajax({
                    type: 'get',
                    data: {id: id,product_id:product_id},
                    url: "{{ route('delete.product-existing-img') }}",
                    success: function (response) {
                        console.log('response',response);

                        if(response['status']=='success'){

                            var img_val=$('#img').val('');
                            // var json = $.parseJSON(response);

                                $('#img').val(response['product_ids']);

                            toastr.success("Image Deleted Successfully!!");
                        }
                    }
                });
            })


            var variants_data = {!! json_encode($product->variants) !!};
            var variants_data_stringified = JSON.stringify(variants_data);
            var parsedVariantsData = JSON.parse(variants_data_stringified);


            var images_data = {!! json_encode($product->ProductImages) !!};
            var images_data_stringified = JSON.stringify(images_data);
            var parsedImagesData = JSON.parse(images_data_stringified);
        console.log(parsedImagesData,'parsedImagesData');

            function findRecordByTitle(searchTitle) {
                return parsedVariantsData.find(function(item) {
                    return item.title === searchTitle;
                });
            }

            function findRecordById(variant_id) {
                return parsedImagesData.find(function(item1) {
                    console.log(item1,'item1');
                    console.log(variant_id,'variant_id');
                    return item1.variant_ids === variant_id;
                });
            }

            function updateToggleIcons() {
                $('.category-checkbox').each(function() {
                    const $this = $(this);
                    const isChecked = $this.prop('checked');
                    const $parentLi = $this.closest('.category-item');
                    const $toggleIcon = $parentLi.find('.toggle-icon');
                    const $subCategories = $parentLi.find('.sub-categories');

                    // Update toggle icon based on checkbox state
                    $toggleIcon.text(isChecked ? '▼' : '▶');

                    // Toggle sub-categories visibility if checked
                    $subCategories.toggleClass('open', isChecked);
                });
            }

            // Call the function when the page loads
            updateToggleIcons();


            $('.js-example-basic-multiple').select2({
                tags: true,
                tokenSeparators: [",", " "],
                createTag: function (tag) {
                    return {
                        id: tag.term,
                        text: tag.term,
                        isNew : true
                    };
                }
            }).on("select2:select", function(e) {
                if(e.params.data.isNew){
                    var r = confirm("do you want to create a New Product Type?");
                    if (r == true) {

                        $.ajax({
                            url: '/add-product-type',
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Fetch the CSRF token from the meta tag
                            },
                            data: {
                                product_type: e.params.data.text // send the new product type text
                            },
                            success: function(response) {
                                if (response.status === 'success') {
                                    var newOption = new Option(response.product_type_name, response.product_type_id, true, true);
                                    $('.js-example-basic-multiple').append(newOption).trigger('change');
                                } else {
                                    console.log(response,'dssd');
                                    // alert('Failed to create the new Product Type.');
                                }
                            },
                            error: function() {
                                // alert('Error occurred while saving the new Product Type.');
                            }
                        });

                    }
                    else
                    {
                        $('.select2-selection__choice:last').remove();
                        $('.select2-search__field').val(e.params.data.text).focus()
                    }
                }

            });

            $('.toggle-icon').click(function() {
                $(this).siblings('.sub-categories').toggleClass('open');
                $(this).text(function(i, text) {
                    return text === '▶' ? '▼' : '▶';
                });
            });


            function selectAncestors(categoryId) {
                const parentCheckbox = $(`.category-checkbox[data-id="${categoryId}"]`);
                parentCheckbox.prop('checked', true);

                const grandparentId = parentCheckbox.data('parent-id');
                if (grandparentId) {
                    selectAncestors(grandparentId); // Recursive call to select its ancestors
                }
            }

            function deselectAncestors(categoryId) {
                const parentCheckbox = $(`.category-checkbox[data-id="${categoryId}"]`);
                parentCheckbox.prop('checked', false);

                const grandparentId = parentCheckbox.data('parent-id');
                if (grandparentId) {
                    deselectAncestors(grandparentId); // Recursive call to deselect its ancestors
                }
            }

            $('.category-checkbox.child').change(function() {
                const parentId = $(this).data('parent-id');

                if ($(this).prop('checked')) {
                    // If checked, select its ancestors
                    selectAncestors(parentId);
                } else {
                    // If unchecked, deselect its ancestors
                    deselectAncestors(parentId);
                }
            });




            document
                .getElementById("pro-image")
                .addEventListener("change", readImage, false);

            //$(".preview-images-zone").sortable();

            $(document).on("click", ".image-cancel", function () {
                let no = $(this).data("no");
                $(".preview-image.preview-show-" + no).remove();
            });

            $('input[type="checkbox"][name="variants"]').click(function () {
                if ($(this).prop("checked") == true) {
                    $('.variant_options').show();
                } else if ($(this).prop("checked") == false) {
                    $('.variant_options').hide();
                }
            });
            $('.option_btn_1').click(function () {
                if($(this).prev().find('.options-preview').val() !== ''){
                    $('.option_2').show();
                    $('.option_btn_1').hide();
                }
                else{
                    toastr.error('The Option1 must have atleast one option value');
                }

            });
            $('.option_btn_2').click(function () {
                if($(this).prev().find('.options-preview').val() !== ''){
                    $('.option_3').show();
                    $('.option_btn_2').hide();
                }
                else{
                    toastr.error('The Option2 must have atleast one option value');
                }
            });


            $('.tags').tagsInput();

            $('.js-tags-options').tagsInput({
                height: '36px',
                width: '100%',
                defaultText: 'Add tag',
                removeWithBackspace: true,
                onRemoveTag:function(){
                    var option1 = $('input[type="text"][name="option1"]').val();
                    var option2 = $('input[type="text"][name="option2"]').val();
                    if(option1 === ''){
                        $('input[type="text"][name="option2"]').val('');
                        $('input[type="text"][name="option3"]').val('');
                        $('.option_2').hide();
                        $('.option_3').hide();
                        $('.option_btn_2').hide();
                        $('.option_btn_1').show();
                        $('.variants_table').hide();
                        $("tbody").empty();

                    }
                    if(option2 === ''){
                        $('input[type="text"][name="option3"]').val('');
                        $('.option_3').hide();
                        $('.option_btn_2').show();


                    }
                },
                onChange: function(){

                    var option1 = $('input[type="text"][name="option1"]').val();
                    console.log(option1);
                    var option2 = $('input[type="text"][name="option2"]').val();
                    var option3 = $('input[type="text"][name="option3"]').val();
                    var substr1 = option1.split(',');
                    var substr2 = option2.split(',');
                    var substr3 = option3.split(',');

                    $('.variants_table').show();
                    $("tbody").empty();
                    var data_array=[];
                    var title = '';
                    jQuery.each(substr1, function (index1, item1) {
                        title = item1;
                        console.log('1',title);
                        jQuery.each(substr2, function (index2, item2) {
                            if(item2 !== ''){
                                title = item1+'/'+item2;

                            }
                            jQuery.each(substr3, function (index3, item3) {

                                if(item3 !== ''){
                                    title = item1+'/'+item2+'/'+item3;

                                }
                                var record = findRecordByTitle(title);
                                var price ='';
                                var weight = '';
                                var empty_image = "{{ asset('empty.jpg') }}";
                                var image='';
                                var sku = '';
                                var quantity = '';
                                var height;
                                var width='';
                                var id='';
                                var length='';
                                var shelf_life='';
                                var temp_require='';
                                if(record!=undefined){
                                    price=record.base_price;
                                   var image_get = findRecordById(record.id);
                                    if(image_get!=undefined){
                                        image=image_get.image;
                                        id=image_get.id;
                                    }
                                    weight=record.grams ? record.grams:'';

                                    quantity=record.qty ? record.qty: '';
                                    sku=record.sku ? record.sku: '';
                                    dimensions=record.dimensions;
                                    var splittedDimensions = dimensions.split('-');
                                    height = splittedDimensions.length > 0 ? splittedDimensions[0] : '';
                                    width = splittedDimensions.length > 1 ? splittedDimensions[1] : '';
                                    length = splittedDimensions.length > 2 ? splittedDimensions[2] : '';
                                   shelf_life=record.shelf_life ? record.shelf_life : '';
                                   temp_require=record.temp_require ? record.temp_require : '';
                                }


                                $('tbody').append('   <tr>\n' +
                                    '<td class="image-preview-cell">\n' +
                                    '   <img class="variant-image-preview" src="' + (image ? image : empty_image) + '" alt="Variant Image">\n' +
                                    '   <input type="file" class="form-control image-input" name="varient_image[]" accept="image/*" style="display: none;">\n' +
                                    '</td>\n'+
                                    // '   <td><input type="file" class="form-control" name="varient_image[]" placeholder=""></td>\n' +
                                   ' <input type="hidden"  class="form-control" name="varient_id[]" placeholder="$0.00" value="' + id + '">'+
                                   ' <input type="hidden"  class="form-control" name="existing_varient_images[]" placeholder="$0.00" value="' + image + '">'+
                                    '   <td class="variant_title">' + title + '<input type="hidden" name="varient_value[]" value="' + title + '"></td>\n' +
                                    '   <td><input type="number" step="any" class="form-control" name="varient_price[]" placeholder="$0.00" value="' + price + '"></td>\n' +
                                    '   <td><input type="number" step="any" class="form-control" name="varient_grams[]" value="' + weight + '" placeholder=""></td>\n' +
                                    '   <td><input type="number" step="any" class="form-control" name="varient_quantity[]" value="' + quantity + '" placeholder="0"></td>\n' +
                                    '   <td><input type="text" class="form-control" name="varient_sku[]" value="' + sku + '"></td>\n' +
                                    '   <td class="dimensions-input">\n' +
                                    '       <input type="text" name="varient_height[]" value="' + height + '" class="form-control qty" placeholder="H">\n' +
                                    '       <input type="text" name="varient_width[]" value="' + width + '" class="form-control qty" placeholder="W">\n' +
                                    '       <input type="text" name="varient_length[]" value="' + length + '" class="form-control qty" placeholder="L">\n' +
                                    '   </td>\n' +
                                    '   <td><input type="text" class="form-control" name="varient_shelf_life[]" value="' + shelf_life + '"></td>\n' +
                                    '   <td><input type="text" class="form-control" name="varient_temp[]" value="' + temp_require + '"></td>\n' +
                                    '</tr>');

                            });
                        });
                    });

                    console.log(data_array,'data_array');
                },
                delimiter: [',']
            });
        });

        function variantDetail(title){
            var product_id={{$product->id}}
            $.ajax({
                type: 'get',
                data: {id: product_id,title:title},
                url: "{{ route('get.variant.detail') }}",
                success: function (response) {
                    if(response['status']=='success'){

                        price = response['product_variant']['base_price'];


                    }
                }
            });
        }
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
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <script >
        $('#pro-image').change(function(e){
            let TotalFiles = $('#pro-image')[0].files.length;
            profile = e.target.files[0];
            var formData= new FormData();
            let files = $('#pro-image')[0].files;
            for (let i = 0; i < files.length; i++) {
                formData.append('profile[]', files[i]);
            }
            $.ajax({
                cache: false,
                contentType: false,
                processData: false,
                type:'post',
                data:formData,
                headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')},
                url:"{{ route('save-product') }}",
                success:function(response){
                    console.log('response',response);
                    var img_val=$('#img').val();
                    // var json = $.parseJSON(response);
                    if(img_val=='')
                        $('#img').val(response.product_ids);
                    else
                    {
                        $('#img').val(img_val+","+response.product_ids);
                    }
                }
            });
        });

    </script>
@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-tagsinput/1.3.6/jquery.tagsinput.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        var editor1 = new RichTextEditor("#editor1", { editorResizeMode: "none" });
    </script>

@stop





