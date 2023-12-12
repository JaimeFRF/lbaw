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
                        for($j = 0; $j < count($items_cart); $j++) {
                            echo "<span class='item-quantity'>{$items_cart[$j]->name} ({$items_cart[$j]->pivot->quantity})</span>, ";
                        }
                    @endphp

                </span>
                </p>
            </div>
            <button class="btn btn-outline-dark btn-sm">Details</button>
        </div>
    </div>
    @if ($i < count($purchases) - 1)
        <hr class="my-3">
    @endif

@endfor