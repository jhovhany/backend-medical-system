<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                       => $this->id,
            'first_name'               => $this->first_name,
            'last_name'                => $this->last_name,
            'full_name'                => $this->full_name,
            'date_of_birth'            => $this->date_of_birth?->toDateString(),
            'gender'                   => $this->gender,
            'email'                    => $this->email,
            'phone'                    => $this->phone,
            'address'                  => $this->address,
            'blood_type'               => $this->blood_type,
            'allergies'                => $this->allergies,
            'emergency_contact_name'   => $this->emergency_contact_name,
            'emergency_contact_phone'  => $this->emergency_contact_phone,
            'is_active'                => $this->is_active,
            'medical_record'           => new MedicalRecordResource($this->whenLoaded('medicalRecord')),
            'appointments'             => AppointmentResource::collection($this->whenLoaded('appointments')),
            'created_at'               => $this->created_at?->toISOString(),
            'updated_at'               => $this->updated_at?->toISOString(),
        ];
    }
}
