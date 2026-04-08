<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Prescription\StorePrescriptionRequest;
use App\Http\Resources\PrescriptionResource;
use App\Models\Consultation;
use App\Models\Prescription;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PrescriptionController extends Controller
{
    public function store(StorePrescriptionRequest $request, Consultation $consultation): JsonResponse
    {
        $this->authorize('create', Prescription::class);

        if ($consultation->prescription()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'This consultation already has a prescription.',
            ], 409);
        }

        $prescription = $consultation->prescription()->create(
            array_merge($request->validated(), ['issued_by' => auth('api')->id()])
        );

        return response()->json([
            'success' => true,
            'message' => 'Prescription created successfully.',
            'data'    => new PrescriptionResource($prescription->load('consultation.medicalRecord.patient', 'issuedBy')),
        ], 201);
    }

    public function show(Prescription $prescription): JsonResponse
    {
        $this->authorize('view', $prescription);

        $prescription->load(['consultation.medicalRecord.patient', 'issuedBy']);

        return response()->json([
            'success' => true,
            'data'    => new PrescriptionResource($prescription),
        ]);
    }

    public function update(StorePrescriptionRequest $request, Prescription $prescription): JsonResponse
    {
        $this->authorize('update', $prescription);

        $prescription->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Prescription updated successfully.',
            'data'    => new PrescriptionResource($prescription->fresh(['consultation.medicalRecord.patient', 'issuedBy'])),
        ]);
    }

    public function destroy(Prescription $prescription): JsonResponse
    {
        $this->authorize('delete', $prescription);

        $prescription->delete();

        return response()->json([
            'success' => true,
            'message' => 'Prescription deleted successfully.',
        ]);
    }

    public function downloadPdf(Prescription $prescription): Response
    {
        $this->authorize('view', $prescription);

        $prescription->load(['consultation.medicalRecord.patient', 'issuedBy']);

        $pdf = Pdf::loadView('prescriptions.pdf', compact('prescription'))
            ->setPaper('a4', 'portrait');

        $filename = "prescription-{$prescription->id}-{$prescription->created_at->format('Ymd')}.pdf";

        return $pdf->download($filename);
    }
}
