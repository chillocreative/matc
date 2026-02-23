<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
});
</script>

<template>
    <Head title="Mesyuarat Agung Tahunan Cabang" />

    <div class="min-h-screen bg-gradient-to-br from-sky-950 via-sky-900 to-blue-950 relative overflow-hidden">
        <!-- Background pattern -->
        <div class="absolute inset-0 opacity-[0.04]" style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;1&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')"></div>

        <!-- Decorative circles -->
        <div class="absolute -top-40 -right-40 h-96 w-96 rounded-full bg-sky-400/10 blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 h-96 w-96 rounded-full bg-blue-400/10 blur-3xl"></div>

        <!-- Nav -->
        <nav v-if="canLogin" class="relative z-10 flex items-center justify-end px-4 py-4 sm:px-12">
            <Link
                v-if="$page.props.auth.user"
                :href="route('dashboard')"
                class="rounded-lg bg-white/10 px-4 py-2 text-sm font-medium text-white backdrop-blur-sm transition hover:bg-white/20"
            >
                Dashboard
            </Link>
            <Link
                v-else
                :href="route('login')"
                class="rounded-lg bg-white/10 px-4 py-2 text-sm font-medium text-white backdrop-blur-sm transition hover:bg-white/20"
            >
                Log Masuk
            </Link>
        </nav>

        <!-- Hero -->
        <div class="relative z-10 flex min-h-[calc(100vh-64px)] flex-col items-center justify-center px-4 py-8 text-center sm:px-6">
            <!-- Logo -->
            <div class="mb-4">
                <img src="/logo-keadilan.png" alt="Logo Keadilan" class="mx-auto w-40 sm:w-48 lg:w-56 object-contain drop-shadow-2xl" />
            </div>

            <!-- Badge -->
            <div class="inline-flex items-center rounded-full bg-sky-400/15 px-3 py-1 text-[10px] font-medium tracking-wider text-sky-300 uppercase backdrop-blur-sm ring-1 ring-sky-400/20 sm:px-4 sm:py-1.5 sm:text-xs">
                Parti Keadilan Rakyat
            </div>

            <h1 class="mt-4 text-2xl font-bold tracking-tight text-white sm:text-4xl lg:text-5xl">
                Mesyuarat Agung<br>
                <span class="text-sky-300">Tahunan Cabang</span>
            </h1>
            <p class="mt-1 text-3xl font-extrabold text-sky-300 sm:text-5xl lg:text-6xl">2026</p>

            <p class="mt-2 text-base font-medium text-sky-200/90 sm:mt-3 sm:text-xl">
                Kepala Batas, Pulau Pinang
            </p>

            <!-- Meeting Info -->
            <div class="mt-5 w-full max-w-sm rounded-2xl bg-white/10 px-5 py-4 backdrop-blur-sm ring-1 ring-white/15 sm:mt-6 sm:max-w-none sm:w-auto sm:inline-flex sm:flex-row sm:items-center sm:gap-6 sm:px-8 sm:py-5">
                <div class="flex items-center justify-center gap-2 text-sm text-sky-100">
                    <svg class="h-4 w-4 shrink-0 text-sky-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                    </svg>
                    7 Mac 2026
                </div>
                <div class="my-2 h-px w-full bg-white/10 sm:hidden"></div>
                <div class="hidden sm:block h-4 w-px bg-white/20"></div>
                <div class="flex items-center justify-center gap-2 text-sm text-sky-100">
                    <svg class="h-4 w-4 shrink-0 text-sky-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    11:30 pagi
                </div>
                <div class="my-2 h-px w-full bg-white/10 sm:hidden"></div>
                <div class="hidden sm:block h-4 w-px bg-white/20"></div>
                <div class="flex items-center justify-center gap-2 text-sm text-sky-100">
                    <svg class="h-4 w-4 shrink-0 text-sky-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                    </svg>
                    Penang Golf Resort
                </div>
            </div>

            <!-- Category Buttons -->
            <p class="mt-8 text-xs font-medium text-sky-200/50 uppercase tracking-wider sm:mt-10 sm:text-sm">Pengesahan Kehadiran</p>
            <div class="mt-3 grid w-full max-w-md grid-cols-3 gap-2 sm:mt-4 sm:grid-cols-3 sm:gap-3">
                <Link
                    :href="route('qr.matc')"
                    class="group flex flex-col items-center justify-center gap-1.5 rounded-xl bg-sky-500 px-3 py-3 text-xs font-semibold text-white shadow-lg shadow-sky-500/30 transition hover:bg-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-400 focus:ring-offset-2 focus:ring-offset-sky-950 sm:py-3.5 sm:text-sm"
                >
                    <svg class="h-5 w-5 transition group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                    MATC
                </Link>
                <Link
                    :href="route('qr.wanita')"
                    class="group flex flex-col items-center justify-center gap-1.5 rounded-xl bg-rose-500 px-3 py-3 text-xs font-semibold text-white shadow-lg shadow-rose-500/30 transition hover:bg-rose-400 focus:outline-none focus:ring-2 focus:ring-rose-400 focus:ring-offset-2 focus:ring-offset-sky-950 sm:py-3.5 sm:text-sm"
                >
                    <svg class="h-5 w-5 transition group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                    MATCWC
                </Link>
                <Link
                    :href="route('qr.amk')"
                    class="group flex flex-col items-center justify-center gap-1.5 rounded-xl bg-emerald-500 px-3 py-3 text-xs font-semibold text-white shadow-lg shadow-emerald-500/30 transition hover:bg-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:ring-offset-2 focus:ring-offset-sky-950 sm:py-3.5 sm:text-sm"
                >
                    <svg class="h-5 w-5 transition group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                    MATCAMK
                </Link>
            </div>

            <!-- Footer -->
            <footer class="mt-10 pb-6 text-center text-[10px] text-sky-200/30 sm:mt-16 sm:pb-8 sm:text-xs">
                MATC &mdash; Sistem Pengurusan Mesyuarat Agung Tahunan Cabang Kepala Batas
            </footer>
        </div>
    </div>
</template>
