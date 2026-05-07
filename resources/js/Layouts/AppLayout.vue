<template>
    <!-- TOAST GLOBAL -->
    <Toast appendTo="body" position="top-right" />

    <!-- layout selon rôle -->
    <component :is="layout">
        <slot />
    </component>
</template>

<script setup>
defineProps({
    layout: Object
})
</script>