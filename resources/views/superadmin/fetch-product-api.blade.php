@extends('layouts.superadmin')
@section('main')
    <main id="main" class="main">
        <div class="home-flex">
            <div class="pagetitle">
                <h1>Product Fetch From API </h1>
            </div><!-- End Page Title -->
        </div>
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <h5>{{ $message }}</h5>
            </div>
        @endif
        @if ($message = Session::get('error'))
            <div class="alert alert-danger">
                <h5>{{ $message }}</h5>
            </div>
        @endif

        <section class="section up-banner">
            <form class="add-product-form" method="post" id="yourFormId" action="{{route('fetch-product-from-api')}}" enctype="multipart/form-data" >
                @csrf
                <div class="card store-config">
                    <div class="row">
                        <div class="col-12">
                            <label for="inputNanme4" class="form-label">API Link</label>
                            <input type="text" class="form-control" placeholder="Enter API Link" name="api_link" required>
                        </div>

                        <div class="col-12">
                            <label for="inputNanme4" class="form-label">Authorization Token</label>
                            <input type="text" class="form-control" placeholder="Enter Authorization Token" name="authorization_token" required>
                        </div>
                    </div>
                    <div class="col-12">
                        <label for="inputNanme4" class="form-label">Vendor Name</label>
                        <input type="text" class="form-control" placeholder="Enter Vendor Name" name="username" required>
                        <span style="color:red;">
                      @error('username')
                            {{$message}}
                            @enderror
                     </span>
                    </div>

                    <div class="col-12">
                        <label for="inputNanme4" class="form-label">Password</label>
                        <input type="text" class="form-control" placeholder="Password" name="password" required>
                        <span style="color:red;">
                      @error('password')
                            {{$message}}
                            @enderror
                     </span>
                    </div>
                </div>
                </div>
                </div>
                <div class="timer-btns pro-submit">
                    <button type="submit" class="btn btn-primary submitBtn">Submit</button>
                </div>
            </form>
        </section>
        </div>
    </main>
@endsection
@section('js')
    <script>
        $(document).ready(function () {
            $("#yourFormId").submit(function () {
                $(".submitBtn").attr("disabled", true);
                return true;
            });
        });
    </script>
@endsection
