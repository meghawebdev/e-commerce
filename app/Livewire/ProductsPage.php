<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Livewire\Partials\Navbar;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Products Page | Shopify')]
class ProductsPage extends Component
{
    use LivewireAlert;
    use WithPagination;

    #[Url]
    public $selected_categories = [];

    #[Url]
    public $selected_brands = [];

    #[Url]
    public $featured;

    #[Url]
    public $on_sale;

    #[Url]
    public $price_range = 300000;

    #[Url]
    public $sort;

    public function addToCart($product_id)
    {
        // dd($product_id);
        $total_count = CartManagement::addItemToCart($product_id);
        $this->dispatch('update-cart-count', total_count: $total_count)->to(Navbar::class);

        $this->alert('success', 'Product added to the cart successfully !', [
            'position' => 'bottom-end',
            'timer' => 3000,
            'toast' => true,
            'timerProgressBar' => false,
        ]);
    }

    public function render()
    {
        $products = Product::where('is_active', true);
        $brands = Brand::where('is_active', true)->get();
        $categories = Category::where('is_active', true)->get();

        // if array is not empty
        if (! empty($this->selected_categories)) {
            $products->whereIn('category_id', $this->selected_categories);
        }

        if (! empty($this->selected_brands)) {
            $products->whereIn('brand_id', $this->selected_brands);
        }

        if (! empty($this->featured)) {
            $products->where('is_featured', $this->featured);
        }
        if (! empty($this->on_sale)) {
            $products->where('on_sale', $this->on_sale);
        }
        if (! empty($this->price_range)) {
            $products->whereBetween('price', [0, $this->price_range]);
        }

        if ($this->sort === 'latest') {
            $products->latest();
        }
        if ($this->sort === 'price') {
            $products->orderBy('price');
        }

        return view('livewire.products-page', [
            'products' => $products->paginate(9),
            'brands' => $brands,
            'categories' => $categories,
        ]);
    }
}
