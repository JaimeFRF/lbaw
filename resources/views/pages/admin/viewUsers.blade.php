@extends('layouts.app')

@section('content')
  <div>
      <h2>All Users</h2>
      <table class="table">
          <thead>
              <tr>
                  <th class="text-center">ID</th>
                  <th class="text-center">Username</th>
                  <th class="text-center">Username</th>
                  <th class="text-center">Email</th>
                  <th class="text-center">Phone Number</th>
                  <th class="text-center"  colspan="2">Actions</th> <!-- New column for actions -->
              </tr>
          </thead>
          <tbody>
              <tr>
                  <td>1</td>
                  <td>John Doe</td>
                  <td>John Doe Bta frit</td>
                  <td>johndoe@example.com</td>
                  <td>123-456-7890</td>
                  <td><button class="btn btn-danger">Ban</button></td>
                  <td><button class="btn btn-primary">Upgrade to Admin</button></td>
                  
              </tr>
              <tr>
                  <td>2</td>
                  <td>Jane Smith</td>
                  <td>Jane smith Btasw frito</td>
                  <td>janesmith@example.com</td>
                  <td>987-654-3210</td>
                  <td><button class="btn btn-danger">Ban</button></td>
                  <td><buttonclass="btn btn-primary">Upgrade to Admin</buttonclass=></td>
              </tr>
      
          </tbody>
      </table>
  </div>
@endsection
