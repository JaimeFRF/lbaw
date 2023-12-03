@extends('layouts.adminApp')

@section('css')
<link href="{{ url('css/admin.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="d-flex align-items-center">
        <script src="{{ asset('js/admin-userspage.js') }}"></script>
        <h2 class="flex-grow-1 text-center">All Users</h2>
        <button type="button" class="btn btn-outline-dark me-5">Add User/Admin</button>
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
                  <th class="text-center"  colspan="4">Actions</th> <!-- New column for actions -->
              </tr>
          </thead>
          <tbody>
            @foreach ($users as $user)
                <tr>
                    <td class="text-center">{{$user->id}}</td>
                    <td class="text-center">{{$user->name}}</td>
                    <td class="text-center">{{$user->username}}</td>
                    <td class="text-center">{{$user->email}}</td>
                    <td class="text-center">{{$user->phone}}</td>
                    <td id="status" class="text-center">{{$user->is_banned === false ? "Active" : "Banned"}}</td>
                    <td class="text-center"><button id="ban" class="btn btn-danger">Ban</button></td>
                    <td class="text-center"><button id="upgrade" class="btn btn-primary">Upgrade to Admin</button></td>
                    <td class="text-center"><button id="edit" class="btn btn-warning">Edit</button></td>
                    <td class="text-center"><button id="delete" class="btn btn-primary">Delete</button></td>
                </tr>
            @endforeach
          </tbody>
      </table>
  </div>
@endsection
