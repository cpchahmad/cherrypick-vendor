@extends('layouts.superadmin')
@section('main')
  <main id="main" class="main">
   <div class="home-flex">
    <div class="pagetitle">
       <h1>All Stores</h1>
    </div><!-- End Page Title -->
   </div>
   @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                       <h5>{{ $message }}</h5>
                     </div>
                 @endif
    <section class="section up-banner">
        <div class="rcv-doc">
        <a class="btn btn-primary" href="{{url('superadmin/uploade-product-form')}}">Upload Products</a>
            &nbsp; &nbsp;<a class="btn btn-primary" href="{{url('superadmin/fetch-product-form')}}">Fetch From Json Url</a>
{{--            &nbsp; &nbsp;<a class="btn btn-primary" href="{{url('superadmin/fetch-product-api')}}">Fetch From Api</a>--}}
        <div class="table-responsive mt-2">
            <table class="table table-bordered table-white">
              <thead>
                <tr>
                  <th scope="col">S.No</th>
                  <th scope="col">Vendor Name</th>
                  <th scope="col">Product CSV</th>

                </tr>
              </thead>
              <tbody>
                <?php $i=1; ?>
                 @foreach($vendorlist as $k=>$row)
                <tr>
                  <td scope="row">{{$k+1}}</td>
                  <td>{{$row->name}}</td>
                  <td class="download-doc"><i class="bi bi-download"></i><a href="{{url('superadmin/store-products-csv')}}/{{$row->id}}">Export Products</a></td>

                </tr>
                @endforeach
              </tbody>
            </table>
            </div>
        </div>
    </section>
   </main>
@endsection
