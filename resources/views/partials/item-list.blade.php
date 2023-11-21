@foreach($items as $item)
    <div>
        @include('partials.item', ['item' => $item])
    </div>
@endforeach
