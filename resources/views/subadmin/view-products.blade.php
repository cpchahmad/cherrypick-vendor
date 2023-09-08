@extends('layouts.admin')
@section('main')
  <main id="main" class="main">
   <div class="home-flex">
    <div class="pagetitle">
       <h1>Product List</h1>
    </div><!-- End Page Title -->
   </div>
   <div class="member-plan-search header onetime-search">
    <div class="search-bar">
        <form class="search-form d-flex align-items-center" method="GET" action="{{route('product-list')}}" onsubmit="return checkSearch()">
          <label>Search Products</label>
          <input type="search" name="search" id="search" placeholder="Search Product Name" title="Enter search keyword" id="search">
          <button type="submit" title="Search"><i class="bi bi-search"></i></button>
        </form>
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
                            <td>@if($row->status=='1') <span class="en-recovered"></span>{{'Approved'}} @elseif($row->status=='3') <span class="en-dismissed"></span>{{'Deny'}} @else <span class="en-dismissed"></span>{{'Pending'}} @endif</td>
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
function checkSearch()
{
	var val=jQuery.trim($('#search').val());
	$('#search').val(val);
	if(val.length==0)
	{
		return false;
	}
}

</script>
@stop