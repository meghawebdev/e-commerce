<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use Livewire\Attributes\Title;
use Livewire\Component;

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

    public function placeOrder()
    {
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
