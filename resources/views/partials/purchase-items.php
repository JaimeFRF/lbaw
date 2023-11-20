$prev = $purchase->cart()->get()->first()->id;
$cart = Cart::find($prev);
$items_cart = $cart->products()->get();
foreach($items_cart as $item) {
    <span class='item-quantity'>{$item->name} ({$item->quantity})</span>;
}