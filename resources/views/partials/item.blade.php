<a href="{{ url('/api/item/' . $item->id) }}" style="text-decoration: none; color: inherit;">
    <div class="product">
        @if($item->images()->first())
            <img src="{{ asset($item->images()->first()->filepath) }}">
        @else
            <!-- Handle the case where there are no images for the item -->
            <img src="{{ asset('images/default-product-image.png') }}">
        @endif

        <h4>{{ $item->name }}</h4>
        <p>{{ $item->description }}</p>
        <span>${{ $item->price }}</span>
    </div>
</a>