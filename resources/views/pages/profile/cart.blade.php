<!-- resources/views/home.blade.php -->
@extends('layouts.app')

@section('content')
    <section class="hero-section">
        <div class="hero-image">
            <!-- Vintage image goes here -->
            <img class="hero-banner" src="{{ asset('images/heroBanner.jpg') }}" alt="Hero Banner">
            
            <!-- Semi-transparent title -->
            <div class="image-overlay">
                <h1>Antiquus</h1>
                <h2>Shop Oldschool</h2>
            </div>

            <div class="image-overlay-btn">
                <a href="">Shop Now</a>
            </div>
        </div>
    </section>

    <section class="product-section">
        <h3>Some of our products</h2>

        <div class="product-container">

            <button class="prev-arrow" onclick="">&#8249;</button>

            <!-- Product 1 -->
            <div class="product">
                <img src="{{ asset('images/default-product-image.png') }}" alt="Product 1">
                <h4>Product 1</h4>
                <p>Description of Product 1.</p>
                <span>$19.99</span>
            </div>

            <!-- Product 2 -->
            <div class="product">
                <img src="{{ asset('images/default-product-image.png') }}" alt="Product 2">
                <h4>Product 2</h4>
                <p>Description of Product 2.</p>
                <span>$24.99</span>
            </div>

            <!-- Product 3 -->
            <div class="product">
                <img src="{{ asset('images/default-product-image.png') }}" alt="Product 3">
                <h4>Product 3</h4>
                <p>Description of Product 3.</p>
                <span>$29.99</span>
            </div>

            <button class="next-arrow" onclick="">&#8250;</button>

        </div>
    </section>
@endsection

