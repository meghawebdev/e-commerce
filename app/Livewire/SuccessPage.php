<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Success Page | Shopify')]

class SuccessPage extends Component
{
    public function render()
    {
        $latest_order = Order::with('address')->where('user_id', auth()->user()->id)->latest()->first();

        return view('livewire.success-page', [
            'latest_order' => $latest_order,
        ]);
    }
}
