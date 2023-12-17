@extends('layouts.app')

@section('css')
    <link href="{{ url('css/home.css') }}" rel="stylesheet">
    <link href="{{ url('css/contextual_help.css') }}" rel="stylesheet">
@endsection

@section('content')
<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

    <script>
        window.totalItems = {{ $totalItems }};
    </script>
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

 
    <section class="swiper-container">
        <div class="swiper-wrapper" style="margin-top: 2rem; ">
            @foreach($items->chunk(3) as $chunk)
                <div class="swiper-slide" style=" display: flex; align-items: center; justify-content: center; flex-wrap: wrap;">
                    @include('partials.item-list', ['items' => $chunk])
                </div>
            @endforeach
        </div>
        <!-- Add Pagination -->
        <!-- <div class="swiper-pagination" ></div> -->
        <!-- Add Navigation -->
        <!-- <div class="swiper-button-next" style="position: absolute; top: 90%; right:15%"></div>
        <div class="swiper-button-prev" style="position: absolute; top: 90%; left: 15%"></div> -->
    </section>
    <div class="swiper-scrollbar" style=" width: 300px; display: flex; position: absolute; left: 50%; transform: translateX(-50%);"></div>



    <script>
        var swiper = new Swiper('.swiper-container', {
            slidesPerView: 1,
            spaceBetween: 10,
            loop: true,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            scrollbar: {
                el: '.swiper-scrollbar',
            }
        });
    </script>


@endsection

