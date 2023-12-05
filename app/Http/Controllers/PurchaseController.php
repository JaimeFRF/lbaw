<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use App\Models\Item;
use App\Models\User;
use App\Models\Cart;
use App\Models\Purchase;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session;


class PurchaseController extends Controller
{
    public function checkout(Request $request)
    {
        $user = User::find(Auth::id());
        $items = json_decode($request->input('items'), true);
        
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
        
        $purchase_price = 0;
        $line_items = [];
        foreach($items as $item){
            $purchase_price += $item['price'] * $item['pivot']['quantity'];
            $line_items[] = [
                'price_data' => [
                    'currency' => env('CASHIER_CURRENCY'),
                    'product_data' => [
                        'name' => $item['name'],
                    ],
                    'unit_amount' => $item['price'] * 100, 
                ],
                'quantity' => $item['pivot']['quantity'],
            ];
        }
    
        $checkout_session = Session::create([
            'ui_mode' => 'hosted',
            'locale' => 'en',
            'billing_address_collection' => 'required',
            'line_items' => $line_items,
            'mode' => 'payment',
            'success_url' => route('checkout.success', [], true)."?session_id={CHECKOUT_SESSION_ID}",
            'cancel_url' => route('checkout.cancel', [], true),
        ]);

        $entry = new Purchase;
        $entry->id_user = $user->id;
        $entry->price = $purchase_price;
        $entry->id_cart = $item['pivot']['id_cart'];
        $entry->purchase_date = date('Y-m-d'); 
        $entry->delivery_date = date('Y-m-d', strtotime('+3 days')); 
        $entry->purchase_status = 'Processing'; 
        $entry->payment_method = 'Transfer'; 
        $entry->id_location = 1; 
        $entry->save();
        
        return redirect($checkout_session->url);
    }

    public function success(Request $request)
    {
        //Falta mudar o status da purchase
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
        $sessionId = $request->get('session_id');

        try{
            $session = \Stripe\Checkout\Session::retrieve($sessionId); // Corrected here
            if(!$session){
                throw new NotFoundHttpException;
            }
        } catch (\Exception $e) {
            Log::info($e->getMessage());

            throw new NotFoundHttpException();
        }

        
        return redirect()->route('home');

    }

    public function cancel(Request $request)
    {
        //Falta corrigir o bug de dar um cart novo ao user
        return redirect()->route('cart');
    }

}