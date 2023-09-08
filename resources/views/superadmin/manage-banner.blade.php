@extends('layouts.superadmin')

  <main id="main" class="main">
   <div class="home-flex">
    <div class="pagetitle">
       <h1>Approval Requests - Banners</h1>
    </div><!-- End Page Title -->
   </div>
    <section class="section up-banner">
      <div class="row">
        <div class="col-lg-12">
          <div class="subscribe-plan">
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-monthly" type="button" role="tab" aria-controls="pills-home" aria-selected="true" name="home">Home Page banner</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-yearly" type="button" role="tab" aria-controls="pills-profile" aria-selected="false"  name="store">Store banner</button>
              </li>
            </ul>
            <div class="tab-content pt-2" id="myTabContent">
              <div class="tab-pane fade show active" id="pills-monthly" role="tabpanel" aria-labelledby="home-tab">
                <div class="row">
                  <div class="table-responsive">
                    <table class="table table-bordered table-white">
                      <thead>
                        <tr>
                          <th scope="col">S.No</th>
                          <th scope="col">Request Date</th>
                          <th scope="col">Vendor Name</th>
                          <th scope="col">Store URL</th>
                          <th scope="col">Banner Image</th>
                          <th scope="col">Banner Size</th>
                          <th scope="col">Approval Status</th>
                          <th scope="col">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $i=1; ?>
                        @foreach($banner as $collection)
                        @foreach($collection as $row)
                        <tr>
                          <th scope="row">{{$i++}}</th>
                          <td>{{$row->created_at->format('m-d-Y')}}</td>
                          <td>{{$row->name}}</td>
                          <td><a href="#">www.vendorurl.com</a></td>
                          <td class="p-img"><img src="{{asset('uploads/banner/'.$row->home_desktop_banner)}}"></td>
                          <td>1920X325px</td>
                          <td><span class="en-dismissed"></span> Pending</td>
                          <td>
                            <a href="#" class="checka"><i class="bi bi-check2"></i></a>
                            <a href="#" class="removex"><i class="bi bi-x"></i></a>
                          </td>
                        </tr>
                        @endforeach
                        @endforeach
                      </tbody>
                    </table>
                    </div>
               </div>
               <nav class="mainpg timer-nav">
                <ul class="pagination">
                  <li class="page-item disabled">
                    <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                  </li>
                  <li class="page-item"><a class="page-link" href="#">1</a></li>
                  <li class="page-item active" aria-current="page">
                    <a class="page-link" href="#">2</a>
                  </li>
                  <li class="page-item"><a class="page-link" href="#">3</a></li>
                  <li class="page-item">
                    <a class="page-link" href="#">Next</a>
                  </li>
                </ul>
              </nav>
              </div>
              <div class="tab-pane fade" id="pills-yearly" role="tabpanel" aria-labelledby="profile-tab">
                <div class="table-responsive">
                  <table class="table table-bordered table-white">
                    <thead>
                      <tr>
                        <th scope="col">S.No</th>
                        <th scope="col">Request Date</th>
                        <th scope="col">Vendor Name</th>
                        <th scope="col">Store URL</th>
                        <th scope="col">Banner Image</th>
                        <th scope="col">Banner Size</th>
                        <th scope="col">Approval Status</th>
                        <th scope="col">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                        <?php $i=1; ?>
                        @foreach($banner as $collection)
                        @foreach($collection as $row)
                      <tr>
                        <th scope="row">{{$i++}}</th>
                        <td>{{$row->created_at->format('m-d-Y')}}</td>
                        <td>{{$row->name}}</td>
                        <td><a href="#">www.vendorurl.com</a></td>
                        <td class="p-img"><img src="{{asset('uploads/banner/'.$row->store_desktop_banner)}}"></td>
                        <td>1920X325px</td>
                        <td><span class="en-dismissed"></span> Pending</td>
                        <td>
                          <a href="#" class="checka"><i class="bi bi-check2"></i></a>
                          <a href="#" class="removex"><i class="bi bi-x"></i></a>
                        </td>
                      </tr>
                      @endforeach
                      @endforeach
                    </tbody>
                  </table>
                  </div>
                  <nav class="mainpg timer-nav">
                    <ul class="pagination">
                      <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                      </li>
                      <li class="page-item"><a class="page-link" href="#">1</a></li>
                      <li class="page-item active" aria-current="page">
                        <a class="page-link" href="#">2</a>
                      </li>
                      <li class="page-item"><a class="page-link" href="#">3</a></li>
                      <li class="page-item">
                        <a class="page-link" href="#">Next</a>
                      </li>
                    </ul>
                  </nav>
              </div>
            </div>
          </div>  
         
        </div>
      </div>
    </section>
   </main>
  <!-- End #main -->
  <!-- ======= Footer ======= -->
 <script>
    $('.sidebar-nav .nav-link:not(.collapsed) ~ .nav-content').addClass('show');
  
    jQuery(function($) {
     var path = window.location.href; // because the 'href' property of the DOM element is the absolute path
     $('ul a').each(function() {
      if (this.href === path) {
       $(this).addClass('active');
      }
     });
    });
  </script>

  <script>
    window.pressed = function(){
    var a = document.getElementById('upfile');
    if(a.value == "")
    {
        fileLabel.innerHTML = "Choose file";
    }
    else
    {
        var theSplit = a.value.split('\\');
        fileLabel.innerHTML = theSplit[theSplit.length-1];
    }
};         
window.pressed1 = function(){
    var a = document.getElementById('upfile1');
    if(a.value == "")
    {
        fileLabel1.innerHTML = "Choose file";
    }
    else
    {
        var theSplit = a.value.split('\\');
        fileLabel1.innerHTML = theSplit[theSplit.length-1];
    }
};     
window.pressed2 = function(){
    var a = document.getElementById('upfile2');
    if(a.value == "")
    {
        fileLabel2.innerHTML = "Choose file";
    }
    else
    {
        var theSplit = a.value.split('\\');
        fileLabel2.innerHTML = theSplit[theSplit.length-1];
    }
};  
window.pressed3 = function(){
    var a = document.getElementById('upfile3');
    if(a.value == "")
    {
        fileLabel3.innerHTML = "Choose file";
    }
    else
    {
        var theSplit = a.value.split('\\');
        fileLabel3.innerHTML = theSplit[theSplit.length-1];
    }
};         
  </script>