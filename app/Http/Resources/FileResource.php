<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'original_name' => $this->original_name,
            'mime_type'     => $this->mime_type,
            'size'          => $this->size,
            'size_human'    => $this->formatBytes($this->size),
            'description'   => $this->description,
            'download_url'  => route('files.download', $this->id),
            'patient'       => new PatientResource($this->whenLoaded('patient')),
            'consultation'  => new ConsultationResource($this->whenLoaded('consultation')),
            'uploaded_by'   => new UserResource($this->whenLoaded('uploadedBy')),
            'created_at'    => $this->created_at?->toISOString(),
        ];
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
