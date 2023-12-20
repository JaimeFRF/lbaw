@if(count($orders) === 0)
    <p class="text-center text-muted">No current pending orders.</p>
@else
    @for($i = 0; $i < count($orders); $i++)
        <div class="order-history mb-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="mb-1">Order nº: {{$orders[$i]-> id}}</h6>
                    <p class="mb-1">Date: {{$orders[$i]->purchase_date}}</p>
                    <p class="mb-1">Value: {{$orders[$i]->price}}</p>
                    <p class="mb-1">Status: {{$orders[$i]->purchase_status}}</p>
                    <p class="mb-0">Items:
                        <span class="item-info">
                            @php
                                $cart = $carts_orders[$i];
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
                <div class="d-flex flex-column align-items-end">
                    <button class="btn btn-outline-dark btn-sm" id="detailsButton" 
                        data-order-number="{{$orders[$i]-> id}}" 
                        data-date="{{$orders[$i]->purchase_date}}" 
                        data-value="{{$orders[$i]->price}}" 
                        data-status="{{$orders[$i]->purchase_status}}"
                        data-address="{{$locations_orders[$i]->address}}"
                        data-city="{{$locations_orders[$i]->city}}"
                        data-country="{{$locations_orders[$i]->country}}"
                        data-postalCode="{{$locations_orders[$i]->postal_code}}"
                        data-items="{{json_encode($items)}}">
                        Details
                    </button>
                    <button class="btn btn-outline-danger btn-sm" id="cancel-button" data-review-id="{{ $orders[$i]->id }}" style="white-space: nowrap;">Cancel </button>
                </div>
            </div>
        </div>
        @if ($i < count($orders) - 1)
            <hr class="my-3">
        @endif

    @endfor
@endif
