@extends('layouts.admin')

  <main id="main" class="main">
    <div class="home-flex">
        <div class="pagetitle fit-title">
            <h1>User Role</h1>
        </div><!-- End Page Title -->
        </div>
        @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                       <h5>{{ $message }}</h5>
                     </div>
                 @endif
         <section class="section up-banner">
            <form class="add-product-form" method="post" action="{{route('update-role')}}">
              @csrf
              <input type='hidden' name='id' value='{{$data->id}}'>
                <div class="card">
                    <div class="row">
                   <div class="col-12">
                       <label for="inputNanme4" class="form-label">Role name</label>
                       <input type="text" class="form-control" id="" value="{{$data->name}}" placeholder="Enter role name" name="name" >
                        <span style="color:red;">
                       @error('name')
                         {{$message}}
                       @enderror
                     </span>  
                      </div>
                      <div class="col-12 fnuser-role">
                        <p>Module permission :</p>
                        <div class="row">
                        <div class="col-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="gridCheck1" name="module1" value="store_configuration" @if($data->store_configuration=='1') {{'checked'}} @endif >
                            <label class="form-check-label" for="gridCheck1"> Store Configuration </label>
                          </div>
                        </div>
                        <div class="col-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="gridCheck2" name="module2" value="products"  @if($data->products=='1') {{'checked'}} @endif >
                                <label class="form-check-label" for="gridCheck2"> Products </label>
                              </div>
                            </div>
                            <div class="col-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="gridCheck3" name="module3" value="orders"  @if($data->orders=='1') {{'checked'}} @endif >
                                <label class="form-check-label" for="gridCheck3"> Orders </label>
                                </div>
                            </div>
                        <div class="col-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="gridCheck4" name="module4" value="marketing"  @if($data->marketing=='1') {{'checked'}} @endif >
                                <label class="form-check-label" for="gridCheck4"> Marketing </label>
                            </div>
                            </div>
                    </div>
                       </div>
                 </div>
           </div>
           <div class="timer-btns">
            <a href="{{url('user-role')}}" class="btn btn-light">Back</a>
            <button type="submit" class="btn btn-primary">Submit</button>
         </div>
            </form>
        </section>
   </main>
  <!-- End #main -->
  <!-- ======= Footer ======= -->
  