<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

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
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Connexion | WIP LITE" />

    <div class="flex min-h-screen bg-slate-50 font-sans antialiased">
        <!-- Section Gauche : Visuel & Branding -->
        <div class="relative hidden w-1/2 flex-col justify-between bg-[#FF7A1A] p-16 lg:flex overflow-hidden">
            <!-- Cercles décoratifs et effets de lumière -->
            <div class="absolute -left-20 -top-20 h-96 w-96 rounded-full bg-white/20 blur-[120px]"></div>
            <div class="absolute -bottom-40 -right-40 h-[500px] w-[500px] rounded-full bg-orange-400/20 blur-[150px]"></div>
            
            <div class="relative z-10">
                <div class="flex items-center gap-4 group">
                    <ApplicationLogo size="md" class="group-hover:scale-105 transition-transform duration-500 shadow-orange-900/20" />
                    <div class="flex flex-col">
                        <span class="text-white font-black text-2xl tracking-tighter leading-none">WIP<span class="text-orange-900">LITE</span></span>
                        <span class="text-orange-100/50 text-[10px] font-black uppercase tracking-[0.3em]">Management Platform</span>
                    </div>
                </div>
            </div>

            <div class="relative z-10 mt-auto">
                <div class="inline-block px-4 py-1.5 rounded-full bg-white/10 border border-white/20 mb-8">
                    <span class="text-white text-[10px] font-black uppercase tracking-[0.2em]">Solution RH Intégrée</span>
                </div>
                <h1 class="text-6xl font-black leading-[1.1] text-white tracking-tighter">
                    Pilotez vos équipes <br />
                    <span class="text-orange-900">avec performance.</span>
                </h1>
                <p class="mt-8 max-w-md text-lg text-orange-50 font-medium leading-relaxed">
                    L'outil centralisé pour le suivi, l'affectation et l'analyse de vos campagnes de téléconseil.
                </p>
            </div>

            <div class="relative z-10 mt-16 pt-8 border-t border-white/10 flex justify-between items-center text-[10px] font-black uppercase tracking-[0.2em] text-white/40">
                <span>© 2026 WIP LITE SOLUTIONS</span>
                <div class="flex gap-8">
                    <a href="#" class="hover:text-white transition-colors">Support</a>
                    <a href="#" class="hover:text-white transition-colors">Confidentialité</a>
                </div>
            </div>
        </div>

        <!-- Section Droite : Formulaire -->
        <div class="flex w-full items-center justify-center p-8 lg:w-1/2 bg-white">
            <div class="w-full max-w-md">
                <!-- Header avec Logo pour toutes les résolutions -->
                <!-- Header avec Logo pour toutes les résolutions -->
                <div class="mb-12 flex flex-col items-center lg:items-start">
                    <div class="flex items-center gap-3 mb-6">
                        <ApplicationLogo size="sm" class="shadow-orange-100" />
                        <span class="text-slate-900 font-black text-xl tracking-tighter uppercase">Wip<span class="text-[#FF7A1A]">Lite</span></span>
                    </div>
                    <h2 class="text-4xl font-black text-slate-900 tracking-tighter uppercase">Connexion</h2>
                    <p class="mt-3 text-slate-500 font-bold text-sm uppercase tracking-wider">Espace de travail sécurisé</p>
                </div>

                <div v-if="status" class="mb-8 rounded-2xl bg-orange-50 border border-orange-100 p-4 text-sm font-bold text-orange-600 flex items-center gap-3">
                    <i class="pi pi-info-circle"></i>
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-8">
                    <div class="space-y-2">
                        <label for="email" class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Identifiant Email</label>
                        <div class="relative group">
                            <i class="pi pi-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-[#FF7A1A] transition-colors"></i>
                            <input
                                id="email"
                                type="email"
                                v-model="form.email"
                                placeholder="nom@wiplite.com"
                                class="block w-full rounded-2xl border-slate-100 bg-slate-50 pl-12 pr-4 py-4 text-sm font-bold text-slate-700 transition-all focus:border-[#FF7A1A] focus:ring-4 focus:ring-orange-500/10 focus:bg-white outline-none"
                                required
                                autofocus
                            />
                        </div>
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center justify-between ml-1">
                            <label for="password" class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Mot de passe</label>
                            <Link v-if="canResetPassword" :href="route('password.request')" class="text-[10px] font-black text-orange-500 hover:text-orange-600 uppercase tracking-widest transition-colors">
                                Oublié ?
                            </Link>
                        </div>
                        <div class="relative group">
                            <i class="pi pi-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-[#FF7A1A] transition-colors"></i>
                            <input
                                id="password"
                                type="password"
                                v-model="form.password"
                                placeholder="••••••••••••"
                                class="block w-full rounded-2xl border-slate-100 bg-slate-50 pl-12 pr-4 py-4 text-sm font-bold text-slate-700 transition-all focus:border-[#FF7A1A] focus:ring-4 focus:ring-orange-500/10 focus:bg-white outline-none"
                                required
                            />
                        </div>
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center group cursor-pointer">
                            <Checkbox name="remember" v-model:checked="form.remember" class="rounded-lg border-slate-200 text-[#FF7A1A] focus:ring-orange-500 focus:ring-offset-0 w-5 h-5 transition-all" />
                            <span class="ml-3 text-xs font-black text-slate-400 uppercase tracking-widest group-hover:text-slate-600 transition-colors">Se souvenir</span>
                        </label>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full relative group overflow-hidden rounded-2xl bg-slate-900 py-5 text-xs font-black text-white uppercase tracking-[0.3em] shadow-2xl shadow-slate-200 transition-all hover:bg-[#FF7A1A] hover:shadow-orange-200 active:scale-[0.98] disabled:opacity-50"
                    >
                        <span class="relative z-10" v-if="form.processing">Connexion...</span>
                        <span class="relative z-10" v-else>Accéder au plateau</span>
                        <div class="absolute inset-0 bg-gradient-to-r from-orange-500 to-[#FF7A1A] opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    </button>
                </form>

                <div class="mt-12 pt-8 border-t border-slate-50 text-center">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                        Pas encore d'accès ? 
                        <Link :href="route('register')" class="ml-2 text-slate-900 hover:text-[#FF7A1A] transition-colors border-b-2 border-orange-200 hover:border-orange-500 pb-0.5">Contacter l'administrateur</Link>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
</style>