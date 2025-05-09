@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-6">
                <h1>Products</h1>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('products.create') }}" class="btn btn-primary">
                    Add New Product
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-header">
                <h2 class="h5 mb-0">Filters</h2>
            </div>
            <div class="card-body">
                <form action="{{ route('products.index') }}" method="GET">
                    <div class="row">
                        <!-- Keyword Search -->
                        <div class="col-md-3 mb-3">
                            <label for="search" class="form-label">Search by Product Name</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Search products..." class="form-control">
                        </div>

                        <!-- Product Type Filter -->
                        <div class="col-md-3 mb-3">
                            <label for="product_type" class="form-label">Product Type</label>
                            <select name="product_type" id="product_type" class="form-select">
                                <option value="">All Types</option>
                                @foreach($productTypes as $type)
                                    <option value="{{ $type }}" {{ request('product_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Price Category Filter -->
                        <div class="col-md-3 mb-3">
                            <label for="price_category" class="form-label">Price Category</label>
                            <select name="price_category" id="price_category" class="form-select">
                                <option value="">All Categories</option>
                                <option value="affordable" {{ request('price_category') == 'affordable' ? 'selected' : '' }}>Affordable</option>
                                <option value="moderate" {{ request('price_category') == 'moderate' ? 'selected' : '' }}>Moderate</option>
                                <option value="premium" {{ request('price_category') == 'premium' ? 'selected' : '' }}>Premium</option>
                            </select>
                        </div>

                        <!-- Max Price Filter -->
                        <div class="col-md-3 mb-3">
                            <label for="max_price" class="form-label">Maximum Price</label>
                            <input type="number" name="max_price" id="max_price" value="{{ request('max_price') }}" placeholder="Max Price" class="form-control">
                        </div>

                        <!-- Price Sort Order -->
                        <div class="col-md-3 mb-3">
                            <label for="price_sort" class="form-label">Price Order</label>
                            <select name="price_sort" id="price_sort" class="form-select">
                                <option value="">Default</option>
                                <option value="low_to_high" {{ request('price_sort') == 'low_to_high' ? 'selected' : '' }}>Low to High</option>
                                <option value="high_to_low" {{ request('price_sort') == 'high_to_low' ? 'selected' : '' }}>High to Low</option>
                            </select>
                        </div>

                        <!-- Filter Button -->
                        <div class="col-md-3 d-flex align-items-end mb-3">
                            <button type="submit" class="btn btn-secondary me-2">
                                Apply Filters
                            </button>
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Products Table -->
        @if($products->count() > 0)
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($products as $product)
                            <tr>
                                <td>{{ $product->name ?? 'N/A' }}</td>
                                <td>${{ $product->price ? number_format($product->price, 2) : 'N/A' }}</td>
                                <td>{{ $product->description ? \Str::words($product->description, 10, '...') : 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('products.view', $product) }}" class="btn btn-sm btn-info">View</a>
                                    <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $products->appends(request()->query())->links() }}
            </div>
        @else
            <div class="card">
                <div class="card-body text-center">
                    <p class="mb-0">No products found. Try adjusting your filters or <a href="{{ route('products.create') }}">add a new product</a>.</p>
                </div>
            </div>
        @endif
    </div>
@endsection
