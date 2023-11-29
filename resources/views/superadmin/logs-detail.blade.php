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
</style>


@section('main')
    <main id="main" class="main">
        <div class="home-flex">
            <div class="pagetitle">
                @if($log)
                <h1>{{$log->name}}</h1>
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
                            @if($log)
                            <table class="table table-bordered table-white">
                                <thead>
                                <tr>
                                    <th scope="col" style="background-color: #E0E0E0;">Product Name</th>
                                    <th scope="col">Status</th>

                                </tr>
                                </thead>
                                <tbody>

                                @foreach($product_ids as $product_id)
                                @php

                                $product=\App\Models\Product::find($product_id);
                                @endphp
                                @if($product)
                                    <tr>

                                        <td>{{$product->title}}</td>
                                        <td>
                                <span class="
                                    @if($product->shopify_status == 'Complete') badge bg-success
                                    @elseif($product->shopify_status == 'Pending') badge bg-warning
                                    @else badge bg-danger
                                    @endif">
                                    {{ $product->shopify_status }}
                                </span>
                                        </td>

                                    </tr>
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                        @endif
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


