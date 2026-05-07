<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\PlanningModelController;
use App\Http\Controllers\PlanningAssignmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Cp\CpController;
use App\Http\Controllers\Sup\SupController;
use App\Http\Controllers\Tc\TcController;
use App\Http\Controllers\EmployeeController;
use App\Models\User;
use App\Notifications\PlanningPublicated;
use App\Notifications\NewEmployeeAdded;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    $user = auth()->user();

    $route = match ($user->role->name) {
        'admin' => 'admin.dashboard',
        'cp' => 'cp.dashboard',
        'sup' => 'sup.dashboard',
        'tc' => 'tc.dashboard',
        default => abort(403),
    };

    return redirect()->route($route);
});

// 2. Routes accessibles uniquement aux invités (non-connectés)
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return Inertia::render('Auth/Login', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
        ]);
    })->name('login');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});



///////////////////////Routes STEVEN ////////////////////////////////////////////////







///////////////////////Routes MAXSON et CEDRIC ////////////////////////////////////////////////

// Admin   acces total via toutes les routes 

// ==========================================
// ADMIN - GOD MODE (aucune restriction)
// ==========================================
Route::middleware(['auth', 'admin'])->group(function () {
    // Modèles de planning : CRUD complet
    Route::get('/admin/planning-models', [PlanningModelController::class, 'index'])->name('admin.planning-models.index');
    Route::get('/admin/planning-models/create', [PlanningModelController::class, 'create'])->name('admin.planning-models.create');
    Route::post('/admin/planning-models', [PlanningModelController::class, 'store'])->name('admin.planning-models.store');
    Route::get('/admin/planning-models/{planningModel}', [PlanningModelController::class, 'show'])->name('admin.planning-models.show');
    Route::get('/admin/planning-models/{planningModel}/edit', [PlanningModelController::class, 'edit'])->name('admin.planning-models.edit');
    Route::put('/admin/planning-models/{planningModel}', [PlanningModelController::class, 'update'])->name('admin.planning-models.update');
    Route::delete('/admin/planning-models/{planningModel}', [PlanningModelController::class, 'destroy'])->name('admin.planning-models.destroy');

    // Affectations : CRUD complet + actions critiques
    Route::get('/admin/planning-assignments', [PlanningAssignmentController::class, 'index'])->name('admin.planning-assignments.index');
    Route::get('/admin/planning-assignments/create', [PlanningAssignmentController::class, 'create'])->name('admin.planning-assignments.create');
    Route::post('/admin/planning-assignments', [PlanningAssignmentController::class, 'store'])->name('admin.planning-assignments.store');
    Route::get('/admin/planning-assignments/{planningAssignment}', [PlanningAssignmentController::class, 'show'])->name('admin.planning-assignments.show');
    Route::get('/admin/planning-assignments/{planningAssignment}/edit', [PlanningAssignmentController::class, 'edit'])->name('admin.planning-assignments.edit');
    Route::put('/admin/planning-assignments/{planningAssignment}', [PlanningAssignmentController::class, 'update'])->name('admin.planning-assignments.update');
    Route::delete('/admin/planning-assignments/{planningAssignment}', [PlanningAssignmentController::class, 'destroy'])->name('admin.planning-assignments.destroy');
    
    // Actions critiques (peut forcer n'importe quel statut)
    Route::patch('/admin/planning-assignments/{planningAssignment}/validate', [PlanningAssignmentController::class, 'validateAssignment'])->name('admin.planning-assignments.validate');
    Route::patch('/admin/planning-assignments/{planningAssignment}/suspend', [PlanningAssignmentController::class, 'suspend'])->name('admin.planning-assignments.suspend');
    Route::patch('/admin/planning-assignments/{planningAssignment}/terminate', [PlanningAssignmentController::class, 'terminate'])->name('admin.planning-assignments.terminate');
    Route::get('/admin/planning-assignments/{planningAssignment}/history', [PlanningAssignmentController::class, 'history'])->name('admin.planning-assignments.history');

    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::post('/admin/no_users', [AdminController::class, 'no_users'])->name('no_users');
});

// ==========================================
// CHEF DE PLATEAU (CP) - Utilisateur principal
// ==========================================
Route::middleware(['auth', 'cp'])->group(function () {
    // Modèles de planning : Créer/Modifier/Supprimer (si non utilisé)
    Route::get('/planning-models', [PlanningModelController::class, 'index'])->name('cp.planning-models.index');
    Route::get('/planning-models/create', [PlanningModelController::class, 'create'])->name('cp.planning-models.create');
    Route::post('/planning-models', [PlanningModelController::class, 'store'])->name('cp.planning-models.store');
    Route::get('/planning-models/{planningModel}', [PlanningModelController::class, 'show'])->name('cp.planning-models.show');
    Route::get('/planning-models/{planningModel}/edit', [PlanningModelController::class, 'edit'])->name('cp.planning-models.edit');
    Route::put('/planning-models/{planningModel}', [PlanningModelController::class, 'update'])->name('cp.planning-models.update');
    Route::delete('/planning-models/{planningModel}', [PlanningModelController::class, 'destroy'])->name('cp.planning-models.destroy');

    // Affectations : Créer/Modifier (pour ses employés) + actions critiques
    Route::get('/planning-assignments', [PlanningAssignmentController::class, 'index'])->name('cp.planning-assignments.index');
    Route::get('/planning-assignments/create', [PlanningAssignmentController::class, 'create'])->name('cp.planning-assignments.create');
    Route::post('/planning-assignments', [PlanningAssignmentController::class, 'store'])->name('cp.planning-assignments.store');
    Route::get('/planning-assignments/{planningAssignment}', [PlanningAssignmentController::class, 'show'])->name('cp.planning-assignments.show');
    Route::get('/planning-assignments/{planningAssignment}/edit', [PlanningAssignmentController::class, 'edit'])->name('cp.planning-assignments.edit');
    Route::put('/planning-assignments/{planningAssignment}', [PlanningAssignmentController::class, 'update'])->name('cp.planning-assignments.update');
    // PAS DE SUPPRESSION D'AFFECTATION POUR CP (doit utiliser suspendre/terminer)
    
    // Actions critiques (workflow principal)
    Route::patch('/planning-assignments/{planningAssignment}/validate', [PlanningAssignmentController::class, 'validateAssignment'])->name('cp.planning-assignments.validate');
    Route::patch('/planning-assignments/{planningAssignment}/suspend', [PlanningAssignmentController::class, 'suspend'])->name('cp.planning-assignments.suspend');
    Route::patch('/planning-assignments/{planningAssignment}/terminate', [PlanningAssignmentController::class, 'terminate'])->name('cp.planning-assignments.terminate');
    Route::get('/planning-assignments/{planningAssignment}/history', [PlanningAssignmentController::class, 'history'])->name('cp.planning-assignments.history');

    Route::get('/cp/dashboard', [CpController::class, 'index'])->name('cp.dashboard');
});

// ==========================================
// SUPERVISEUR (SUP) - Gestionnaire de proximité
// ==========================================
Route::middleware(['auth', 'sup'])->group(function () {
    // Modèles de planning : LECTURE SEULEMENT
    Route::get('/sup/planning-models', [PlanningModelController::class, 'index'])->name('sup.planning-models.index');
    Route::get('/sup/planning-models/{planningModel}', [PlanningModelController::class, 'show'])->name('sup.planning-models.show');
    
    // Affectations : LECTURE de ses TC uniquement
    Route::get('/sup/planning-assignments', [PlanningAssignmentController::class, 'indexSup'])->name('sup.planning-assignments.index');
    Route::get('/sup/planning-assignments/{planningAssignment}', [PlanningAssignmentController::class, 'show'])->name('sup.planning-assignments.show');
    Route::get('/sup/planning-assignments/{planningAssignment}/history', [PlanningAssignmentController::class, 'history'])->name('sup.planning-assignments.history');
    
    // PAS DE MODIFICATION/VALIDATION POUR SUP
    
    Route::get('/sup/dashboard', [SupController::class, 'index'])->name('sup.dashboard');
});

// ==========================================
// TÉLÉCONSEILLER (TC) - Utilisateur final
// ==========================================
Route::middleware(['auth', 'tc'])->group(function () {
    // Accès UNIQUEMENT à sa propre fiche de planning
    Route::get('/tc/my-planning', [TcController::class, 'myPlanning'])->name('tc.my-planning');
    
    // PAS D'ACCÈS AUX MODÈLES DE PLANNING
    // PAS D'ACCÈS AUX AUTRES EMPLOYÉS
    // LECTURE SEULEMENT de son planning validé
    
    Route::get('/tc/dashboard', [TcController::class, 'index'])->name('tc.dashboard');
    Route::get('/tc/planning', [TcController::class, 'planning'])->name('tc.planning');
});

///////////////////////Routes STEVEN ////////////////////////////////////////////////

///////////////////////Routes OTHITHI ////////////////////////////////////////////////
// Routes pour la gestion des employés supprimés (trash/historique)
Route::get('/employees-trash', [EmployeeController::class, 'trash'])
    ->name('employees.trash');

Route::patch('/employees/{id}/restore', [EmployeeController::class, 'restore'])
    ->name('employees.restore');

Route::delete('/employees/{id}/force-delete', [EmployeeController::class, 'forceDelete'])
    ->name('employees.forceDelete');

Route::resource('employees', EmployeeController::class);

Route::patch('/employees/{employee}/status', [EmployeeController::class, 'changeStatus'])
    ->name('employees.changeStatus');

Route::get('/employees/{employee}/history', [EmployeeController::class, 'history'])
    ->name('employees.history');

Route::get('/employees-dialog', [EmployeeController::class, 'index'])
    ->name('employees.indexDialog');





///////////////////////Routes DYLAN ////////////////////////////////////////////////







///////////////////////Routes ROGALEX ////////////////////////////////////////////////
Route::get('/test-notif', function () {
    auth()->user()->notify(new PlanningPublicated());
    return "Notification envoyée ! Retourne sur ton dashboard.";
});



Route::get('/notifications', function () {
    return Inertia::render('Notifications/Index', [
        'allNotifications' => auth()->user()->notifications
    ]);
})->middleware(['auth']);


// ✅ CORRECTION ICI (suppression duplication + fermeture correcte)
Route::get('/test-sidebar', function () {
    auth()->user()->notify(new NewEmployeeAdded("Marc-Antoine"));
    return back()->with('success', 'Notification créée !');
});



use App\Http\Controllers\ReportingController;

Route::middleware(['auth'])->group(function () {

    Route::get('/reporting', [ReportingController::class, 'index'])->name('reporting.index');

    Route::get('/reporting/create', [ReportingController::class, 'create'])->name('reporting.create');

    Route::post('/reporting', [ReportingController::class, 'store'])->name('reporting.store');
});

// Routes pour les dashboards
Route::middleware(['auth', 'admin'])->get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
Route::middleware(['auth', 'cp'])->get('/cp/dashboard', [CpController::class, 'index'])->name('cp.dashboard');
Route::middleware(['auth', 'sup'])->get('/sup/dashboard', [SupController::class, 'index'])->name('sup.dashboard');

require __DIR__ . '/auth.php';
