<?php

namespace App\Http\Requests\Consultation;

use Illuminate\Foundation\Http\FormRequest;

class StoreConsultationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'medical_record_id' => ['required', 'integer', 'exists:medical_records,id'],
            'doctor_id'         => ['required', 'integer', 'exists:users,id'],
            'consultation_date' => ['required', 'date'],
            'reason'            => ['required', 'string', 'max:1000'],
            'diagnosis'         => ['nullable', 'string'],
            'treatment'         => ['nullable', 'string'],
            'notes'             => ['nullable', 'string'],
            'status'            => ['in:scheduled,completed,cancelled'],
        ];
    }
}
