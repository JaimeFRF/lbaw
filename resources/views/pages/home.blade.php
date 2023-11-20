@extends('layouts.app')

@section('css')
<link href="{{ url('css/home.css') }}" rel="stylesheet">
@endsection

@section('content')
    <script>
        window.totalItems = {{ $totalItems }};
    </script>
    <script src="js/item-list.js" data-total-items="{{ $totalItems }}"></script>
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
                <a href="{{route('shop')}}">Shop Now</a>
            </div>
        </div>
    </section>

 
    <section class="product-section">
        <h3>Some of our products</h3>

        <div class="product-container">

            <button class="prev-arrow">&#8249;</button>

            <div class="product-row" id="productRow">
                @include('partials.item-list', ['items' => $items->take(3)])
            </div>


            <button class="next-arrow">&#8250;</button>

        </div>
    </section>

@endsection

