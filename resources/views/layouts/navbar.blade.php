@section('css')
    <link href="{{ url('css/contextual_help.css') }}" rel="stylesheet">
@endsection

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
            <button class="btn btn-secondary dropdown-toggle" id="categoriesDropdown" onclick="toggleDropdown()">
              Categories
            </button>
            <nav class="dropdown-menu" id="dropdownMenu" style="display: none;">
              <form method="GET" action="{{route('shopFilter', ['filter' => 'shirt'])}}">
                  @csrf
                  <button type="submit" class="dropdown-item">Shirt</button>
              </form>
              <form method="GET" action="{{route('shopFilter', ['filter' => 'tshirt'])}}">
                  @csrf
                  <button type="submit" class="dropdown-item">T-Shirt</button>
              </form>
              <form method="GET" action="{{route('shopFilter', ['filter' => 'jacket'])}}">
                  @csrf
                  <button type="submit" class="dropdown-item">Jacket</button>
              </form>
              <form method="GET" action="{{route('shopFilter', ['filter' => 'jeans'])}}">
                  @csrf
                  <button type="submit" class="dropdown-item">Jeans</button>
              </form>
              <form method="GET" action="{{route('shopFilter', ['filter' => 'sneakers'])}}">
                  @csrf
                  <button type="submit" class="dropdown-item">sneakers</button>
              </form>
            </nav>
          </div>
        </li>

        <li class="w-100">
          <form class="d-flex" method = "GET" action = "{{route('search')}}">
            @csrf
            <input class="form-control me-2" type="search" name="search" placeholder="Search for a specific product..." onmouseover="getContextualHelp('search', 'search-help').show()" onmouseout="getContextualHelp('search', 'search-help').hide()">
          </form>
          <div id="search-help" class="help-message">Search for a product name or description</div>
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
              $notifications = Auth::user()->notifications;
              $notificationsCount = count($notifications);
          @endphp

          <a title="Notifications" class="m-3 me-4" id="notificationsDropdown" data-bs-toggle="dropdown">   
          <i class="fa fa-bell text-white fs-5 bar-icon"></i>
          <span class="text-white" id="notificationsCount">({{ $notificationsCount }})</span>
        </a>

          <div  id="notificationsContainer" class="dropdown-menu notifications dropdown-menu-end" aria-labelledby="notificationsDropdown" >
              @foreach($notifications as $notification)
                  @include('partials.notification',['notification' => $notification])
              @endforeach
          </div>
          
          <a title="Cart" class="m-3 me-4" href="{{route('cart')}}">
            <i class="fa fa-shopping-cart text-white fs-5 bar-icon"></i>
            <span id="ItemCartNumber" class="text-white"></span>
          </a> 

          <a title="Profile" class="m-3 me-4" href="{{route('profile')}}">
            <i class="fa fa-user text-white fs-5 bar-icon"></i>
          </a>

          <a title="Logout" class="m-3 me-4" href="{{route('logout')}}">
            <i class="fa fa-sign-out-alt fs-5 text-white bar-icon"></i>
          </a> 

        @else
          <a title="Cart" class="m-4 me-4" href="{{route('cart')}}">
            <i class="fa fa-shopping-cart text-white fs-5 bar-icon"></i>
            <span id="ItemCartNumber" class="text-white"></span>
          </a> 

          <a title="Login" class="btn btn-primary m-3" href="{{route('login')}}"> 
            <i class="fa fa-sign-in-alt"></i>
            <span>Login</span>
          </a>
        @endif
      </div>
    </div>
  </div>
</nav>

<script>
  const pusher = new Pusher(pusherAppKey, {
    cluster: pusherCluster,
    encrypted: true
  });

  const channel = pusher.subscribe('lbaw2366');
  channel.bind('new-notification', function(notification) {
    console.log('New notification received!');
    updateNavbarUI(notification);
  });

  function updateNavbarUI(notificationData) {
    const notificationsContainer = document.getElementById('notificationsContainer');
    const notificationsCountElement = document.getElementById('notificationsCount');

    const currentCount = parseInt(notificationsCountElement.innerText);
    const newCount = currentCount + 1;
    notificationsCountElement.innerText = newCount;

    const newNotificationElement = document.createElement('a');
    newNotificationElement.classList.add('dropdown-item', 'notifi-item');
    newNotificationElement.href = '#'; 

    if (notificationData.notification_type === 'SALE') {
      newNotificationElement.innerHTML = `
        <img src="img/notification_icon.png" alt="img">
        <div class="text">
          <h4>${notificationData.item.name}</h4>
          <p>${notificationData.description}</p>
        </div>
      `;
    } else if (notificationData.notification_type === 'RESTOCK') {
      newNotificationElement.innerHTML = `
        <img src="img/notification_icon.png" alt="img">
        <div class="text">
          <h4>${notificationData.item.name}</h4>
          <p>${notificationData.description}</p>
        </div>
      `;
    } else if (notificationData.notification_type === 'ORDER_UPDATE') {
      newNotificationElement.innerHTML = `
        <img src="img/notification_icon.png" alt="img">
        <div class="text">
          <h4>Purchase ${notificationData.id_purchase} State Changed</h4>
          <p>${notificationData.description}</p>
        </div>
      `;
    }

    notificationsContainer.appendChild(newNotificationElement);

  }
</script>

<script src="{{asset('js/contextual-help.js')}}"defer></script>
