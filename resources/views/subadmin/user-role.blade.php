@extends('layouts.admin')
@section('main')
  <main id="main" class="main">
    <div class="home-flex">
        <div class="pagetitle fit-title">
            <h1>View Role</h1>
        </div><!-- End Page Title -->
        </div>
        @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                       <h5>{{ $message }}</h5>
                     </div>
                 @endif
        <div class="member-plan-search header onetime-search">
            <div class="search-bar">

              </div>
              <div class="create-plan">
                <a class="btn btn-primary" href="{{route('role.create')}}">Add Role</a>
              </div>
           </div>
           <section class="section up-banner">
            <form class="add-product-form">
                    <div class="card table-card">
                      <div class="table-responsive">
                        <table class="table table-borderless view-productd">
                            <thead>
                              <tr>
                                <th scope="col">S.No</th>
                                <th scope="col">Role</th>
                                <th scope="col">Store Configuration</th>
                                <th scope="col">Products</th>
                                <th scope="col">Orders</th>
                                <th scope="col">Marketing</th>
                                <th scope="col">Action</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php $i=1;?>
                               @foreach($data as $row)
                              <tr>
                                <td>{{$i++}}</td>
                                <td>{{$row->name}}</td>
                                <td>@if($row->store_configuration==1) {{'Yes'}} @else {{'No'}} @endif</td>
                                <td>@if($row->products==1) {{'Yes'}} @else {{'No'}} @endif</td>
                                <td>@if($row->orders==1) {{'Yes'}} @else {{'No'}} @endif</td>
                                <td>@if($row->marketing==1) {{'Yes'}} @else {{'No'}} @endif</td>
                                <td class="icon-action">
                                    <a href="{{url('role-edit')}}/{{$row->id}}"><i class="bi bi-pencil-fill"></i></a>
                                </td>
                              </tr>
                              @endforeach
                            </tbody>
                          </table>
                        </div>
                    </div>
        </section>
   </main>
@endsection
  