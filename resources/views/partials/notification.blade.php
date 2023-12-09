<a class="dropdown-item notifi-item" href="#">
   @if($notification->notification_type === 'SALE')
    @if($notification->item && $notification->item->images->isNotEmpty() && $notification->item->images->first()->filepath)
        <img src="{{ asset($notification->item->images->first()->filepath) }}">
    @else
        <img src="{{ asset('images/default-product-image.png') }}">
    @endif
   <div class="text">
      <h4>{{$notification->item->name}}</h4>
      <p>This item is now on sale for ${{ $notification->item->price }}</p>
    </div>
 
    @elseif($notification->notification_type === 'RESTOCK')
      @if($notification->item && $notification->item->images->isNotEmpty() && $notification->item->images->first()->filepath)
          <img src="{{ asset($notification->item->images->first()->filepath) }}">
      @else
          <img src="{{ asset('images/default-product-image.png') }}">
      @endif
    <div class="text">
      <h4>{{$notification->item->name}}</h4>
      <p>The item "{{ $notification->item->name }}" is in <strong>stock</strong></p>
    </div>

    @elseif($notification->notification_type === 'ORDER_UPDATE')
    <img src="img/notification_icon.png" alt="img">
    <div class="text">
      <h4>Purchase{{ $notification->id_purchase}} State Changed</h4>
      <p>The state of your purchase has been updated to <strong>{{ $notification->purchase->purchase_status }}</strong></p>
    </div>
    @endif
</a>