<?php

namespace App\Http\Requests\GameSession;

use Illuminate\Validation\Rule;
use App\Http\Requests\BaseFilterRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class GameSessionFilterRequest extends BaseFilterRequest
{
    /**
     * {@inheritDoc}
     */
    protected array $booleans = [
        'only_available',
    ];

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'date' => ['sometimes', 'date_format:Y-m-d'],
            'city' => ['sometimes', 'string', 'max:255'],
            'state' => ['sometimes', 'string', 'size:2'],
            'only_available' => ['sometimes', 'boolean'],
            'search' => ['sometimes', 'string', 'max:255'],
            'per_page' => ['required', 'numeric', 'between:1,50'],
            'radius_km' => ['sometimes', 'numeric', 'between:1,50'],
            'latitude' => ['sometimes', 'numeric', 'between:-90,90'],
            'sport_id' => ['sometimes', 'numeric', 'exists:sports,id'],
            'venue_id' => ['sometimes', 'numeric', 'exists:venues,id'],
            'longitude' => ['sometimes', 'numeric', 'between:-180,180'],
            'skill_level_id' => ['sometimes', 'numeric', 'exists:skill_levels,id'],
            'sort_by' => ['sometimes', 'string', Rule::in(['start_time', 'created_at'])],
            'sort_order' => ['sometimes', 'string', Rule::in(['asc', 'desc'])],
        ];
    }
}
