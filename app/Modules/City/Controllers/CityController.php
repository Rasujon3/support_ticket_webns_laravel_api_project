<?php

namespace App\Modules\City\Controllers;

use App\Modules\City\Queries\CityDatatable;
use App\Modules\City\Repositories\CityRepository;
use App\Modules\City\Requests\CityRequest;
use App\Http\Controllers\AppBaseController;

class CityController extends AppBaseController
{
    protected CityRepository $cityRepository;
    protected CityDatatable $cityDatatable;

    public function __construct(CityRepository $cityRepo, CityDatatable $cityDatatable)
    {
        $this->cityRepository = $cityRepo;
        $this->cityDatatable = $cityDatatable;
    }
    // Fetch all data
    public function index(CityRequest $request)
    {
        $statues = $this->cityRepository->all($request);
        return $this->sendResponse($statues, 'Cities retrieved successfully.');
    }

    // Store data
    public function store(CityRequest $request)
    {
        $city = $this->cityRepository->store($request->all());
        if (!$city) {
            return $this->sendError('Something went wrong!!! [CS-01]', 500);
        }
        return $this->sendResponse($city, 'City created successfully!');
    }

    // Get single details data
    public function show($city)
    {
        $data = $this->cityRepository->find($city);
        if (!$data) {
            return $this->sendError('City not found');
        }
        $summary = $this->cityRepository->getData($city);
        return $this->sendResponse($summary, 'City retrieved successfully.');
    }
    // Update data
    public function update(CityRequest $request, $city)
    {
        $data = $this->cityRepository->find($city);
        if (!$data) {
            return $this->sendError('City not found');
        }
        if (!empty($request->is_delete) && $request->is_delete == 1) {
            $checkExist = $this->cityRepository->checkExist($data->id);
            if ($checkExist) {
                return $this->sendError('City already used, cannot be deleted', 400);
            }
        }
        $updated = $this->cityRepository->update($data, $request->all());
        if (!$updated) {
            return $this->sendError('Something went wrong!!! [CU-04]', 500);
        }
        return $this->sendResponse($city, 'City updated successfully!');
    }
    // bulk update
    public function bulkUpdate(CityRequest $request)
    {
        $bulkUpdate = $this->cityRepository->bulkUpdate($request);
        if (!$bulkUpdate) {
            return $this->sendError('Something went wrong!!! [CBU-05]', 500);
        }
        return $this->sendResponse([],'City Bulk updated successfully!');
    }
    public function import(CityRequest $request)
    {
        $import = $this->cityRepository->import($request);
        if (!$import) {
            return $this->sendError('Something went wrong!!! [CCBU-06]', 500);
        }
        return $this->sendResponse([],'City imported successfully!');
    }
    // check availability
    public function checkAvailability(CityRequest $request)
    {
        $checkAvailability = $this->cityRepository->checkAvailability($request->all());
        if ($checkAvailability) {
            return $this->sendError('City is already exist!', 500);
        }
        return $this->sendResponse([],'City is available!');
    }
    // history
    public function history()
    {
        $history = $this->cityRepository->history();
        return $this->sendResponse($history,'City history retrieved successfully.');
    }
}
