<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Order Page')]

class OrdersPage extends Component
{
    use WithPagination;

    public function render()
    {
        // fetch all orders of currently logged in user
        $orders = Order::where('user_id', auth()->user()->id)->latest()->paginate(6);

        return view('livewire.orders-page', [
            'orders' => $orders,
        ]);
    }
}
