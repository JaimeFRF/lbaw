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
                            <a href="#" class="btn btn-secondary rounded-circle">
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
                        <p class="card-text">Your wishlist is empty.</p>
                        <a href="#" class="btn btn-link text-decoration-none text-reset align-self-end mt-auto">See more...</a>
                    </div>
                </div>
            </div>

            <!-- Order History -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header text-center">
                        Order History
                    </div>
                    <div class="card-body d-flex flex-column">
                        <p class="card-text">No orders yet.</p>
                        <a href="#" class="btn btn-link text-decoration-none text-reset align-self-end mt-auto">See more...</a>
                    </div>
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
                        <a href="#" class="btn btn-link text-decoration-none text-reset align-self-end mt-auto">See more...</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
