@extends('layouts.app')

@section('content')
  <div>
    <h2>Product Items</h2>
    <table class="table">
      <thead>
        <tr>
          <th class="text-center">ID</th>
          <th class="text-center">Product Image</th>
          <th class="text-center">Product Name</th>
          <th class="text-center">Product Description</th>
          <th class="text-center">Category Name</th>
          <th class="text-center">Unit Price</th>
          <th class="text-center" colspan="2">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>1</td>
          <td><img src='path_to_image1'></td>
          <td>Product 1</td>
          <td>Description for Product 1</td>
          <td>Category A</td>
          <td>19.99€</td>
          <td><button class="btn btn-primary">Edit</button></td>
          <td><button class="btn btn-danger">Delete</button></td>
        </tr>
        <tr>
          <td>2</td>
          <td><img src='path_to_image2'></td>
          <td>Product 2</td>
          <td>Description for Product 2</td>
          <td>Category B</td>
          <td>24.99€</td>
          <td><button class="btn btn-primary">Edit</button></td>
          <td><button class="btn btn-danger">Delete</button></td>
        </tr>
  
      </tbody>
    </table>
  </div>
@endsection
   