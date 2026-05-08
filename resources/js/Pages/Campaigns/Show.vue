<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    campaign: Object
});
</script>

<template>
    <Head :title="`Détails - ${campaign.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center py-2">
                <div class="flex items-center gap-4">
                    <Link :href="route('campaigns.index')" class="text-slate-400 hover:text-slate-800 transition-colors">
                        <i class="pi pi-arrow-left text-lg"></i>
                    </Link>
                    <h2 class="text-xl font-bold text-slate-800 tracking-tight uppercase">Détails de la campagne</h2>
                </div>
                <div class="flex gap-2">
                    <span :class="['px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest', 
                        campaign.status === 'active' ? 'bg-orange-100 text-[#FF7A1A]' : 'bg-slate-100 text-slate-500']">
                        {{ campaign.status }}
                    </span>
                </div>
            </div>
        </template>

        <div class="py-10 bg-slate-50 min-h-screen">
            <div class="max-w-4xl mx-auto px-4">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <!-- Bannière de titre -->
                    <div class="bg-slate-900 p-8 text-white">
                        <h1 class="text-3xl font-black tracking-tighter uppercase mb-2">{{ campaign.name }}</h1>
                        <div class="flex gap-6 text-slate-400 text-xs font-bold uppercase tracking-widest">
                            <div class="flex items-center gap-2">
                                <i class="pi pi-calendar text-[#FF7A1A]"></i>
                                Du {{ campaign.start_date }} au {{ campaign.end_date }}
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="pi pi-tag text-[#FF7A1A]"></i>
                                ID: #00{{ campaign.id }}
                            </div>
                        </div>
                    </div>

                    <!-- Contenu -->
                    <div class="p-8">
                        <div class="mb-10">
                            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Description du projet</h4>
                            <p class="text-slate-600 leading-relaxed text-lg">
                                {{ campaign.description || 'Aucune description détaillée fournie pour cette campagne.' }}
                            </p>
                        </div>

                        <hr class="border-slate-100 mb-10" />

                        <!-- Statistiques ou infos complémentaires (Placeholders) -->
                        <div class="grid grid-cols-3 gap-6">
                            <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                                <span class="block text-[9px] font-black text-slate-400 uppercase mb-1">Date de création</span>
                                <span class="text-slate-800 font-bold">{{ new Date(campaign.created_at).toLocaleDateString() }}</span>
                            </div>
                            <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                                <span class="block text-[9px] font-black text-slate-400 uppercase mb-1">Dernière mise à jour</span>
                                <span class="text-slate-800 font-bold">{{ new Date(campaign.updated_at).toLocaleDateString() }}</span>
                            </div>
                            <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                                <span class="block text-[9px] font-black text-slate-400 uppercase mb-1">Priorité</span>
                                <span class="text-[#FF7A1A] font-black uppercase text-xs tracking-tighter">Haute</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>