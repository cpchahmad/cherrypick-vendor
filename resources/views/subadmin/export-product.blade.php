@extends('layouts.admin')
@section('main')
<main id="main" class="main">
   <div class="home-flex">
    <div class="pagetitle">
       <h1>Product - Bulk Export</h1>
    </div><!-- End Page Title -->
   </div>
     
    <section class="section up-banner">
        <div class="bulk-intcn">
        <h2 class="p-export">Export Products</h2>
       </div>
    
            <form class="add-product-form">
            <div class="card">
                <div class="row">
                    <div class="col-12">
                     <div class="row">
                    <div class="col-12">
                        <label for="inputNanme4" class="form-label">Export Products</label>
                        <div class="timer-btns pro-submit expoert-ns">
                          <a href="{{url('exportProduct')}}"><button type="button" class="btn btn-primary">Export Now</button></a>
                       </div>
                       </div>
                    </div>
                </div>
               </div>
            </div>
          
        </form>
          
       
    </section>
   </main>
  @endsection