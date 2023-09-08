@extends('layouts.admin')
@section('main')
<main id="main" class="main">
   <div class="home-flex">
    <div class="pagetitle fit-title">
      <h1>Discount</h1>
    </div><!-- End Page Title -->
   </div>
   <div class="member-plan-search header onetime-search">
    <div class="search-bar">
        <form class="search-form d-flex align-items-center" method="GET" action="{{route('manage-product-discount')}}">
            <label>Search Product</label>
          <input type="text" name="code" id="code" placeholder="Search product Name" title="Enter search keyword">
          <button type="submit" title="Search"><i class="bi bi-search"></i></button>
        </form>
      </div>
      <div class="create-plan">
        <a class="btn btn-primary" href="{{url('product-add-discount')}}">+ Add Discount</a>
      </div>
   </div>
   @if ($message = Session::get('success'))
        <div class="alert alert-success">{{ $message }}</div>
   @endif
   <section class="section dashboard">
   @if(count($data) > 0)
   <a class="btn btn-primary" href="{{url('delete-store-product-discount')}}">Delete All Discount</a>
@endif
    <div class="row">
      <div class="col-lg-12">
          <div class="card">
              <div class="card-body show-plan collections disct">
                <!-- Bordered Table -->
                <div class="table-responsive">
                <table class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th scope="col">Product</th>
                      <th scope="col">Discount</th>
                      <th scope="col">Delete</th>
                    </tr>
                  </thead>
                  <tbody>
                      @foreach($data as $row)
                    <tr id="row_{{$row->id}}">
                      <td>{{$row->title}}</td>
                      <td>{{$row->discount}} %</td>
                      <td class="icon-action">
                          <a href="javascript:void(0)" onclick="deleteProductDiscount({{$row->id}})"> <i class="bi bi-trash"></i></a>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
                </div>
                <!-- End Bordered Table -->
  
              </div>
            </div>
      </div>
    </div>
	<nav class="mainpg timer-nav">
        {{ $data->links( "pagination::bootstrap-4") }}
      </nav>
  </section>
  </main><!-- End #main -->
@endsection
@section('js')
<script type="text/javascript">
 jQuery(document).ready(function() {
    $('#code').keypress(function(event) {
        if (event.which === 32) {
            event.preventDefault();
        }
    });
});
function deleteProductDiscount(id)
    {
            $.ajax({
                url: "{{url('delete-product-discount')}}",
                type: 'GET',
                data: { id : id},
                success: function(response)
                {
                    var json = $.parseJSON(response);
                    if(json.status=='success')
                    {
                        alert("Discount Deleted Successfully");
						$('#row_'+id).remove();
                    }
                }
            });
    }
</script>
@stop