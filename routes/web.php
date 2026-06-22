<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\DeliveryProcessController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\CourierController;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsAdminOrApoteker;
use App\Http\Middleware\IsApoteker;
use App\Http\Middleware\IsKurir;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Registration Routes
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::middleware(['auth'])->group(function () {
    // Dashboard - Semua role
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile - Semua role bisa edit profil sendiri
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // ============ ADMIN-ONLY ROUTES (Users, Couriers, etc.) ============
    Route::middleware([IsAdmin::class])->group(function () {
        // User Management
        Route::resource('users', UserController::class);
        Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        
        // Courier Management
        Route::get('/couriers/performance/index', [CourierController::class, 'performanceIndex'])->name('couriers.performance-index');
        Route::resource('couriers', CourierController::class);
        Route::get('/couriers/{courier}/performance', [CourierController::class, 'performance'])->name('couriers.performance');
        Route::post('/couriers/{courier}/assign-delivery', [CourierController::class, 'assignDelivery'])->name('couriers.assign-delivery');
        
        // Admin specific exports
        Route::get('/patients/{patient}/export', [PatientController::class, 'export'])->name('patients.export');
        Route::get('/deliveries/{delivery}/export', [DeliveryController::class, 'export'])->name('deliveries.export');
    });
    
    // ============ APOTEKER ROUTES ============
    Route::middleware([IsApoteker::class])->group(function () {
        // Hanya route spesifik apoteker yang tersisa (jika ada)
    });
    
    // ============ KURIR ROUTES ============
    Route::middleware([IsKurir::class])->group(function () {
        // Delivery Process
        Route::prefix('delivery-process')->name('delivery-process.')->group(function () {
            Route::get('/', [DeliveryProcessController::class, 'index'])->name('index');
            Route::post('/select', [DeliveryProcessController::class, 'selectDelivery'])->name('select');
            Route::get('/{assessmentId}/route', [DeliveryProcessController::class, 'showRoute'])->name('route');
            // Legacy route for backward compatibility
            Route::get('/route/{assessmentId}', function($assessmentId){
                return redirect()->route('delivery-process.route', $assessmentId);
            })->name('legacy.route');
            Route::post('/{assessmentId}/start', [DeliveryProcessController::class, 'startDelivery'])->name('start');
            Route::post('/{assessmentId}/location', [DeliveryProcessController::class, 'updateLocation'])->name('location');
            Route::post('/{assessmentId}/arrival', [DeliveryProcessController::class, 'markArrival'])->name('arrival');
            Route::get('/{assessmentId}/assessment', [DeliveryProcessController::class, 'showAssessment'])->name('assessment');
            Route::post('/{assessmentId}/assessment', [DeliveryProcessController::class, 'submitAssessment'])->name('submit');
            Route::get('/{assessmentId}/complete', [DeliveryProcessController::class, 'showComplete'])->name('complete');
            Route::post('/{assessmentId}/cancel', [DeliveryProcessController::class, 'cancelDelivery'])->name('cancel');
            Route::get('/{deliveryId}/details', [DeliveryProcessController::class, 'getDeliveryDetails'])->name('details');
            Route::post('/{assessmentId}/calculate-route', [DeliveryProcessController::class, 'calculateRoute'])->name('calculate-route');
        });
        
        // My Deliveries
        Route::get('/my-deliveries', [DeliveryProcessController::class, 'myDeliveries'])->name('my-deliveries');
        Route::get('/my-deliveries/{delivery}/print', [DeliveryProcessController::class, 'printDeliveryProof'])->name('my-deliveries.print');
        Route::get('/my-deliveries/{delivery}', [DeliveryProcessController::class, 'myDeliveryDetail'])->name('my-deliveries.detail');
    });
    
    // ============ SHARED ROUTES (Admin + Apoteker) ============
    Route::middleware([IsAdminOrApoteker::class])->group(function () {
        // Patient Management
        Route::resource('patients', PatientController::class);
        Route::get('/patients/{patient}/history', [PatientController::class, 'history'])->name('patients.history');
        Route::get('/patients/{patient}/print', [PatientController::class, 'printLabel'])->name('patients.print');
        Route::post('/patients/{patient}/address', [PatientController::class, 'updateAddress'])->name('patients.update-address');
        
        // Prescription Management
        Route::resource('prescriptions', PrescriptionController::class);
        Route::get('/prescriptions/{prescription}/verify', [PrescriptionController::class, 'verify'])->name('prescriptions.verify');
        Route::get('/prescriptions/{prescription}/print', [PrescriptionController::class, 'printLabels'])->name('prescriptions.print');
        Route::post('/prescriptions/{prescription}/approve', [PrescriptionController::class, 'approve'])->name('prescriptions.approve');
        Route::post('/prescriptions/{prescription}/reject', [PrescriptionController::class, 'reject'])->name('prescriptions.reject');
        Route::post('/prescriptions/{prescription}/update-status', [PrescriptionController::class, 'updateStatus'])->name('prescriptions.update-status');
        
        // Delivery Management
        Route::resource('deliveries', DeliveryController::class);
        Route::post('/deliveries/{delivery}/assign', [DeliveryController::class, 'assignCourier'])->name('deliveries.assign');
        Route::get('/deliveries/{delivery}/track', [DeliveryController::class, 'track'])->name('deliveries.track');
        Route::post('/deliveries/{delivery}/status', [DeliveryController::class, 'updateStatus'])->name('deliveries.update-status');
        Route::get('/deliveries/{delivery}/print', [DeliveryController::class, 'printLabel'])->name('deliveries.print');
        Route::get('/deliveries/{delivery}/print-report', [DeliveryController::class, 'printReport'])->name('deliveries.print-report');
        
        // Reports Management (Unified)
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/users', [ReportController::class, 'userReport'])->name('users');
            Route::get('/deliveries', [ReportController::class, 'deliveryReport'])->name('deliveries');
            Route::get('/prescriptions', [ReportController::class, 'prescriptionReport'])->name('prescriptions');
            Route::match(['get', 'post'], '/generate', [ReportController::class, 'generateReport'])->name('generate');
            Route::post('/export', [ReportController::class, 'exportReport'])->name('export');
            Route::get('/quick/{type}/{range}', [ReportController::class, 'quickReport'])->name('quick');
        });

        // Alternative routes untuk backward compatibility
        Route::get('/patients-all', [PatientController::class, 'indexAll'])->name('patients-all.index');
        Route::get('/deliveries-all', [DeliveryController::class, 'indexAll'])->name('deliveries-all.index');
    });
    
    // ============ MAP ROUTES ============
    Route::middleware(['can:canDeliverPackage'])->prefix('map')->name('map.')->group(function () {
        Route::get('/', [MapController::class, 'showMap'])->name('index');
        Route::get('/navigate/{delivery}', [MapController::class, 'startNavigation'])->name('navigate');
        Route::get('/navigate-real/{delivery}', [MapController::class, 'showRealTimeNavigation'])->name('navigate.real');
        Route::get('/complete-delivery/{delivery}', [MapController::class, 'completeDelivery'])->name('complete-delivery');
        Route::get('/api/deliveries/today', [MapController::class, 'getTodayDeliveries']);
        Route::get('/api/deliveries/{delivery}/route', [MapController::class, 'getRouteToDestination']);
        Route::post('/api/deliveries/{delivery}/arrived', [MapController::class, 'markAsArrived']);
        Route::post('/api/deliveries/{delivery}/status', [MapController::class, 'updateDeliveryStatus']);
    });
    
    // ============ API ROUTES ============
    Route::middleware(['can:canDeliverPackage'])->prefix('api')->group(function () {
        Route::get('/deliveries/today', [MapController::class, 'getTodayDeliveries']);
        Route::get('/deliveries/{delivery}/route', [MapController::class, 'getNavigationRoute']);
        Route::get('/deliveries/{delivery}/details', [MapController::class, 'getDeliveryDetails']);
        Route::post('/deliveries/{delivery}/arrived', [MapController::class, 'markAsArrived']);
        Route::post('/deliveries/{delivery}/status', [MapController::class, 'updateDeliveryStatus']);
        Route::get('/deliveries/{delivery}/tracking-data', [\App\Http\Controllers\TrackingController::class, 'getTrackingData'])->name('deliveries.tracking-data');
    });

    // Notifications
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::get('/notifications/{id}/mark-as-read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');

    // ============ RADIOLOGY ROUTES ============
    Route::prefix('radiology')->name('radiology.')->group(function () {
        Route::get('/', [App\Http\Controllers\RadiologyController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\RadiologyController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\RadiologyController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\RadiologyController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [App\Http\Controllers\RadiologyController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\RadiologyController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\RadiologyController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/send/{channel}', [App\Http\Controllers\RadiologyController::class, 'send'])->name('send');
        
        // Chat simulator routes
        Route::get('/chat/center', [App\Http\Controllers\RadiologyController::class, 'chatIndex'])->name('chat-center');
        Route::get('/chat/{id}/history', [App\Http\Controllers\RadiologyController::class, 'chatShow'])->name('chat-show');
        Route::post('/chat/{id}/send', [App\Http\Controllers\RadiologyController::class, 'chatSend'])->name('chat-send');
        Route::post('/chat/{id}/simulate', [App\Http\Controllers\RadiologyController::class, 'simulateReply'])->name('chat-simulate');
    });
});

// Public Radiology Report Portal
Route::get('/report/radiology/{share_token}', [App\Http\Controllers\RadiologyController::class, 'publicReport'])
    ->name('radiology.public-report');
Route::get('/report/radiology/{share_token}/pdf', [App\Http\Controllers\RadiologyController::class, 'publicReportPdf'])
    ->name('radiology.public-report.pdf');