@for($i = 0; $i < count($purchases); $i++)
    <div class="order-history mb-3">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h6 class="mb-1">Order nº: {{$purchases[$i]-> id}}</h6>
                <p class="mb-1">Date: {{$purchases[$i]->purchase_date}}</p>
                <p class="mb-1">Value: {{$purchases[$i]->price}}</p>
                <p class="mb-1">Status: {{$purchases[$i]->purchase_status}}</p>
                <p class="mb-0">Items:
                <span class="item-info">
                    @php
                        $cart = $carts_purchases[$i];
                        $items_cart = $cart->products()->get();
                        $items = [];
                        for($j = 0; $j < count($items_cart); $j++) {
                            echo "<span class='item-quantity'>{$items_cart[$j]->name} ({$items_cart[$j]->pivot->quantity})</span>, ";
                            $items[] = [
                                'name' => $items_cart[$j]->name,
                                'quantity' => $items_cart[$j]->pivot->quantity
                            ];
                        }
                    @endphp
                </span>
                </p>
            </div>
            <button class="btn btn-outline-dark btn-sm" id="detailsButton" 
                data-order-number="{{$purchases[$i]-> id}}" 
                data-date="{{$purchases[$i]->purchase_date}}" 
                data-value="{{$purchases[$i]->price}}" 
                data-status="{{$purchases[$i]->purchase_status}}"
                data-address="{{$purchases_locations[$i]->address}}"
                data-city="{{$purchases_locations[$i]->city}}"
                data-country="{{$purchases_locations[$i]->country}}"
                data-postalCode="{{$purchases_locations[$i]->postal_code}}"
                data-items="{{json_encode($items)}}">
                Details
            </button>
        </div>
    </div>
    @if ($i < count($purchases) - 1)
        <hr class="my-3">
    @endif

@endfor