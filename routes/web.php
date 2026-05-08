<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Cp\CpController;
use App\Http\Controllers\Sup\SupController;
use App\Http\Controllers\Tc\TcController;
use App\Http\Controllers\EmployeeController;
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

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    
    

///////////////////////Routes STEVEN ////////////////////////////////////////////////


Route::middleware(['auth', 'verified'])->group(function () {
    // Gestion des Campagnes (CRUD classique)
    Route::resource('campaigns', CampaignController::class);

    // Gestion des Affectations (Plateau de Production)
    // C'est ici que tes vues Index.vue, CPIndex.vue et SUPIndex.vue puisent leurs données
    Route::get('/assignments', [AssignmentController::class, 'index'])->name('assignments.index');
    Route::post('/assignments', [AssignmentController::class, 'store'])->name('assignments.store');
    
    // Correction cruciale : Laravel resource/delete attend un DELETE.
    // Ton bouton dans la vue utilise router.delete(route('assignments.release', id))
    Route::delete('/assignments/{assignment}/release', [AssignmentController::class, 'release'])->name('assignments.release');
});







///////////////////////Routes MAXSON ////////////////////////////////////////////////

Route::middleware(['auth', 'admin'])->group(function () {

    Route::get('/admin/dashboard', [AdminController::class, 'index'])
        ->name('admin.dashboard');
    Route::get('/admin/history', [AdminController::class, 'history'])
        ->name('admin.history');

    Route::post('/admin/no_users', [AdminController::class, 'no_users'])->name('no_users');
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

    Route::get('/tc/dashboard', [TcController::class, 'index'])
        ->name('tc.dashboard');
});







///////////////////////Routes OTHITHI ////////////////////////////////////////////////
// Routes pour la gestion des employés
Route::resource('employees', EmployeeController::class);
Route::patch('/employees/{employee}/status', [EmployeeController::class, 'changeStatus'])
    ->name('employees.changeStatus');
Route::get('/employees/{employee}/history', [EmployeeController::class, 'history'])
    ->name('employees.history');
});









///////////////////////Routes CEDRIC ////////////////////////////////////////////////









///////////////////////Routes DYLAN ////////////////////////////////////////////////











///////////////////////Routes ROGALEX ////////////////////////////////////////////////


use App\Notifications\PlanningPublicated;
use App\Models\User;

Route::get('/test-notif', function () {
    $user = auth()->user(); // C'est toi
    $user->notify(new PlanningPublicated());
    return "Notification envoyée ! Retourne sur ton dashboard.";
});



// Route::get('/admin/dashboard', function () {
//     return Inertia::render('Dashboard/AdminDashboard'); // Le chemin vers ton fichier .vue
// })->middleware(['auth'])->name('admin.dashboard');


// Route::get('/notifications', function () {
//     return Inertia::render('Notifications/Index', [
//         'allNotifications' => auth()->user()->notifications // Récupère tout l'historique
//     ]);
// })->middleware(['auth']);


use App\Http\Controllers\DashboardController;

// Route::get('/admin/dashboard', DashboardController::class)->middleware(['auth'])->name('admin.dashboard');


use App\Notifications\NewEmployeeAdded;


Route::get('/test-sidebar', function () {
    $user = auth()->user(); // L'utilisateur connecté (toi)

    // On envoie la notification à toi-même pour tester
    $user->notify(new NewEmployeeAdded("Marc-Antoine"));

    return back()->with('success', 'Notification créée !');
});




// Route::get('/cp/dashboard', function () {
//     return Inertia::render('CP/Dashboard', [
//         // On envoie des données de test pour remplir tes composants
//         'stats' => [
//             'teams_count' => 12,
//             'pending_plannings' => 4
//         ],
//         'my_teams' => [
//             ['id' => 1, 'name' => 'Alice Martin', 'initials' => 'AM', 'campaign' => 'Project Alpha'],
//             ['id' => 2, 'name' => 'Kevin Durand', 'initials' => 'KD', 'campaign' => 'Project Beta'],
//             ['id' => 3, 'name' => 'Sara Ben', 'initials' => 'SB', 'campaign' => 'Project Alpha'],
//         ]
//     ]);
// })->middleware(['auth'])->name('cp.dashboard');


require __DIR__ . '/auth.php';
