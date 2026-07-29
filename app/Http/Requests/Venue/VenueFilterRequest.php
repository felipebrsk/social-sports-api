<?php

namespace App\Http\Requests\Venue;

use Illuminate\Validation\Rule;
use App\Http\Requests\BaseFilterRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Contracts\Repositories\VenueRepositoryInterface;

class VenueFilterRequest extends BaseFilterRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var VenueRepositoryInterface $repository */
        $repository = app(VenueRepositoryInterface::class);

        $allowedSorts = $repository->getAllowedSorts();

        return [
            'id' => ['sometimes', 'numeric'],
            'sport_id' => ['sometimes', 'numeric'],
            'city' => ['sometimes', 'string', 'max:255'],
            'state' => ['sometimes', 'string', 'size:2'],
            'search' => ['sometimes', 'string', 'max:255'],
            'limit' => ['required', 'numeric', 'between:1,50'],
            'radius_km' => ['sometimes', 'numeric', 'between:1,30'],
            'latitude' => ['sometimes', 'numeric', 'between:-90,90'],
            'longitude' => ['sometimes', 'numeric', 'between:-180,180'],
            'sort_by' => ['sometimes', 'string', Rule::in($allowedSorts)],
            'sort_order' => ['sometimes', 'string', Rule::in(['asc', 'desc'])],
        ];
    }
}
