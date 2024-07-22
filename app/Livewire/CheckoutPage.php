<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Mail\OrderPlaced;
use App\Models\Address;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Title;
use Livewire\Component;
use Stripe\Checkout\Session;
use Stripe\Stripe;

#[Title('Checkout Page | Shopify')]

class CheckoutPage extends Component
{
    public $first_name;

    public $last_name;

    public $phone_number;

    public $street_address;

    public $state;

    public $city;

    public $pin_code;

    public $payment_method;

    public function mount()
    {
        // if there is no cart-items and if we go in checkout page then it redirect to products page
        $cart_items = CartManagement::getCartItemsFromCookie();
        if (count($cart_items) == 0) {
            return redirect('/products');
        }
    }

    public function placeOrder()
    {
        // dd($this->payment_method);
        $this->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'phone_number' => 'required',
            'street_address' => 'required',
            'state' => 'required',
            'city' => 'required',
            'pin_code' => 'required',
            'payment_method' => 'required',
        ]);

        $cart_items = CartManagement::getCartItemsFromCookie();

        $line_items = [];

        foreach ($cart_items as $item) {
            $line_items[] = [
                'price_data' => [
                    'currency' => 'inr',
                    'unit_amount' => $item['unit_amount'] * 100,
                    'product_data' => [
                        'name' => $item['name'],
                    ],
                ],
                'quantity' => $item['quantity'],
            ];
        }
        $order = new Order();
        $order->user_id = auth()->user()->id;
        $order->grand_total = CartManagement::calculateGrandTotal($cart_items);
        $order->payment_method = $this->payment_method;
        $order->payment_status = 'pending';
        $order->status = 'new';
        $order->currency = 'inr';
        $order->shipping_amount = 0;
        $order->shipping_method = 'none';
        $order->notes = 'Order placed by '.auth()->user()->name;

        $address = new Address();
        $address->first_name = $this->first_name;
        $address->last_name = $this->last_name;
        $address->phone_number = $this->phone_number;
        $address->street_address = $this->street_address;
        $address->city = $this->city;
        $address->state = $this->state;
        $address->pin_code = $this->pin_code;

        $redirect_url = '';

        if ($this->payment_method == 'stripe') {
            Stripe::setApiKey(env('STRIPE_SECRET'));
            $sessionCheckout = Session::create([
                'payment_method_types' => ['card'],
                'customer_email' => auth()->user()->email,
                'line_items' => $line_items,
                'mode' => 'payment',
                'success_url' => route('success').'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('cancel'),
            ]);
            $redirect_url = $sessionCheckout->url;
        } else {
            $redirect_url = route('success');
        }
        $order->save();
        $address->order_id = $order->id;
        $address->save();
        $order->orderItems()->createMany($cart_items);
        // once the order is processed then i will clear all the cart items
        CartManagement::clearCartItemsFromCookie();
        // befor redirecting to success page, we will send a mail
        Mail::to(auth()->user()->email)->send(new OrderPlaced($order));

        return redirect($redirect_url);

    }

    public function render()
    {
        $cart_items = CartManagement::getCartItemsFromCookie();
        $grand_total = CartManagement::calculateGrandTotal($cart_items);

        return view('livewire.checkout-page', [
            'cart_items' => $cart_items,
            'grand_total' => $grand_total,
        ]);
    }
}
