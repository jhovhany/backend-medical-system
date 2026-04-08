<?php

namespace App\Http\Requests\Prescription;

use Illuminate\Foundation\Http\FormRequest;

class StorePrescriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'medications'                  => ['required', 'array', 'min:1'],
            'medications.*.name'           => ['required', 'string', 'max:200'],
            'medications.*.dosage'         => ['required', 'string', 'max:100'],
            'medications.*.frequency'      => ['required', 'string', 'max:100'],
            'medications.*.duration'       => ['required', 'string', 'max:100'],
            'medications.*.notes'          => ['nullable', 'string', 'max:500'],
            'instructions'                 => ['nullable', 'string'],
            'valid_until'                  => ['nullable', 'date', 'after:today'],
        ];
    }
}
