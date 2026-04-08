<?php

namespace App\Http\Requests\Patient;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $patientId = $this->route('patient')?->id;

        return [
            'first_name'               => ['sometimes', 'required', 'string', 'max:100'],
            'last_name'                => ['sometimes', 'required', 'string', 'max:100'],
            'date_of_birth'            => ['sometimes', 'required', 'date', 'before:today'],
            'gender'                   => ['sometimes', 'required', 'in:male,female,other'],
            'email'                    => ['nullable', 'email', 'max:255', "unique:patients,email,{$patientId}"],
            'phone'                    => ['nullable', 'string', 'max:20'],
            'address'                  => ['nullable', 'string', 'max:500'],
            'blood_type'               => ['nullable', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'allergies'                => ['nullable', 'string'],
            'emergency_contact_name'   => ['nullable', 'string', 'max:150'],
            'emergency_contact_phone'  => ['nullable', 'string', 'max:20'],
            'is_active'                => ['boolean'],
        ];
    }
}
