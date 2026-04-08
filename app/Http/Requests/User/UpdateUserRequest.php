<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'name'      => ['sometimes', 'required', 'string', 'max:255'],
            'email'     => ['sometimes', 'required', 'string', 'email', 'max:255', "unique:users,email,{$userId}"],
            'password'  => ['sometimes', 'nullable', Password::min(8)->mixedCase()->numbers()],
            'phone'     => ['nullable', 'string', 'max:20'],
            'specialty' => ['nullable', 'string', 'max:100'],
            'is_active' => ['boolean'],
        ];
    }
}
