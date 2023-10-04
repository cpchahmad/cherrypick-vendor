@extends('layouts.superadmin')
@section('main')
  <main id="main" class="main">
   <div class="home-flex">
    <div class="pagetitle">
       <h1>Bannner Lists</h1>
    </div><!-- End Page Title -->
   </div>

    <section class="section up-banner">
      <div class="row">
        <P><b>Bannner Lists</b></P>
       <div class="col-md-12">
        <div class="card-body show-plan collections">
          <!-- Bordered Table -->
          <div class="table-responsive">
          <table class="table table-bordered table-white">
            <thead>
              <tr>
                <th scope="col">S.No</th>
                <th scope="col">Vendor Name</th>
                <th scope="col">Banner</th>
                <th scope="col">Status</th>
				<th scope="col">Download</th>
                <th scope="col">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php $i=1; ?>
              @foreach($list as $vendor)
              <tr>
                <td>{{$i++}}</td>
                <td>{{$vendor->name}}</td>
                <td class="p-img"><img src="{{asset('uploads/banner/'.$vendor->store_desktop_banner)}}"></td>
                <td><span class="@if($vendor->approve_status=='Approved') {{'en-recovered'}} @else {{'en-dismissed'}} @endif"></span> {{$vendor->approve_status}}</td>
                <!--<td class="icon-action">
                  <span class="form-switch">
                      <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault_{{$vendor->id}}" onclick="changeBannerStatus({{$vendor->id}})" @if($vendor->approve_status=='Approved') {{'checked'}} @endif>
                     </span>
               </td>-->
			   <td class="download-doc"><i class="bi bi-download"></i><a href="{{ route('download',$vendor->store_desktop_banner) }}">Download Banner</a><br>
			   @if($vendor->logo!='')<i class="bi bi-download"></i><a href="{{ route('download_logo',$vendor->logo) }}">Download Logo</a>@endif
			   </td>
			   <td>
					<a class="btn btn-success btn-sm" href="javascript:void(0)" onclick="changeBannerStatus('{{$vendor->id}}','Approved')">Approve</a>
					<a class="btn btn-danger btn-sm" href="javascript:void(0)" onclick="changeBannerStatus('{{$vendor->id}}','Disable')">Deny</a>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
          </div>
          <!-- End Bordered Table -->
        </div>
       </div>
      </div>
    </section>
   </main>
  <!-- End #main -->
  <!-- ======= Footer ======= -->
@endsection
<script>
     function changeBannerStatus(id,status)
     {
        var v_token = "{{csrf_token()}}";
        $.ajax({
                type:'post',
                data:{id : id, status : status},
                headers: {'X-CSRF-Token': v_token},
                url:"{{ route('superadmin.change-banner-status') }}",
                success:function(response){
                    var json = $.parseJSON(response);
                    if(json.status=='success')
                    {
                        alert("Status Updated Successfully!!");
						window.location.href='banner';
                    }
                }
            });
     }
</script>
