@extends('layouts.app')

@section('title', 'Carts')

@section('content')

<section id="cards">
    @each('partials.cart', $carts, 'cart')
    <article class="cart">
        <form class="new_cart">
            <input type="text" name="name" placeholder="new cart">
        </form>
    </article>
</section>

@endsection