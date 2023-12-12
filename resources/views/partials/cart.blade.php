
<tr id="product-row"> 
  <td>
    <div class ="cart-info">
      @if (Auth::check())
        <img src="{{ $item->picture }}">
        <div>
            <h6 id="name">{{ $item->name }}</h6>
            {{-- <small>Size: {{$item->size}}</small> --}}
            <br>
            <a class="remove" href=""> Remove</a>
        </div>
      @else
        <img src="{{ $item['picture'] }}">
        <div>
            <h6 id="name">{{ $item['name'] }}</h6>
            {{-- <small>Size: {{$item['size']}}</small> --}}
            <br>
            <a class="remove" href=""> Remove</a>
        </div>
      @endif
    </div>
  </td>
  <td>
    <div class="cart-item" data-item-id="{{ Auth::check() ? $item->id : $item['id'] }}" data-item-price="{{ Auth::check() ? $item->price : $item['price'] }}">
      <button class="quantity-btn decrement" aria-label="Decrease quantity">-</button>
      <span id="quantity-item-{{ Auth::check() ? $item->id : $item['id'] }}" class="quantity-text">{{ Auth::check() ? $item->pivot->quantity ?? $item->quantity : $item['quantity']}}</span>
      <button class="quantity-btn increment" aria-label="Increase quantity">+</button>
    </div>
  </td>
  <td>{{ Auth::check() ? $item->price : $item['price'] }}€</td>
</tr>

