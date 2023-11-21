@extends('layouts.app')

@section('content')

<section class="container mt-5">
        <div class="row">
            <!-- User Information with Picture -->
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
                            <!-- User profile picture -->
                            <img src={{$profile_picture}} alt="Profile Picture" class="rounded-circle" width="100" height="100">
                        </div>
                        <div>
                            <p class="mb-1"><strong>Username:</strong> {{$user->username}}</p>
                            <p class="mb-1"><strong>Name:</strong> Não definido</p>
                            <p class="mb-0"><strong>Email:</strong> {{$user->email}}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Wishlist -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header text-center">
                        Wishlist
                    </div>
                    <div class="card-body d-flex flex-column">

                        @foreach($items_wishlist as $item)
                            <div class="wishlist-item mb-3 d-flex align-items-center">                   
                            @if($item->images()->first() && $item->images()->first()->filepath)
                                <img src="{{ asset($item->images()->first()->filepath) }}" class= "w-30 h-30">
                            @else
                                <img src="{{ asset('images/default-product-image.png') }}" class= "w-30 h-30">
                            @endif
                                <div class = "ms-2" style="max-width: 200px;">
                                    <h6>{{ $item->name }}</h6>
                                    <form method = "POST" action={{url('users/wishlist/product/'.$item->id)}}>
                                        @csrf
                                        @method('delete')

                                        <button class = "btn btn-outline-danger btn-sm" type = "submit">
                                            <i class="fa fa-times"></i>
                                            <span>Remove</span>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            @if(!$loop->last)
                                    <hr class="my-2">
                            @endif

                        @endforeach
                    </div>
                    <a href="#" class="btn btn-link text-decoration-none text-reset align-self-end mt-auto">See more...</a>
                </div>
            </div>        

            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header text-center">
                        Order History
                    </div>
                    <div class="card-body">
                        <!-- Order 1 -->
                        @for($i = 0; $i < count($purchases); $i++)
                            <div class="order-history mb-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h6 class="mb-1">{{$purchases[$i]->id}}</h6>
                                        <p class="mb-1">Date: {{$purchases[$i]->purchase_date}}</p>
                                        <p class="mb-1">Value: {{$purchases[$i]->price}}</p>
                                        <p class="mb-0">Items:
                                        <span class="item-info">

                                            @php
                                                $cart = $carts_purchase[$i];
                                                $items_cart = $cart->products()->get();
                                                for($j = 0; $j < count($items_cart); $j++) {
                                                    echo "<span class='item-quantity'>{$items_cart[$j]->name} ({$items_cart[$j]->pivot->quantity})</span>, ";
                                                }
                                            @endphp

                                        </span>
                                        </p>
                                    </div>
                                    <button class="btn btn-outline-dark btn-sm">Details</button>
                                </div>
                            </div>
                            <hr class="my-3">
                        @endfor

                    </div>
                    <a href="#" class="btn btn-link text-decoration-none text-reset align-self-end mt-auto">See more...</a>
                </div>
            </div>

            <!-- Reviews -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header text-center">
                        Reviews
                    </div>
                    <div class="card-body d-flex flex-column">
                        <p class="card-text">No reviews yet.</p>
                    </div>
                    <a href="#" class="btn btn-link text-decoration-none text-reset align-self-end mt-auto">See more...</a>
                </div>
            </div>
        </div>
    </section>
@endsection