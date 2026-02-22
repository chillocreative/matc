<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-sky-950 via-sky-900 to-blue-950 relative overflow-hidden flex flex-col items-center px-4 pt-8 pb-8 sm:justify-center sm:pt-0 sm:px-6">
        <!-- Background pattern -->
        <div class="absolute inset-0 opacity-[0.04]" style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;1&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')"></div>

        <!-- Decorative circles -->
        <div class="absolute -top-40 -right-40 h-96 w-96 rounded-full bg-sky-400/10 blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 h-96 w-96 rounded-full bg-blue-400/10 blur-3xl"></div>

        <!-- Logo -->
        <div class="relative z-10">
            <Link href="/">
                <img src="/logo-keadilan.png" alt="Logo Keadilan" class="mx-auto w-28 sm:w-32 object-contain drop-shadow-2xl" />
            </Link>
        </div>

        <!-- Card -->
        <div class="relative z-10 mt-6 w-full overflow-hidden rounded-2xl bg-white/10 px-5 py-6 shadow-xl backdrop-blur-md ring-1 ring-white/20 sm:max-w-md sm:px-8 sm:py-8">
            <slot />
        </div>
    </div>
</template>
