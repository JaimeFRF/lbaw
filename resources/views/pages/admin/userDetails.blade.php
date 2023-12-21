@extends('layouts.adminApp')

@section('title', 'Antiquus Backoffice - User Details')


@section('content')
    @include('partials.common.breadcrumbs', ['breadcrumbs' => $breadcrumbs , 'current' => $current ])
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <h1 class="mb-4 text-center">User Details</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{$user->username}}'s Information</h3>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <li class="list-group-item"><strong>Name:</strong> {{ $user->name }}</li>
                            <li class="list-group-item"><strong>Username:</strong> {{ $user->username }}</li>
                            <li class="list-group-item"><strong>Email:</strong> {{ $user->email }}</li>
                            <li class="list-group-item"><strong>Phone Number:</strong> {{ $user->phone }}</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Order History</h3>
                    </div>
                    <div class="card-body">
                        @if ($orders->isEmpty())
                            <p class="text-muted">No orders recorded.</p>
                        @else
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center">ID</th>
                                        <th class="text-center">Price</th>
                                        <th class="text-center">Purchase Date</th>
                                        <th class="text-center">Delivery Date</th>
                                        <th class="text-center">Purchase Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orders as $order)
                                        <tr class="order-row" data-order-id={{$order->id}}>
                                            <td class="text-center">{{$order->id}}</td>
                                            <td class="text-center">{{$order->price}}€</td>
                                            <td class="text-center">{{$order->purchase_date}}</td>
                                            <td class="text-center">{{$order->delivery_date}}</td>
                                            <td class="text-center">{{$order->purchase_status}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
