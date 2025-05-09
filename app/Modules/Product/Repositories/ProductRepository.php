<?php

namespace App\Modules\Product\Repositories;

use App\Modules\Product\Models\Product;
use Illuminate\Support\Facades\Log;

class ProductRepository
{
    public function all()
    {
        return Product::all();
    }

    public function store(array $data): ?Product
    {
        try {
            // Create the record in the database
            $product = Product::create($data);

            return $product;
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error in storing data: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function update(Product $product, array $data): ?Product
    {
        try {
            // Perform the update
            $product->update($data);

            return $product;
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error updating data: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function delete(Product $product)
    {
        try {
            $product->delete();
            return true;
        } catch (\Exception $e) {
            // Log error
            Log::error('Error deleting data: ' , [
                'country_id' => $product->id,
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    public function find($id)
    {
        return Product::findOrFail($id);
    }
}
