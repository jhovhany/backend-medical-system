<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'  => ['required', Password::min(8)->mixedCase()->numbers()],
            'phone'     => ['nullable', 'string', 'max:20'],
            'specialty' => ['nullable', 'string', 'max:100'],
            'is_active' => ['boolean'],
            'role'      => ['nullable', 'string', 'exists:roles,name'],
        ];
    }
}
