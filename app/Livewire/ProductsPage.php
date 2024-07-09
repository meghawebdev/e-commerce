<?php

namespace App\Livewire;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Products Page | Shopify')]
class ProductsPage extends Component
{
    use WithPagination;

    public function render()
    {
        $products = Product::where('is_active', true)->paginate();
        $brands = Brand::where('is_active', true)->get();
        $categories = Category::where('is_active', true)->get();

        return view('livewire.products-page', [
            'products' => $products,
            'brands' => $brands,
            'categories' => $categories,
        ]);
    }
}
