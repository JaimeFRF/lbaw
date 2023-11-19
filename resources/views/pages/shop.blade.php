@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-3">

            <form class="row card-body d-flex align-items-center justify-content-between" method="POST" action="{{route('filter')}}" id="filter" >                    
                    @csrf

                    <label for="category">Category:</label>
                    <select id="category" name="category"class="form-select mb-3">
                        <option value="all" {{ session('category') == 'all' ? 'selected' : '' }}>All</option>
                        <option value="shirt" {{ session('category') == 'shirt' ? 'selected' : '' }}>Shirts</option>
                        <option value="tshirt" {{ session('category') == 'tshirt' ? 'selected' : '' }}>T-Shirts</option>
                        <option value="jacket" {{ session('category') == 'jacket' ? 'selected' : '' }}>Jackets</option>
                        <option value="jeans" {{ session('category') == 'jeans' ? 'selected' : '' }}>Jeans</option>
                        <option value="sneaker" {{ session('category') == 'sneaker' ? 'selected' : '' }}>Snickers</option>
                    </select>

                    <label for="color">Color:</label>
                    <select id="color" name="color" class="form-select mb-3">
                        <option value="None" {{ session('color') == 'None' ? 'selected' : '' }}>---</option>
                        <option value="Black" {{ session('color') == 'Black' ? 'selected' : '' }}>Black</option>
                        <option value="White" {{ session('color') == 'White' ? 'selected' : '' }}>White</option>
                        <option value="Red" {{ session('color') == 'Red' ? 'selected' : '' }}>Red</option>
                        <option value="Green" {{ session('color') == 'Green' ? 'selected' : '' }}>Green</option>
                        <option value="Blue" {{ session('color') == 'Blue' ? 'selected' : '' }}>Blue</option>
                        <option value="Yellow" {{ session('color') == 'Yellow' ? 'selected' : '' }}>Yellow</option>
                        <option value="Brown" {{ session('color') == 'Brown' ? 'selected' : '' }}>Brown</option>
                        <option value="Multi" {{ session('color') == 'Multi' ? 'selected' : '' }}>Multi</option>
                    </select>


                    <label for="orderBy">Order by:</label>
                    <select id="orderBy" name="orderBy"class="form-select mb-3">
                        <option value="None" {{ session('orderBy') == 'None' ? 'selected' : '' }}>---</option>
                        <option value="price-high-low" {{ session('orderBy') == 'price-high-low' ? 'selected' : '' }}>Price: high to low</option>
                        <option value="price-low-high" {{ session('orderBy') == 'price-low-high' ? 'selected' : '' }}>Price: low to high</option>
                        <option value="rating-low-high" {{ session('orderBy') == 'rating-low-high' ? 'selected' : '' }}>Rating: low to high</option>
                        <option value="rating-high-low" {{ session('orderBy') == 'rating-high-low' ? 'selected' : '' }}>Rating: high to low</option>
                    </select>
                    
                    <label for="inStock">In Stock:</label>
                    <input type="checkbox" id="inStock" name="inStock" value="1" {{ session('inStock') ? 'checked' : '' }}>

                    <label for="price">Price:</label>
                    <select id="price" name="price" class="form-select mb-3">
                        <option value="null" {{ session('price') == 'null' ? 'selected' : '' }}>---</option>
                        <option value="0to15" {{ session('price') == '0to15' ? 'selected' : '' }}>0 - 14,99 €</option>
                        <option value="15to30" {{ session('price') == '15to30' ? 'selected' : '' }}>15 - 29,99 €</option>
                        <option value="30to50" {{ session('price') == '30to50' ? 'selected' : '' }}>30 - 49,99 €</option>
                        <option value="50to75" {{ session('price') == '50to75' ? 'selected' : '' }}>50 - 74,99 €</option>
                        <option value="75to100" {{ session('price') == '75to100' ? 'selected' : '' }}>75 - 99,99 €</option>
                        <option value="100plus" {{ session('price') == '100plus' ? 'selected' : '' }}>100+ €</option>
                    </select>


                    <div class="col-md d-flex justify-content-center">
                        <button id="filterButton" class = "btn btn-success">
                        Filter
                        </button>
                    </div>

                </form>

                <form class="row card-body d-flex align-items-center justify-content-between" method="POST" action="{{route('clearFilters')}}" id="filter" >                    
                    @csrf

                    <button id="clearButton" class="btn btn-secondary">
                        Clear Filters
                    </button>                
                </form>

            </div>

            <!-- Product Section (Right) -->
            <div class="col-md-9">
                <li class="w-100 mx auto">
                    <form class="d-flex" method = "POST" action = "{{route('search')}}">
                        @csrf
                        <input class="form-control me-2" type="search" name="search" placeholder="Search for a specific product...">
                    </form>
                </li>

                <div class="row">

                    <div class="product-row" id="productRow">
                        @include('partials.item-list', ['items' => $items])
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
