<a href="{{ url('/api/item/' . $item->id) }}" style="text-decoration: none; color: inherit;">
    <div class="product">
        @if($item->images()->first())
            <img src="{{ asset($item->images()->first()->filepath) }}">
        @else
            <img src="{{ asset('images/default-product-image.png') }}">
        @endif

        <h4>{{ $item->name }}</h4>
        <span>${{ $item->price }}</span>
    </div>
</a>