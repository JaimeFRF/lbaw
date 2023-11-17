<!-- resources/views/home.blade.php -->
@extends('layouts.app')

@section('content')

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antiquus</title>
    <link href="{{ url('css/home.css') }}" rel="stylesheet">
</head>
<body>
<section class="hero-section">
        <div class="hero-image">
            <!-- Vintage image goes here -->
            <img src="{{ asset('images/heroBanner.jpg') }}" alt="Hero Banner">
            
            <!-- Semi-transparent title -->
            <div class="image-overlay">
                <h1>Antiquus</h1>
            </div>
        </div>
    </section>
</body>
</html>
