<?php

namespace App\Livewire\Auth;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Login Page | Shopify')]

class Login extends Component
{
    public $email;

    public $password;

    public function save()
    {
        // dd($this->email, $this->password);
        $this->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required',
        ]);
        if (! auth()->attempt(['email' => $this->email, 'password' => $this->password])) {
            session()->flash('error', 'Invalid credentials');

            return;
        }

        return redirect('/');

    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
