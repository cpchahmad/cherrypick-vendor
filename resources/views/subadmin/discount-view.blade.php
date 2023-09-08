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
        <form class="search-form d-flex align-items-center" method="GET" action="{{route('manage-discount')}}">
            <label>Search Discount</label>
          <input type="text" name="code" id="code" placeholder="Search Discount Name" title="Enter search keyword">
          <button type="submit" title="Search"><i class="bi bi-search"></i></button>
        </form>
      </div>
      <div class="create-plan">
        <a class="btn btn-primary" href="{{url('add-discount')}}">Add Discount</a>
      </div>
   </div>
   <section class="section dashboard">
    <div class="row">
      <div class="col-lg-12">
          <div class="card">
              <div class="card-body show-plan collections disct">
                <!-- Bordered Table -->
                <div class="table-responsive">
                <table class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th scope="col">Discount Code</th>
                      <th scope="col">Discount Type</th>
                      <th scope="col">Discount Value</th>
                      <th scope="col">Start Date</th>
                      <th scope="col">Settings</th>
                    </tr>
                  </thead>
                  <tbody>
                      @foreach($data as $row)
                    <tr>
                      <td>{{$row->code}}</td>
                      <td>{{$row->type}}</td>
                      <td>{{$row->discount_value}}</td>
                      <td>{{date('d-m-Y', strtotime($row->start_date))}}</td>
                      <td class="icon-action">
                          <a href="{{url('discount-delete')}}/{{$row->price_rule_id}}"><i class="bi bi-trash"></i></a>
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
</script>
@stop