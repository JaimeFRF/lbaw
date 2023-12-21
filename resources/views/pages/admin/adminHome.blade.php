@extends('layouts.adminApp')

@section('title', 'Antiquus Backoffice')


@section('css')
<link href="{{ url('css/admin.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endsection

@section('content')
  <section id="main-content" class="container allContent-section py-4">
    <div class="row">
      <div class="col-sm-4">
        <div class="card text-center p-3">
          <i class="fa fa-users fa-3x mb-2"></i>
          <h4>Total Users</h4>
          <h5> {{ $totalUsers }}</h5>
        </div>
      </div>
      <div class="col-sm-4">
        <div class="card text-center p-3">
          <i class="fa fa-th-large fa-3x mb-2"></i>
          <h4>Total Items</h4>
          <h5> {{ $totalItems }}</h5>
        </div>
      </div>
      <div class="col-sm-4">
        <div class="card text-center p-3">
          <i class="fa fa-th-list fa-3x mb-2"></i>
          <h4>Total Stock</h4>
          <h5>{{ $totalStock }}</h5>
        </div>
      </div>
    </div>       
  </section>
@endsection