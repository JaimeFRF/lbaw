
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
    <div class="quantity-control">
        <button class="quantity-btn" onclick="updateQuantity({{ $item->id }}, -1)">-</button>
        <span class="quantity-text">{{ $item->pivot->quantity }}</span>
        <button class="quantity-btn" onclick="updateQuantity({{ $item->id }}, 1)">+</button>
    </div>
</td>
  <td>{{$item->price}}€</td>
</tr>
