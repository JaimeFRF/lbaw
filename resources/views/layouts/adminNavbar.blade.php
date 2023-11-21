<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
  <div class="container-fluid jusityf-content-between">
    <a class="navbar-brand" href="{{route('home')}}"> <span class="fs-2 ms-4">Antiquus</span> </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

      <!-- User features -->
      <div class="navbar-nav text-white d-flex flex-row">
        <a title="Stock" class="m-3 me-4" href="{{ route('stock') }}">
            <i class="fa fa-th-list text-white fs-5 bar-icon"></i>
            <span class="text-white">Stock</span>
        </a>     
    
        <a title="Items" class="m-3 me-4" href="{{ route('items') }}">
            <i class="fa fa-th text-white fs-5 bar-icon"></i>
            <span class="text-white">Items</span>
        </a> 
    
        <a title="Users and Admins" class="m-3 me-4" href="{{ route('view-users-admins') }}">
            <i class="fa fa-user text-white fs-5 bar-icon"></i>
            <span class="text-white">Users and Admins</span>
        </a>
    
        <a title="Logout" class="m-3 me-4" href="{{ route('logout') }}">
            <i class="fa fa-sign-out-alt fs-5 text-white bar-icon"></i>
        </a> 
    </div>
    
    </div>
  </div>
</nav>