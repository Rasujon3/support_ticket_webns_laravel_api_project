@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Edit Product</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('products.update', $product->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="form-group row mb-3">
                                <label for="business_id" class="col-md-4 col-form-label text-md-right">Business</label>
                                <div class="col-md-6">
                                    <select id="business_id" class="form-control @error('business_id') is-invalid @enderror" name="business_id" required>
                                        <option value="">Select Business</option>
                                        @foreach($businesses as $id => $name)
                                            <option value="{{ $id }}" {{ old('business_id', $product->business_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('business_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label for="product_name" class="col-md-4 col-form-label text-md-right">Product Name</label>
                                <div class="col-md-6">
                                    <input id="product_name" type="text" class="form-control @error('product_name') is-invalid @enderror" name="product_name" value="{{ old('product_name', $product->product_name) }}" required>
                                    @error('product_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label for="description" class="col-md-4 col-form-label text-md-right">Description</label>
                                <div class="col-md-6">
                                    <textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description" rows="3">{{ old('description', $product->description) }}</textarea>
                                    @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label for="health_benefits" class="col-md-4 col-form-label text-md-right">Health Benefits</label>
                                <div class="col-md-6">
                                    <textarea id="health_benefits" class="form-control @error('health_benefits') is-invalid @enderror" name="health_benefits" rows="3">{{ old('health_benefits', $product->health_benefits) }}</textarea>
                                    @error('health_benefits')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label for="certifications" class="col-md-4 col-form-label text-md-right">certifications</label>
                                <div class="col-md-6">
                                    <textarea id="certifications" class="form-control @error('certifications') is-invalid @enderror" name="certifications" rows="3">{{ old('certifications', $product->certifications) }}</textarea>
                                    @error('certifications')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label for="price_category" class="col-md-4 col-form-label text-md-right">Price Category</label>
                                <div class="col-md-6">
                                    <select id="price_category" class="form-control @error('price_category') is-invalid @enderror" name="price_category" required>
                                        <option value="affordable" {{ old('price_category', $product->price_category) == 'affordable' ? 'selected' : '' }}>Affordable</option>
                                        <option value="moderate" {{ old('price_category', $product->price_category) == 'moderate' ? 'selected' : '' }}>Moderate</option>
                                        <option value="premium" {{ old('price_category', $product->price_category) == 'premium' ? 'selected' : '' }}>Premium</option>
                                    </select>
                                    @error('price_category')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label for="product_type" class="col-md-4 col-form-label text-md-right">Product Type</label>
                                <div class="col-md-6">
                                    <input id="product_type" type="text" class="form-control @error('product_type') is-invalid @enderror" name="product_type" value="{{ old('product_type', $product->product_type) }}" required>
                                    @error('product_type')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label for="price" class="col-md-4 col-form-label text-md-right">Price</label>
                                <div class="col-md-6">
                                    <input id="price" type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" name="price" value="{{ old('price', $product->price) }}" required>
                                    @error('price')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label for="is_available" class="col-md-4 col-form-label text-md-right">Availability</label>
                                <div class="col-md-6 mt-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_available" id="is_available" value="1" {{ old('is_available', $product->is_available) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_available">
                                            Available for sale
                                        </label>
                                    </div>
                                    @error('is_available')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        Update Product
                                    </button>
                                    <a href="{{ route('products.index') }}" class="btn btn-secondary">
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
