@extends('layouts.app')

@section('css')
    <link href="{{ url('css/item.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
@endsection

@section('content')
@include('partials.common.breadcrumbs', ['breadcrumbs' => $breadcrumbs , 'current' => $current ])

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <body data-item-id="{{$item->id}}">
    <script src="{{ asset('js/item-review.js') }}" defer></script>

    <section class="container-fluid mt-2">
        <script src="{{asset('js/item-page_script.js')}}" defer></script>
        <div class="row m-5 mt-1">
            <div class="col-md product-info">

                <h2 class= "mt-2" id="productName">{{$item->name}}</h2>

                <h4 class="my-4 price"> {{$item->price}} €</h4>

                <h5 class="my-4">
                    <span class="size-label">Size:</span>
                    <span class="size-value">{{$size}}</span>
                </h5>                

                <h5 class="my-4 size">
                    @if($item->reviews()->count() > 0)
                    <span id="star-rating"></span> 
                    <span id="numeric-rating">{{ number_format($item->rating, 2) }}/5</span>
                    @else
                        No reviews on this item yet
                    @endif
                </h5>

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
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                <strong>Era</strong>
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree">
                            <div class="accordion-body">
                                {{$item->era}}
                            </div>
                        </div>
                    </div>
                    <div class=" accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                <strong>Stock</strong>
                            </button>
                        </h2>
                        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour">
                            <div class="accordion-body">
                                <?php if($item->stock > 0): ?>
                                    In Stock
                                <?php else: ?>
                                    Not In Stock
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    @if(Auth::check() && $userHasNotPurchasedItem)
                        <div class="review-container">
                            <form id="reviewForm" class="d-flex flex-column align-items-start">
                                <label for="reviewText" class="mb-2">Add a Review</label>
                                <div class="d-flex mb-3">
                                    <div class="form-group me-3" style="padding-right: 10%;">
                                        <textarea class="form-control transparent-textarea" id="reviewText" rows="5" style="width: 600px; height: 50px; resize: none;"></textarea>
                                    </div>
                                    <div class="rating">
                                        @php $userRating = isset($review) ? $review->rating : 0; @endphp
                                        @for ($i = 5; $i >= 1; $i--)
                                            <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" @if ($userRating == $i) checked @endif>
                                            <label for="star{{ $i }}">&#9733;</label>
                                        @endfor
                                    </div>
                                </div>
                            </form>
                        </div>
                    @endif

                    <div class="reviews-section">
                        <h3>Reviews:</h3>
                        @foreach($itemReviews as $review)
                            <div class="review">
                                <hr></hr>
                                <div class="review-header">
                                    <div class="username">{{ $review->user->username }}</div>
                                    <div class="rating" data-rating="{{ $review->rating }}">
                                        @php $reviewRating = $review->rating; @endphp
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= $reviewRating)
                                                <span class="star">&#9733;</span>
                                            @endif
                                        @endfor
                                    </div>
                                    @if(Auth::check())
                                        @if(Auth::user()->id == $review->user->id)
                                            <button class="edit-button" data-review-id="{{ $review->id }}"><i class="fa fa-pencil"></i></button>
                                            <button class="delete-button" data-review-id="{{ $review->id }}"><i class="fa fa-trash"></i></button>
                                        @endif
                                    @endif
                                </div>
                                <p>{{ $review->description }}</p>
                                <hr></hr>

                            </div>
                        @endforeach
                    </div>

                </div>
            </div>

            <div class="col-md m-1">
                <div class="d-flex flex-column align-items-center">
                    <div id="carouselExampleIndicators" class="carousel slide carousel-no-zoom" data-ride="carousel" style="width: 90%; height: 90%; margin: auto;">
                            <div class="carousel-inner">
                            @if($item->images()->get()->isEmpty())
                                <div class="carousel-item active">
                                    <img src="{{ asset('images/default-product-image.png') }}" class="d-block carImg">
                                </div>
                            @else
                                @foreach($item->images()->get() as $image)
                                    <div class="carousel-item {{$loop->first ? 'active' : ''}}">
                                        <img src="{{ asset($image->filepath) }}" class="d-block carImg">
                                    </div>
                                @endforeach
                            @endif
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
                        <form onclick="addItemToCart({{$item->id}}, this.querySelector('button').getAttribute('data-stock'))">
                            @csrf
                            <button class="btn btn-outline-primary" type="button" id="addToCart" data-stock="{{ $item->stock }}" {{ $item->stock == 0 ? 'disabled' : '' }}> 
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


