@extends('layouts.admin')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-tagsinput/1.3.6/jquery.tagsinput.min.css">
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

    .tagsinput{
        height: auto !important;
        width: 100% !important;
        min-height:unset !important;
        margin-top:5px !important;
    }
    .tagsinput div input{
        width: 90px !important;
    }
    div.tagsinput{
        padding: unset !important;
    }
    .toggle_div{
        text-align: right;
    }
    .toggle-text{
        font-weight: 600;
    }
</style>
@section('main')
<main id="main" class="main">
{{--   <div class="home-flex">--}}
{{--    <div class="pagetitle">--}}
{{--       <h1>View Category</h1>--}}
{{--    </div><!-- End Page Title -->--}}

{{--       <span class="form-switch">--}}
{{--            <span class="toggle-text">Enable Category Hierarchy</span>--}}
{{--           <input class="form-check-input" type="checkbox" onclick="changeStatus({{$user->id}})" id="flexSwitchCheckDefault_{{$user->id}}"  @if($user->category_hierarchy_status==1) {{'checked'}} @endif>--}}
{{--       </span>--}}

{{--   </div>--}}

    <div class="row mt-3">
        <div class="col-4">
            <div class="pagetitle">
            <h1>View Category</h1>
            </div>
        </div>
        <div class="col-8 toggle_div">
            <span class="toggle-text">Enable Category Hierarchy</span>
                   <span class="form-switch">
                       <input class="form-check-input" type="checkbox" onclick="changeStatus({{$user->id}})" id="flexSwitchCheckDefault_{{$user->id}}"  @if($user->category_hierarchy_status==1) {{'checked'}} @endif>
                   </span>

        </div>
    </div>
     <div class="member-plan-search header onetime-search mt-2">
            <div class="search-bar">

              </div>
              <div class="create-plan">
                <a class="btn btn-primary"  href="#" data-bs-toggle="modal" data-bs-target="#basicModal_toAdd">Add Category</a>

                  <div class="modal fade" id="basicModal_toAdd"  aria-hidden="true" style="display: none;">
                      <div class="modal-dialog modal-md">
                          <div class="modal-content">
                              <div class="modal-header">
                                  <h5 class="modal-title">Add Category</h5>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body">

                                  <form role="form" method="POST" action="{{route('save-category')}}" enctype="multipart/form-data">

                                      @csrf
                                      @method('post')

                                        <div class="row">
                                      <div class="col-12 mt-2">
                                          <label for="inputNanme4" class="form-label">Category</label>
                                          <input type="text" class="form-control" placeholder="Enter category name" name="name" required>
                                      </div>

                                            <div class="col-12 mt-2">
                                                <label for="inputNanme4" class="form-label">Tags</label>
                                                <input type="text" class="form-control tags" placeholder="Tags" name="tags">
                                            </div>

                                            <div class="col-12 mt-2">
                                                <label for="inputNanme4" class="form-label">Image</label>
                                                <input name="file" type="file" src="" class="form-control" data-height="100" />
                                            </div>

                                            <div class="col-12 mt-3">
                                                <label for="inputNanme4" class="form-label">Select Sub-Category</label>
                                           <select class="form-select" name="parent_id">

                                               <option value="0">No</option>
                                               @foreach($vendor_categories as $v_category)
                                               <option value="{{$v_category->id}}">{{$v_category->name}}</option>
                                               @endforeach
                                           </select>
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
    <section class="section up-banner">
        <div class="rcv-doc">
            <div class="table-responsive">
                @php
                    function displayCategories($categories, $level = 0) {
    foreach ($categories as $category) {
        $padding = ($level > 0) ? (30 * $level) . 'px !important;' : '0px;';

        echo '<tr>';
          // Conditionally display the image if $category->category_image exists
        if ($category->category_image) {
            echo '<td><img src="' . $category->category_image . '" width="70px"></td>';
        } else {
            echo '<td></td>';
        }
        echo '<td style="padding-left: ' . $padding . '">' . $category->name . '</td>';
        echo '<td class="icon-action">';
        echo '<a href="' . url('edit-category') . '/' . $category->id . '" data-bs-toggle="modal" data-bs-target="#basicModal_toEdit_' . $category->id . '"><i class="bi bi-pencil-fill"></i></a>';
        echo '<a href="' . url('delete-category') . '/' . $category->id . '" onclick="return confirm(\'Are you sure you want to delete this category?\');"><i class="bi bi-trash"></i></a>';
        echo '</td>';
        echo '</tr>';

        // Recursive call for child categories
        if ($category->childrenRecursive && $category->childrenRecursive->count() > 0) {
            displayCategories($category->childrenRecursive, $level + 1);
        }
    }
}

                @endphp

                <table class="table table-bordered table-white">
                    <thead>
                    <tr>
                        <th>Image</th>
                        <th>Category</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($vendor_categories_data->count() > 0)
                        @php displayCategories($vendor_categories_data) @endphp
                    @else
                        <tr>
                            <td colspan="2">No categories found!</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Modals for Edit Category -->
    @foreach($vendor_categories as $category)

        <div class="modal fade" id="basicModal_toEdit_{{ $category->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Edit Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <form role="form" method="POST" action="{{url('update-category',$category->id)}}" enctype="multipart/form-data">

                            @csrf
                            @method('post')

                            <div class="row">
                                <div class="col-12 mt-2">
                                    <label for="inputNanme4" class="form-label">Category</label>
                                    <input type="text" class="form-control" value="{{$category->name}}" placeholder="Enter category name" name="name" required>
                                </div>

                                <div class="col-12 mt-2">
                                    <label for="inputNanme4" class="form-label">Tags</label>
                                    <input type="text"   @if($category->tags) value="{{$category->tags}}"
                                           @endif class="form-control tags" placeholder="Tags" name="tags">
                                </div>

                                @if(isset($category->category_image))
                                    <div class="col-3 mt-2">
                                        <div class="image-container category_img ">
                                            <img src="{{$category->category_image}}" height="120px" width="120px">
                                            <div class="delete-button">

                                                <i class="bi bi-x-circle category_img_del" data-id="{{$category->id}}"></i>
                                            </div>
                                        </div>

                                    </div>
                                @endif

                                <div class="col-12 mt-2">
                                    <label for="inputNanme4" class="form-label">Image</label>
                                    <input name="file" type="file" src="" class="form-control" data-height="100" />
                                </div>

                                <div class="col-12 mt-3">
                                    <label for="inputNanme4" class="form-label">Select Sub-Category</label>
                                    <select class="form-select" name="parent_id">

                                        <option value="0">No</option>
                                        @foreach($vendor_categories as $v_category)
                                            <option @if($category->parent_id==$v_category->id) selected @endif value="{{$v_category->id}}">{{$v_category->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                            <div class="modal-footer">

                                <div class="form-group">
                                    <div>

                                        <button type="submit" class="btn btn-info btn-block">Update</button>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
   </main>
@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-tagsinput/1.3.6/jquery.tagsinput.min.js"></script>
    <script>
        $(document).ready(function(){
            $('.tags').tagsInput();
            $('.category_img_del').click(function (){

                var id=$(this).data('id');
                $('.category_img').css('display', 'none');


                $.ajax({
                    type: 'get',
                    data: {id: id},
                    url: "{{ route('delete-category-image') }}",
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

    <script>
        function changeStatus(id)
        {
            var v_token = "{{csrf_token()}}";
            $.ajax({
                type:'post',
                data:{id : id},
                headers: {'X-CSRF-Token': v_token},
                url:"{{ route('change-category-hierarchy-status') }}",
                success:function(response) {
                    if (response['status'] == 'success') {
                        toastr.success("Status Updated Successfully!!");
                    }
                }
            });
        }
    </script>
@endsection




