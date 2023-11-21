@extends('layouts.app')

@section('content')

<div class="admin-add-item">
    <div class="container col-md-6">
        <h2>Add Item Sizes Stock</h2>

        <form method="POST" action="" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label for="productName" class="form-label">Product Name:</label>
                <select class="form-select" id="size" name="itemName" required>
                    <option value="XS">ALL POSSIBLE ITEMS ITERATE ...</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="size" class="form-label">Size:</label>
                <select class="form-select" id="size" name="size" required>
                    <option value="XS">XS</option>
                    <option value="S">S</option>
                    <option value="M">M</option>
                    <option value="L">L</option>
                    <option value="XL">XL</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="stockQuantity" class="form-label">Stock Quantity:</label>
                <input type="number" class="form-control" id="stockQuantity" name="stockQuantity" required>
            </div>

            <button type="submit" class="btn btn-primary add">Add Stock</button>
        </form>
    </div>
</div>

@endsection