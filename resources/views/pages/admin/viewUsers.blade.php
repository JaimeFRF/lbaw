@extends('layouts.adminApp')

@section('css')
<link href="{{ url('css/admin.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="d-flex align-items-center">
    <h2 class="flex-grow-1 text-center">All Users</h2>
    <button type="button" class="btn btn-outline-dark me-5" data-bs-toggle="modal" data-bs-target="#addUserModal">Add User/Admin</button>
</div>
<div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th class="text-center">ID</th>
                <th class="text-center">Name</th>
                <th class="text-center">Username</th>
                <th class="text-center">Email</th>
                <th class="text-center">Phone Number</th>
                <th class="text-center">Status</th>
                <th class="text-center"  colspan="4">Actions</th> 
            </tr>
        </thead>
        <tbody>
          @foreach ($users as $user)
              <tr data-user-id={{$user->id}}>
                  <td class="text-center">{{$user->id}}</td>
                  <td class="text-center">{{$user->name}}</td>
                  <td class="text-center">{{$user->username}}</td>
                  <td class="text-center">{{$user->email}}</td>
                  <td class="text-center">{{$user->phone}}</td>
                  <td id="status" class="text-center">{{$user->is_banned === false ? "Active" : "Banned"}}</td>
                  <td class="text-center">
                      <button id="ban" data-user-id={{$user->id}} class="btn btn-danger">
                          <span>Ban</span>
                      </button>
                  </td>
                  <td class="text-center"><button id="upgrade" class="btn btn-primary">Promote to Admin</button></td>
                  <td class="text-center"><button class="btn btn-warning edit-btn">Edit</button></td>
                  <td class="text-center">
                      <button id="delete" data-user-id={{$user->id}} class="btn btn-outline-danger btn-sm">
                          <i class="fa fa-times"></i>
                          <span>Delete</span>
                      </button>
                  </td>
              </tr>
          @endforeach
        </tbody>
    </table>
</div>

<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                <button type="button" class="btn btn-secondary" id="manualCloseModalButton" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="editUserForm">
                    @csrf
                    <input type="hidden" id="editUserId" name="user_id">
                    <div class="mb-3">
                        <label for="editName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="editName" name="name" placeholder="Enter your name">
                    </div>
                    <div class="mb-3">
                        <label for="editUsername" class="form-label">Username</label>
                        <input type="text" class="form-control" id="editUsername" name="username" placeholder="Enter your new username">
                    </div>
                    <div class="mb-3">
                        <label for="editEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="editEmail" name="email">
                    </div>
                    <div class="mb-3">
                        <label for="editPhone" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="editPhone" name="phone" placeholder="Enter phone number">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                {{-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="closeModalButton">Close</button> --}}
                    <button form="editUserForm" class="btn btn-primary update-user-btn">Update User</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Add User/Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addUserForm">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter user's name">
                    </div>
                    <div class="mb-3">
                        <small class="text-danger required-text">*</small>
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Enter username" required>
                    </div>
                    <div class="mb-3">
                        <small class="text-danger required-text">*</small>
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="phone" name="phone" placeholder="Enter phone number">
                    </div>
                    <div class="mb-3">
                        <small class="text-danger required-text">*</small>
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="addUserForm" class="btn btn-primary">Add</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="{{ asset('js/admin-userspage.js') }}"></script>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

@endsection
