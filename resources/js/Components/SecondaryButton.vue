<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center rounded-md border border-white/15 bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-sky-100 shadow-sm transition duration-150 ease-in-out hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-sky-400 focus:ring-offset-2 focus:ring-offset-sky-950 disabled:opacity-25"
    >
        <slot />
    </button>
</template>
