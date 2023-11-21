@extends('layouts.app')

@section('css')
<link href="{{ url('css/cart.css') }}" rel="stylesheet">
@endsection

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
            <td>Shipping</td>
            <td>Free</td>
        </tr>
        <tr>
            <td class="fw-bold">Total</td>
            <td id="total-price" class="fw-bold">{{ $items->sum(function($item) { return $item->price * $item->pivot->quantity; }) }}€</td>
        </tr>
        </table>
    </div>
    <div class="cart-buttons d-flex justify-content-around">
        <form method="post" action="{{ route('add_purchase') }}">
            @csrf
            <div class="cart-buttons d-flex justify-content-around">
                <!-- You can add hidden input for the items -->
                <input type="hidden" id="items" name="items" value="{{ json_encode($items) }}">

                <button type="submit" class="btn btn-success m-2 w-100">
                    Checkout
                </button>
            </div>
        </form>
        <button type="submit" class="btn btn-outline-danger m-2 w-100">
                Empty Cart
        </button>
    </div>
</section>

@endsection


