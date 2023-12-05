@for($i = 0; $i < count($purchases); $i++)
    <div class="order-history mb-3">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h6 class="mb-1">Order nº: {{$purchases[$i]-> id}}</h6>
                <p class="mb-1">Date: {{$purchases[$i]->purchase_date}}</p>
                <p class="mb-1">Value: {{$purchases[$i]->price}}</p>
                <p class="mb-1">Status: Delivered</p>
                <p class="mb-0">Items:
                    <span class="item-info">
                        @php
                            $cart = $carts_purchase[$i];
                            $items_cart = $cart->products()->get();
                            for($j = 0; $j < count($items_cart); $j++) {
                                echo "<span class='item-quantity'>{$items_cart[$j]->name} ({$items_cart[$j]->pivot->quantity})</span>, ";
                            }
                        @endphp
                    </span>
                </p>
            </div>
            <div class="d-flex flex-column align-items-end">
                <button class="btn btn-outline-dark btn-sm mb-2">Details</button>
                <button class="btn btn-outline-danger btn-sm" style="white-space: nowrap;">Cancel </button>
            </div>
        </div>
    </div>
    <hr class="my-3">
@endfor
