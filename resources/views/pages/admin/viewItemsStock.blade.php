@extends('layouts.adminApp')

@section('content')

<div>
  <h2>Item Sizes Stock</h2>
  <table class="table">
    <thead>
      <tr>
        <th class="text-center">ID</th>
        <th class="text-center">Product Name</th>
        <th class="text-center">Size</th>
        <th class="text-center">Stock Quantity</th>
        <th class="text-center" colspan="2">Action</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>1</td>
        <td>Product 1</td>
        <td>XL</td>
        <td>100</td>
        <td><button class="btn btn-primary">Edit</button></td>
        <td><button class="btn btn-danger">Delete</button></td>
      </tr>
      <tr>
        <td>2</td>
        <td>Product 1</td>
        <td>L</td>
        <td>50</td>
        <td><button class="btn btn-primary">Edit</button></td>
        <td><button class="btn btn-danger" >Delete</button></td>
      </tr>
      <!-- Add more rows as needed -->
    </tbody>
  </table>
</div>

@endsection
   