<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'doctor_id'        => ['sometimes', 'integer', 'exists:users,id'],
            'appointment_date' => ['sometimes', 'required', 'date'],
            'reason'           => ['nullable', 'string', 'max:1000'],
            'status'           => ['sometimes', 'required', 'in:pending,confirmed,completed,cancelled'],
            'notes'            => ['nullable', 'string'],
        ];
    }
}
