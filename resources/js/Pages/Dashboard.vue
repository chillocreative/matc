<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    stats: Object,
    upcoming_meetings: Array,
});

const statCards = [
    { label: 'Jumlah Ahli', key: 'total_members', icon: 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z' },
    { label: 'Jumlah Mesyuarat', key: 'total_meetings', icon: 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5' },
    { label: 'Rekod Kehadiran', key: 'total_attendances', icon: 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
    { label: 'Kadar Kehadiran', key: 'attendance_rate', suffix: '%', icon: 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z' },
];
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-white">Dashboard</h2>
        </template>

        <div class="py-8 sm:py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Stats Grid -->
                <div class="grid grid-cols-2 gap-3 sm:gap-6 lg:grid-cols-4">
                    <div
                        v-for="card in statCards"
                        :key="card.key"
                        class="rounded-2xl bg-white/10 p-4 shadow-lg backdrop-blur-md ring-1 ring-white/15 sm:p-6"
                    >
                        <div class="flex items-center gap-3">
                            <div class="rounded-lg bg-sky-400/15 p-2">
                                <svg class="h-5 w-5 text-sky-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" :d="card.icon" />
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-sky-200/60 sm:text-sm">{{ card.label }}</span>
                        </div>
                        <div class="mt-3 text-2xl font-bold text-white sm:text-3xl">
                            {{ stats[card.key] }}{{ card.suffix || '' }}
                        </div>
                    </div>
                </div>

                <!-- QR Code -->
                <div class="mt-6 rounded-2xl bg-white/10 p-4 shadow-lg backdrop-blur-md ring-1 ring-white/15 sm:mt-8 sm:p-6">
                    <h3 class="text-base font-semibold text-white sm:text-lg">Jana QR Code</h3>
                    <p class="mt-1 text-sm text-sky-200/50">Jana kod QR untuk pengesahan kehadiran mengikut kategori.</p>
                    <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
                        <Link
                            :href="route('admin.qr.anggota')"
                            class="flex items-center gap-3 rounded-xl bg-sky-500/15 px-4 py-3 ring-1 ring-sky-400/20 transition hover:bg-sky-500/25"
                        >
                            <div class="rounded-lg bg-sky-400/15 p-2">
                                <svg class="h-5 w-5 text-sky-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75H16.5v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75H16.5v-.75z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-white">Anggota</div>
                                <div class="text-xs text-sky-200/50">QR kehadiran</div>
                            </div>
                        </Link>
                        <Link
                            :href="route('admin.qr.ajk-cabang')"
                            class="flex items-center gap-3 rounded-xl bg-amber-500/15 px-4 py-3 ring-1 ring-amber-400/20 transition hover:bg-amber-500/25"
                        >
                            <div class="rounded-lg bg-amber-400/15 p-2">
                                <svg class="h-5 w-5 text-amber-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75H16.5v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75H16.5v-.75z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-white">AJK Cabang</div>
                                <div class="text-xs text-sky-200/50">QR kehadiran</div>
                            </div>
                        </Link>
                        <Link
                            :href="route('admin.qr.wanita')"
                            class="flex items-center gap-3 rounded-xl bg-rose-500/15 px-4 py-3 ring-1 ring-rose-400/20 transition hover:bg-rose-500/25"
                        >
                            <div class="rounded-lg bg-rose-400/15 p-2">
                                <svg class="h-5 w-5 text-rose-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75H16.5v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75H16.5v-.75z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-white">Wanita</div>
                                <div class="text-xs text-sky-200/50">QR kehadiran</div>
                            </div>
                        </Link>
                        <Link
                            :href="route('admin.qr.amk')"
                            class="flex items-center gap-3 rounded-xl bg-emerald-500/15 px-4 py-3 ring-1 ring-emerald-400/20 transition hover:bg-emerald-500/25"
                        >
                            <div class="rounded-lg bg-emerald-400/15 p-2">
                                <svg class="h-5 w-5 text-emerald-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75H16.5v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75H16.5v-.75z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-white">AMK</div>
                                <div class="text-xs text-sky-200/50">QR kehadiran</div>
                            </div>
                        </Link>
                    </div>
                </div>

                <!-- Upcoming Meetings -->
                <div class="mt-6 rounded-2xl bg-white/10 p-4 shadow-lg backdrop-blur-md ring-1 ring-white/15 sm:mt-8 sm:p-6">
                    <h3 class="text-base font-semibold text-white sm:text-lg">Mesyuarat Akan Datang</h3>
                    <div v-if="upcoming_meetings.length" class="mt-4 divide-y divide-white/10">
                        <div v-for="meeting in upcoming_meetings" :key="meeting.id" class="flex items-center justify-between py-3">
                            <div>
                                <Link :href="route('meetings.show', meeting.id)" class="font-medium text-sky-300 hover:text-sky-200 transition">
                                    {{ meeting.title }}
                                </Link>
                                <p class="text-sm text-sky-200/50">{{ meeting.date }} &middot; {{ meeting.location }}</p>
                            </div>
                            <span class="inline-flex items-center rounded-full bg-sky-400/15 px-2.5 py-0.5 text-xs font-medium text-sky-300 ring-1 ring-sky-400/20">
                                {{ meeting.status }}
                            </span>
                        </div>
                    </div>
                    <p v-else class="mt-4 text-sm text-sky-200/50">Tiada mesyuarat akan datang.</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
