
<tr> 
  <td>
    <div class ="cart-info">
      <img src= {{$item->picture}}>
      <div>
        <p id=name>{{$item->name}}</p>
        <small>Size: {{$item->size}}</small>
        <br>
        <a id="removeItem" href = "">Remove</a>
      </div>
    </div>
  </td>
  <td><input type= "number" value={{$item->pivot->quantity}}> </td>
  <td>{{$item->price}}€</td>
</tr>
