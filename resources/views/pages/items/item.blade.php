@extends('layouts.app')

@section('content')
    <section class="container-fluid mt-2">
        <div class="row m-5 mt-1">
            <div class="col-md product-info">
                <h2 id="productName">NOME DA ROUPA</h2>
                <small class="text-muted">Article: 01234</small>

                <h4 class="my-4 price">
                    <span> PREÇO</span> €
                </h4>

                <div class="mt-3">
                    <label for="size" class="text-muted">Size:</label>
                    <select class="form-select" id="size" name="size">
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
                                DESCRIÇÃO
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
                                MATERIAIS
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md m-1">
                <div class="d-flex flex-column align-items-center">
                    <img src="https://s2.glbimg.com/OJaLvTztZb--1SM69gMp8ynZ7bI=/e.glbimg.com/og/ed/f/original/2019/04/10/roupa-branca.jpg"
                        class="m-1 w-100 sliderMainImage" alt="NOME DA ROUPA">

                    <div class="d-flex justify-content-between mt-3">
                        <button class="btn btn-outline-danger me-2" type="submit">
                            <i class="fa fa-heart"></i>
                            <span>Add to wishlist</span>
                        </button>
                        <button class="btn btn-outline-primary" type="submit">
                            <i class="fa fa-cart-plus"></i>
                            <span>Add to Cart</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection