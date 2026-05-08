<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue';

defineProps({
    canResetPassword: { type: Boolean },
    status: { type: String },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        replace:true,// Ceci remplace l'entrée /login dans l'historique par /dashboard
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Connexion - WIP LITE" />

    <div class="flex min-h-screen bg-white font-sans antialiased">
        <!-- Section Gauche : Visuel & Branding -->
        <div class="relative hidden w-1/2 flex-col justify-between bg-[#FF7A1A] p-12 lg:flex overflow-hidden">
            <!-- Cercles décoratifs en arrière-plan -->
            <div class="absolute -left-20 -top-20 h-96 w-96 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute -bottom-20 -right-20 h-96 w-96 rounded-full bg-orange-400/20 blur-3xl"></div>

            <div class="relative z-10">
                <!-- Ton Logo SVG -->
                <div class="w-16 h-16 shadow-2xl rounded-2xl overflow-hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                        <defs>
                            <linearGradient id="orangeGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#FF9E4F" />
                                <stop offset="100%" stop-color="#FF7A1A" />
                            </linearGradient>
                        </defs>
                        <rect width="512" height="512" rx="112" fill="url(#orangeGrad)" />
                        <g>
                            <path d="M 156 220 A 100 100 0 0 1 356 220" fill="none" stroke="#FFFFFF" stroke-width="14" stroke-linecap="round" />
                            <rect x="138" y="210" width="18" height="45" rx="9" fill="#FFFFFF" />
                            <rect x="356" y="210" width="18" height="45" rx="9" fill="#FFFFFF" />
                            <circle cx="256" cy="220" r="55" fill="none" stroke="#FFFFFF" stroke-width="12" />
                            <text x="256" y="395" font-family="sans-serif" font-size="36" font-weight="800" fill="#FFFFFF" text-anchor="middle" letter-spacing="4">WIP</text>
                            <text x="256" y="435" font-family="sans-serif" font-size="22" font-weight="300" fill="#FFFFFF" text-anchor="middle" letter-spacing="8">LITE</text>
                        </g>
                    </svg>
                </div>
            </div>

            <div class="relative z-10 mt-auto">
                <h1 class="text-5xl font-bold leading-tight text-white">
                    Gérez vos projets <br />
                    <span class="italic text-orange-100 font-serif">avec précision.</span>
                </h1>
                <p class="mt-6 max-w-md text-lg text-orange-50 font-medium">
                    L'outil ultime pour le suivi de performance et l'analyse de vos campagnes en temps réel.
                </p>
            </div>

            <div class="relative z-10 mt-12 flex gap-6 text-sm font-medium text-orange-100">
                <span>© 2026 WIP LITE</span>
                <a href="#" class="hover:text-white transition">Confidentialité</a>
            </div>
        </div>

        <!-- Section Droite : Formulaire -->
        <div class="flex w-full items-center justify-center p-8 lg:w-1/2">
            <div class="w-full max-w-md">
                <!-- Logo mobile uniquement -->
                <div class="mb-8 flex justify-center lg:hidden">
                     <div class="w-12 h-12">
                        <!-- Version simplifiée du logo pour mobile -->
                        <svg viewBox="0 0 512 512"><rect width="512" height="512" rx="112" fill="#FF7A1A"/><text x="256" y="320" font-family="sans-serif" font-size="160" font-weight="800" fill="#FFFFFF" text-anchor="middle">W</text></svg>
                     </div>
                </div>

                <div class="mb-10 text-center lg:text-left">
                    <h2 class="text-3xl font-bold text-gray-900">Bienvenue</h2>
                    <p class="mt-2 text-sm font-medium text-gray-500">Veuillez entrer vos accès pour continuer.</p>
                </div>

                <div v-if="status" class="mb-6 rounded-lg bg-green-50 p-4 text-sm font-medium text-green-600">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700">Email</label>
                        <input
                            id="email"
                            type="email"
                            v-model="form.email"
                            placeholder="nom@exemple.com"
                            class="mt-1.5 block w-full rounded-xl border-gray-200 bg-gray-50 text-black px-4 py-3 text-sm transition focus:border-[#FF7A1A] focus:ring-[#FF7A1A] focus:bg-gray-400"
                            required
                            autofocus
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div>
                        <div class="flex items-center justify-between">
                            <label for="password" class="block text-sm font-semibold text-gray-700">Mot de passe</label>
                            <Link v-if="canResetPassword" :href="route('password.request')" class="text-xs font-bold text-[#FF7A1A] hover:underline">
                                Oublié ?
                            </Link>
                        </div>
                        <input
                            id="password"
                            type="password"
                            v-model="form.password"
                            placeholder="••••••••"
                            class="mt-1.5 block w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3 text-sm transition focus:border-[#FF7A1A] focus:ring-[#FF7A1A] focus:bg-gray-400"
                            required
                        />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div class="flex items-center">
                        <Checkbox name="remember" v-model:checked="form.remember" class="rounded border-gray-300 text-[#FF7A1A] focus:ring-[#FF7A1A]" />
                        <span class="ml-2 text-sm font-medium text-gray-600">Se souvenir de moi</span>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full transform rounded-xl bg-[#FF7A1A] py-3.5 text-sm font-bold text-white shadow-lg transition duration-200 hover:bg-[#e66a12] active:scale-[0.98] disabled:opacity-50"
                    >
                        <span v-if="form.processing">Connexion...</span>
                        <span v-else>Se connecter</span>
                    </button>
                </form>

                <p class="mt-8 text-center text-sm font-medium text-gray-500">
                    Nouveau ici ? 
                    <Link :href="route('register')" class="font-bold text-gray-900 hover:text-[#FF7A1A] transition">Créer un compte</Link>
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Ajout d'une police serif pour l'aspect "Opportunities" du template */
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@1,500&display=swap');
.font-serif {
    font-family: 'Playfair Display', serif;
}
</style>