<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Définir les props partagées par défaut avec toutes les pages Inertia.
     * On y inclut les messages flash pour les notifications toast.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {

        $user = $request->user();
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
               'notifications' => $request->user() 
                ? $request->user()->unreadNotifications 
                : [],

                'role' => $request->user()?->role?->name,
            ],
            // Partage des messages flash pour les toasts PrimeVue
            // Ces messages sont définis via redirect()->with('success', '...')
            'flash' => [
                'success' => $request->session()->get('success'),
                'error'   => $request->session()->get('error'),
                'info'    => $request->session()->get('info'),
                'warning' => $request->session()->get('warning'),
            ],
        ];
    }
}
