
<tr> 
  <td>
    <div class ="cart-info">
      <img src= {{$item->picture}}>
      <div>
        <p id=name value={{$item->name}}></p>
        <small>Price: {{$item->price}}€</small>
        <br>
        <a class = "remove" href = ""> Remove</a>
      </div>
    </div>
  </td>
  <td><input type= "number" value={{$item->pivot->quantity}}> </td>
  <td>{{$item->price}}€</td>
</tr>
