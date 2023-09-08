@extends('layouts.admin')
  <main id="main" class="main">
   <div class="home-flex">
    <div class="pagetitle">
        <h1>Banners</h1>
    </div><!-- End Page Title -->
   </div>
    <section class="section up-banner">
      
        <form class="edit-timer-form email-defaul-set" method="post" action="{{url('savebanner')}}" enctype="multipart/form-data" id="saveprofile">
          @csrf
        <!-- <p><b>Home Page banner</b></p>
         <div class="row">
         <div class="col-md-8">
            <div class="card">
                <div class="my-profile seprate row g-3">
                  <div class="col-12">
                    <label for="inputNanme4" class="form-label">Desktop Banner</label>
                 <div>
                    <input type='file' title="Upload Logo" id="upfile" name="home_desktop_banner" onchange="pressed()">
                    <label id="fileLabel" for="upfile">Drag and drop here <br> or Choose from the system</label>
                 </div>
                    
                </div>
                </div>
             </div>
             <span class="file0-size">Suggestion Size - 1920px * 375px </span>
           </div>
           <div class="col-md-4">
            <div class="card">
                <div class="seprate row g-3">
                  <div class="col-12">
                    <label for="inputNanme4" class="form-label">Mobile Banner</label>
                 <div>
                    <input type='file' title="Upload Mobile Logo" id="upfile1" onchange="pressed1()" name="home_mobile_banner">
                    <label id="fileLabel1" for="upfile1">Drag and drop here <br> or Choose from the system</label>
                 </div>
                    
                </div>
                </div>
             </div>
             <span class="file0-size">Suggestion Size - 720px * 1080px </span>
           </div>
          </div>-->
          <p><b>Store banner</b></p>
          <div class="row">
            <div class="col-md-8">
               <div class="card">
                   <div class="seprate row g-3">
                       <input type='file' title="Upload Logo" id="upfile2" onchange="pressed2()" name="store_desktop_banner">
                       <label id="fileLabel2" for="upfile2">Drag and drop here <br> or Choose from the system</label>
                    </div>
                       
                   </div>
                   </div>
                </div>
                <span class="file0-size">Suggestion Size - 1920px * 375px </span>
                <div class="pro-submit">
                <button type="submit" class="btn btn-primary">Submit</button>
             </div>
              </div>
              <!--<div class="col-md-4">
               <div class="card">
                   <div class="seprate row g-3">
                     <div class="col-12">
                       <label for="inputNanme4" class="form-label">Mobile Banner</label>
                    <div>
                       <input type='file' title="Upload Mobile Logo" id="upfile3" onchange="pressed3()" name="store_mobile_banner">
                       <label id="fileLabel3" for="upfile3">Drag and drop here <br> or Choose from the system</label>
                    </div>
                       
                   </div>
                   </div>
                </div>
                <span class="file0-size">Suggestion Size - 720px * 1080px </span>
              </div>-->
             </div>
          
        </form>
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <div class="banner-logs">
          <div class="row">
            <P><b>Banner </b></P>
           <div class="col-md-12">
            <div class="card-body show-plan collections">
              <!-- Bordered Table -->
              <div class="table-responsive">
              <table class="table table-bordered table-white">
                <thead>
                  <tr>
                    <th scope="col">S.No</th>
                    <th scope="col">Created Date</th>
                    <th scope="col">Store Banner</th>
                    <th scope="col">Banner Size</th>
                    <th scope="col">Status</th>
                  </tr>
                </thead>
                <tbody>
                  @if($data)
                  <?php $i =1;?>
                  @foreach($data as $row)
                  <tr>
                    <th scope="row">{{$i++}}</th>
                    <td>{{$row->created_at->format('m-d-Y')}}</td>
                    <td class="p-img"><img src="{{asset('uploads/banner/'.$row->store_desktop_banner)}}"></td>
                    <td>1920X325px</td>
                    <td><span class="@if($row->approve_status=='Approved') {{'en-recovered'}} @else {{'en-dismissed'}} @endif"></span> {{$row->approve_status}}</td>
                  </tr>
                  @endforeach
                  @else
                  <tr>
                    <th scope="row"></th>
                    <td></td>
                    <td class="p-img"></td>
                    <td></td>
                    <td></td>
                  </tr>
                  @endif
                </tbody>
              </table>
              </div>
              <!-- End Bordered Table -->
            </div>
           </div>
          </div>
        </div>
    </section>
   </main>
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
  <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
  <script >
   $('#upfile').change(function(e){
            profile = e.target.files[0];
            var formData= new FormData();
            formData.append('profile' , profile);
            $.ajax({
                cache: false,
                contentType: false,
                processData: false,
                type:'post',
                data:formData,
                headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')},

                url:"{{ route('home-desktop-banner') }}",
                success:function(response){

                }
            });
        });
  
</script>
  <script >
   $('#upfile1').change(function(e){
            profile = e.target.files[0];
            var formData= new FormData();
            formData.append('profile' , profile);
            $.ajax({
                cache: false,
                contentType: false,
                processData: false,
                type:'post',
                data:formData,
                headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')},

                url:"{{ route('home-mobile-banner') }}",
                success:function(response){

                }
            });
        });
  
</script>
<script >
   $('#upfile2').change(function(e){
            profile = e.target.files[0];
            var formData= new FormData();
            formData.append('profile' , profile);
            $.ajax({
                cache: false,
                contentType: false,
                processData: false,
                type:'post',
                data:formData,
                headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')},

                url:"{{ route('store-desktop-banner') }}",
                success:function(response){

                }
            });
        });
  
</script>
<script >
   $('#upfile3').change(function(e){
            profile = e.target.files[0];
            var formData= new FormData();
            formData.append('profile' , profile);
            $.ajax({
                cache: false,
                contentType: false,
                processData: false,
                type:'post',
                data:formData,
                headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')},

                url:"{{ route('store-mobile-banner') }}",
                success:function(response){

                }
            });
        });
  
</script>

    


  <!-- End #main -->
  <!-- ======= Footer ======= -->
  