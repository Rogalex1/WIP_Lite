<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PlanningModelController;
use App\Http\Controllers\PlanningAssignmentController;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Cp\CpController;
use App\Http\Controllers\Sup\SupController;
use App\Http\Controllers\Tc\TcController;
use App\Http\Controllers\EmployeeController;
use App\Models\User;
use App\Notifications\PlanningPublicated;
use App\Notifications\NewEmployeeAdded;
use App\Http\Controllers\UserController;
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



    ///////////////////////Routes CEDRIC ////////////////////////////////////////////////
    // --- Modèles de planning ---
    Route::resource('planning-models', PlanningModelController::class)
        ->parameters(['planning-models' => 'planningModel']);

    // --- Affectations ---
    Route::resource('planning-assignments', PlanningAssignmentController::class)
        ->parameters(['planning-assignments' => 'planningAssignment']);

    // --- Actions spéciales sur une affectation ---
    Route::patch('planning-assignments/{planningAssignment}/validate',
        [PlanningAssignmentController::class, 'validateAssignment']
    )->name('planning-assignments.validate');

    Route::patch('planning-assignments/{planningAssignment}/suspend',
        [PlanningAssignmentController::class, 'suspend']
    )->name('planning-assignments.suspend');

    Route::patch('planning-assignments/{planningAssignment}/terminate',
        [PlanningAssignmentController::class, 'terminate']
    )->name('planning-assignments.terminate');


    // --- Historique ---
    Route::get(
        'planning-assignments/{planningAssignment}/history',
        [PlanningAssignmentController::class, 'history']
    )->name('planning-assignments.history');
});



///////////////////////Routes STEVEN ////////////////////////////////////////////////


Route::middleware(['auth', 'verified'])->group(function () {
    // Cette ligne génère automatiquement : campaigns.index, campaigns.store, campaigns.update, campaigns.destroy
    Route::resource('campaigns', CampaignController::class);
});
Route::middleware(['auth'])->group(function () {
    Route::get('/assignments', [AssignmentController::class, 'index'])->name('assignments.index');
    Route::post('/assignments', [AssignmentController::class, 'store'])->name('assignments.store');
    Route::delete('/assignments/{assignment}', [AssignmentController::class, 'release'])->name('assignments.release');
});




///////////////////////Routes MAXSON ////////////////////////////////////////////////

Route::middleware(['auth', 'admin'])->group(function () {

    Route::get('/admin/dashboard', [AdminController::class, 'index'])
        ->name('admin.dashboard');

    Route::post('/admin/no_users', [AdminController::class, 'no_users'])
        ->name('no_users');
    Route::get('/admin/no_users', [AdminController::class, 'no_user'])->name('admin.no_users');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');

    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');

    Route::post('/users', [UserController::class, 'store'])->name('users.store');

    Route::get('/users/deactivated', [UserController::class, 'deactivated'])->name('users.deactivated');

    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');

    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');

    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    
    Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
});

Route::middleware(['auth', 'cp'])->group(function () {

    Route::get('/cp/dashboard', [CpController::class, 'index'])
        ->name('cp.dashboard');
});

Route::middleware(['auth', 'sup'])->group(function () {

    Route::get('/sup/dashboard', [SupController::class, 'index'])
        ->name('sup.dashboard');
});

Route::middleware(['auth', 'tc'])->group(function () {

    Route::get('/tc/dashboard', [TcController::class, 'index'])->name('tc.dashboard');
    Route::get('/tc/planning', [TcController::class, 'planning'])->name('tc.planning');
});



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

Route::middleware(['auth'])->group(function () {
    Route::resource('reporting', ReportingController::class);
});


require __DIR__ . '/auth.php';