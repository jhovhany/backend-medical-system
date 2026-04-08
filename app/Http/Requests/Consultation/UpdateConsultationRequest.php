<?php

namespace App\Http\Requests\Consultation;

use Illuminate\Foundation\Http\FormRequest;

class UpdateConsultationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'doctor_id'         => ['sometimes', 'integer', 'exists:users,id'],
            'consultation_date' => ['sometimes', 'required', 'date'],
            'reason'            => ['sometimes', 'required', 'string', 'max:1000'],
            'diagnosis'         => ['nullable', 'string'],
            'treatment'         => ['nullable', 'string'],
            'notes'             => ['nullable', 'string'],
            'status'            => ['sometimes', 'required', 'in:scheduled,completed,cancelled'],
        ];
    }
}
