@extends('layouts.superadmin')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .image-container {
        position: relative;
        display: inline-block;
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

    .subpagetitle{
        border-bottom: none !important;
    }

    .sub_product_type{
     float: inline-start;
}

    span.select2.select2-container.select2-container--default {
        width: 100% !important;
        /*float: inline-start !important;*/
        text-align: left;
    }

.select2-results__option{
    text-align: left !important;
}

    table.table.table-borderless.view-productd img{
        width: unset !important;
    }


</style>
@section('main')
    <main id="main" class="main">
        <div class="subpagetitle fit-title">
            <div class="row">
                <div class="col-6">
                    <h1>{{$product_type->product_type}}</h1>
                </div>

                <div class="col-6" style="text-align: right">
                    <a class="btn btn-primary" href="#" data-bs-toggle="modal" data-bs-target="#basicModal_toAdd" >Add</a>
                    <div class="modal fade" id="basicModal_toAdd"  aria-hidden="true" style="display: none;">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Size Chart</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">

                                    <form role="form" method="POST" action="{{route('superadmin.save-product-type-subcategory')}}" enctype="multipart/form-data">

                                        @csrf
                                        @method('post')

                                        <input type="hidden" name="product_type_id" value="{{$product_type->id}}">
                                        <input type="hidden" name="vendor_id" value="{{$product_type->vendor_id}}">

                                        <label for="inputNanme4"  class="form-label sub_product_type ">Select Tag</label>
                                            <div class="col-12 mt-2">
                                            <select style="width:100%"   class="js-example-basic-multiple " required name="tags[]" multiple="multiple">

                                               @foreach($tags as $tag)
                                                <option value="{{$tag}}">{{$tag}}</option>
                                                @endforeach
                                            </select>

                                            </div>


                                        <label for="inputNanme4" class="form-label sub_product_type mt-3">HTML</label>
                                        <div class="col-12 mt-5">

                                            <div>
                                                <textarea style="width: 100% !important;" name="product_type_sub_html" id="editor_id" class="form-control editor" rows="3"></textarea>

                                            </div>
                                        </div>

                                        <div class="col-12 mt-2">



                                            <label for="inputNanme4" class="form-label mt-2 sub_product_type">Image</label>
                                            <div>
                                                <input name="product_type_sub_file" type="file" src="" class="form-control" data-height="100" />
                                            </div>
                                        </div>

                                        <div class="modal-footer">

                                            <div class="form-group">
                                                <div>

                                                    <button type="submit" class="btn btn-info btn-block">Add</button>
                                                </div>
                                            </div>

                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <section class="section up-banner">

            <form class="add-product-form" method="post" action="{{route('superadmin.update-product-type-sizechart')}}"  enctype="multipart/form-data">
                    @csrf

                        <div class="card">
                            <div class="row">
                                <h4>Default Size Chart</h4>

                                <input type="hidden" name="product_type_id" value="{{$product_type->id}}">
                                <div class="col-12 mt-2">
                                    <label for="inputNanme4" class="form-label">HTML</label>

                                    <div>
                                        <textarea style="width: 100% !important;" name="product_type_html" id="editor1" class="form-control tooltip_logo_text" rows="3">@if(isset($product_type->size_chart_html)){{$product_type->size_chart_html}}@endif </textarea>

                                    </div>
                                </div>

                                <div class="col-12 mt-2">

                                    @if(isset($product_type->size_chart_image))
                                        <div class="col-3 mt-2">
                                            <div class="image-container setting_sizechart_img ">
                                                <img src="{{$product_type->size_chart_image}}" height="120px" width="120px">
                                                <div class="delete-button">

                                                    <i class="bi bi-x-circle product_type_setting" data-id="{{$product_type->id}}"></i>
                                                </div>
                                            </div>

                                        </div>
                                    @endif

                                    <label for="inputNanme4" class="form-label mt-2">Image</label>
                                    <div>
                                        <input name="product_type_file" type="file" src="@if(isset($product_type->size_chart_image)){{$product_type->size_chart_image}}@endif" class="form-control" data-height="100" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="timer-btns">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>


            </form>

        @if(count($product_type_subcatgories) > 0)

            <div class="container">
            <div class="row">

                <h4>Product Type Tags Based</h4>


                    <div class="card table-card">
                        <div class="table-responsive">
                            <table class="table table-borderless view-productd">
                                <thead>
                                <tr>
                                    <th scope="col" style="width: 100%">Tags</th>
                                    <th scope="col">Action</th>

                                </tr>
                                </thead>
                                <tbody>

                                @foreach($product_type_subcatgories as $index=> $product_type_subcategory)
                                    <tr>
                                        <td class="alignment">
                                            @php
                                                $p_tags=explode(',',$product_type_subcategory->tags);
                                            @endphp
                                            @foreach($p_tags as $p_tag)
                                                @if($p_tag!='' || $p_tag!=null)
                                                    <span class="badge bg-success mt-1">{{$p_tag}}</span>
                                                @endif
                                            @endforeach
                                        </td>

                                        <td style="display: flex">
                                            <a href="#"  data-bs-toggle="modal" data-bs-target="#basicModal_toAdd_{{$product_type_subcategory->id}}"  class="btn btn-success mx-2 ">Edit</a>
                                            <a href="{{route('superadmin.delete-product-type-subcategory',$product_type_subcategory->id)}}" class="btn btn-danger ">Delete</a>
                                            <div class="modal fade" id="basicModal_toAdd_{{$product_type_subcategory->id}}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Size Chart</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form role="form" method="POST" action="{{route('superadmin.update-product-type-subcategory')}}" enctype="multipart/form-data">

                                                            @csrf
                                                            @method('post')
                                                        <div class="modal-body">



                                                                <input type="hidden" name="product_type_subcategory_id" value="{{$product_type_subcategory->id}}">
                                                                <input type="hidden" name="product_type_id" value="{{$product_type->id}}">
                                                                <input type="hidden" name="vendor_id" value="{{$product_type->vendor_id}}">


                                                                <label for="inputNanme4" class="form-label sub_product_type ">Select Tag</label>
                                                                <div class="col-12 mt-2">
                                                                    @php
                                                                        $check_tag=explode(',',$product_type_subcategory->tags);
                                                                        $remove_tags=\App\Models\ProductTypeSubCategory::where('id','!=',$product_type_subcategory->id)->where('product_type_id',$product_type->id)->pluck('tags');
                                                                            $product_tags=\App\Models\Product::where('product_type_id',$product_type->id)->pluck('tags');
                                                                            $tag_array=array();
                                                                                 foreach ($product_tags as $product_tag){

                                                                                     $tags_data=explode(',',$product_tag);

                                                                                     $tag_array = array_merge($tag_array, $tags_data);
                                                                                 }

                                                                                   $remove_tag_array=array();
                                                                                 foreach ($remove_tags as $remove_tag){

                                                                                     $remove_tags_data=explode(',',$remove_tag);

                                                                                     $remove_tag_array = array_merge($remove_tag_array, $remove_tags_data);
                                                                                 }



                                                                                    $tags = array_unique($tag_array);
                                                                                    $tags=array_diff($tags,$remove_tag_array);



                                                                    @endphp
                                                                    <select style="width:100%" required class="js-example-basic-multiple_{{$product_type_subcategory->id}}" name="tags[]"  multiple="multiple">
                                                                        @foreach($tags as $tag)
                                                                            <option @if(in_array($tag,$check_tag)) selected @endif  value="{{$tag}}">{{$tag}}</option>
                                                                        @endforeach
                                                                    </select>

                                                                </div>


                                                                <label for="inputNanme4" class="form-label sub_product_type mt-3">HTML</label>
                                                                <div class="col-12 mt-5">

                                                                    <div>
                                                                        <textarea style="width: 100% !important;" name="product_type_sub_html" id="editor_id_{{$product_type_subcategory->id}}" class="form-control editor" rows="3">{{$product_type_subcategory->size_chart_html}}</textarea>

                                                                    </div>
                                                                </div>

                                                                <div class="col-12 mt-2">


                                                                    @if(isset($product_type_subcategory->size_chart_image))
                                                                        <div class="col-3 mt-2">
                                                                            <div class="image-container_edit">
                                                                                <img src="{{$product_type_subcategory->size_chart_image}}" height="120px" width="120px">
                                                                                <div class="delete-button">

                                                                                    <i class="bi bi-x-circle product_type_subcategory_setting" data-id="{{$product_type_subcategory->id}}"></i>
                                                                                </div>
                                                                            </div>

                                                                        </div>
                                                                    @endif
                                                                    <label for="inputNanme4" class="form-label mt-2 sub_product_type">Image</label>
                                                                    <div>
                                                                        <input name="product_type_sub_file" type="file" src="{{$product_type_subcategory->size_chart_image}}" class="form-control" data-height="100" />
                                                                    </div>
                                                                </div>

                                                                <div class="modal-footer">

                                                                    <div class="form-group">
                                                                        <div>

                                                                            <button type="submit" class="btn btn-info btn-block">Add</button>
                                                                        </div>
                                                                    </div>

                                                                </div>

                                                        </div>
                                                        </form>

                                                    </div>
                                                </div>
                                            </div>
                                        </td>




                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>

            </div>
            </div>

            @endif
        </section>
    </main>
@endsection

@section('js')
    <script src="https://cdn.ckeditor.com/4.11.3/standard/ckeditor.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function(){



            $('.tooltip_logo_text').each(function () {
                CKEDITOR.replace($(this).prop('id'));
            });

            $('.editor').each(function () {
                CKEDITOR.replace($(this).prop('id'));
            });

            $('.js-example-basic-multiple').select2({
                dropdownParent: $('#basicModal_toAdd .modal-content')
            });



            // $('body').click('.select2-selection__choice__remove', function(e) {
            //     e.preventDefault();
            //     e.stopPropagation();
            //     alert(2);
            //     var evt = "scroll.select2"
            //     $(e.target).parents().off(evt)
            //     $(window).off(evt)
            // })

            @foreach($product_type_subcatgories as $index=> $product_type_subcategory)

            $('.js-example-basic-multiple_{{$product_type_subcategory->id}}').select2({
                dropdownParent: $('#basicModal_toAdd_{{$product_type_subcategory->id}}')
            });
                @endforeach


            $('.product_type_setting').click(function (){

                var id=$(this).data('id');
                $('.setting_sizechart_img').css('display', 'none');


                $.ajax({
                    type: 'get',
                    data: {id: id},
                    url: "{{ route('superadmin.delete.product-type-img') }}",
                    success: function (response) {
                        var json = $.parseJSON(response);
                        if (json.status == 'success') {

                            toastr.success("Image Deleted Successfully!!");
                        }
                    }
                });
            })


            $('.product_type_subcategory_setting').click(function (){

                var id=$(this).data('id');
                $(this).parents('.modal-body').find('.image-container_edit').css('display', 'none');


                $.ajax({
                    type: 'get',
                    data: {id: id},
                    url: "{{ route('superadmin.delete.product-type-subcategory-img') }}",
                    success: function (response) {
                        var json = $.parseJSON(response);
                        if (json.status == 'success') {

                            toastr.success("Image Deleted Successfully!!");
                        }
                    }
                });
            })
        });

    </script>

@endsection

