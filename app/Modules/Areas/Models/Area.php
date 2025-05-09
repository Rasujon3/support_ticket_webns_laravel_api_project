<?php

namespace App\Modules\Areas\Models;

use App\Modules\City\Models\City;
use App\Modules\Countries\Models\Country;
use App\Modules\States\Models\State;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class Area extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'areas';

    protected $fillable = [
        'code',
        'name',
        'name_in_bangla',
        'name_in_arabic',
        'is_default',
        'draft',
        'drafted_at',
        'is_active',
        'country_id',
        'state_id',
        'city_id',
        'is_deleted',
        'deleted_at',
    ];

    public static function rules($areaId = null)
    {
        $uniqueCodeRule = Rule::unique('areas', 'code');

        if ($areaId) {
            $uniqueCodeRule->ignore($areaId);
        }
        return [
            'code' => ['nullable', 'string', 'max:45', $uniqueCodeRule],
            'name' => 'nullable|string|max:191|regex:/^[ ]*[a-zA-Z][ a-zA-Z]*[ ]*$/u', // regex for English characters with spaces
            'name_in_bangla' => 'nullable|string|max:191|regex:/^[\p{Bengali}\s]+$/u', // regex for Bangla characters with spaces
            'name_in_arabic' => 'nullable|string|max:191|regex:/^[\p{Arabic}\s]+$/u', // regex for Arabic characters with spaces
            'is_default' => 'nullable|boolean',
            'draft' => 'nullable|boolean',
            'drafted_at' => 'nullable|date',
            'is_active' => 'nullable|boolean',
            'country_id' => [
                'required',
                Rule::exists('countries', 'id')->whereNull('deleted_at')
            ],
            'state_id' => [
                'required',
                Rule::exists('states', 'id')->whereNull('deleted_at')
            ],
            'city_id' => [
                'required',
                Rule::exists('cities', 'id')->whereNull('deleted_at')
            ],
        ];
    }
    public static function bulkRules()
    {
        return [
            'areas' => 'required|array|min:1',
            'areas.*.id' => [
                'required',
                Rule::exists('areas', 'id')->whereNull('deleted_at')
            ],
            'areas.*.code' => [
                'nullable',
                'string',
                'max:45',
                function ($attribute, $value, $fail) {
                    $areaId = request()->input(str_replace('.code', '.id', $attribute));
                    $exists = Area::where('code', $value)
                        # ->whereNull('deleted_at')
                        ->where('id', '!=', $areaId)
                        ->exists();

                    if ($exists) {
                        $fail('The area code "' . $value . '" has already been taken.');
                    }
                },
            ],
            'areas.*.name' => 'nullable|string|max:191|regex:/^[ ]*[a-zA-Z][ a-zA-Z]*[ ]*$/u',
            'areas.*.name_in_bangla' => 'nullable|string|max:191|regex:/^[\p{Bengali}\s]+$/u',
            'areas.*.name_in_arabic' => 'nullable|string|max:191|regex:/^[\p{Arabic}\s]+$/u',
            'areas.*.is_default' => 'nullable|boolean',
            'areas.*.draft' => 'nullable|boolean',
            'areas.*.drafted_at' => 'nullable|date',
            'areas.*.is_active' => 'nullable|boolean',
            'areas.*.country_id' => [
                'required',
                Rule::exists('countries', 'id')->whereNull('deleted_at')
            ],
            'areas.*.state_id' => [
                'required',
                Rule::exists('states', 'id')->whereNull('deleted_at')
            ],
            'areas.*.city_id' => [
                'required',
                Rule::exists('cities', 'id')->whereNull('deleted_at')
            ],
        ];
    }
    public static function listRules()
    {
        return [
            'draft' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
            'is_deleted' => 'nullable|boolean',
            'is_updated' => 'nullable|boolean',
            'country_id' => [
                'nullable',
                Rule::exists('countries', 'id')->whereNull('deleted_at')
            ],
            'state_id' => [
                'nullable',
                Rule::exists('states', 'id')->whereNull('deleted_at')
            ],
            'city_id' => [
                'nullable',
                Rule::exists('cities', 'id')->whereNull('deleted_at')
            ],
        ];
    }
    public static function checkAvailabilityRules()
    {
        return [
            'code' => 'nullable|string|required_without:name',
            'name' => 'nullable|string|required_without:code',
        ];
    }
    public static function importRules()
    {
        return [
            'areas' => [
                'required',
                'array',
                'min:1',
                function ($attribute, $value, $fail) {
                    if (self::withTrashed()->exists()) {
                        $fail('Import not allowed: The areas table already contains data.');
                    }
                },
            ],
            'areas.*.code' => ['required', 'string', 'max:45'],
            'areas.*.name' => 'required|string|max:191|regex:/^[ ]*[a-zA-Z][ a-zA-Z]*[ ]*$/u',
            'areas.*.name_in_bangla' => 'nullable|string|max:191|regex:/^[\p{Bengali}\s]+$/u',
            'areas.*.name_in_arabic' => 'nullable|string|max:191|regex:/^[\p{Arabic}\s]+$/u',
            'areas.*.is_default' => 'nullable|boolean',
            'areas.*.draft' => 'nullable|boolean',
            'areas.*.drafted_at' => 'nullable|date',
            'areas.*.is_active' => 'nullable|boolean',
            'areas.*.country_id' => [
                'required',
                Rule::exists('countries', 'id')->whereNull('deleted_at')
            ],
            'areas.*.state_id' => [
                'required',
                Rule::exists('states', 'id')->whereNull('deleted_at')
            ],
            'areas.*.city_id' => [
                'required',
                Rule::exists('cities', 'id')->whereNull('deleted_at')
            ],
        ];
    }
    public function country() : belongsTo
    {
        return $this->belongsTo(Country::class,'country_id');
    }
    public function state() : belongsTo
    {
        return $this->belongsTo(State::class, 'state_id');
    }
    public function city() : belongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }
}
