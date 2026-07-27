<?php

namespace App\Http\Requests\Authentication;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Contracts\Validation\ValidationRule;

class RegisterRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', Password::default()],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:users,email'],
        ];
    }
}
