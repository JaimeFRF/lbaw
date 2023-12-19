@section('css')
    <link href="{{ url('css/contextual_help.css') }}" rel="stylesheet">
@endsection
@php
use App\Models\Wishlist;
use App\Models\Item;
if(Auth::check()){
  $wishlist = Wishlist::where('id_user', Auth::user()->id)->get();
  $items_wishlist = [];
  foreach($wishlist as $item){
    $items_wishlist[] = Item::find($item->id_item);
  }
}
@endphp
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
                  <button type="submit" class="dropdown-item">Sneakers</button>
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
            <a title="Wishlist" class="m-3 me-4" id="wishlistDropdown" data-bs-toggle="dropdown">
              <i class="fa fa-heart text-white fs-5 bar-icon"></i>
            </a>
            <div  id="wishlistContainer" class="dropdown-menu notifications dropdown-menu-end" aria-labelledby="wishlistDropdown" >
                @include('partials.profile.wishlist', ['items_wishlist' => $items_wishlist])

            </div>     
          @endif

          @php
              $notifications = Auth::user()->notifications;
              $notificationsCount = count($notifications);
          @endphp

          <a title="Notifications" class="m-3 me-4" id="notificationsDropdown" data-bs-toggle="dropdown">   
            <i class="fa fa-bell text-white fs-5 bar-icon"></i>
            <span class="text-white" id="notificationsCount" value = "{{$notificationsCount}}">({{ $notificationsCount }})</span>
          </a>
          <div id="notificationsContainer" class="dropdown-menu notifications dropdown-menu-end" aria-labelledby="notificationsDropdown" 
            style="{{ $notifications->isEmpty() ? 'margin-right: 170px; text-align: center;' : '' }}">
            @forelse($notifications as $notification)
                @include('partials.notification',['notification' => $notification])
            @empty
                <p>No notifications</p>
            @endforelse
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
  var pusherAppKey = "{{ env('PUSHER_APP_KEY') }}";
  var pusherCluster = "{{ env('PUSHER_APP_CLUSTER') }}";
  var userId = "{{ Auth::id() }}";
  console.log(userId);
  const pusher = new Pusher(pusherAppKey, {
    cluster: pusherCluster,
    encrypted: true
  });

  const channel = pusher.subscribe('lbaw2366-' + userId);
  channel.bind('new-notification', function(notification) {
    console.log('New notification received!');
    updateNavbarUI(notification);
  });
  
  function updateNavbarUI(notificationData) {
    const notificationsContainer = document.getElementById('notificationsContainer');
    const notificationsCountElement = document.getElementById('notificationsCount');
    var currentCount = document.getElementById('notificationsCount').getAttribute('value');
    currentCount = parseInt(currentCount); 
    const newCount = currentCount + 1;
    notificationsCountElement.innerText = `(${newCount})`;

    const notificationType = notificationData.notification.notification_type;
    console.log(notificationData);

    const newNotificationElement = document.createElement('a');
    newNotificationElement.classList.add('dropdown-item', 'notifi-item');
    newNotificationElement.href = '#'; 

    if (notificationType === 'SALE') {
      newNotificationElement.innerHTML = `
        <div class="text">
          <h4>${notificationData.item.name}</h4>
          <p>${notificationData.notification.description}</p>
        </div>
      `;
    } else if (notificationType === 'RESTOCK') {
      newNotificationElement.innerHTML = `
        <div class="text">
          <h4>${notificationData.item.name}</h4>
          <p>${notificationData.notification.description}</p>
        </div>
      `;
    } else if (notificationType === 'ORDER_UPDATE') {
      newNotificationElement.innerHTML = `
        <img  src="{{ asset('images/shop.jpg') }}"alt="img">
        <div class="text">
          <h4>Purchase (${notificationData.notification.id_purchase}) State Changed</h4>
          <p>${notificationData.notification.description }</p>
        </div>
      `;
    } else if (notificationType === 'PRICE_CHANGE') {
      newNotificationElement.innerHTML = `
        <div class="text">
          <h4>${notificationData.item.name}</h4>
          <p>${notificationData.notification.description }</p>
        </div>
      `;
    }

    notificationsContainer.appendChild(newNotificationElement);

  }
</script>
<script src="{{asset('js/contextual-help.js')}}"defer></script>

