<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class ExampleTest extends TestCase
{

    public function test_the_application_returns_a_successful_response()
    {
        $existingProduct = Item::find(4);
        $existingProduct2 = Item::find(2);
        
        // Create a cart
        $cart = Cart::create();
    
        // Add the product to the cart
        $cart->products()->attach($existingProduct->id);
        $cart->products()->attach($existingProduct2->id);
    
        // Assert that the product is in the cart with the correct quantity
        $this->assertEquals(1, $cart->products->find($existingProduct->id)->pivot->quantity);
        $this->assertEquals(1, $cart->products->find($existingProduct2->id)->pivot->quantity);
    }
}
