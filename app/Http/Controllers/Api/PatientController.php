<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Patient\StorePatientRequest;
use App\Http\Requests\Patient\UpdatePatientRequest;
use App\Http\Resources\PatientResource;
use App\Models\MedicalRecord;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PatientController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Patient::class);

        $patients = Patient::query()
            ->when(request('search'), fn($q, $s) => $q->where(
                fn($q) => $q->where('first_name', 'ilike', "%{$s}%")
                             ->orWhere('last_name', 'ilike', "%{$s}%")
                             ->orWhere('email', 'ilike', "%{$s}%")
            ))
            ->when(request()->has('is_active'), fn($q) => $q->where('is_active', request()->boolean('is_active')))
            ->orderBy('last_name')
            ->paginate(request()->integer('per_page', 15));

        return PatientResource::collection($patients);
    }

    public function store(StorePatientRequest $request): JsonResponse
    {
        $this->authorize('create', Patient::class);

        $patient = DB::transaction(function () use ($request) {
            $patient = Patient::create($request->validated());

            MedicalRecord::create([
                'patient_id'    => $patient->id,
                'record_number' => 'MR-' . strtoupper(Str::random(8)),
            ]);

            return $patient;
        });

        return response()->json([
            'success' => true,
            'message' => 'Patient created successfully.',
            'data'    => new PatientResource($patient->load('medicalRecord')),
        ], 201);
    }

    public function show(Patient $patient): JsonResponse
    {
        $this->authorize('view', $patient);

        return response()->json([
            'success' => true,
            'data'    => new PatientResource($patient->load('medicalRecord', 'appointments')),
        ]);
    }

    public function update(UpdatePatientRequest $request, Patient $patient): JsonResponse
    {
        $this->authorize('update', $patient);

        $patient->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Patient updated successfully.',
            'data'    => new PatientResource($patient->fresh('medicalRecord')),
        ]);
    }

    public function destroy(Patient $patient): JsonResponse
    {
        $this->authorize('delete', $patient);

        $patient->delete();

        return response()->json([
            'success' => true,
            'message' => 'Patient deleted successfully.',
        ]);
    }
}
