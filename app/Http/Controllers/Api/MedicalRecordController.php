<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MedicalRecord\UpdateMedicalRecordRequest;
use App\Http\Resources\MedicalRecordResource;
use App\Models\MedicalRecord;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;

class MedicalRecordController extends Controller
{
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
