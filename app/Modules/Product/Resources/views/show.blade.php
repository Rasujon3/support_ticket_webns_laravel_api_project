@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-8">
                <h1>{{ $product->name ?? 'Unknown Product' }}</h1>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('products.index') }}" class="btn btn-secondary me-2">
                    Back to Products
                </a>
                <a href="{{ route('products.edit', $product) }}" class="btn btn-warning">
                    Edit Product
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <!-- Product Details Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Product Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Product Type:</strong>
                                <p>{{ $product->name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Price:</strong>
                                <p>
                                    ${{ $product->price ? number_format($product->price, 2) : 'N/A' }}
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Added:</strong>
                                <p>{{ $product->created_at->format('F j, Y') }}</p>
                            </div>
                            <div class="col-md-12 mb-3">
                                <strong>Description:</strong>
                                <p>{{ $product->description ?? 'No description provided' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
