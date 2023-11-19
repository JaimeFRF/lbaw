@extends('layouts.app')

@section('title', 'Cart')

@section('content')
    <section id="carts">
        @include('partials.cart', ['cart' => $cart])
    </section>
@endsection