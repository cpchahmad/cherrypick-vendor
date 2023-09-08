@extends('layouts.admin')
  <main id="main" class="main">
   <div class="home-flex">
    <div class="pagetitle">
       <h1>Payment Configuration</h1>
    </div><!-- End Page Title -->
   </div>
     
    <section class="section up-banner">
            <div class="card">
                <div class="row">
                  <div class="col-12 bank-info">
                    <h5>My Bank Info</h5>
                    @forelse($data as $row)
                    <p><b>Account No : </b><span>{{$row->account_no}}</span></p>
                    <p><b>Bank Name : </b><span>{{$row->bank_name}}</span></p>
                    <p><b>IFSC : </b><span>{{$row->ifsc}}</span></p>
                    <p><b>GST : </b><span>{{$row->gst}}</span></p>
                    <p><b>Address : </b><span>{{$row->address}}</span></p>
                    @empty
                    <p><b>Data Not Found.</b></p>
                    @endforelse
                   
                    <a href="{{route('admin.editpaymentconfig')}}" class="btn btn-primary edit-bank">Edit</a>
                </div>
               </div>
            </div>
    </section>
   </main>
  <!-- End #main -->
  <!-- ======= Footer ======= -->
 