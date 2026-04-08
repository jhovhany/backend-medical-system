<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'appointment_date' => $this->appointment_date?->toISOString(),
            'reason'           => $this->reason,
            'status'           => $this->status,
            'notes'            => $this->notes,
            'patient'          => new PatientResource($this->whenLoaded('patient')),
            'doctor'           => new UserResource($this->whenLoaded('doctor')),
            'created_at'       => $this->created_at?->toISOString(),
            'updated_at'       => $this->updated_at?->toISOString(),
        ];
    }
}
