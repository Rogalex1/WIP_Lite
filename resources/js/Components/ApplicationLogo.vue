<template>
    <div :class="[sizeClass, 'shadow-2xl rounded-2xl overflow-hidden']">
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
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    size: {
        type: String,
        default: 'md'
    }
});

const sizeClass = computed(() => {
    return {
        'xs': 'w-8 h-8',
        'sm': 'w-10 h-10',
        'md': 'w-16 h-16',
        'lg': 'w-24 h-24',
        'xl': 'w-32 h-32'
    }[props.size] || 'w-16 h-16';
});
</script>
