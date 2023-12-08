<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use App\Models\Item;
use App\Models\User;
use App\Models\Location;
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

        $customer = \Stripe\Customer::create([
            'email' => $user->email,
        ]);
        $checkout_session = Session::create([
            'customer' => $customer->id,
            'ui_mode' => 'hosted',
            'locale' => 'en',
            'shipping_address_collection' => [
                'allowed_countries' => ['AT', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR', 
                'DE', 'GR', 'HU', 'IE', 'IT', 'LV', 'LT', 'LU', 'MT', 'NL', 
                'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE', 'GB', 'IS', 'NO', 
                'LI', 'CH', 'ME', 'MK', 'AL', 'RS', 'BA', 'XK', 'MD', 'AM', 
                'BY', 'GE', 'AZ', 'RU', 'US', 'CA'],          
            ],

            'line_items' => $line_items,
            'mode' => 'payment',
            'success_url' => route('checkout.success', [], true)."?session_id={CHECKOUT_SESSION_ID}&cart_id=".$item['pivot']['id_cart']."&purchase_price=".$purchase_price,
            'cancel_url' => route('checkout.cancel', [], true),
        ]);

        
        return redirect($checkout_session->url);
    }

    public function success(Request $request)
    {
        //Falta mudar o status da purchase
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
        $sessionId = $request->get('session_id');
        $cartId = $request->get('cart_id');
        $purchasePrice = $request->get('purchase_price');


        try{
            $session = \Stripe\Checkout\Session::retrieve($sessionId); // Corrected here
            if(!$session){
                throw new NotFoundHttpException;
            }
        } catch (\Exception $e) {
            Log::info($e->getMessage());

            throw new NotFoundHttpException();
        }

        $customerEmail = $session->customer_details->email;
        $cartId = $request->get('cart_id');


        $shippingAddress = $session->shipping_details;
        $city = $shippingAddress->address->city;
        $country = $shippingAddress->address->country;
        $postal_code = $shippingAddress->address->postal_code;
        $mergedAddress = ($shippingAddress->address->line1 ?? "") . " " . ($shippingAddress->address->line2 ?? "");
        $mergedAddress = trim($mergedAddress);

        $entry = new Location;
        $entry->city = $city;
        $entry->country = $country;
        $entry->postal_code = $postal_code;
        $entry->address = $mergedAddress;
        $entry->save();

        $purchase = new Purchase;
        $purchase->id_cart = $cartId;
        $purchase->id_user = Auth::id();
        $purchase->id_location = $entry->id;
        $purchase->price = $purchasePrice;
        $purchase->purchase_date = date('Y-m-d'); 
        $purchase->delivery_date = date('Y-m-d', strtotime('+3 days')); 
        $purchase->purchase_status = 'Paid'; 
        $purchase->payment_method = 'Transfer'; 
        $purchase->save();


        
        return redirect()->route('home');

    }

    public function cancel(Request $request)
    {
        return redirect()->route('cart');
    }

    public function cancelPurchase(Request $request, $id)
    {

        $purchase = Purchase::find($id);
        $purchase->delete();
    
        return response()->json(['success' => true]);
    }


}