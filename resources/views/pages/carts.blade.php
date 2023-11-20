@extends('layouts.app')

@section('title', 'Cart')

@section('content')
<section class="small-container cart-page">
    <script src="{{ asset('js/script.js') }}"></script>
    <table>
        <tr>
          <th>Item</th>
          <th>Quantity</th>
          <th>Subtotal</th>
        </tr>
        @foreach($items as $item)
        @include('partials.cart', ['item' => $item])
        @endforeach
    </table>

    <div  class = "m-4 cart-total">
        <h4 class="fw-bold">Cart Total</h4>
        <table>
        <tr>
            <td> Cart Subtotal</td>
            <td>{{ $items->sum(function($item) { return $item->price * $item->pivot->quantity; }) }}€</td>
        </tr>
        <tr>
            <td>Shipping</td>
            <td>Free</td>
        </tr>
        <tr>
            <td class="fw-bold">Total</td>
            <td class="fw-bold">{{ $items->sum(function($item) { return $item->price * $item->pivot->quantity; }) }}€</td>
        </tr>
        </table>
        <div class="cart-buttons d-flex justify-content-around">
            <button class="btn btn-success m-2 w-100">
                    Checkout
                </button>
            <button class="btn btn-outline-danger m-2 w-100">
                    Empty Cart
            </button>
        </div>

    </div>

</section>

@endsection


