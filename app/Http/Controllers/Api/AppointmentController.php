<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Appointment\StoreAppointmentRequest;
use App\Http\Requests\Appointment\UpdateAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AppointmentController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Appointment::class);

        $appointments = Appointment::with(['patient', 'doctor'])
            ->when(request('patient_id'), fn($q, $id) => $q->where('patient_id', $id))
            ->when(request('doctor_id'), fn($q, $id) => $q->where('doctor_id', $id))
            ->when(request('status'), fn($q, $s) => $q->where('status', $s))
            ->when(request('date'), fn($q, $d) => $q->whereDate('appointment_date', $d))
            ->orderBy('appointment_date')
            ->paginate(request()->integer('per_page', 15));

        return AppointmentResource::collection($appointments);
    }

    public function store(StoreAppointmentRequest $request): JsonResponse
    {
        $this->authorize('create', Appointment::class);

        $appointment = Appointment::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Appointment created successfully.',
            'data'    => new AppointmentResource($appointment->load('patient', 'doctor')),
        ], 201);
    }

    public function show(Appointment $appointment): JsonResponse
    {
        $this->authorize('view', $appointment);

        return response()->json([
            'success' => true,
            'data'    => new AppointmentResource($appointment->load('patient', 'doctor')),
        ]);
    }

    public function update(UpdateAppointmentRequest $request, Appointment $appointment): JsonResponse
    {
        $this->authorize('update', $appointment);

        $appointment->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Appointment updated successfully.',
            'data'    => new AppointmentResource($appointment->fresh(['patient', 'doctor'])),
        ]);
    }

    public function destroy(Appointment $appointment): JsonResponse
    {
        $this->authorize('delete', $appointment);

        $appointment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Appointment deleted successfully.',
        ]);
    }
}
