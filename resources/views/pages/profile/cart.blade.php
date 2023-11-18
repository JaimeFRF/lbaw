@extends('layouts.app')

@section('content')
    <section class="small-container cart-page">
      <table>
        <tr>
          <th>Product</th>
          <th>Quantity</th>
          <th>Subtotal</th>
        </tr>
        <tr>
          <td>
            <div class ="cart-info">
              <img src="image">
              <div>
                <p> Blue Jeans</p>
                <small>Price: 30.00€</small>
                <br>
                <a href = ""> Remove</a>
              </div>
            </div>
          </td>
          <td><input type= "number" value="1"</td>
          <td>30.00€</td>
        </tr>
      </table>
    </section>
@endsection