<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConsultationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'consultation_date' => $this->consultation_date?->toISOString(),
            'reason'            => $this->reason,
            'diagnosis'         => $this->diagnosis,
            'treatment'         => $this->treatment,
            'notes'             => $this->notes,
            'status'            => $this->status,
            'medical_record'    => new MedicalRecordResource($this->whenLoaded('medicalRecord')),
            'doctor'            => new UserResource($this->whenLoaded('doctor')),
            'prescription'      => new PrescriptionResource($this->whenLoaded('prescription')),
            'files'             => FileResource::collection($this->whenLoaded('files')),
            'created_at'        => $this->created_at?->toISOString(),
            'updated_at'        => $this->updated_at?->toISOString(),
        ];
    }
}
