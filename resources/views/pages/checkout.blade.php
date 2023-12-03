@extends('layouts.app')

@section('css')
    <link href="{{ url('css/checkout.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="checkout">
        <div class="container">
            <h2 class="text-center">Checkout</h2>

                <div class="col-md-6 mx-auto checkout-forms">
                    <label for="fullName" class="form-label">Full Name:</label>
                    <input type="text" class="form-control" id="fullName" name="fullName" required>
                </div>

                <div class="col-md-6 mx-auto checkout-forms">
                    <label for="contact" class="form-label">Contact:</label>
                    <input type="tel" class="form-control" id="contact" name="contact" required>
                </div>

                <div class="col-md-6 mx-auto checkout-forms">
                    <label for="address" class="form-label">Address:</label>
                    <input type="text" class="form-control" id="address" name="address" required>
                </div>

                <div class="col-md-6 mx-auto checkout-forms">
                    <label for="postalCode" class="form-label">Postal Code:</label>
                    <input type="text" class="form-control" id="postalCode" name="postalCode" required>
                </div>

                <div class="col-md-6 mb-3 mx-auto payment-options">
                    <div class="row">
                        <div class="col-6 mb-3 payment-option">
                            <input type="radio" id="paypal" name="paymentOption" value="paypal" required>
                            <label for="paypal">PayPal</label>
                        </div>
                        <div class="col-6 mb-3 payment-option">
                            <input type="radio" id="card" name="paymentOption" value="card" required>
                            <label for="card">Card Payment</label>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-primary">Pay</button>
                </div>
            </form>
        </div>
    </div>
@endsection
