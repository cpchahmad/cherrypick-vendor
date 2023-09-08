@extends('layouts.admin')
@section('main')
  <main id="main" class="main">
    <div class="home-flex">
        <div class="pagetitle fit-title">
            <h1>Category</h1>
        </div><!-- End Page Title -->
        </div>
        @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                       <h5>{{ $message }}</h5>
                     </div>
                 @endif
         <section class="section up-banner">
            <form class="add-product-form" method="post" action="{{url('save-category')}}">
              @csrf
                <div class="card">
                    <div class="row">
                   <div class="col-12">
                       <label for="inputNanme4" class="form-label">Category</label>
                       <input type="text" class="form-control" placeholder="Enter category name" name="name" required>
                        <span style="color:red;">
                       @error('name')
                         {{$message}}
                       @enderror
                     </span>  
                      </div>
                 </div>
           </div>
           <div class="timer-btns">
            <a href="{{url('category')}}" class="btn btn-light">Back</a>
            <button type="submit" class="btn btn-primary">Submit</button>
         </div>
            </form>
        </section>
   </main>
@endsection
  