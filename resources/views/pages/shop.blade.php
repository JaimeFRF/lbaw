@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-3">

                <form class = "row card-body d-flex align-items-center justify-content-between" method = "POST" action="{{route('filter')}}" id = "filter">    
                    @csrf
                    <label for="category">Category:</label>
                    <select id="category" name="category"class="form-select mb-3">
                        <option value="all">All</option>
                        <option value="shirt">Shirts</option>
                        <option value="tshirt">T-Shirts</option>
                        <option value="jacket">Jackets</option>
                        <option value="jeans">Jeans</option>
                        <option value="sneaker">Snickers</option>
                    </select>

                    <!-- <label for="size">Size:</label>
                    <select id="size" class="form-select mb-3">
                        <option value="null">---</option>
                        <option value="XS">XS</option>
                        <option value="S">S</option>
                        <option value="M">M</option>
                        <option value="L">L</option>
                        <option value="XL">XL</option>
                    </select> -->

                    <label for="color">Color:</label>
                    <select id="color" name="color" class="form-select mb-3">
                        <option value="None" >---</option>
                        <option value="Black">Black</option>
                        <option value="White">White</option>
                        <option value="Red">Red</option>
                        <option value="Green">Green</option>
                        <option value="Blue">Blue</option>
                        <option value="Yellow">Yellow</option>
                        <option value="Brown">Brown</option>
                        <option value="Multi">Multi</option>
                    </select>


                    <label for="orderBy">Order by:</label>
                    <select id="orderBy" name="orderBy"class="form-select mb-3">
                        <option value="None">---</option>
                        <option value="price-high-low">Price: high to low</option>
                        <option value="price-low-high">Price: low to high</option>
                        <option value="rating-low-high">Rating: low to high</option>
                        <option value="rating-high-low">Rating: high to low</option>
                    </select>
                    
                    <label for="inStock">In Stock:</label>
                    <input type="checkbox" id="inStock" name="inStock" value="1" checked>

                    <label for="price">Price:</label>
                    <select id="price" name="price" class="form-select mb-3">
                        <option value="null">---</option>
                        <option value="0to15">0 - 14,99 €</option>
                        <option value="15to30">15 - 29,99 €</option>
                        <option value="30to50">30 - 49,99 €</option>
                        <option value="50to75">50 - 74,99 €</option>
                        <option value="75to100">75 - 99,99 €</option>
                        <option value="100plus">100+ €</option>
                    </select>


                    <div class="col-md d-flex justify-content-center">
                        <button id="filterButton" class = "btn btn-success">
                        Filter
                        </button>
                    </div>


                    <!--
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle button-margin" type="button" id="sortDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Sort By
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="sortDropdown"> -->
                            <!-- <a class="dropdown-item">Alphabetically, A-Z</a>
                            <a class="dropdown-item">Alphabetically, Z-A</a> -->
                            <!-- <a class="dropdown-item">Price, low to high</a>
                            <a class="dropdown-item">Price, high to low</a>
                        </div>
                    </div> -->

                    <!-- <button type="button" class="btn btn-outline-secondary button-margin">Apply Filters</button>
                    <button type="button" class="btn btn-outline-secondary button-margin">Clear Filters</button> -->

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
