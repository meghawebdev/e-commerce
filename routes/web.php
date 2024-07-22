<?php

use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\CancelPage;
use App\Livewire\CartPage;
use App\Livewire\CategoriesPage;
use App\Livewire\CheckoutPage;
use App\Livewire\HomePage;
use App\Livewire\OrderDetailPage;
use App\Livewire\OrdersPage;
use App\Livewire\ProductDetailPage;
use App\Livewire\ProductsPage;
use App\Livewire\SuccessPage;
use Illuminate\Support\Facades\Route;

Route::get('/', HomePage::class);
Route::get('/categories', CategoriesPage::class);
Route::get('/products', ProductsPage::class);
Route::get('/products/{slug}', ProductDetailPage::class);
Route::get('/cart', CartPage::class);

Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class);
    Route::get('/forgot-password', ForgotPassword::class)->name('password.forgot');
    Route::get('/reset-password', ResetPassword::class)->name('password.reset');
});

Route::middleware('auth')->group(function () {
    // once a user is logged in or authenticated then only user can able to view the page
    Route::get('/logout', function () {
        auth()->logout();

        return redirect('/');
    });
    Route::get('/checkout', CheckoutPage::class);
    Route::get('/orders', OrdersPage::class);
    Route::get('/orders/{order}', OrderDetailPage::class)->name('orders.show');
    Route::get('/success', SuccessPage::class)->name('success');
    Route::get('/cancel', CancelPage::class)->name('cancel');
});
