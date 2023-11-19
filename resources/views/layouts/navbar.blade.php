<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
  <div class="container-fluid jusityf-content-between">
    <a class="navbar-brand" href="{{route('home')}}"> <span>Antiquus</span> </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse align-items-center" id="navbarSupportedContent">
      <!-- Categories Dropdown -->
      <ul class="navbar-nav me-auto mb-lg-0 align-items-center w-75">
        <li>
          <div class="dropdown m-2">
      
            <button class="btn btn-secondary dropdown-toggle" id = "categoriesDropdown" data-toggle="dropdown">
              Categories
            </button>
            <nav class="dropdown-menu">
              <a class="dropdown-item" href="#">Shirt</a>
              <a class="dropdown-item" href="#">T-Shirt</a>
              <a class="dropdown-item" href="#">Jacket</a>
              <a class="dropdown-item" href="#">Jeans</a>
              <a class="dropdown-item" href="#">Sneaker</a>
            </nav>
          </div>
        </li>
        <li class = "w-100">
          <!-- Search bar -->
          <form class="d-flex" method = "POST" action = "#">
            @csrf
            
            <input class="form-control me-2" type="search" name = "search" placeholder="Search">
          </form>
        </li>
      </ul>  
      
      <!-- User features -->
      <div class = "navbar-nav d-flex flex-row">
        @if (Auth::check())    
            @if(!Auth::user()->isadmin)
              <a title="Wishlist" class="btn btn-primary m-2" href = "">
                <i class="fa fa-heart"></i>
              </a>
            @endif
            
     
            @php
              $n = DB::table('notification')->where('id_user', '=', Auth::id())->count();
            @endphp

            <a title="Notifications" class="btn btn-primary m-2" href = "">   
              @if($n > 0){{$n}}@endif  
                <i class="fa fa-bell"></i>
            </a> 
            
            @if(!Auth::user()->isadmin)
              <a title="Cart" class="btn btn-primary m-2"  href = "{{route('cart')}}">
                <i class="fa fa-shopping-cart"></i>
              </a> 
            @endif

            <a title="Profile" class="btn btn-primary m-2" href = "{{route('profile')}}">
              <i class="fa fa-user"></i>
              <span>{{Auth::user()->username}}</span>
            </a>
            

            <a title="Logout" class="btn btn-primary m-2" href = "{{route('logout')}}">
              <i class="fa fa-sign-out-alt"></i>
            </a> 

        @else
          <a title="Login" class="btn btn-primary m-2" href = "{{route('login')}}"> 
            <i class="fa fa-sign-in-alt"></i>
            <span>Login</span>
          </a>
        @endif
      </div>
    </div>
  </div>
</nav>