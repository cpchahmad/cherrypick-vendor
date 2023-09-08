@extends('layouts.admin')

  <main id="main" class="main">
    <div class="home-flex">
        <div class="pagetitle fit-title">
          <h1>Add User</h1>
        </div><!-- End Page Title -->
       </div>
     
    <section class="section up-banner">
        <form class="add-product-form" method="post" action="{{route('users.store')}}" enctype="multipart/form-data">
          @csrf
            <div class="card">
                     <div class="row">
                    <div class="col-6">
                        <label for="inputNanme4" class="form-label">First name</label>
                        <input type="text" class="form-control" id="" value="" placeholder="Enter first name" name="first_name">
                        <span style="color:red;">
                         @error('first_name')
                           {{$message}}
                         @enderror
                     </span> 
                       </div>
                    <div class="col-6">
                        <label for="inputNanme4" class="form-label">Last name</label>
                        <input type="text" class="form-control" id="" placeholder="Enter last name" name="last_name">
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
                   <input type="text" class="form-control" id="" value="" placeholder="Enter phone" name="phone">
                   <span style="color:red;">
                         @error('phone')
                           {{$message}}
                         @enderror
                     </span> 
                  </div>
               <div class="col-6">
                   <label for="inputNanme4" class="form-label">Role</label>
                   <select class="form-select all-roles" aria-label="Default select example" name="role">
                    <option >Select Role</option>
                    @foreach($role as $roles)
                    <option value="{{$roles->name}}">{{$roles->name}}</option>
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
           <input type="email" class="form-control" id="" value="" placeholder="Enter email" name="email">
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
               <span style="color:red;">
                         @error('profile')
                           {{$message}}
                         @enderror
                     </span> 
              </div>
              </div>
            </div>
            <div class="timer-btns">
              <button type="submit" class="btn btn-primary">Submit</button>
           </div>
        </form>
    </section>
   </main>

  <!-- End #main -->
  <!-- ======= Footer ======= -->
  