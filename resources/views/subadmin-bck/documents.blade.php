@extends('layouts.admin')

  <main id="main" class="main">
   <div class="home-flex">
    <div class="pagetitle">
       <h1>Documents</h1>
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
                  <th scope="col">Name</th>
                  <th scope="col">Email Id</th>
                  <th scope="col">Documents</th>
                </tr>
              </thead>
              <tbody>
                <?php $i =1;?>
                @foreach($data as $row)
                 <tr>
                  <td scope="row">{{$i++}}</td>
                  <td>{{$row->created_at->format('m-d-Y')}}</td>
                  <td>{{$row->name}}</td>
                  <td>{{$row->email}}</td>
                   <td class="download-doc"><i class="bi bi-download"></i><a href="{{ route('downloaddocument',$row->document) }}" download="{{$row->document}}">Download File</a></td>
                   <!--<td><a href="{{ route('downloaddocument',$row->document) }}"> Download  </a></td>-->
                </tr>
                @endforeach
              </tbody>
            </table>
            </div>
        </div>
        <form class="add-product-form" method="post" action="{{route('post.document')}}" enctype="multipart/form-data">
          @csrf
            <h5>Send Documents</h5>
            <div class="card store-config">
                     <div class="row">
                        <div class="col-12">
                            <label for="inputNanme4" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="" value="" placeholder="enter name" name="name">
                            <span style="color:red;">
                             @error('name')
                               {{$message}}
                            @enderror
                           </span>
                           </div>
                           <div class="col-12">
                            <label for="inputNanme4" class="form-label">Email</label>
                            <input type="email" class="form-control" id="" value="" placeholder="enter email Id" name="email">
                            <span style="color:red;">
                              @error('email')
                               {{$message}}
                             @enderror
                           </span>
                           </div>
                       <div class="col-12">
                        <label for="inputNanme4" class="form-label">Upload Documents File</label>
                        <input type="file" class="form-control" id="" value="" name="document">
                        <span style="color:red;">
                         @error('document')
                          {{$message}}
                        @enderror
                      </span>
                       </div>
                    </div>
            </div>
            <div class="timer-btns pro-submit">
              <button type="submit" class="btn btn-primary">Submit</button>
           </div>
        </form>
    </section>
   </main>
  <!-- End #main -->
  <!-- ======= Footer ======= -->
  