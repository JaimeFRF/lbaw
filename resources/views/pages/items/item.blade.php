@extends('layouts.app')

@section('content')
    <section class="container-fluid mt-2">
        <script src="{{asset('js/item-page_script.js')}}" defer></script>
        <div class="row m-5 mt-1">
            <div class="col-md product-info">
                <p class= "mt-1">Home / CATEGORIA DE ITEM</p>

                <h2 class= "mt-2" id="productName">{{$item->name}}</h2>

                <small class="text-muted">Article: 01234</small>

                <h4 class="my-4 price">
                    <span>Preço</span> {{$item->price}} €
                </h4>

                <div class="mt-3">
                    <label for="size" class="text-muted">Size:</label>
                    <select class="form-select" id="size" name="size">
                        <option value="XS">XS</option>
                        <option selected>S</option>
                        <option value="M">M</option>
                        <option value="L">L</option>
                        <option value="XL">XL</option>
                    </select>
                </div>

                <div class="mt-3  accordion">
                    <div class=" accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                <strong>Description</strong>
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne">
                            <div class="accordion-body">
                                {{$item->description}}
                            </div>
                        </div>
                    </div>
                
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                <strong>Material</strong>
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo">
                            <div class="accordion-body">
                                {{$item->fabric}}
                            </div>
                        </div>
                    </div>
                    <div class=" accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                <strong>Stock</strong>
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree">
                            <div class="accordion-body">
                                <?php if($item->stock > 0): ?>
                                    In Stock
                                <?php else: ?>
                                    Not In Stock
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md m-1">
                <div class="d-flex flex-column align-items-center">
                    <div id="carouselExampleIndicators" class="carousel slide carousel-no-zoom" data-ride="carousel" style="width: 90%; height: 90%; margin: auto;">
                            <div class="carousel-inner">
                                @foreach($item->images()->get() as $image)
                                    <div class="carousel-item {{$loop->first ? 'active' : ''}}">
                                        <img src="{{ asset($image->filepath) }}" class="d-block carImg">
                                    </div>
                                @endforeach
                            </div>
                            <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="sr-only">Previous</span>
                            </a>
                            <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="sr-only">Next</span>
                            </a>
                        </div>

                    <div class="d-flex justify-content-between mt-3">
                        <script src="{{asset('js/item-page_script.js')}}" defer></script>
                        <form method="POST" action="{{ url('/users/wishlist/product/'.$item->id) }}">
                            @csrf
                            @method('PUT')
                            <button class="btn btn-outline-danger me-2" type="submit">
                                <i class="fa fa-heart"></i>
                                <span>Add to wishlist</span>
                            </button>
                        </form>
                        <form onclick="addItemToCart({{$item->id}})">
                            @csrf
                            <button class="btn btn-outline-primary" type="button" id="addToCart"> 
                                <i class="fa fa-cart-plus"></i>
                                <span>Add to Cart</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection