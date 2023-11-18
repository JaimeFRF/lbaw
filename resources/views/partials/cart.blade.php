<article class="cart" data-id="{{ $cart->id }}">
    <header>
        <h2><a href="/carts/{{ $cart->id }}">{{ $cart->name }}</a></h2>
        <a href="#" class="delete">&#10761;</a>
    </header>
    <ul>
        @each('partials.item', $cart->items()->orderBy('id')->get(), 'item')
    </ul>
    <form class="new_item">
        <input type="text" name="description" placeholder="new item">
    </form>
</article>