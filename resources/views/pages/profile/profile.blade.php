@extends('layouts.app')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="{{ url('js/profile.js') }}"></script>

@section('content')
@include('partials.common.breadcrumbs', ['breadcrumbs' => $breadcrumbs , 'current' => $current ])

<section class="container mt-5">
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>User Information</span>
                        <div>
                            <span class="ms-2">Edit Profile</span>
                            <a href="{{ route('edit_profile') }}" class="btn btn-secondary rounded-circle">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body d-flex align-items-center">
                        <div class="profile-picture me-4">
                            
                            <img src="{{ asset($profile_picture) }}" alt="Profile Picture" class="rounded-circle" width="100" height="100">
                        </div>
                        <div>
                            <p class="mb-1"><strong>Username:</strong> {{$user->username}}</p>
                            <p class="mb-1"><strong>Name:</strong> {{$user->name}}</p>
                            <p class="mb-0"><strong>Email:</strong> {{$user->email}}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header text-center">
                        Wishlist
                    </div>

                    <div class="card-body d-flex flex-column scrollable-content" id="wishlist-history">
                        @include('partials.profile.wishlist', ['items_wishlist' => $items_wishlist])
                    </div>
                    @if(count($items_wishlist) > 3)
                    <a href="#" class="btn btn-link text-decoration-none text-reset align-self-end mt-auto" onclick="toggleScroll('wishlist-history')">See more...</a>
                    @endif
                </div>
            </div>  

            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header text-center">
                        Pending Orders
                    </div>
                    
                    <div class="card-body  d-flex flex-column scrollable-content" id="orders-history">
                        @include('partials.profile.profile-pending', ['orders' => $orders, 'carts_orders' => $carts_orders])
                    </div>
                    @if(count($carts_orders) > 2)
                    <a href="#" class="btn btn-link text-decoration-none text-reset align-self-end mt-auto" onclick="toggleScroll('orders-history')">See more...</a>
                    @endif
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header text-center">
                        Purchase History
                    </div>

                    <div class="card-body  d-flex flex-column scrollable-content" id="purchase-history">
                        @include('partials.profile.profile-purchases', ['purchases' => $purchases, 'carts_purchases' => $carts_purchases])
                    </div>
                    @if(count($carts_purchases) > 2)
                    <a href="#" class="btn btn-link text-decoration-none text-reset align-self-end mt-auto" onclick="toggleScroll('purchase-history')">See more...</a>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection