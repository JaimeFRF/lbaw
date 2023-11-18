@extends('layouts.app')

@section('title', $cart->name)

@section('content')
    <section id="carts">
        @include('partials.cart', ['cart' => $cart])
    </section>
@endsection