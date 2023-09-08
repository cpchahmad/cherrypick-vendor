@extends('layouts.admin')
@section('main')
  <main id="main" class="main">
   <div class="home-flex">
    <div class="pagetitle">
       <h1>Product - Out of Stock</h1>
    </div><!-- End Page Title -->
   </div>
   <div class="member-plan-search header onetime-search">
    <div class="search-bar">
	<form class="search-form d-flex align-items-center" method="GET" action="{{route('product-list')}}" onsubmit="return checkSearch()">
          <label>Search Products</label>
          <input type="search" name="search" id="search" placeholder="Search Product Name" title="Enter search keyword" id="search">
          <button type="submit" title="Search"><i class="bi bi-search"></i></button>
        </form>
        <!--<form class="search-form d-flex align-items-center" method="GET" action="#">
          <label>Search Products</label>
          <input type="search" name="search" id="search" placeholder="Search Product Name" title="Enter search keyword">
          <button type="submit" title="Search"><i class="bi bi-search"></i></button>
        </form>-->
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
							<th scope="col">Variant</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Update Quantity</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($product as $row)
                          <tr>
                            @php 
                                $image=\App\Models\ProductImages::where(['product_id' => $row->pid])->pluck('image')->first();
                            @endphp
                            <th scope="row"><a href="#"><img src="{{$image}}" alt=""></a></th>
                            <td><a href="#" class="text-primary fw-bold">{{$row->title}}</a></td>
                            <td class="fw-bold" >@if($row->varient_name!='') {{'('.$row->varient_name."-".$row->varient_value.')'}}@endif</td>
							<td class="fw-bold" id="stock_{{$row->id}}">{{$row->stock}}</td>
                            <td class="up-quantity">
                                <input type="text" name="qtn_{{$row->id}}" id="qtn_{{$row->id}}" class="numericOnly">
                                <button type="button" onclick="updateProductStock({{$row->id}})">Save</button>
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
   </main>
@endsection
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
<script>
    function updateProductStock(id)
    {
        var qty=$('#qtn_'+id).val();
        if(qty!='')
        {
            $.ajax({
                url: "{{url('update-stock')}}",
                type: 'GET',
                data: { id : id, qty : qty},
                success: function(response)
                {
                    var json = $.parseJSON(response);
                    if(json.status=='success')
                    {
                        $('#stock_'+id).html(json.qty);
                        $('#qtn_'+id).val('');
                    }
                }
            });
        }
    }
</script>