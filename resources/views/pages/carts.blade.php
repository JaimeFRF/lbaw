@extends('layouts.app')

@section('css')
<link href="{{ url('css/cart.css') }}" rel="stylesheet">
@endsection

@section('title', 'Cart')

@section('content')
@include('partials.common.breadcrumbs', ['breadcrumbs' => $breadcrumbs , 'current' => $current ])

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
            <td id="total-price" class="fw-bold">
                @php
                    $total = 0;
                    foreach($items as $item) {
                        $quantity = $item->pivot->quantity ?? $item['quantity'];
                        $price = $item->price ?? $item['price'];
                        $total += $price * $quantity;
                    }
                @endphp
                {{ number_format($total, 2) }}€
            </td>
        </tr>
        
        </table>
    </div>

    <div class="cart-buttons d-flex justify-content-around">
        <form method="post" action="{{ route('checkout') }}">
            @csrf
            <div class="cart-buttons d-flex justify-content-around">
                <input type="hidden" id="items" name="items" value="{{ json_encode($items) }}">
                @if (Auth::check())
                    <button type="submit" class="btn btn-success m-2 w-100" {{ count($items) == 0 ? 'disabled' : '' }}>
                        Checkout
                    </button>    
                @else
                    <button type="submit" class="btn btn-success m-2 w-100" id="checkoutButton" {{ count($items) == 0 ? 'disabled' : '' }}>
                        Checkout
                    </button>
                @endif
            </div>
        </form>
    </div>
</section>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


@endsection


