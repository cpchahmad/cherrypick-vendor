@extends('layouts.admin')
@section('main')
<main id="main" class="main">
   <div class="home-flex">
    <div class="pagetitle">
       <h1>View Category</h1>
    </div><!-- End Page Title -->
   </div>
     <div class="member-plan-search header onetime-search">
            <div class="search-bar">

              </div>
              <div class="create-plan">
                <a class="btn btn-primary" href="{{url('add-category')}}">Add Category</a>
              </div>
           </div>
    <section class="section up-banner">
        <div class="rcv-doc">
        <div class="table-responsive">
            <table class="table table-bordered table-white">
              <thead>
                <tr>
                  <th scope="col">S.No</th>
                  <th scope="col">Category Name</th>
                  <th scope="col">Action</th>
                </tr>
              </thead>
              <tbody>
			  @foreach($data as $k=>$row)
                <tr>
                  <td scope="row">{{$k+1}}</td>
                  <td>{{$row->category}}</td>
                  <td class="icon-action">
                    <a href="{{url('edit-category')}}/{{$row->id}}"><i class="bi bi-pencil-fill"></i></a>
                    <a href="{{url('delete-category')}}/{{$row->id}}" onclick="return confirm('Are you sure you want to delete this category?');"><i class="bi bi-trash"></i></a>
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



  
  