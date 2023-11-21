@extends('layouts.adminApp')

@section('content')
  <section id="main-content" class="container allContent-section py-4">
    <div class="row">
      <div class="col-sm-4">
        <div class="card bg-primary text-white text-center p-3">
          <i class="fa fa-users fa-3x mb-2"></i>
          <h4>Total Users</h4>
          <h5>10</h5>
        </div>
      </div>
      <div class="col-sm-4">
        <div class="card bg-primary text-white text-center p-3">
          <i class="fa fa-th-large fa-3x mb-2"></i>
          <h4>Total Items</h4>
          <h5>10</h5>
        </div>
      </div>
      <div class="col-sm-4">
        <div class="card bg-primary text-white text-center p-3">
          <i class="fa fa-th-list fa-3x mb-2"></i>
          <h4>Total Stock</h4>
          <h5>10</h5>
        </div>
      </div>
    </div>       
  </section>
@endsection