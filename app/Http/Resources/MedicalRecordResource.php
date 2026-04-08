<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicalRecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'record_number'     => $this->record_number,
            'notes'             => $this->notes,
            'diagnosis_history' => $this->diagnosis_history,
            'patient'           => new PatientResource($this->whenLoaded('patient')),
            'consultations'     => ConsultationResource::collection($this->whenLoaded('consultations')),
            'created_at'        => $this->created_at?->toISOString(),
            'updated_at'        => $this->updated_at?->toISOString(),
        ];
    }
}
