<?php

namespace App\Modules\Product\Controllers;

use App\Modules\Product\Models\Product;
use App\Modules\Product\Queries\ProductDatatable;
use App\Modules\Product\Repositories\ProductRepository;
use App\Modules\Product\Requests\ProductRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

class ProductController extends AppBaseController
{
    protected $productRepository;
    protected $productDatatable;

    // Inject the repository using the constructor
    public function __construct(ProductRepository $productRepo, ProductDatatable $productDatatable)
    {
        $this->productRepository = $productRepo;
        $this->productDatatable = $productDatatable;
    }
    public function index(Request $request)
    {
        $query = Product::query();

        // Search by keyword (product name)
        if ($request->has('search') && $request->search != '') {
            $query->where('product_name', 'like', '%' . $request->search . '%');
        }

        // Filter by product type
        if ($request->has('product_type') && $request->product_type != '') {
            $query->where('product_type', $request->product_type);
        }

        // Filter by price
        if ($request->has('max_price') && is_numeric($request->max_price)) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter by price category
        if ($request->has('price_category') && $request->price_category != '') {
            $query->where('price_category', $request->price_category);
        }

        // Sort by price
        if ($request->has('price_sort') && $request->price_sort != '') {
            if ($request->price_sort == 'low_to_high') {
                $query->orderBy('price', 'asc');
            } elseif ($request->price_sort == 'high_to_low') {
                $query->orderBy('price', 'desc');
            }
        } else {
            // Default sorting if no price sort is specified
            $query->orderBy('created_at', 'desc'); // Or whatever your default sort is
        }

        $products = $query->paginate(10)->withQueryString(); // withQueryString preserves the filter parameters in pagination links

        // Get unique product types for filter dropdown
        $productTypes = [1 => 'Type 1', 2 => 'Type 2', 3 => 'Type 3'];

        return view('Product::index', compact('products', 'productTypes'));
    }

    public function create()
    {
        return view('Product::create');
    }

    public function store(ProductRequest $request)
    {
        $product = $this->productRepository->store($request->all());
        if (!$product) {
            return redirect()->route('products.create')->with('error', 'Something went wrong!!! [PCS-01]!');
        }
        return redirect()->route('products.index')->with('success', 'Product created successfully!');
    }
    public function edit(Product $product)
    {
        return view('Product::edit', compact('product'));
    }

    public function destroy(Product $product)
    {
        $product = $this->productRepository->delete($product);
        if (!$product) {
            return redirect()->route('products.index')->with('error', 'Something went wrong!!! [PCD-02]!');
        }
        return redirect()->route('products.index')->with('success', 'Product deleted successfully!');
    }
    public function view(Product $product)
    {
        return view('Product::show', compact(['product']));
    }
    public function update(ProductRequest $request, Product $product)
    {
        $this->productRepository->update($product, $request->all());
        return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    }
}
