@extends('layouts.app')

@section('content')
  <div>
      <h2>All Admins</h2>
      <table class="table">
          <thead>
              <tr>
                  <th class="text-center">ID</th>
                  <th class="text-center">Username</th>
                  <th class="text-center">Email</th>
                  <th class="text-center">Phone Number</th>
                  <th class="text-center" >Actions</th> <!-- New column for actions -->
              </tr>
          </thead>
          <tbody>
              <tr>
                  <td>1</td>
                  <td>John Doe</td>
                  <td>johndoe@example.com</td>
                  <td>123-456-7890</td>
                  <td>
                      <button class="btn btn-danger">Remove</button>
                  </td>
              </tr>
              <tr>
                  <td>2</td>
                  <td>Jane Smith</td>
                  <td>janesmith@example.com</td>
                  <td>987-654-3210</td>
                  <td>
                      <button class="btn btn-danger">Remove</button>
                  </td>
              </tr>
    
          </tbody>
      </table>
  </div>
@endsection