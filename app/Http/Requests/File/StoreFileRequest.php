<?php

namespace App\Http\Requests\File;

use Illuminate\Foundation\Http\FormRequest;

class StoreFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file'            => ['required', 'file', 'max:51200', 'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,dicom'],
            'patient_id'      => ['nullable', 'integer', 'exists:patients,id'],
            'consultation_id' => ['nullable', 'integer', 'exists:consultations,id'],
            'description'     => ['nullable', 'string', 'max:500'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            if (! $this->filled('patient_id') && ! $this->filled('consultation_id')) {
                $v->errors()->add('patient_id', 'A patient or consultation must be specified.');
            }
        });
    }
}
