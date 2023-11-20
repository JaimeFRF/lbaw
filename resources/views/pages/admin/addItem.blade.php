@extends('layouts.app')

@section('css')
    <link href="{{ url('css/admin.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="admin-add-item">
        <div class="container col-md-6">
            <h2>Add Item</h2>

            <form method="POST" action="" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="itemName" class="form-label">Name:</label>
                    <input type="text" class="form-control" id="itemName" name="itemName" required>
                </div>

                <div class="mb-3">
                    <label for="itemPhoto" class="form-label">Photo:</label>
                    <input type="file" class="form-control" id="itemPhoto" name="itemPhoto" accept="image/*" required>
                </div>

                <div class="mb-3">
                    <label for="itemSize" class="form-label">Size:</label>
                    <select class="form-select" id="itemSize" name="itemSize" required>
                        <option value="null">---</option>
                        <option value="XS">XS</option>
                        <option value="S">S</option>
                        <option value="M">M</option>
                        <option value="L">L</option>
                        <option value="XL">XL</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="quantityInStock" class="form-label">Quantity in Stock:</label>
                    <input type="number" class="form-control" id="quantityInStock" name="quantityInStock" min="0" required>
                </div>


                <div class="mb-3">
                    <label for="itemFabric" class="form-label">Fabric:</label>
                    <input type="text" class="form-control" id="itemFabric" name="itemFabric" required>
                </div>

                <div class="mb-3">
                    <label for="itemDescription" class="form-label">Description:</label>
                    <textarea class="form-control" id="itemDescription" name="itemDescription" rows="5" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary add">Add Item</button>
            </form>
        </div>
    </div>
@endsection
