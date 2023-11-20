@extends('layouts.app')

@section('title', 'Cart')

@section('content')
<section class="small-container cart-page">
    <table>
        <tr>
          <th>Item</th>
          <th>Quantity</th>
          <th>Subtotal</th>
        </tr>
    @foreach($items as $item)
    @include('partials.cart', ['item' => $item])
    @endforeach
    <div class = "total-price">
        <table>
            <tr>
                <td>Total</td>
                <td>@foreach($items as $item)
                    {{$item->price * $item->pivot->quantity}}€
                    @endforeach
                </td>
            </tr>
        </table>
    </div>

    <div class="cart-buttons d-flex justify-content-around">
            <button class="btn btn-success m-2 w-100">
                Checkout
            </button>
            <button class="btn btn-outline-danger m-2 w-100">
                Empty Cart
            </button>
    </div>


</section>
@endsection


