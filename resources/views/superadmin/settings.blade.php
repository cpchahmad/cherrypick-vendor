@extends('layouts.superadmin')
@section('main')
    <main id="main" class="main">
        <div class="home-flex">
            <div class="pagetitle fit-title">
                <h1>Settings</h1>
            </div><!-- End Page Title -->
        </div>

        <section class="section up-banner">
            @if ($message = Session::get('success'))
                <div class="alert alert-success">{{ $message }}</div>
            @endif
            <form class="add-product-form" method="post" action="{{url('superadmin/save-settings')}}" enctype="multipart/form-data">
                @csrf
                <div class="card">
                    <div class="row">
                        <div class="col-6">
                            <label for="inputNanme4" class="form-label">API Key</label>
                            <input type="text" class="form-control shiping" id="" name="api_key" required="true" value="@if(isset($setting->api_key)){{$setting->api_key}}@endif" required="true">
                        </div>
                        <div class="col-6">
                            <label for="inputNanme4" class="form-label">Password</label>
                            <input type="text" class="form-control shiping" id="" name="password" value="@if(isset($setting->password)){{$setting->password}}@endif" required="true">
                        </div>

                        <div class="col-6 mt-2">
                            <label for="inputNanme4" class="form-label">Shop URL</label>
                            <input type="text" class="form-control shiping" id="" name="shop_url" value="@if(isset($setting->shop_url)){{$setting->shop_url}}@endif" required="true">
                        </div>
                    </div>
                </div>


                <div class="timer-btns">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </section>
    </main>
@endsection
@section('js')
    <script type="text/javascript">
        jQuery(document).ready(function() {
            $('.shiping').keypress(function(event) {
                if ((event.which < 48 || event.which > 57)) {
                    event.preventDefault();
                }
            });
        });
    </script>
@stop
