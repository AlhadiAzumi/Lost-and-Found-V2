<?php

use App\Http\Controllers\Admin\AdminWorkflowController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CampusController;
use App\Http\Controllers\CampusApplicationController;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Platform\CampusManagementController;
use App\Http\Controllers\UserHistoryController;
use App\Http\Middleware\EnsureUserBelongsToCampus;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\EnsurePlatformAdmin;
use Illuminate\Support\Facades\Route;

Route::get('/', [CampusController::class, 'index'])->name('campuses.index');
Route::post('/campus-applications', [CampusApplicationController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('campus-applications.store');
Route::get('/campus-applications/{application}/setup/{token}', [CampusApplicationController::class, 'setup'])
    ->name('campus-applications.setup');
Route::post('/campus-applications/{application}/setup/{token}', [CampusApplicationController::class, 'completeSetup'])
    ->middleware('throttle:5,1')
    ->name('campus-applications.setup.complete');
Route::get('/catalog', [ItemController::class, 'catalog'])->name('catalog');
Route::get('/barang/{item}', [ItemController::class, 'show'])->name('items.show');

Route::middleware('auth')->group(function (): void {
    Route::get('/items/create', [ItemController::class, 'create'])->name('items.create');
    Route::post('/items', [ItemController::class, 'store'])->name('items.store');
    Route::get('/items/{item}/edit', [ItemController::class, 'edit'])->name('items.edit');
    Route::put('/items/{item}', [ItemController::class, 'update'])->name('items.update');
    Route::post('/items/{item}/close', [ItemController::class, 'close'])->name('items.close');
    Route::get('/items/{item}/claim', [ClaimController::class, 'create'])->name('claims.create');
    Route::post('/items/{item}/claim', [ClaimController::class, 'store'])->name('claims.store');
    Route::get('/claims/{claim}/proof', [ClaimController::class, 'proof'])->name('claims.proof');
    Route::get('/my/history', [UserHistoryController::class, 'index'])->name('my.history');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('/password', [PasswordController::class, 'update'])->name('password.update');
    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
});

Route::middleware(['auth', EnsureUserIsAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/dashboard', [AdminWorkflowController::class, 'dashboard'])->name('dashboard');
        Route::get('/reports', [AdminWorkflowController::class, 'reports'])->name('reports');
        Route::post('/reports/{item}/status', [AdminWorkflowController::class, 'moderate'])->name('reports.status');
        Route::get('/claims', [AdminWorkflowController::class, 'claims'])->name('claims');
        Route::post('/claims/{claim}/verify', [AdminWorkflowController::class, 'verify'])->name('claims.verify');
        Route::get('/handovers', [AdminWorkflowController::class, 'handovers'])->name('handovers');
        Route::post('/handovers/{claim}', [AdminWorkflowController::class, 'processHandover'])->name('handovers.process');
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
        Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');
        Route::post('/locations', [LocationController::class, 'store'])->name('locations.store');
        Route::put('/locations/{location}', [LocationController::class, 'update'])->name('locations.update');
        Route::delete('/locations/{location}', [LocationController::class, 'destroy'])->name('locations.destroy');
    });

Route::middleware(['auth', EnsurePlatformAdmin::class])
    ->prefix('platform/campuses')
    ->name('platform.campuses.')
    ->group(function (): void {
        Route::get('/', [CampusManagementController::class, 'index'])->name('index');
        Route::post('/', [CampusManagementController::class, 'store'])->name('store');
        Route::post('/applications/{application}/approve', [CampusManagementController::class, 'approve'])->name('applications.approve');
        Route::post('/applications/{application}/reject', [CampusManagementController::class, 'reject'])->name('applications.reject');
        Route::post('/applications/{application}/resend-invitation', [CampusManagementController::class, 'resendInvitation'])->name('applications.resend');
        Route::patch('/{campus}/status', [CampusManagementController::class, 'updateStatus'])->name('status');
    });

Route::middleware(['auth'])->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::prefix('kampus/{campus:slug}')
    ->name('campus.')
    ->group(function (): void {
        Route::get('/', [ItemController::class, 'catalog'])->name('home');
        Route::get('/catalog', [ItemController::class, 'catalog'])->name('catalog');
        Route::get('/barang/{item}', [ItemController::class, 'show'])->name('items.show');

        Route::middleware('guest')->group(function (): void {
            Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
            Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');
            Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
            Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:10,1');
        });

        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
            ->middleware('auth')
            ->name('logout');

        Route::middleware(['auth', EnsureUserBelongsToCampus::class])->group(function (): void {
            Route::get('/lost/create', [ItemController::class, 'create'])->defaults('type', 'lost')->name('reports.lost.create');
            Route::get('/found/create', [ItemController::class, 'create'])->defaults('type', 'found')->name('reports.found.create');
            Route::post('/reports', [ItemController::class, 'store'])->name('reports.store');
            Route::get('/my/items/{item}/edit', [ItemController::class, 'edit'])->name('items.edit');
            Route::put('/my/items/{item}', [ItemController::class, 'update'])->name('items.update');
            Route::post('/my/items/{item}/close', [ItemController::class, 'close'])->name('items.close');
            Route::get('/barang/{item}/claim', [ClaimController::class, 'create'])->name('claims.create');
            Route::post('/barang/{item}/claim', [ClaimController::class, 'store'])->name('claims.store');
            Route::get('/claims/{claim}/proof', [ClaimController::class, 'proof'])->name('claims.proof');
            Route::get('/my/history', [UserHistoryController::class, 'index'])->name('my.history');
            Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
            Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
            Route::put('/password', [PasswordController::class, 'update'])->name('password.update');
            Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
                ->middleware('throttle:6,1')
                ->name('verification.send');
            Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
            Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
            Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

            Route::middleware(EnsureUserIsAdmin::class)
                ->prefix('admin')
                ->name('admin.')
                ->group(function (): void {
                    Route::get('/', [AdminWorkflowController::class, 'dashboard'])->name('dashboard');
                    Route::get('/reports', [AdminWorkflowController::class, 'reports'])->name('reports');
                    Route::post('/reports/{item}/status', [AdminWorkflowController::class, 'moderate'])->name('reports.status');
                    Route::get('/claims', [AdminWorkflowController::class, 'claims'])->name('claims');
                    Route::post('/claims/{claim}/verify', [AdminWorkflowController::class, 'verify'])->name('claims.verify');
                    Route::get('/handovers', [AdminWorkflowController::class, 'handovers'])->name('handovers');
                    Route::post('/handovers/{claim}', [AdminWorkflowController::class, 'processHandover'])->name('handovers.process');
                    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
                    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
                    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
                    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
                    Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');
                    Route::post('/locations', [LocationController::class, 'store'])->name('locations.store');
                    Route::put('/locations/{location}', [LocationController::class, 'update'])->name('locations.update');
                    Route::delete('/locations/{location}', [LocationController::class, 'destroy'])->name('locations.destroy');
                });
        });
    });

require __DIR__.'/auth.php';
