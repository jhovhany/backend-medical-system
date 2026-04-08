<?php

use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ConsultationController;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\MedicalRecordController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\PrescriptionController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes  —  Prefix: /api
|--------------------------------------------------------------------------
*/

// ── Health check ──────────────────────────────────────────────────────────
Route::get('/health', HealthController::class)->name('health');

// ── Backward-compatible auth routes (/api/auth/*) ────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('auth.legacy.login');

    Route::middleware('jwt.auth')->group(function () {
        Route::post('/logout',  [AuthController::class, 'logout'])->name('auth.legacy.logout');
        Route::post('/refresh', [AuthController::class, 'refresh'])->name('auth.legacy.refresh');
        Route::get('/me',       [AuthController::class, 'me'])->name('auth.legacy.me');
    });
});

// ── v1 ─────────────────────────────────────────────────────────────────────
Route::prefix('v1')->group(function () {

    // ── Authentication (public) ────────────────────────────────────────────
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
    });

    // ── Protected routes ──────────────────────────────────────────────────
    Route::middleware('jwt.auth')->group(function () {

        // Auth
        Route::prefix('auth')->group(function () {
            Route::post('/logout',  [AuthController::class, 'logout'])->name('auth.logout');
            Route::post('/refresh', [AuthController::class, 'refresh'])->name('auth.refresh');
            Route::get('/me',       [AuthController::class, 'me'])->name('auth.me');
        });

        // Users
        Route::prefix('users')->group(function () {
            Route::get('/',                    [UserController::class, 'index'])->name('users.index');
            Route::post('/',                   [UserController::class, 'store'])->name('users.store');
            Route::get('/{user}',              [UserController::class, 'show'])->name('users.show');
            Route::put('/{user}',              [UserController::class, 'update'])->name('users.update');
            Route::delete('/{user}',           [UserController::class, 'destroy'])->name('users.destroy');
            Route::post('/{user}/assign-role', [UserController::class, 'assignRole'])->name('users.assign-role');
        });

        // Patients
        Route::apiResource('patients', PatientController::class);

        // Medical Records
        Route::prefix('medical-records')->group(function () {
            Route::get('/{medicalRecord}',        [MedicalRecordController::class, 'show'])->name('medical-records.show');
            Route::put('/{medicalRecord}',         [MedicalRecordController::class, 'update'])->name('medical-records.update');
        });
        Route::get('/patients/{patient}/medical-record', [MedicalRecordController::class, 'showByPatient'])
            ->name('patients.medical-record');

        // Consultations
        Route::apiResource('consultations', ConsultationController::class);

        // Prescriptions
        Route::prefix('consultations/{consultation}/prescriptions')->group(function () {
            Route::post('/',  [PrescriptionController::class, 'store'])->name('consultations.prescriptions.store');
        });
        Route::prefix('prescriptions')->group(function () {
            Route::get('/{prescription}',         [PrescriptionController::class, 'show'])->name('prescriptions.show');
            Route::put('/{prescription}',          [PrescriptionController::class, 'update'])->name('prescriptions.update');
            Route::delete('/{prescription}',       [PrescriptionController::class, 'destroy'])->name('prescriptions.destroy');
            Route::get('/{prescription}/pdf',      [PrescriptionController::class, 'downloadPdf'])->name('prescriptions.pdf');
        });

        // Appointments
        Route::apiResource('appointments', AppointmentController::class);

        // Files
        Route::prefix('files')->group(function () {
            Route::get('/',             [FileController::class, 'index'])->name('files.index');
            Route::post('/',            [FileController::class, 'store'])->name('files.store');
            Route::get('/{file}',       [FileController::class, 'show'])->name('files.show');
            Route::delete('/{file}',    [FileController::class, 'destroy'])->name('files.destroy');
            Route::get('/{file}/download', [FileController::class, 'download'])->name('files.download');
        });
    });
});
