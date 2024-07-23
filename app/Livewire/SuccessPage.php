<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Stripe\Checkout\Session;
use Stripe\Stripe;

#[Title('Success Page | Shopify')]

class SuccessPage extends Component
{
    // we will get the session id in this property
    #[Url]
    public $session_id;

    public function render()
    {
        // fetch latest order of currently logged in user
        $latest_order = Order::with('address')->where('user_id', auth()->user()->id)->latest()->first();
        // if there is any session_id in url then we will get all the session checkout details
        if ($this->session_id) {
            Stripe::setApiKey(env('STRIPE_SECRET'));
            $session_info = Session::retrieve($this->session_id);
            // dd($session_info);

            if ($session_info->payment_status != 'paid') {
                $latest_order->payment_status = 'failed';
                $latest_order->save();

                return redirect('cancel');
            } elseif ($session_info->payment_status === 'paid') {
                $latest_order->payment_status = 'paid';
                $latest_order->save();
            }
        }

        return view('livewire.success-page', [
            'latest_order' => $latest_order,
        ]);
    }
}
