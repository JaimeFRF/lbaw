@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <label for="category">Category:</label>
                <select id="category" class="form-select mb-3">
                    <option value="all">All</option>
                    <option value="shirt">Shirts</option>
                    <option value="tshirt">T-Shirts</option>
                    <option value="jacket">Jackets</option>
                    <option value="jeans">Jeans</option>
                    <option value="snickers">Snickers</option>
                </select>

                <label for="size">Size:</label>
                <select id="size" class="form-select mb-3">
                    <option value="null">---</option>
                    <option value="XS">XS</option>
                    <option value="S">S</option>
                    <option value="M">M</option>
                    <option value="L">L</option>
                    <option value="XL">XL</option>
                </select>

                <label for="color">Color:</label>
                <select id="color" class="form-select mb-3">
                    <option value="null">---</option>
                    <option value="black">Black</option>
                    <option value="white">White</option>
                    <option value="red">Red</option>
                    <option value="green">Green</option>
                    <option value="blue">Blue</option>
                    <option value="yellow">Yellow</option>
                    <option value="brown">Brown</option>
                </select>

                <label for="price">Price:</label>
                <select id="color" class="form-select mb-3">
                    <option value="null">---</option>
                    <option value="to15">0 - 14,99 €</option>
                    <option value="to30">15 - 29,99 €</option>
                    <option value="to50">30 - 49,99 €</option>
                    <option value="to75">50 - 74,99 €</option>
                    <option value="to100">75 - 99,99 €</option>
                    <option value="100plus">100+ €</option>
                </select>
            </div>

            <!-- Product Section (Right) -->
            <div class="col-md-9">
                <li class="w-100 mx auto">
                    <form class="d-flex" method="POST" action="#">
                        @csrf
                        <input class="form-control me-2" type="search" name="search" placeholder="Search for a specific product...">
                        <button type="button" class="btn btn-outline-secondary button-margin" onclick="applyFilters()">Apply Filters</button>
                        <button type="button" class="btn btn-outline-secondary button-margin" onclick="clearFilters()">Clear Filters</button>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle button-margin" type="button" id="sortDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Sort By
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="sortDropdown">
                                <a class="dropdown-item" href="#" onclick="sort('name')">Alphabetically, A-Z</a>
                                <a class="dropdown-item" href="#" onclick="sort('price')">Alphabetically, Z-A</a>
                                <a class="dropdown-item" href="#" onclick="sort('price')">Price, low to high</a>
                                <a class="dropdown-item" href="#" onclick="sort('price')">Price, high to low</a>
                            </div>
                        </div>

                    </form>
                </li>

                <div class="row">
                    <!-- Product 1 -->
                    <div class="col-md-4 mb-4">
                        <a href="#" class="card-link">
                            <div class="card">
                                <img src="{{ asset('images/default-product-image.png') }}" class="card-img-top" alt="Product 1">
                                <div class="card-body">
                                    <h5 class="card-title">Product 1</h5>
                                    <p class="card-text">Description of Product 1.</p>
                                    <p class="card-text">XX,XX€</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Product 2 -->
                    <div class="col-md-4 mb-4">
                        <a href="#" class="card-link">
                            <div class="card">
                                <img src="{{ asset('images/default-product-image.png') }}" class="card-img-top" alt="Product 2">
                                <div class="card-body">
                                    <h5 class="card-title">Product 2</h5>
                                    <p class="card-text">Description of Product 2.</p>
                                    <p class="card-text">XX,XX€</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-4 mb-4">
                        <a href="#" class="card-link">
                            <div class="card">
                                <img src="{{ asset('images/default-product-image.png') }}" class="card-img-top" alt="Product 2">
                                <div class="card-body">
                                    <h5 class="card-title">Product 3</h5>
                                    <p class="card-text">Description of Product 3.</p>
                                    <p class="card-text">XX,XX€</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-4 mb-4">
                        <a href="#" class="card-link">
                            <div class="card">
                                <img src="{{ asset('images/default-product-image.png') }}" class="card-img-top" alt="Product 2">
                                <div class="card-body">
                                    <h5 class="card-title">Product 4</h5>
                                    <p class="card-text">Description of Product 4.</p>
                                    <p class="card-text">XX,XX€</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    
                </div>
            </div>
        </div>
    </div>
@endsection
