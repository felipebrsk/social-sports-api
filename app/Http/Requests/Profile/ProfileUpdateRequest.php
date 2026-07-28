<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class ProfileUpdateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'bio' => ['nullable', 'string', 'max:500'],
            'whatsapp' => ['nullable', 'string', 'max:255'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'file', 'mimes:png,jpg,jpeg'],
        ];
    }
}
