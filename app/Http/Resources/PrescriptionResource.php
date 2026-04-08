<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrescriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'medications'  => $this->medications,
            'instructions' => $this->instructions,
            'valid_until'  => $this->valid_until?->toDateString(),
            'consultation' => new ConsultationResource($this->whenLoaded('consultation')),
            'issued_by'    => new UserResource($this->whenLoaded('issuedBy')),
            'pdf_url'      => route('prescriptions.pdf', $this->id),
            'created_at'   => $this->created_at?->toISOString(),
            'updated_at'   => $this->updated_at?->toISOString(),
        ];
    }
}
