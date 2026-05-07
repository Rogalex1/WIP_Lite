<script setup>
import { onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import Toast from 'primevue/toast';
import { Link } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Card from 'primevue/card';
const toast = useToast();

onMounted(() => {
    toast.add({
        severity: 'success',
        summary: 'Bienvenue 👋',
        detail: 'Heureux de vous revoir sur votre tableau de bord !',
        life: 4000
    });
});



</script>

<template>
    <Head title="Dashboard" />

        <Toast />


</template>
