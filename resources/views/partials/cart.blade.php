
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
  <td><input type= "number" min="0" value={{$item->pivot->quantity}}> </td>
  <td>{{$item->price}}€</td>
</tr>
