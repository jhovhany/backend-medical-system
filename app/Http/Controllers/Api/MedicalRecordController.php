<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MedicalRecord\UpdateMedicalRecordRequest;
use App\Http\Resources\MedicalRecordResource;
use App\Models\MedicalRecord;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MedicalRecordController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', MedicalRecord::class);

        $records = MedicalRecord::query()
            ->with(['patient'])
            ->orderByDesc('updated_at')
            ->paginate(request()->integer('per_page', 15));

        return MedicalRecordResource::collection($records);
    }

    public function showByPatient(Patient $patient): JsonResponse
    {
        $this->authorize('view', $patient);

        $medicalRecord = $patient->medicalRecord()->with(['consultations.doctor', 'consultations.prescription'])->firstOrFail();

        return response()->json([
            'success' => true,
            'data'    => new MedicalRecordResource($medicalRecord),
        ]);
    }

    public function show(MedicalRecord $medicalRecord): JsonResponse
    {
        $this->authorize('view', $medicalRecord);

        $medicalRecord->load(['patient', 'consultations.doctor', 'consultations.prescription']);

        return response()->json([
            'success' => true,
            'data'    => new MedicalRecordResource($medicalRecord),
        ]);
    }

    public function update(UpdateMedicalRecordRequest $request, MedicalRecord $medicalRecord): JsonResponse
    {
        $this->authorize('update', $medicalRecord);

        $medicalRecord->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Medical record updated successfully.',
            'data'    => new MedicalRecordResource($medicalRecord->fresh('patient')),
        ]);
    }
}
