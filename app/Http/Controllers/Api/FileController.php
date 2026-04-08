<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\File\StoreFileRequest;
use App\Http\Resources\FileResource;
use App\Models\File;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', File::class);

        $files = File::with(['patient', 'uploadedBy', 'consultation'])
            ->when(request('patient_id'), fn($q, $id) => $q->where('patient_id', $id))
            ->when(request('consultation_id'), fn($q, $id) => $q->where('consultation_id', $id))
            ->orderByDesc('created_at')
            ->paginate(request()->integer('per_page', 15));

        return FileResource::collection($files);
    }

    public function store(StoreFileRequest $request): JsonResponse
    {
        $this->authorize('create', File::class);

        $uploaded = $request->file('file');
        $storedName = Str::uuid() . '.' . $uploaded->getClientOriginalExtension();
        $directory  = 'medical_files/' . ($request->input('patient_id') ?? 'general');

        $path = $uploaded->storeAs($directory, $storedName, 'medical_files');

        $file = File::create([
            'patient_id'      => $request->input('patient_id'),
            'consultation_id' => $request->input('consultation_id'),
            'uploaded_by'     => auth('api')->id(),
            'original_name'   => $uploaded->getClientOriginalName(),
            'stored_name'     => $storedName,
            'path'            => $path,
            'mime_type'       => $uploaded->getMimeType(),
            'size'            => $uploaded->getSize(),
            'description'     => $request->input('description'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'File uploaded successfully.',
            'data'    => new FileResource($file->load('patient', 'uploadedBy')),
        ], 201);
    }

    public function show(File $file): JsonResponse
    {
        $this->authorize('view', $file);

        return response()->json([
            'success' => true,
            'data'    => new FileResource($file->load('patient', 'uploadedBy', 'consultation')),
        ]);
    }

    public function destroy(File $file): JsonResponse
    {
        $this->authorize('delete', $file);

        Storage::disk('medical_files')->delete($file->path);
        $file->delete();

        return response()->json([
            'success' => true,
            'message' => 'File deleted successfully.',
        ]);
    }

    public function download(File $file): mixed
    {
        $this->authorize('view', $file);

        if (! Storage::disk('medical_files')->exists($file->path)) {
            return response()->json([
                'success' => false,
                'message' => 'File not found on storage.',
            ], 404);
        }

        return Storage::disk('medical_files')->download($file->path, $file->original_name);
    }
}
