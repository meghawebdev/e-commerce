<?php

namespace App\Livewire;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Products Page | Shopify')]
class ProductsPage extends Component
{
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

        return view('livewire.products-page', [
            'products' => $products->paginate(9),
            'brands' => $brands,
            'categories' => $categories,
        ]);
    }
}
