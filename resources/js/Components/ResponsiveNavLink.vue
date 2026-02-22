<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-sky-400 text-start text-base font-medium text-white bg-sky-400/10 focus:outline-none focus:text-white focus:bg-sky-400/15 focus:border-sky-300 transition duration-150 ease-in-out'
        : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-sky-200/60 hover:text-sky-100 hover:bg-white/5 hover:border-sky-400/30 focus:outline-none focus:text-sky-100 focus:bg-white/5 focus:border-sky-400/30 transition duration-150 ease-in-out',
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
