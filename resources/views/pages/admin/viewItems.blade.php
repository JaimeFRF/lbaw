@extends('layouts.adminApp')

@section('css')
<link href="{{ url('css/admin.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endsection

@section('content')
  @include('partials.common.breadcrumbs', ['breadcrumbs' => $breadcrumbs , 'current' => $current ])

  <div>
    <h2>Items List</h2>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th class="text-center">ID</th>
          <th class="text-center">Product Name</th>
          {{-- <th class="text-center">Product Description</th> --}}
          <th class="text-center">Category Name</th>
          <th class="text-center">Size</th>
          <th class="text-center">Unit Price</th>
          <th class="text-center">Stock</th>
          <th class="text-center" colspan="2">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($items as $item)
          <tr>
            <td class="text-center">{{$item->id}}</td>
            {{-- <td><img src='path_to_image1'></td> --}}
            <td class="text-center">{{$item->name}}</td>
            {{-- <td class="text-center">{{$item->description}}</td> --}}
            <td class="text-center">Categoria</td>
            <td class="text-center">{{$item->size}}</td>            
            <td class="text-center">{{$item->price}}€</td>
            <td class="text-center">{{$item->stock}}</td>

            <td class="text-center"><button id="edit" class="btn btn-warning">Edit</button></td>
            <td class="text-center"><button id="delete" class="btn btn-danger">Delete</button></td>
          </tr>
          @endforeach
      </tbody>
    </table>
  </div>
@endsection
   