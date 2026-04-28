<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Consultation;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function kpis(): JsonResponse
    {
        try {
            $today = now()->toDateString();

            return response()->json([
                'success' => true,
                'data' => [
                    'patients_total' => Patient::query()->count(),
                    'patients_active' => Patient::query()->where('is_active', true)->count(),
                    'users_total' => User::query()->count(),
                    'users_active' => User::query()->where('is_active', true)->count(),
                    'appointments_today' => Appointment::query()->whereDate('appointment_date', $today)->count(),
                    'consultations_today' => Consultation::query()->whereDate('consultation_date', $today)->count(),
                    'prescriptions_total' => Prescription::query()->count(),
                    'generated_at' => now()->toISOString(),
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('dashboard.kpis.failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to build dashboard KPIs.',
            ], 500);
        }
    }
}
