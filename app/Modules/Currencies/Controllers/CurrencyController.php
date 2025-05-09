<?php

namespace App\Modules\Currencies\Controllers;

use App\Modules\Currencies\Queries\CurrencyDatatable;
use App\Modules\Currencies\Repositories\CurrencyRepository;
use App\Modules\Currencies\Requests\CurrencyRequest;
use App\Http\Controllers\AppBaseController;

class CurrencyController extends AppBaseController
{
    protected CurrencyRepository $currencyRepository;
    protected CurrencyDatatable $currencyDatatable;

    public function __construct(CurrencyRepository $currencyRepo, CurrencyDatatable $currencyDatatable)
    {
        $this->currencyRepository = $currencyRepo;
        $this->currencyDatatable = $currencyDatatable;
    }
    // Fetch all data
    public function index(CurrencyRequest $request)
    {
        $countries = $this->currencyRepository->all($request);
        return $this->sendResponse($countries, 'Currencies retrieved successfully.');
    }
    // Get single details
    public function show($currency)
    {
        $data = $this->currencyRepository->find($currency);
        if (!$data) {
            return $this->sendError('Currency not found');
        }
        return $this->sendResponse($data, 'Currency retrieved successfully.');
    }
    // store data
    public function store(CurrencyRequest $request)
    {
        $currency = $this->currencyRepository->store($request->all());
        if (!$currency) {
            return $this->sendError('Something went wrong!!! [CCYS-01]', 500);
        }
        return $this->sendResponse($currency, 'Currency created successfully!');
    }
    // Update data
    public function update(CurrencyRequest $request, $currency)
    {
        $data = $this->currencyRepository->find($currency);
        if (!$data) {
            return $this->sendError('Currency not found');
        }
        if (!empty($request->is_delete) && $request->is_delete == 1) {
            $checkExist = $this->currencyRepository->checkExist($data->id);
            if ($checkExist) {
                return $this->sendError('Currency already used, cannot be deleted', 400);
            }
        }
        $updated = $this->currencyRepository->update($data, $request->all());
        if (!$updated) {
            return $this->sendError('Something went wrong!!! [CCYU-01]', 500);
        }
        return $this->sendResponse($currency, 'Currency updated successfully!');
    }
    // bulk update
    public function bulkUpdate(CurrencyRequest $request)
    {
        $bulkUpdate = $this->currencyRepository->bulkUpdate($request);
        if (!$bulkUpdate) {
            return $this->sendError('Something went wrong!!! [CCYBU-05]', 500);
        }
        return $this->sendResponse([],'Currency Bulk updated successfully!');
    }
}
