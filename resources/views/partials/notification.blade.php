<a class="dropdown-item notifi-item" href="#">
    <button class="btn btn-outline-danger btn-sm" type="button" style="max-height: 30px; position: absolute; left:90%" onclick="removeNotification({{ $notification->id }})"><i class="fa fa-times"></i></button>  
    @if($notification->notification_type === 'SALE')
    @if($notification->item && $notification->item->images->isNotEmpty() && $notification->item->images->first()->filepath)
        <img src="{{ asset($notification->item->images->first()->filepath) }}">
    @else
        <img src="{{ asset('images/default-product-image.png') }}">
    @endif
    <div class="text">
      <h4>{{$notification->item->name}}</h4>
      <p>{{ $notification->description}}</p>
    </div>

    @elseif($notification->notification_type === 'RESTOCK')
      @if($notification->item && $notification->item->images->isNotEmpty() && $notification->item->images->first()->filepath)
          <img src="{{ asset($notification->item->images->first()->filepath) }}">
      @else
          <img src="{{ asset('images/default-product-image.png') }}">
      @endif
    <div class="text">
      <h4>{{$notification->item->name}}</h4>
      <p>{{ $notification->description}}</p>
    </div>

    @elseif ($notification->notification_type == 'PRICE_CHANGE')
      @if($notification->item && $notification->item->images->isNotEmpty() && $notification->item->images->first()->filepath)
          <img src="{{ asset($notification->item->images->first()->filepath) }}">
      @else
          <img src="{{ asset('images/default-product-image.png') }}">
      @endif
      <div class="text">
        <h4>{{$notification->item->name}}</h4>
        <p>{{ $notification->description }}</p>
    </div>
    @endif

    @elseif($notification->notification_type === 'ORDER_UPDATE')
    <img   src="{{ asset('images/shop.png') }}" alt="img">
    <div class="text">
      <h4>Purchase ({{ $notification->id_purchase}}) State Changed</h4>
      <p>{{ $notification->description }}</p>
    </div>
    @endif
</a>