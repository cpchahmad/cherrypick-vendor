@extends('layouts.superadmin')
@section('main')
    <main id="main" class="main">
        <div class="home-flex">
            <div class="pagetitle">
                <h1>Logs</h1>
            </div><!-- End Page Title -->
        </div>
        <section class="section up-banner">

            <form class="add-product-form">
                <div class="card table-card">
                    <div class="table-responsive">
                        <table class="table table-borderless view-productd">
                            <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Date</th>
                                <th scope="col">Start Time</th>
                                <th scope="col">End Time</th>
                                <th scope="col">Total Products</th>
                                <th scope="col">Products Pushed</th>
                                <th scope="col">Products Left</th>
                                <th scope="col">Status</th>

                            </tr>
                            </thead>
                            <tbody>
                            @php $i=0; @endphp
                            @foreach($logs as $index=> $log)
                                <tr>

                                    <td>{{ $log->name }}</td>
                                    <td>{{ $log->date }}</td>
                                    <td>{{ $log->start_time }}</td>
                                    <td>{{ $log->end_time }}</td>
                                    <td>{{ $log->total_product }}</td>
                                    <td>{{ $log->product_left }}</td>
                                    <td>{{ $log->product_pushed }}</td>
                                    <td>@if($log->status=='In-Progress') <span class="en-in-progress"></span> In Progress @elseif($log->status=='Complete') <span class="en-recovered"></span>{{'Completed'}} @else ($log->status=='Failed') <span class="en-dismissed"></span>{{'Failed'}} @endif</td>

                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
            <nav class="mainpg timer-nav">
                {{ $logs->links( "pagination::bootstrap-4") }}
            </nav>
        </section>
    </main>
@endsection

