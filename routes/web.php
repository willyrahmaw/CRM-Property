<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CRM\ActivityController;
use App\Http\Controllers\CRM\LeadController;
use App\Http\Controllers\CRM\PipelineController;
use App\Http\Controllers\CRM\SiteVisitController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Finance\CommissionController;
use App\Http\Controllers\Finance\PaymentController;
use App\Http\Controllers\Inventory\ClusterController;
use App\Http\Controllers\Inventory\ProjectController;
use App\Http\Controllers\Inventory\PropertyUnitController;
use App\Http\Controllers\Inventory\SiteplanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Sales\BookingController;
use App\Http\Controllers\Sales\NegotiationController;
use App\Http\Controllers\Settings\CommissionSettingController;
use App\Http\Controllers\Settings\UserController;
use App\Http\Controllers\Settings\WebsiteSettingController;
use App\Http\Controllers\TimezoneController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - PROPFlow Property CRM
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Authentication
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Protected CRM & ERP Routes
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // User Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
        Route::delete('/avatar', [ProfileController::class, 'destroyAvatar'])->name('avatar.destroy');
    });

    // Timezone Switcher
    Route::post('/timezone/switch', [TimezoneController::class, 'switch'])->name('timezone.switch');

    // CRM
    Route::prefix('crm')->name('crm.')->group(function () {
        Route::get('/leads', [LeadController::class, 'index'])->name('leads.index');
        Route::get('/leads/create', [LeadController::class, 'create'])->name('leads.create');
        Route::post('/leads', [LeadController::class, 'store'])->name('leads.store');
        Route::get('/leads/{lead}', [LeadController::class, 'show'])->name('leads.show');
        Route::patch('/leads/{lead}/status', [LeadController::class, 'updateStatus'])->name('leads.update-status');

        Route::get('/pipeline', [PipelineController::class, 'index'])->name('pipeline.index');
        Route::post('/pipeline/{lead}/stage', [PipelineController::class, 'updateStage'])->name('pipeline.update-stage');

        Route::post('/activities', [ActivityController::class, 'store'])->name('activities.store');

        // Site Visits & Field Surveys
        Route::get('/site-visits', [SiteVisitController::class, 'index'])->name('site-visits.index');
        Route::get('/site-visits/create', [SiteVisitController::class, 'create'])->name('site-visits.create');
        Route::post('/site-visits', [SiteVisitController::class, 'store'])->name('site-visits.store');
        Route::patch('/site-visits/{siteVisit}/status', [SiteVisitController::class, 'updateStatus'])->name('site-visits.update-status');

        Route::get('/customers', function () {
            return redirect()->route('sales.bookings.index');
        })->name('customers.index');
    });

    // Inventory
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
        Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create')->middleware('role:inventory_team');
        Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store')->middleware('role:inventory_team');
        Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
        Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit')->middleware('role:inventory_team');
        Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update')->middleware('role:inventory_team');
        Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy')->middleware('role:inventory_team');

        Route::get('/clusters', [ClusterController::class, 'index'])->name('clusters.index');
        Route::get('/clusters/create', [ClusterController::class, 'create'])->name('clusters.create')->middleware('role:inventory_team');
        Route::post('/clusters', [ClusterController::class, 'store'])->name('clusters.store')->middleware('role:inventory_team');
        Route::get('/clusters/{cluster}/edit', [ClusterController::class, 'edit'])->name('clusters.edit')->middleware('role:inventory_team');
        Route::put('/clusters/{cluster}', [ClusterController::class, 'update'])->name('clusters.update')->middleware('role:inventory_team');
        Route::delete('/clusters/{cluster}', [ClusterController::class, 'destroy'])->name('clusters.destroy')->middleware('role:inventory_team');

        Route::get('/units', [PropertyUnitController::class, 'index'])->name('units.index');
        Route::get('/units/create', [PropertyUnitController::class, 'create'])->name('units.create')->middleware('role:inventory_team');
        Route::post('/units', [PropertyUnitController::class, 'store'])->name('units.store')->middleware('role:inventory_team');
        Route::get('/units/{unit}', [PropertyUnitController::class, 'show'])->name('units.show');

        Route::get('/siteplan', [SiteplanController::class, 'index'])->name('siteplan.index');
        Route::get('/siteplan/{project}', [SiteplanController::class, 'show'])->name('siteplan.show');
        Route::get('/siteplan/unit/{unit}', [SiteplanController::class, 'getUnitDetail'])->name('siteplan.unit');
    });

    // Sales
    Route::prefix('sales')->name('sales.')->group(function () {
        Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
        Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
        Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
        Route::post('/bookings/{booking}/approve', [BookingController::class, 'approve'])->name('bookings.approve')->middleware('role:managerial,finance_team');
        Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel')->middleware('role:managerial,finance_team');

        Route::get('/negotiations', [NegotiationController::class, 'index'])->name('negotiations.index');
        Route::get('/negotiations/create', [NegotiationController::class, 'create'])->name('negotiations.create');
        Route::post('/negotiations', [NegotiationController::class, 'store'])->name('negotiations.store');
        Route::get('/negotiations/{negotiation}', [NegotiationController::class, 'show'])->name('negotiations.show');
        Route::post('/negotiations/{negotiation}/approve', [NegotiationController::class, 'approve'])->name('negotiations.approve')->middleware('role:managerial');
        Route::post('/negotiations/{negotiation}/reject', [NegotiationController::class, 'reject'])->name('negotiations.reject')->middleware('role:managerial');
    });

    // Finance
    Route::prefix('finance')->name('finance.')->group(function () {
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index')->middleware('role:finance_team');
        Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
        Route::post('/payments/{payment}/verify', [PaymentController::class, 'verify'])->name('payments.verify')->middleware('role:finance_team');

        Route::get('/mortgages', function () {
            return redirect()->route('finance.payments.index')->with('info', 'Modul KPR terhubung langsung pada data pemesanan.');
        })->name('mortgages.index')->middleware('role:finance_team');

        Route::get('/commissions', [CommissionController::class, 'index'])->name('commissions.index');
        Route::post('/bookings/{booking}/commissions/generate', [CommissionController::class, 'generate'])->name('commissions.generate')->middleware('role:managerial,finance_team');
        Route::post('/commissions/{commission}/approve', [CommissionController::class, 'approve'])->name('commissions.approve')->middleware('role:managerial');
        Route::post('/commissions/{commission}/pay', [CommissionController::class, 'markAsPaid'])->name('commissions.pay')->middleware('role:finance_team');
    });

    // Reports
    Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales')->middleware('role:managerial,finance_team');

    // Settings
    Route::prefix('settings')->name('settings.')->middleware('role:managerial')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

        // Website Settings for Company Owner & Super Admin
        Route::get('/website', [WebsiteSettingController::class, 'index'])->name('website.index')->middleware('role:company_owner');
        Route::put('/website', [WebsiteSettingController::class, 'update'])->name('website.update')->middleware('role:company_owner');

        // Commission Settings for Company Owner & Super Admin
        Route::get('/commissions', [CommissionSettingController::class, 'index'])->name('commissions.index')->middleware('role:company_owner');
        Route::put('/commissions', [CommissionSettingController::class, 'update'])->name('commissions.update')->middleware('role:company_owner');
    });
});
