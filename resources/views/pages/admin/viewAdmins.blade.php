@extends('layouts.adminApp')

@section('css')
<link href="{{ url('css/admin.css') }}" rel="stylesheet">
@endsection

@section('content')
@include('partials.common.breadcrumbs', ['breadcrumbs' => $breadcrumbs , 'current' => $current ])

<div class="d-flex align-items-center">
    <h2 class="flex-grow-1 text-center">Admins</h2>
    <button type="button" class="btn btn-outline-dark me-5" data-bs-toggle="modal" data-bs-target="#addAdminModal">Add Admin</button>
</div>
<div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th class="text-center">ID</th>
                <th class="text-center">Username</th>
                <th class="text-center">Email</th>
                <th class="text-center">Phone Number</th>
                <th class="text-center" colspan="2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($admins as $admin)
                <tr data-user-id={{ $admin->id }}>
                    <td class="text-center">{{ $admin->id }}</td>
                    <td class="text-center">{{ $admin->username }}</td>
                    <td class="text-center">{{ $admin->email }}</td>
                    <td class="text-center">{{ $admin->phone }}</td>
                    <td class="text-center"><button class="btn btn-warning edit-btn">Edit</button></td>
                    <td class="text-center">
                        <button id="delete" data-user-id={{ $admin->id }} class="btn btn-outline-danger btn-sm">
                            <i class="fa fa-times"></i>
                            <span>Delete</span>
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="modal fade" id="editAdminModal" tabindex="-1" aria-labelledby="editAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAdminModalLabel">Edit Admin</h5>
                <button type="button" class="btn btn-secondary" id="manualCloseModalButton" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="editAdminForm">
                    @csrf
                    <input type="hidden" id="editAdminId" name="admin_id">
                    <div class="mb-3">
                        <label for="editUsername" class="form-label">Username</label>
                        <input type="text" class="form-control" id="editAdminUsername" name="username" placeholder="Enter admin's new username">
                    </div>
                    <div class="mb-3">
                        <label for="editEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="editAdminEmail" name="email">
                    </div>
                    <div class="mb-3">
                        <label for="editPhone" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="editAdminPhone" name="phone" placeholder="Enter phone number">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button form="editAdminForm" class="btn btn-primary update-admin-btn">Update Admin</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addAdminModal" tabindex="-1" aria-labelledby="addAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAdminModalLabel">Add Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addAdminForm">
                    @csrf
                    <div class="mb-3">
                        <small class="text-danger required-text">*</small>
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="adminUsername" name="username" placeholder="Enter username" required>
                    </div>
                    <div class="mb-3">
                        <small class="text-danger required-text">*</small>
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="adminEmail" name="email" placeholder="Enter email" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="adminPhone" name="phone" placeholder="Enter phone number">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="addAdminForm" class="btn btn-primary">Add</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js" defer></script>
<script src="{{ asset('js/admin-adminspage.js') }}"></script>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
@endsection
