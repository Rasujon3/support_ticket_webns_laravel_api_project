<?php

namespace App\Modules\Product\Queries;

use App\Modules\Product\Models\Product;
use App\Modules\Product\Repositories\ProductRepository;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;



class ProductDatatable
{
    protected $productRepository;
    protected $countryDatatable;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getGridData($request)
    {
        $filters = $request->only(['search', 'filter']);
        $page = !empty($filters['search']) ? 1 : $request->input('page', 1); // Reset page to 1 if search exists
        $perPage = 24;

        $query = $this->productRepository->getFilteredQuery($filters);
        $query->skip(($page - 1) * $perPage)->take($perPage);

        $currencies = $query->select(['id', 'code', 'name', 'is_default', 'is_active', 'draft', 'drafted_at', 'flag'])
            ->orderBy('updated_at', 'desc')
            ->get();

        $response = DataTables::of($currencies)
            ->addColumn('status', function ($currency) {
                return $currency->is_active ? 'Active' : 'Inactive';
            })
            ->addColumn('default', function ($currency) {
                return $currency->is_default ? 'Yes' : 'No';
            })
            ->addColumn('drafted_at', function ($currency) {
                return $currency->drafted_at ? Carbon::parse($currency->drafted_at)->format('Y-m-d') : 'N/A';
            })
            ->make(true)
            ->getData(true);

        $response['summary'] = $this->productRepository->getSummaryData();

        return $response;
    }

    public static function getDataForDatatable()
    {
        $countries = Product::select(['id', 'code', 'name', 'is_active', 'draft', 'is_default', 'flag']);
        return DataTables::of($countries)->make(true);
    }
}
