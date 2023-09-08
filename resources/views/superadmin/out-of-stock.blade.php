@extends('layouts.superadmin')
@section('main')
  <main id="main" class="main">
   <div class="home-flex">
    <div class="pagetitle">
       <h1>Out of Stock Items</h1>
    </div><!-- End Page Title -->
   </div>
   
    <section class="section up-banner">
      <div class="row">
        <P><b>Out of Stock Items</b></P>
       <div class="col-md-12">
        <div class="card-body show-plan collections">
          <!-- Bordered Table -->
          <div class="table-responsive">
          <table class="table table-bordered table-white">
            <thead>
              <tr>
                <th scope="col">S.No</th>
                <th scope="col">Vendor Name</th>
                <th scope="col">Vendor Email</th>
<!--                <th scope="col">Store URL</th>-->
                <th scope="col">Number of Out of Stock Items</th>
                <th scope="col">Action</th>
              </tr>
            </thead>
            <tbody>
                @php $i=1; @endphp
                @foreach($data as $row)
              <tr>
                <td>{{$i++}}</td>
                <td>{{$row->name}}</td>
                <td>{{$row->email}}</td>
<!--                <td><a href="#">wwww.storeurl.com</a></td>-->
                <td class="cnt-stock">{{$row->products}}</td>
                <td class="icon-action">
                  <a href="{{url('superadmin/out-of-stock-products')}}/{{$row->id}}"><i class="bi bi-eye"></i></a>
                 
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
    </section>
   </main>
  <!-- End #main -->
  <!-- ======= Footer ======= -->
@endsection