<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
  <div class="container-fluid jusityf-content-between">
    <a class="navbar-brand" href="{{route('home')}}"> <span class="fs-2 ms-4">Antiquus</span> </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse  " id="navbarSupportedContent">
      <script src="{{asset('js/navbar_script.js')}}"defer></script>
      <ul class="navbar-nav ms-auto mb-lg-0 align-items-center w-30  me-4">
        <li>
          <div class="dropdown m-2">
            <button class="btn btn-secondary dropdown-toggle" id="categoriesDropdown" data-toggle="dropdown">
              Categories
            </button>
            <nav class="dropdown-menu">
              <form method="POST" action="{{route('shopFilter', ['filter' => 'shirt'])}}">
                  @csrf
                  <button type="submit" class="dropdown-item">Shirt</button>
              </form>
              <form method="POST" action="{{route('shopFilter', ['filter' => 'tshirt'])}}">
                  @csrf
                  <button type="submit" class="dropdown-item">T-Shirt</button>
              </form>
              <form method="POST" action="{{route('shopFilter', ['filter' => 'jacket'])}}">
                  @csrf
                  <button type="submit" class="dropdown-item">Jacket</button>
              </form>
              <form method="POST" action="{{route('shopFilter', ['filter' => 'jeans'])}}">
                  @csrf
                  <button type="submit" class="dropdown-item">Jeans</button>
              </form>
              <form method="POST" action="{{route('shopFilter', ['filter' => 'sneaker'])}}">
                  @csrf
                  <button type="submit" class="dropdown-item">Sneaker</button>
              </form>
            </nav>
          </div>
        </li>
        <li class="w-100">
          <!-- Search bar -->
          <form class="d-flex" method = "POST" action = "{{route('search')}}">
            @csrf
            <input class="form-control me-2" type="search" name="search" placeholder="Search for a specific product...">
          </form>
        </li>
      </ul>

      <!-- User features -->
      <div class="navbar-nav d-flex flex-row">
        @if (Auth::check())    
          @if(!Auth::user()->isadmin)
            <a title="Wishlist" class="m-3 me-4" href="">
              <i class="fa fa-heart text-white fs-5 bar-icon"></i>
            </a>     
          @endif

          @php
            $n = DB::table('notification')->where('id_user', '=', Auth::id())->count();
          @endphp

          <a title="Notifications" class="m-3 me-4" href="">   
            @if($n > 0){{$n}}@endif  
            <i class="fa fa-bell text-white fs-5 bar-icon"></i>
          </a> 
          
          @if(!Auth::user()->isadmin)
            <a title="Cart" class="m-3 me-4" href="{{route('cart')}}">
              <i class="fa fa-shopping-cart text-white fs-5 bar-icon"></i>
              <span id="ItemCartNumber" class="text-white"></span>
            </a> 
          @endif

          <a title="Profile" class="m-3 me-4" href="{{route('profile')}}">
            <i class="fa fa-user text-white fs-5 bar-icon"></i>
          </a>

          <a title="Logout" class="m-3 me-4" href="{{route('logout')}}">
            <i class="fa fa-sign-out-alt fs-5 text-white bar-icon"></i>
          </a> 

        @else
          <a title="Login" class="btn btn-primary m-3" href="{{route('login')}}"> 
            <i class="fa fa-sign-in-alt"></i>
            <span>Login</span>
          </a>
        @endif
      </div>
    </div>
  </div>
</nav>