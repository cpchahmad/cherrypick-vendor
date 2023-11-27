@extends('layouts.superadmin')
<style>
    .btn_size{
        font-size: 11px !important;
    }
</style>
@section('main')
    <main id="main" class="main">
        <div class="row">
            <div class="col-6">
        <div class="home-flex">
            <div class="pagetitle">
                <h1>Logs</h1>
            </div><!-- End Page Title -->
        </div>
        </div>
            <div class="col-6 mt-3">

                <a href="{{route('superadmin.update.product.shopifystatus')}}" style="float: right" class="btn btn-primary btn-sm" >Reset Status</a>
            </div>
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
                                <th scope="col">Action</th>

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
                                    <td>{{ $log->product_pushed }}</td>
                                    <td>{{ $log->product_left }}</td>

                                    <td>@if($log->status=='In-Progress') <span class="en-in-progress"></span> In Progress @elseif($log->status=='Complete') <span class="en-recovered"></span>{{'Completed'}} @elseif ($log->status=='Failed') <span class="en-dismissed"></span>{{'Failed'}} @else <span class="en-dismissed"></span>{{$log->status}}@endif</td>
                                    <td>
                                        @if($log->name=='Approve Product Push')
                                            @if($log->status=='In-Progress')
                                                <a href="{{route('pause.shopifypush.cronjob',$log->id)}}" class="btn btn-primary btn_size">Paused</a>
                                            @elseif($log->status=='Paused')
                                                <a href="{{route('start.shopifypush.cronjob',$log->id)}}" class="btn btn-success btn_size">Start</a>
                                            @endif
                                        @endif
                                    </td>

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

