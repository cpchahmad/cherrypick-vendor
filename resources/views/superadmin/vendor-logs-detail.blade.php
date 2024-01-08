@extends('layouts.superadmin')

<style>
    .table-responsive {
        overflow-x: auto;
    }

    .table-bordered th,
    .table-bordered td {
        border: 1px solid #dee2e6;
    }

    .table-responsive thead th:first-child,
    .table-responsive tbody td:first-child {
        position: sticky;
        left: 0;
        background-color: #fff; /* Adjust background color as needed */
        z-index: 2;
    }

    .table-responsive thead th:first-child::after,
    .table-responsive tbody td:first-child::after {
        content: '\00a0'; /* Add a non-breaking space to ensure content is visible */
    }
    .a_tag{
        color: #a70b44 !important;
        text-decoration: none;
    }
</style>


@section('main')
    <main id="main" class="main">
        <div class="home-flex">
            <div class="pagetitle">
                @if($log)
                    <h1>{{$log->name}} ( {{$log->date}})</h1>
                @endif
            </div><!-- End Page Title -->
            {{--            <a class="btn btn-primary" href="{{url('superadmin/updateprice')}}">Update Product Prices</a>--}}
        </div>

        <section class="section up-banner">
            <div class="row">
                <div class="col-md-12">
                    <div class="card-body show-plan collections">
                        <!-- Bordered Table -->
                        <div class="table-responsive">

                                <table class="table table-bordered table-white">
                                    <thead>
                                    <tr>
                                        <th scope="col" style="background-color: #E0E0E0;">Vendor Name</th>
                                        <th scope="col" style="background-color: #E0E0E0;">Start Time</th>
                                        <th scope="col" style="background-color: #E0E0E0;">End Time</th>
                                        <th scope="col" style="background-color: #E0E0E0;">Total Products</th>
                                        <th scope="col">Status</th>

                                    </tr>
                                    </thead>
                                    <tbody>

                                    @foreach($vendor_logs as $vendor_log)


                                            <tr>

                                                <td ><a target="_blank" class="a_tag" href="{{route('superadmin.logs.detail',$log->id)}}">{{$vendor_log->vendor_name}}
                                                    </a>


                                                </td>
                                                <td>{{$vendor_log->start_time}}</td>
                                                <td>{{$vendor_log->end_time}}</td>
                                                <td>{{$vendor_log->total_products}}</td>
                                                <td>
                                <span class="
                                    @if($vendor_log->status == 'Complete') badge bg-success
                                    @elseif($vendor_log->status == 'In-Progress') badge bg-warning
                                    @else badge bg-danger
                                    @endif">
                                    {{ $vendor_log->status }}
                                </span>
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


