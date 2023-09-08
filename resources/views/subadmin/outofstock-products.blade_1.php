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
        <form class="search-form d-flex align-items-center" method="GET" action="#">
          <label>Search Products</label>
          <input type="search" name="search" placeholder="Search Product Name" title="Enter search keyword">
          <button type="submit" title="Search"><i class="bi bi-search"></i></button>
        </form>
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
                            <th scope="row"><a href="#"><img src="{{asset('uploads/profile/'.$image)}}" alt=""></a></th>
                            <td><a href="#" class="text-primary fw-bold">{{$row->title}}@if($row->varient_name!='') {{'('.$row->varient_name."-".$row->varient_value.')'}}@endif</a></td>
                            <td class="fw-bold" id="stock_{{$row->id}}">{{$row->stock}}</td>
                            <td class="up-quantity">
                                <input type="text" name="qtn_{{$row->id}}" id="qtn_{{$row->id}}">
                                <button type="button" onclick="updateProductStock({{$row->id}})">Save</button>
                            </td>
                          </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                </div>
        </form>
    </section>
   </main>
@endsection
<script>
    function updateProductStock(id)
    {
        var qty=$('#qtn_'+id).val();
        if(qty > 0)
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