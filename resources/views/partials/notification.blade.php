<a class="dropdown-item notifi-item" id="notification-{{ $notification->id }}" href="#">
    <button class="btn btn-outline-danger btn-sm" type="button" style="max-height: 30px; position: absolute; left:90%" onclick="removeNotification({{ $notification->id }})"><i class="fa fa-times"></i></button>  
    
    @if($notification->notification_type === 'SALE')
    <img  src="{{ asset('images/item.png') }}"alt="img">
        <div class="text">
      <h4>{{$notification->item->name}}</h4>
      <p>{{ $notification->description}}</p>
    </div>

    @elseif($notification->notification_type === 'RESTOCK')
    <img  src="{{ asset('images/item.png') }}"alt="img">

    <div class="text">
      <h4>{{$notification->item->name}}</h4>
      <p>{{ $notification->description}}</p>
    </div>

    @elseif ($notification->notification_type == 'PRICE_CHANGE')
    <img  src="{{ asset('images/item.png') }}"alt="img">

      <div class="text">
        <h4>{{$notification->item->name}}</h4>
        <p>{{ $notification->description }}</p>
    </div>

    @elseif($notification->notification_type === 'ORDER_UPDATE')
    <img  src="{{ asset('images/shop.jpg') }}"alt="img">
    <div class="text">
      <h4>Purchase ({{ $notification->id_purchase}}) State Changed</h4>
      <p>{{ $notification->description }}</p>
    </div>
    @endif

</a>