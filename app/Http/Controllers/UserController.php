<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with(['role'])
            ->where('status', 'active')
            ->paginate(10);

        return Inertia::render('Users/Index', [
            'users' => $users,
            'roles' => Role::whereIn('name', ['cp', 'sup', 'tc'])->get(),
        ]);
    }

    public function deactivated()
    {
        $users = User::with(['role'])
            ->where('status', 'inactive')
            ->paginate(15);
        return Inertia::render('Users/Deactivated', [
            'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $employe = null;

        if ($request->filled('employe_id')) {
            $employe = Employee::with('position')
                ->whereNull('user_id')
                ->findOrFail($request->employe_id);
        }

        return Inertia::render('Users/No_users', [
            'employe' => $employe,
            'roles' => Role::whereIn('name', ['cp', 'sup', 'tc'])->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
public function store(Request $request)
{
    $validated = $request->validate([
        'employee_id' => ['required', 'exists:employees,id'],
        'email' => ['required', 'email', 'unique:users,email'],
        'role_id' => ['required', 'exists:roles,id'],
        'password' => ['required', 'string', 'min:6', 'confirmed'],
    ]);

    DB::transaction(function () use ($validated) {

        // 1. Vérifier employé sans compte (IMPORTANT: lock + sécurité)
        $employee = Employee::where('id', $validated['employee_id'])
            ->whereNull('user_id')
            ->firstOrFail();

        // 2. Créer user
        $user = User::create([
            'role_id' => $validated['role_id'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(),
        ]);

        // 3. Lier employee → user
        $employee->update([
            'user_id' => $user->id,
        ]);
    });

    return redirect()
        ->route('users.index')
        ->with('success', 'Compte créé avec succès');
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::with(['role'])
            ->findOrFail($id);

        return Inertia::render('Users/Index', [
            'user' => $user,
            'roles' => Role::whereIn('name', ['cp', 'sup', 'tc'])->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'password_confirmation' => ['nullable', 'string'],
        ]);

        $user->email = $validated['email'];
        $user->role_id = $validated['role_id'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()
            ->route('users.index')
            ->with('success', 'Compte mis à jour avec succès.');
    }

    public function toggleStatus(string $id)
    {
        $user = User::findOrFail($id);

        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();

        $message = $user->status === 'active' ? 'Compte réactivé avec succès.' : 'Compte désactivé avec succès.';

        return back()->with('success', $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
