<?php

namespace App\Modules\City\Repositories;

use App\Modules\Areas\Models\Area;
use App\Modules\City\Models\City;
use App\Modules\City\Models\CityHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class CityRepository
{
    public function all($request)
    {
        $list = $this->list($request);

        $cities = City::withTrashed()->get(); // Load all records including soft-deleted

        $totalDraft = $cities->whereNull('deleted_at')->where('draft', true)->count();
        $totalInactive = $cities->whereNull('deleted_at')->where('is_active', false)->count();
        $totalActive = $cities->whereNull('deleted_at')->where('is_active', true)->count();
        $totalDeleted = $cities->whereNotNull('deleted_at')->count();
        $totalUpdated = $cities->whereNull('deleted_at')->whereNotNull('updated_at')->count();

        // Ensure total count is without soft-deleted
        $totalCities = $cities->count();

        return [
            'totalCities' => $totalCities,
            'totalDraft' => $totalDraft,
            'totalInactive' => $totalInactive,
            'totalActive' => $totalActive,
            'totalUpdated' => $totalUpdated,
            'totalDeleted' => $totalDeleted,
            'list' => $list,
        ];
    }

    public function list($request)
    {
        $query = City::withTrashed()
            ->leftJoin('countries', 'cities.country_id', '=', 'countries.id')
            ->leftJoin('states', 'states.id', '=', 'cities.state_id')
            ->select('cities.*', 'countries.name as country_name', 'states.name as state_name');

        if ($request->has('draft')) {
            $query->where('cities.draft', $request->input('draft'));
        }
        if ($request->has('is_active')) {
            $query->where('cities.is_active', $request->input('is_active'));
        }
        if ($request->has('is_default')) {
            $query->where('cities.is_default', $request->input('is_default'));
        }
        if ($request->has('is_deleted')) {
            if ($request->input('is_deleted') == 1) {
                $query->whereNotNull('cities.deleted_at');
            } else {
                $query->whereNull('cities.deleted_at');
            }
        }
        if ($request->has('is_updated')) {
            if ($request->input('is_updated') == 1) {
                $query->whereNotNull('cities.updated_at');
            } else {
                $query->whereNull('cities.updated_at');
            }
        }
        if ($request->has('country_id')) {
            $query->where('cities.country_id', $request->input('country_id'));
        }
        if ($request->has('state_id')) {
            $query->where('cities.state_id', $request->input('state_id'));
        }

        $list = $query->orderBy('id', 'desc')->get();
        return $list;
    }
    public function store(array $data): ?City
    {
        DB::beginTransaction();
        try {
            // Set drafted_at timestamp if it's a draft
            if (isset($data['draft'])) {
                if($data['draft'] == 1) {
                    $data['drafted_at'] = now();
                    $data['is_active'] = 0;
                } else {
                    $data['is_active'] = 1;
                }
            } else {
                $data['is_active'] = 1;
            }

            // Create the City record in the database
            $city = City::create($data);

            $this->cityHistoryCreate('City Add');

            DB::commit();

            return $city;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error in storing City: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    public function update(City $city, array $data): ?City
    {
        DB::beginTransaction();
        try {
            // Set drafted_at timestamp if it's a draft
            if (isset($data['draft'])) {
                if($data['draft'] == 1) {
                    $data['drafted_at'] = now();
                    $data['is_active'] = 0;
                } else {
                    $data['is_active'] = 1;
                }
            } else {
                $data['is_active'] = 1;
            }

            // Perform the update
            $city->update($data);
            // Soft delete the record if 'is_delete' is 1
            if (isset($data['is_delete'])) {
                if ($data['is_delete'] == 1) {
                    $this->delete($city);
                } else {
                    $city->update([
                        'is_deleted' => 0,
                        'deleted_at' => null,
                        'is_active' => 1,
                        'draft' => 0
                    ]);
                    $this->cityHistoryCreate('City Updated');
                }
            } else {
                $this->cityHistoryCreate('City Updated');
            }

            DB::commit();
            return $city;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error updating City: ' , [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
    public function delete(City $city): bool
    {
        DB::beginTransaction();
        try {
            $city->update([
                'is_deleted' => 1,
                'is_active' => 0,
                'draft' => 1,
                'drafted_at' => now()
                ]);
            // Perform soft delete
            $deleted = $city->delete();
            if (!$deleted) {
                DB::rollBack();
                return false;
            }
            $this->cityHistoryCreate('City Deleted');
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log error
            Log::error('Error deleting City: ' , [
                'state_id' => $city->id,
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
        return City::withTrashed()->find($id);
    }
    public function getData($id)
    {
        $city = City::withTrashed()
            ->leftJoin('countries', 'cities.country_id', '=', 'countries.id')
            ->leftJoin('states', 'states.id', '=', 'cities.state_id')
            ->where('cities.id', $id)
            ->select('cities.*', 'countries.name as country_name', 'states.name as state_name')
            ->first();
        return $city;
    }
    public function bulkUpdate($request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->cities as $data) {
                $city = City::find($data['id']);

                if (!$city) {
                    continue; // Skip if city not found
                }

                // Update state details
                $city->update([
                    'code' => $data['code'] ?? $city->code,
                    'name' => $data['name'] ?? $city->name,
                    'name_in_bangla' => $data['name_in_bangla'] ?? $city->name_in_bangla,
                    'name_in_arabic' => $data['name_in_arabic'] ?? $city->name_in_arabic,
                    'is_default' => $data['is_default'] ?? $city->is_default,
                    'draft' => $data['draft'] ?? $city->draft,
                    'drafted_at' => (isset($data['draft']) && $data['draft'] == 1) ? now() : $city->drafted_at,
                    'is_active' => ((isset($data['draft']) && $data['draft'] == 1)) ? 0 : 1,
                    'country_id' => $data['country_id'] ?? $city->country_id,
                    'state_id' => $data['state_id'] ?? $city->state_id,
                ]);
                $this->cityHistoryCreate('City Updated');
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error Bulk updating City: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
    public function checkExist($id): bool
    {
        $existOnArea = Area::where('city_id', $id)->whereNull('deleted_at')->exists();
        if ($existOnArea) {
            return true;
        }
        return false;
    }
    public function import($request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->cities as $data) {
                // create data
                $city = City::create([
                    'code' => $data['code'] ?? '',
                    'name' => $data['name'] ?? '',
                    'name_in_bangla' => $data['name_in_bangla'] ?? null,
                    'name_in_arabic' => $data['name_in_arabic'] ?? null,
                    'is_default' => $data['is_default'] ?? 0,
                    'draft' => (isset($data['draft']) && $data['draft']) ?? 0,
                    'drafted_at' => (isset($data['draft']) && $data['draft'] == 1) ? now() : null,
                    'is_active' => isset($data['is_active']) ?? 1,
                    'country_id' => $data['country_id'] ?? null,
                    'state_id' => $data['state_id'] ?? null,
                ]);

                // Handle flag image create if provided
                /*
                if (isset($data['flag']) && $request->hasFile("countries.{$data['id']}.flag")) {
                    $flagPath = $request->file("countries.{$data['id']}.flag")->store('flags', 'public');
                    $country->update(['flag' => $flagPath]);
                }
                */

                $this->cityHistoryCreate('City Created');
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error bulk import city: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
    private function cityHistoryCreate(string $actionType, $exportPdf = false, $exportXls = false, $exportPrint = false): bool
    {
        DB::beginTransaction();
        try {
            // Get the authenticated user
            $user = Auth::user();

            CityHistory::create([
                'client_id' => $user->admin_client_id ?? null,
                'action_date' => now(),
                'action_by' => $user->name ?? null,
                'action_type' => $actionType,
                'export_pdf' => $exportPdf,
                'export_xls' => $exportXls,
                'export_print' => $exportPrint,
            ]);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error creating city history', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }
    public function checkAvailability(array $data): bool
    {
        try {
            $code = $data['code'] ?? null;
            $name = $data['name'] ?? null;

            // Check if either code or name already exists
            $exists = City::where(function ($query) use ($code, $name) {
                if ($code) {
                    $query->orWhere('code', $code);
                }
                if ($name) {
                    $query->orWhere('name', $name);
                }
            })
                ->exists();

            // Return true if no match (available), false if exists (not available)
            return $exists;
        } catch (\Exception $e) {
            Log::error('Error checking city availability', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Default to false (not available) on error
            return false;
        }
    }
    public function history()
    {
        try {
            $history = CityHistory::orderBy('id', 'desc')->get();
            return $history;
        } catch (\Exception $e) {
            Log::error('Error checking city history', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Default to false (not available) on error
            return false;
        }
    }
}
