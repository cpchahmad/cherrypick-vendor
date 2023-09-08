@extends('layouts.admin')

  <main id="main" class="main">
    <div class="home-flex">
        <div class="pagetitle fit-title">
          <h1>Edit User</h1>
        </div><!-- End Page Title -->
       </div>
       @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                       <h5>{{ $message }}</h5>
                     </div>
                 @endif
     
    <section class="section up-banner">
        <form class="add-product-form" method="POST" action="{{route('update-user',$user->id)}}" enctype="multipart/form-data">
          @csrf
            <div class="card">
                     <div class="row">
                    <div class="col-6">
                        <label for="inputNanme4" class="form-label">First name</label>
                        <input type="text" class="form-control" id="" value="{{$user->name}}" placeholder="Enter first name" name="first_name">
                        <span style="color:red;">
                         @error('first_name')
                           {{$message}}
                         @enderror
                     </span> 
                       </div>
                    <div class="col-6">
                        <label for="inputNanme4" class="form-label">Last name</label>
                        <input type="text" class="form-control" id="" placeholder="Enter last name" name="last_name" value="{{$user->last_name}}">
                        <span style="color:red;">
                         @error('last_name')
                           {{$message}}
                         @enderror
                     </span> 

                       </div>
                    </div>
            </div>
            <div class="card">
                <div class="row">
               <div class="col-6">
                   <label for="inputNanme4" class="form-label">Phone</label>
                   <input type="text" class="form-control" id=""  placeholder="Enter phone" name="phone" value="{{$user->mobile}}">
                   <span style="color:red;">
                         @error('phone')
                           {{$message}}
                         @enderror
                     </span> 
                  </div>
               <div class="col-6">
                   <label for="inputNanme4" class="form-label">Role</label>
                   <select class="form-select all-roles" aria-label="Default select example" name="role" required>
                    <option value="">Select Role</option>
                    @foreach($role as $roles)
                    <option value="{{$roles->id}}" {{$roles->id == $user->store_role_id  ? 'selected' : ''}}>{{$roles->name}}</option>
                    @endforeach
                  </select>
                  <span style="color:red;">
                         @error('role')
                           {{$message}}
                         @enderror
                     </span> 
                  </div>
               </div>
       </div>
       <div class="card">
        <div class="row">
          <div class="col-6">
           <label for="inputNanme4" class="form-label">Email</label>
           <input type="email" class="form-control" id=""  placeholder="Enter email" name="email" value="{{$user->email}}">
           <span style="color:red;">
                         @error('email')
                           {{$message}}
                         @enderror
                     </span> 
          </div>
         <div class="col-6">
           <label for="inputNanme4" class="form-label">Password</label>
           <input type="password" class="form-control" id="" placeholder="Enter password" name="password">
           <span style="color:red;">
                         @error('password')
                           {{$message}}
                         @enderror
                     </span> 
          </div>
          </div>
        </div>
        <div class="card">
            <div class="row">
             <div class="col-12 userup">
               <label for="inputNanme4" class="form-label">Upload User Profile</label>
               <input type="file" class="form-control" id="" name="profile">
               <img src="{{asset('/uploads/userprofile/'.$user->profile_picture)}}" height="50" width="50">
               <span style="color:red;">
                         @error('profile')
                           {{$message}}
                         @enderror
                     </span> 
              </div>
              </div>
            </div>
            <div class="timer-btns">
              <a href="{{url('users')}}" class="btn btn-light">Back</a>
              <button type="submit" class="btn btn-primary">Update</button>
           </div>
        </form>
    </section>
   </main>
  <!-- End #main -->
  <!-- ======= Footer ======= -->
  