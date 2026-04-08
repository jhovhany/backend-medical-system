<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id'       => ['required', 'integer', 'exists:patients,id'],
            'doctor_id'        => ['required', 'integer', 'exists:users,id'],
            'appointment_date' => ['required', 'date', 'after:now'],
            'reason'           => ['nullable', 'string', 'max:1000'],
            'notes'            => ['nullable', 'string'],
        ];
    }
}
