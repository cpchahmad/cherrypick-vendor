@extends('layouts.superadmin')

  <main id="main" class="main">
   <div class="home-flex">
    <div class="pagetitle">
       <h1>Signed Documents</h1>
    </div><!-- End Page Title -->
   </div>
   @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                       <h5>{{ $message }}</h5>
                     </div>
                 @endif
     
    <section class="section up-banner">
        <div class="rcv-doc">
        <h5>Received Documents</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-white">
              <thead>
                <tr>
                  <th scope="col">S.No</th>
                  <th scope="col">Date</th>
                  <th scope="col">Docuent Name</th>
				  <th scope="col">Vendor Name</th>
                  <th scope="col">Vendor Email Id</th>
                  <th scope="col">Documents</th>
                </tr>
              </thead>
              <tbody>
                <?php $i=1; ?>
                @foreach($document as $collection)
                 @foreach($collection as $row)
                <tr>
                  <td scope="row">{{$i++}}</td>
                  <td>{{$row->created_at->format('m-d-Y')}}</td>
                  <td>{{$row->name}}</td>
				  <td>{{$row->vendorname}}</td>
                  <td>{{$row->email}}</td>
                  <td class="download-doc"><i class="bi bi-download"></i><a href="{{route('downloadfile',$row->document) }}" download="{{$row->document}}">Download File</a></td>
                </tr>
                @endforeach
                @endforeach
              </tbody>
            </table>
            </div>
        </div>
        <!--<form class="add-product-form" method="post" action="{{route('submitdocument')}}" enctype="multipart/form-data" >
          @csrf
            <h5>Send Documents</h5>
            <div class="card store-config">
                     <div class="row">
                        <div class="col-12">
                            <label for="inputNanme4" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="" value="" placeholder="Enter Name" name="name">
                           </div>
                           <div class="col-12">
                            <label for="inputNanme4" class="form-label">Email</label>
                            <input type="email" class="form-control" id="" value="" placeholder="Enter Email" name="email">
                           </div>
                       <div class="col-12">
                        <label for="inputNanme4" class="form-label">Upload Documents File</label>
                        <input type="file" class="form-control" id="" value="" name="file">
                       </div>
                    </div>
            </div>
            <div class="timer-btns pro-submit">
              <button type="submit" class="btn btn-primary">Submit</button>
           </div>
        </form>-->
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
       
</script>
