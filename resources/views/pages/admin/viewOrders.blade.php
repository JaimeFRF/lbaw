@extends('layouts.adminApp')

@section('css')
<link href="{{ url('css/admin.css') }}" rel="stylesheet">
@endsection

@section('content')
@include('partials.common.breadcrumbs', ['breadcrumbs' => $breadcrumbs , 'current' => $current ])

<div class="d-flex align-items-center">
    <h2 class="flex-grow-1 text-center">All Orders</h2>
    <button type="button" class="btn btn-outline-dark me-5" data-bs-toggle="modal" data-bs-target="#addOrderModal">Add Order</button>
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
                <th class="text-center">Address</th>
                <th class="text-center"  colspan="2">Actions</th> 
            </tr>
        </thead>
        <tbody>
          @foreach ($orders as $order)
              <tr data-order-id={{$order->id}}>
                  <td class="text-center">{{$order->id}}</td>
                  <td class="text-center">John Doe</td>
                  <td class="text-center">{{$order->price}}</td>
                  <td class="text-center">{{$order->purchase_date}}</td>
                  <td class="text-center">{{$order->delivery_date}}</td>
                  <td class="text-center">{{$order->purchase_status}}</td>
                  <td class="text-center">Rua dos Anjos 123</td>
                  <td class="text-center"><button class="edit-btn btn btn-warning" data-bs-toggle="modal" data-bs-target="#editOrderModal">Edit</button></td>
                  <td class="text-center">
                      <button id="delete" data-order-id={{$order->id}} class="btn btn-outline-danger btn-sm">
                          <i class="fa fa-times"></i>
                          <span>Delete</span>
                      </button>
                  </td>
              </tr>
          @endforeach
        </tbody>
    </table>
</div>

<div class="modal fade" id="editOrderModal" tabindex="-1" aria-labelledby="editOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editOrderModalLabel">Edit Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editOrderForm">
                    @csrf
                    <input type="hidden" id="editOrderId" name="order_id">
                    <div class="mb-3">
                        <label for="editCustomerName" class="form-label">Customer Name</label>
                        <input type="text" class="form-control" id="editCustomerName" name="customer_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="editOrderAmount" class="form-label">Amount</label>
                        <input type="number" class="form-control" id="editOrderAmount" name="amount" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" form="editOrderForm" class="btn btn-primary update-order-btn">Update Order</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addOrderModal" tabindex="-1" aria-labelledby="addOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addOrderModalLabel">Add Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>          
            </div>
            <div class="modal-body">
                <form id="addOrderForm">
                    @csrf
                    <div class="mb-3">
                        <label for="customerName" class="form-label">Customer Name</label>
                        <input type="text" class="form-control" id="customerName" name="customer_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="orderAmount" class="form-label">Price</label>
                        <input type="number" class="form-control" id="orderAmount" name="amount" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" form="addOrderForm" class="btn btn-primary">Add Order</button>
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
