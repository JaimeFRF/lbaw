@extends('layouts.adminApp')

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
                    <label for="itemCategory" class="form-label">Category:</label>
                    <select class="form-select" id="itemCategory" name="itemCategory" required>
                        <option value="null">---</option>
                        <option value="Shirt">Shirt</option>
                        <option value="T-Shirt">T-Shirt</option>
                        <option value="Jacket">Jacket</option>
                        <option value="Jeans">Jeans</option>
                        <option value="sneakers">sneakers</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="itemPrice" class="form-label">Price:</label>
                    <input type="number" class="form-control" id="itemPrice" name="itemPrice" required>
                </div>

                <div class="mb-3">
                    <label for="itemDescription" class="form-label">Description:</label>
                    <textarea class="form-control" id="itemDescription" name="itemDescription" rows="5" required></textarea>
                </div>

                <div class="mb-3">
                    <label for="itemFabric" class="form-label">Material:</label>
                    <input type="text" class="form-control" id="itemFabric" name="itemFabric" required>
                </div>

                <div class="mb-3">
                    <label for="itemPhoto" class="form-label">Photo:</label>
                    <input type="file" class="form-control" id="itemPhoto" name="itemPhoto" accept="image/*" required>
                </div>

                <button type="submit" class="btn btn-primary add">Add Item</button>
            </form>
        </div>
    </div>
@endsection
