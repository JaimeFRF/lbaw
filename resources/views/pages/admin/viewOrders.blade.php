@extends('layouts.adminApp')

@section('css')
<link href="{{ url('css/admin.css') }}" rel="stylesheet">
@endsection

@section('content')

<div class="d-flex align-items-center">
    @include('partials.common.breadcrumbs', ['breadcrumbs' => $breadcrumbs , 'current' => $current ])
    <h2 class="flex-grow-1 text-center">All Orders</h2>
    {{-- <button type="button" class="btn btn-outline-dark me-5" data-bs-toggle="modal" data-bs-target="#addOrderModal">Add Order</button> --}}
</div>
<div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th class="text-center">ID</th>
                <th class="text-center">Name</th>
                <th class="text-center">Price</th>
                <th class="text-center">Purchase Date</th>
                <th class="text-center">Delivery Date</th>
                <th class="text-center">Purchase Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
                <tr class="order-row" data-order-id={{$order->id}}>
                    <td class="text-center">{{$order->id}}</td>
                    <td class="text-center">John Doe</td>
                    <td class="text-center">{{$order->price}}€</td>
                    <td class="text-center">{{$order->purchase_date}}</td>
                    <td class="text-center">{{$order->delivery_date}}</td>
                    <td class="text-center">{{$order->purchase_status}}</td>
                    <td class="text-center">
                    <button id="delete" data-order-id={{$order->id}} class="btn btn-outline-danger btn-sm">
                        <i class="fa fa-times"></i>
                    </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>


<!-- Detailed Order Modal -->
<div class="modal fade" id="detailedOrderModal" tabindex="-1" aria-labelledby="detailedOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg"> 
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailedOrderModalLabel">Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="detailedOrderForm">
                    @csrf
                    <input type="hidden" id="detailedOrderId" name="order_id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="detailedCustomerName" class="form-label">Customer Name</label>
                            <input type="text" class="form-control" id="detailedCustomerName" name="customer_name" disabled>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="detailedOrderAmount" class="form-label">Amount</label>
                            <input type="number" class="form-control" id="detailedOrderAmount" name="amount" step="0.01" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="detailedOrderStatus" class="form-label">Status</label>
                        <select class="form-select" id="detailedOrderStatus" name="status" required>
                            <option value="Processing">Processing</option>
                            <option value="Packed">Packed</option>
                            <option value="Sent">Sent</option>
                            <option value="Delivered">Delivered</option>                        
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="detailedOrderDeliveryDate" class="form-label">Delivery Date</label>
                        <input type="date" class="form-control" id="detailedOrderDeliveryDate" name="deliveryDate" required>
                    </div>

                    <div class="mb-3">
                        <label for="detailedOrderAddress" class="form-label">Address</label>
                        <input type="text" class="form-control" id="detailedOrderAddress" name="address" required>
                    </div>

                    <div class="mb-3">
                        <label for="detailedOrderCity" class="form-label">City</label>
                        <input type="text" class="form-control" id="detailedOrderCity" name="city" required>
                    </div>

                    <div class="mb-3">
                        <label for="detailedOrderCountry" class="form-label">Country</label>
                        <input type="text" class="form-control" id="detailedOrderCountry" name="country" required>
                    </div>

                    <div class="mb-3">
                        <label for="detailedOrderPostalCode" class="form-label">Postal Code</label>
                        <input type="text" class="form-control" id="detailedOrderPostalCode" name="postalCode" required>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" form="detailedOrderForm" class="btn btn-primary" id="updateOrderButton">Save Changes</button>
            </div>            
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js" defer></script>
<script src="{{ asset('js/admin-orderspage.js') }}" defer></script>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>

@endsection
