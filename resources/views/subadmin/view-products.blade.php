@extends('layouts.admin')
<style>
    .member-plan-search .search-form.d-flex.align-items-center{
        margin: 0;
    }
    .member-plan-search.header.onetime-search .search-bar{
        width:60% !important;
    }
</style>
@section('main')
  <main id="main" class="main">
   <div class="home-flex">
    <div class="pagetitle">
       <h1>Product List</h1>
    </div><!-- End Page Title -->
   </div>
   <div class="member-plan-search header onetime-search">
    <div class="search-bar">
        <form class="search-form d-flex align-items-center" method="get" action="">

          <label>Search Products</label>
            <input type="text" name="search" id='search' value='{{Request::get('search')}}' placeholder="Search products" title="Enter search keyword">
            <button type="button" title="Search" onclick="filterByName()"><i class="bi bi-search"></i></button>
        </form>


      </div>

       <div class="label-area sort-area mx-2">
           <select class="form-select" aria-label="Default select example" onchange='filterByStatus(this.value)'>
               <option value=''  selected="">Select Status</option>
               <option value="0" {{ Request::get('status') == "0" ? 'selected' : '' }}>Pending</option>
               <option value="1" {{ Request::get('status') == "1" ? 'selected' : '' }}>Approved</option>
               <option value="2" {{ Request::get('status') == "2" ? 'selected' : '' }}>Changes Pending</option>
               <option value="3" {{ Request::get('status') == "3" ? 'selected' : '' }}>Deny</option>

           </select>
       </div>
      <div class="create-plan">
        <a class="btn btn-primary" href="{{route('add-product')}}">Add New Product</a>
      </div>
   </div>
    <section class="section up-banner">
        <form class="add-product-form">
                <div class="card table-card">
                  <div class="table-responsive">
                    <table class="table table-borderless view-productd">
                        <thead>
                          <tr>
                            <th scope="col">Preview</th>
                            <th scope="col">Product</th>
                            <th scope="col">Type</th>
                            <th scope="col">Tags</th>
                            <th scope="col">Status</th>
                            <th scope="col">Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($product as $row)
                          <tr>
                            @php
                                $image=\App\Models\ProductImages::where(['product_id' => $row->id])->pluck('image')->first();
                            @endphp
                            <th scope="row"><a href="#"><img src="{{$image}}" alt=""></a></th>
                            <td>{{$row->title}}</td>
                            @php
                                //$info_query=\App\Models\ProductInfo::where(['product_id' => $row->id])->pluck('price')->first();
								$info_query=\App\Models\Category::where(['id' => $row->category])->pluck('category')->first();
                            @endphp
                            <td>{{ $info_query }}</td>
                            <td>{{$row->tags}}</td>

                            <td>@if($row->status=='1') <span class="en-recovered"></span>{{'Approved'}}@elseif($row->status=='2') <span class="en-in-progress"></span>{{'Changes Pending'}} @elseif($row->status=='3') <span class="en-dismissed"></span>{{'Deny'}} @else <span class="en-dismissed"></span>{{'Pending'}} @endif</td>
                            <td class="icon-action">
                                <a href="{{route('edit-product',$row->id)}}" title="Edit Product"><i class="bi bi-pencil-fill" aria-hidden="true"></i></a>
                                <a title="Edit Variant" href="{{route('edit-variant',$row->id)}}"><span><img style="width:20px;" src="{{url('subadmin/assets/img/Edit-Variant.png')}}" alt=""></span></a>
<!--                                <a href="#verticalycentered" id='verticalycentered ' data-bs-toggle="modal"><i class="bi bi-trash"></i></a>-->
                                <a href="{{url('delete-product')}}/{{$row->id}}" onclick="return confirm('Are you sure you want to delete this product?');"><i class="bi bi-trash"></i></a>
                            </td>
                          </tr>
                          @endforeach

                        </tbody>
                      </table>

                    </div>
                </div>
        </form>
         <nav class="mainpg timer-nav">
             {{ $product->links( "pagination::bootstrap-4") }}
        </nav>
    </section>
    <div class="modal fade" id="verticalycentered" tabindex="-1" data-bs-backdrop="false">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><b><i class="bi bi-trash"></i></b> Delete</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            Are you sure you want to Delete ?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
            <button type="button" class="btn btn-primary">Delete!</button>
          </div>
        </div>
      </div>
    </div>
   </main>
@stop
  @section('js')
<script type="text/javascript">
// function checkSearch()
// {
// 	var val=jQuery.trim($('#search').val());
// 	$('#search').val(val);
// 	if(val.length==0)
// 	{
// 		return false;
// 	}
// }


function filterByName(val)
{
    var search=$('#search').val();
    if(search!='')
    {
        var status='{{Request::get('status')}}';
        window.location.href='product-list?search='+search+'&status='+status;
    }
}

function filterByStatus(id)
{
    var search='{{Request::get('search')}}';
    window.location.href='product-list?search='+search+'&status='+id;
}




</script>
@stop
