<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Consultation\StoreConsultationRequest;
use App\Http\Requests\Consultation\UpdateConsultationRequest;
use App\Http\Resources\ConsultationResource;
use App\Models\Consultation;
use App\Models\MedicalRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ConsultationController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Consultation::class);

        $consultations = Consultation::with(['medicalRecord.patient', 'doctor', 'prescription'])
            ->when(request('doctor_id'), fn($q, $id) => $q->where('doctor_id', $id))
            ->when(request('status'), fn($q, $s) => $q->where('status', $s))
            ->when(request('medical_record_id'), fn($q, $id) => $q->where('medical_record_id', $id))
            ->orderByDesc('consultation_date')
            ->paginate(request()->integer('per_page', 15));

        return ConsultationResource::collection($consultations);
    }

    public function store(StoreConsultationRequest $request): JsonResponse
    {
        $this->authorize('create', Consultation::class);

        $medicalRecord = MedicalRecord::findOrFail($request->input('medical_record_id'));

        $consultation = $medicalRecord->consultations()->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Consultation created successfully.',
            'data'    => new ConsultationResource($consultation->load('doctor', 'medicalRecord.patient')),
        ], 201);
    }

    public function show(Consultation $consultation): JsonResponse
    {
        $this->authorize('view', $consultation);

        $consultation->load(['medicalRecord.patient', 'doctor', 'prescription', 'files']);

        return response()->json([
            'success' => true,
            'data'    => new ConsultationResource($consultation),
        ]);
    }

    public function update(UpdateConsultationRequest $request, Consultation $consultation): JsonResponse
    {
        $this->authorize('update', $consultation);

        $consultation->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Consultation updated successfully.',
            'data'    => new ConsultationResource($consultation->fresh(['doctor', 'medicalRecord.patient'])),
        ]);
    }

    public function destroy(Consultation $consultation): JsonResponse
    {
        $this->authorize('delete', $consultation);

        $consultation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Consultation deleted successfully.',
        ]);
    }
}
