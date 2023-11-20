
<tr> 
  <td>
    <div class ="cart-info">
      <img src= {{$item->picture}}>
      <div>
        <h6  id=name> {{$item->name}}</h5>
        <small>Size: {{$item->size}}</small>
        <br>
        <a class = "remove" href = ""> Remove</a>
      </div>
    </div>
  </td>
  <td>
    <div class="cart-item" data-item-id="{{ $item->id }}" data-item-price="{{ $item->price }}">
      <button class="quantity-btn decrement" aria-label="Decrease quantity">-</button>
      <span id="quantity-item-{{ $item->id }}" class="quantity-text">{{ $item->pivot->quantity }}</span>
      <button class="quantity-btn increment" aria-label="Increase quantity">+</button>
  </div>
  </td>
  <td>{{$item->price}}€</td>
</tr>
