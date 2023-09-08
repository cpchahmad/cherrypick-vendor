@extends('layouts.admin')
@section('main')
  <main id="main" class="main">
    <div class="home-flex">
        <div class="pagetitle fit-title">
            <h1>View Users</h1>
        </div><!-- End Page Title -->
        </div>
        @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                       <h5>{{ $message }}</h5>
                     </div>
                 @endif
        <div class="member-plan-search header onetime-search">
            <!--<div class="search-bar">
                <form class="search-form d-flex align-items-center" method="GET" action="">
                  <label>Search User</label>
                  <input type="search" name="search" id="search" placeholder="Search User" value="{{ request()->get('search') }}" title="Enter search keyword">
                  <button type="submit" title="Search"><i class="bi bi-search"></i></button>
                </form>
              </div>-->
              <div class="create-plan">
                <a class="btn btn-primary" href="{{route('users.create')}}">Add User</a>
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
                                <th scope="col">Name</th>
                                <th scope="col">Email</th>
                                <th scope="col">Phone</th>
                                <th scope="col">Role</th>
                                <th scope="col">Action</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php $i=1;?>
                               @foreach($user as $users)
                              <tr>
                                <td>{{$i++}}</td>
                                <td>{{$users->name}} {{$users->last_name}}</td>
                                <td>{{$users->email}}</td>
                                <td>{{$users->mobile}}</td>
                                <td>{{$users->role_name}}</td>
                                <td class="icon-action">
                                    <a href="{{url('users-edit')}}/{{$users->id}}"><i class="bi bi-pencil-fill"></i></a>
                                    <a href="{{url('users-delete')}}/{{$users->id}}" onclick="return confirm('Are you sure you want to delete this user?');"><i class="bi bi-trash"></i></a>
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
 @section('js')
<script type="text/javascript">
 jQuery(document).ready(function() {
    $('#search').keypress(function(event) {
        if (event.which === 32) {
            event.preventDefault();
        }
    });
});
</script>
@stop