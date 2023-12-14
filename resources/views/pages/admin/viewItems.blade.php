@extends('layouts.adminApp')

@section('css')
<link href="{{ url('css/admin.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endsection

@section('content')
<div class="d-flex align-items-center">
    <h2 class="flex-grow-1 text-center">All Items</h2>
    <button type="button" class="btn btn-outline-dark me-5" data-bs-toggle="modal" data-bs-target="#addItemModal">Add Item</button>
</div>
<div>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th class="text-center">ID</th>
        <th class="text-center">Product Name</th>
        {{-- <th class="text-center">Product Description</th> --}}
        <th class="text-center">Category</th>
        <th class="text-center">Sub-category</th>
        <th class="text-center">Size</th>
        <th class="text-center">Unit Price</th>
        <th class="text-center">Stock</th>
        <th class="text-center" colspan="2">Actions</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($items as $item)
        <tr data-item-id={{$item->id}}>
          <td class="text-center">{{$item->id}}</td>
          {{-- <td><img src='path_to_image1'></td> --}}
          <td class="text-center">{{$item->name}}</td>
          {{-- <td class="text-center">{{$item->']deription}}</td> --}}
          <td class="text-center">{{$item->category}}</td>
          <td class="text-center">{{$item->type}}</td>
          <td class="text-center">{{$item->size}}</td>            
          <td class="text-center">{{$item->price}}€</td>
          <td class="text-center">{{$item->stock}}</td>
          <td class="text-center">
            <button class="btn btn-warning edit-btn">Edit</button>
          </td>
          <td class="text-center">
              <button id="delete" data-item-id={{$item->id}} class="btn btn-outline-danger btn-sm delete-item-btn">
                  <i class="fa fa-times"></i>
                  <span>Remove Stock</span>
              </button>
          </td>
        </tr>
        @endforeach
    </tbody>
  </table>
</div>
<div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="addItemModalLabel">Add Item</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="addItemForm"enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <small class="text-danger required-text">*</small>
                    <label for="productName" class="form-label">Product Name</label>
                    <input type="text" class="form-control" id="productName" name="name" placeholder="Enter product name" required>
                </div>
                
                <div class="mb-3">
                  <label for="description" class="form-label">Description</label>
                  <input type="text" class="form-control" id="description" name="description" placeholder="Enter a small description">
              </div>

                <div class="mb-3">
                    <small class="text-danger required-text">*</small>
                    <label for="category" class="form-label">Category</label>
                    <select class="form-select" id="category" name="category" aria-label="Category select" required>
                        <option value="">Select a category</option>
                        <option value="tshirt">Tshirt</option>
                        <option value="shirt">Shirt</option>
                        <option value="jacket">Jacket</option>
                        <option value="jeans">Jeans</option>
                        <option value="sneakers">Sneakers</option>                    
                    </select>
                </div>
                <div class="mb-3">
                  <small class="text-danger required-text">*</small>
                  <label for="subCategory" class="form-label">Sub-category</label>
                  <select class="form-select" id="subCategory" name="subCategory" aria-label="Sub-category select" disabled required>
                      <option value="">Select a sub-category</option>
                  </select>
              </div>
                <div class="mb-3">
                    <small class="text-danger required-text">*</small>
                    <label for="size" class="form-label">Size</label>
                    <input type="text" class="form-control" id="size" name="size" placeholder="Enter size" required>
                </div>
                <div class="mb-3">
                    <small class="text-danger required-text">*</small>
                    <label for="unitPrice" class="form-label">Unit Price(€)</label>
                    <input type="number" class="form-control" id="unitPrice" name="price" placeholder="Enter unit price" required>
                  </div>
                <div class="mb-3">
                    <small class="text-danger required-text">*</small>
                    <label for="stock" class="form-label">Stock</label>
                    <input type="number" class="form-control" id="stock" name="stock" placeholder="Enter stock quantity" required>
                </div>
                <div class="mb-3">
                  <label for="era" class="form-label">Era</label>
                  <input type="text" class="form-control" id="era" name="era" placeholder="Enter era">
              </div>
              <div class="mb-3">
                  <small class="text-danger required-text">*</small>
                  <label for="color" class="form-label">Color</label>
                  <input type="text" class="form-control" id="color" name="color" placeholder="Enter color" required>
              </div>
      
              <div class="mb-3">
                  <label for="fabric" class="form-label">Fabric</label>
                  <input type="text" class="form-control" id="fabric" name="fabric" placeholder="Enter fabric">
              </div>
      
              <div class="mb-3">
                  <label for="brand" class="form-label">Brand</label>
                  <input type="text" class="form-control" id="brand" name="brand" placeholder="Enter brand">
              </div>
                <div class="mb-3">
                  <label for="photos" class="form-label">Photos</label>
                  <input type="file" class="form-control" id="photos" name="photos[]" multiple>
                </div>
            </form>
        </div>
        
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" form="addItemForm" class="btn btn-primary">Add</button>
          </div>
      </div>
  </div>
</div>

<div class="modal fade" id="editItemModal" tabindex="-1" aria-labelledby="editItemModalLabel" aria-hidden="true">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="editItemModalLabel">Edit Item</h5>
              <button type="button" class="btn btn-secondary" id="manualCloseModalButton" aria-label="Close">
                  <i class="fas fa-times"></i>
              </button>
          </div>
          <div class="modal-body">
              <form id="editItemForm">
                  @csrf
                  <input type="hidden" id="editItemId" name="item_id">
                  <div class="mb-3">
                      <label for="editProductName" class="form-label">Product Name</label>
                      <input type="text" class="form-control" id="editProductName" name="name" placeholder="Enter product name">
                  </div>
                    <div class="mb-3">
                      <label for="editCategory" class="form-label">Category</label>
                      <select class="form-control" id="editCategory" name="category">
                          <option value="tshirt">T-Shirt</option>
                          <option value="shirt">Shirt</option>
                          <option value="jacket">Jacket</option>
                          <option value="jeans">Jeans</option>
                          <option value="sneakers">Sneakers</option>
                      </select>
                  </div>
                  <div class="mb-3">
                      <label for="editSubCategory" class="form-label">Sub-category</label>
                      <input type="text" class="form-control" id="editSubCategory" name="subCategory" placeholder="Enter sub-category">
                  </div>
                  <div class="mb-3">
                      <label for="editSize" class="form-label">Size</label>
                      <input type="text" class="form-control" id="editSize" name="size" placeholder="Enter size">
                  </div>
                  <div class="mb-3">
                      <label for="editUnitPrice" class="form-label">Unit Price(€)</label>
                      <input type="text" class="form-control" id="editUnitPrice" name="price" placeholder="Enter unit price">
                  </div>
                  <div class="mb-3">
                      <label for="editStock" class="form-label">Stock</label>
                      <input type="number" class="form-control" id="editStock" name="stock" placeholder="Enter stock quantity">
                  </div>
                  <div class="mb-3">
                    <label for="photos" class="form-label">Photos</label>
                    <input type="file" class="form-control" id="photos" name="photos[]" multiple>
                  </div>
              </form>
          </div>
          <div class="modal-footer">
            {{-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="closeModalButton">Close</button> --}}  
              <button form="editItemForm" class="btn btn-primary update-item-btn">Update Item</button>
          </div>
      </div>
  </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="{{ asset('js/admin-itemspage.js') }}"></script>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
@endsection
   