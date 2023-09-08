@extends('layouts.superadmin')
@section('main')
  <main id="main" class="main">
   <div class="home-flex">
    <div class="pagetitle">
       <h1>Vendor Orders Amounts</h1>
    </div><!-- End Page Title -->
   </div>
   
    <section class="section up-banner">
      <div class="row">
       <div class="col-md-12">
        <div class="card-body show-plan collections">
          <!-- Bordered Table -->
          <div class="table-responsive">
		  <form action="{{url('superadmin/store-amount')}}" method="get">
                                <table border="0" cellspacing="5" cellpadding="5">
                                  <tbody>
                                    <tr>
                                      <td>
                                        <input type="date" id="min" name="min" @if(Request::get('min')) value="{{Request::get('min')}}" @endif>
                                      </td>
                                      <td>
                                        <input type="date" id="max" name="max" @if(Request::get('max')) value="{{Request::get('max')}}" @endif>
                                      </td>
                                      <td>
                                          <button type="submit" id="filtrar_fecha">Filter</button>
                                      </td>
                                    </tr>
                                  </tbody>
                                </table>
                                </form>    
                                <br><br>
          <table class="table table-bordered table-white">
            <thead>
              <tr>
                <th scope="col">S.No</th>
                <th scope="col">Vendor Name</th>
                <th scope="col">Payment</th>
                <th scope="col">Action</th>
              </tr>
            </thead>
            <tbody>
                @php $i=1; @endphp
                @foreach($data as $row)
              <tr>
                <td>{{$i++}}</td>
                <td>{{$row['name']}}</td>
                <td>{{ceil($row['total_amount'])}}</td>
				<td><a class="btn btn-warning btn-sm" href="{{url('superadmin/store-amount-history')}}/{{$row['id']}}"><i class="bi bi-eye"></i></a></td>
              </tr>
              @endforeach
              
            </tbody>
          </table>
          </div>
          <!-- End Bordered Table -->
        </div>
       </div>
      </div>
    </section>
   </main>
  <!-- End #main -->
  <!-- ======= Footer ======= -->
@endsection