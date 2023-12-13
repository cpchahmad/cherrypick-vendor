@extends('layouts.superadmin')
<style>
    .btn_size{
        font-size: 11px !important;
    }
    .pause_btn{
        margin-right: 5px;
    }

    .tool-tip {
        content: attr(data-bs-content);
        position: absolute;
        border: #c0c0c0 1px dotted;
        padding: 10px;
        display: none; /* Hide the tooltip by default */
        z-index: 100;
        background-color: #000000;
        color: #ffffff;
        max-width: 200px;
        text-decoration: none;
        text-align: center;
    }

    /* Show the tooltip when hovering over the parent element */
    .items:hover .tool-tip {
        display: block;
        /*top: -50px; !* Adjust as needed *!*/
        /*right: 0;*/
    }


    .sort-by{
        justify-content: normal !important;
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

            <p><strong>Filter by Status and date.</strong></p>
            <div class="row">
                <div class="col-6 mt-1">

                    <div class="input-group">
                        <input type="text" class="datepicker_input form-control datepicker-input" id="fil_date" placeholder="@if(Request::get('date')!='') {{Request::get('date')}} @else {{'Select Date'}} @endif" onblur='filterByDate(this.value)'  aria-label="Date and Month">
                        <i class="bi bi-calendar4 input-group-text"></i>
                    </div>

                </div>
                <div class="col-6 ">

                    <select class="form-select" aria-label="Default select example" onchange='filterByStatus(this.value)'>
                        <option value=''  selected="">Select Status</option>
                        <option value="Complete" {{ Request::get('status') == "Complete" ? 'selected' : '' }}>Complete</option>
                        <option value="In-Progress" {{ Request::get('status') == "In-Progress" ? 'selected' : '' }}>In-Progress</option>
                        <option value="Paused" {{ Request::get('status') == "Paused" ? 'selected' : '' }}>Pause</option>
                        <option value="In-Queue" {{ Request::get('status') == "In-Queue" ? 'selected' : '' }}>In-Queue</option>
                        <option value="On-Hold" {{ Request::get('status') == "On-Hold" ? 'selected' : '' }}>On-Hold</option>
                        <option value="Processing" {{ Request::get('status') == "Processing" ? 'selected' : '' }}>Processing</option>
                        <option value="Failed" {{ Request::get('status') == "Failed" ? 'selected' : '' }}>Failed</option>

                    </select>

                </div>


                <!--<div class="create-plan">
                  <a href="#">Export Products</a>
                </div> -->
            </div>


            <form class="add-product-form">
                <div class="card table-card mt-3">
                    <div class="table-responsive">
                        <table class="table table-borderless view-productd">
                            <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Start Time</th>
                                <th scope="col">End Time</th>
                                <th scope="col">Total Products</th>
                                <th scope="col">Products Pushed</th>
                                <th scope="col">Products Left</th>
                                <th scope="col">Status</th>
                                <th scope="col">Run At</th>
                                <th scope="col">Action</th>

                            </tr>
                            </thead>
                            <tbody>
                            @php $i=0; @endphp


                            @foreach($logs as $index=> $log)

                                @php

                                @endphp


                                <tr>


{{--                                    <td class="tool-tip" data-bs-toggle="tooltip" data-bs-html="true" data-bs-content="1st line of text <br> 2nd line of text">--}}


{{--                                        <a href="@if($log->name=='Approve Product Push'){{route('superadmin.logs.detail',$log->id)}}@else # @endif">--}}

{{--                                        {{ $log->name }}--}}
{{--                                        <br>--}}
{{--                                        <small>{{ $log->date }}</small>--}}
{{--                                     </a>--}}

{{--                                    </td>--}}


                                    @php
                                        $searchData=null;
                                        $vendor=null;
                                        $date=null;
                                        $status=null;
                                        $shopify_status=null;
                                        $product_type=null;
                                        $stock=null;


                                            $data = json_decode($log->filters, true);
                                            if($data){
                                            $searchData = $data['search'];
                                            $vendor = $data['vendor'];
                                            if($vendor){
                                            $vendor_data=\App\Models\Store::find($vendor);
                                            $vendor=$vendor_data->name;
                                            }
                                            $date = $data['date'];
                                            $status = $data['status'];
                                            if($status==1){
                                                $status="Approved";
                                            }elseif ($status==0){
                                                $status="Pending";
                                            }elseif($status==2){
                                                $status="Changes Pending";
                                            }else{
                                                $status="Deny";
                                            }
                                            $shopify_status = $data['shopify_status'];
                                            $product_type = $data['product_type'];
                                            if($product_type){
                                                $product_type_data=\App\Models\ProductType::find($product_type);
                                                $product_type=$product_type_data->product_type;
                                            }
                                            if(isset($data['stock'])){
                                            $stock=$data['stock'];
                                            }
                                            }
                                    @endphp
                                    <td class="item_parent">

                                   <a target="_blank" href="{{route('superadmin.logs.detail',$log->id)}}">

                                  {{ $log->name }}   @if($log->name=='Approve Product Push')

                                           <i class="bi bi-question-circle items"></i> @endif
                                   </a>
                                     <br>
                                     <small>{{ $log->date }}</small>


                                        @if($log->name=='Approve Product Push')
                                        <div class="tool-tip">
                                            <div class="row" style="text-align: start">
                                                @if ($searchData !== null)
                                                <p>Search:  {{$searchData}}  </p>
                                                @endif
                                                    @if ($vendor !== null)
                                                <p>Vendor: {{$vendor}} </p>

                                                    @endif
                                                    @if ($date !== null)
                                                <p>Date: {{$date}} </p>
                                                    @endif
                                                    @if($product_type!==null)
                                                <p>Product Type: {{$product_type}} </p>
                                                    @endif
                                                    @if($status)
                                                <p>App Status: {{$status}} </p>
                                                    @endif
                                                    @if($shopify_status!==null)
                                                <p>Shopify Status: {{$shopify_status}} </p>
                                                        @endif

                                                    @if($stock!==null)
                                                        <p>Stock Type: {{$stock}} </p>
                                                    @endif
                                            </div>
                                        </div>
                                            @endif

                                    </td>
                                    <td> {{ \Carbon\Carbon::parse($log->start_time)->format('m-d-Y H:i:s') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($log->end_time)->format('m-d-Y H:i:s') }}</td>
                                    <td>{{ $log->total_product }}</td>
                                    <td>{{ $log->product_pushed }}</td>
                                    <td>{{ $log->product_left }}</td>

                                    <td>@if($log->status=='In-Progress') <span class="en-in-progress"></span> In Progress @elseif($log->status=='Complete') <span class="en-recovered"></span>{{'Completed'}} @elseif ($log->status=='Paused') <span class="en-dismissed"></span>{{'Pause'}} @else <span class="en-dismissed"></span>{{$log->status}}@endif</td>

                                    <td>@if($log->status=='On-Hold' && $log->running_at){{ \Illuminate\Support\Carbon::parse($log->running_at)->format('F j, Y H:i:s') }}@endif</td>
                                    <td>
                                        @if($log->name=='Approve Product Push')
                                            @if($log->status=='In-Progress' || $log->status=='On-Hold')
                                                <a href="{{route('pause.shopifypush.cronjob',$log->id)}}" class="btn btn-primary pause_btn btn_size">Pause</a>
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
    <script src="https://code.jquery.com/jquery-3.6.0.slim.js" integrity="sha256-HwWONEZrpuoh951cQD1ov2HUK5zA5DwJ1DNUXaM6FsY=" crossorigin="anonymous"></script>

    <script>

        $(document).ready(function (){

            // Show tooltip on hover
            $('.item_parent').on('mouseenter', '.items', function () {
                $(this).parents('.item_parent').find('.tool-tip').show();
            });

            // Hide tooltip when not hovering
            $('.item_parent').on('mouseleave', '.items', function () {
                $(this).parents('.item_parent').find('.tool-tip').hide();
            });
        });
    </script>
    <script>

        function filterByDate(val)
        {
            if(val!='')
            {

                var status='{{Request::get('status')}}';
                window.location.href='logs?&date='+val+'&status='+status;
            }
        }

        function filterByStatus(id)
        {

            var date='{{Request::get('date')}}';

            window.location.href='logs?&date='+date+'&status='+id;
        }

    </script>
@endsection

