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
                            for($j = 0; $j < count($items_cart); $j++) {
                                echo "<span class='item-quantity'>{$items_cart[$j]->name} ({$items_cart[$j]->pivot->quantity})</span>, ";
                            }
                        @endphp
                    </span>
                </p>
            </div>
            <div class="d-flex flex-column align-items-end">
                <button class="btn btn-outline-dark btn-sm mb-2">Details</button>
                <button class="btn btn-outline-danger btn-sm" id="cancel-button" data-review-id="{{ $orders[$i]->id }}" style="white-space: nowrap;">Cancel </button>
            </div>
        </div>
    </div>
    <hr class="my-3">
@endfor
