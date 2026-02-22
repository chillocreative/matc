<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';
import { Bar } from 'vue-chartjs';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend,
} from 'chart.js';

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend);

const props = defineProps({
    stats: Object,
    chartData: Object,
    members: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');
const category = ref(props.filters.category || '');

let searchTimeout = null;

watch(search, (value) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 300);
});

watch(category, () => {
    applyFilters();
});

function applyFilters() {
    router.get(route('admin.dashboard'), {
        search: search.value || undefined,
        category: category.value || undefined,
    }, {
        preserveState: true,
        replace: true,
    });
}

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'bottom',
            labels: {
                padding: 20,
                usePointStyle: true,
                pointStyle: 'rectRounded',
                font: { size: 13 },
                color: 'rgba(186, 230, 253, 0.6)',
            },
        },
        tooltip: {
            backgroundColor: '#0c4a6e',
            titleFont: { size: 13 },
            bodyFont: { size: 12 },
            padding: 12,
            cornerRadius: 8,
        },
    },
    scales: {
        x: {
            grid: { display: false },
            ticks: { font: { size: 11 }, color: 'rgba(186, 230, 253, 0.5)' },
        },
        y: {
            beginAtZero: true,
            ticks: {
                stepSize: 1,
                font: { size: 11 },
                color: 'rgba(186, 230, 253, 0.5)',
            },
            grid: { color: 'rgba(255, 255, 255, 0.05)' },
        },
    },
};

const categoryOptions = [
    { value: '', label: 'Semua Kategori' },
    { value: 'anggota', label: 'Anggota' },
    { value: 'ajk_cabang', label: 'AJK Cabang' },
    { value: 'amk', label: 'AMK' },
    { value: 'wanita', label: 'Wanita' },
];

const categoryLabels = {
    anggota: 'Anggota',
    ajk_cabang: 'AJK Cabang',
    amk: 'AMK',
    wanita: 'Wanita',
};

const categoryColors = {
    anggota: 'bg-violet-400/15 text-violet-300 ring-1 ring-violet-400/20',
    ajk_cabang: 'bg-sky-400/15 text-sky-300 ring-1 ring-sky-400/20',
    amk: 'bg-emerald-400/15 text-emerald-300 ring-1 ring-emerald-400/20',
    wanita: 'bg-rose-400/15 text-rose-300 ring-1 ring-rose-400/20',
};

function formatDate(date) {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('ms-MY', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
}
</script>

<template>
    <Head title="Admin Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-white">Admin Dashboard</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

                <!-- Stat Cards -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-5">
                    <!-- Total -->
                    <div class="relative overflow-hidden rounded-xl bg-white/10 p-6 shadow-lg backdrop-blur-md ring-1 ring-white/15">
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white/10">
                                <svg class="h-6 w-6 text-sky-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-sky-200/50">Jumlah Daftar</p>
                                <p class="text-2xl font-bold text-white">{{ stats.total.toLocaleString() }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Anggota -->
                    <div class="relative overflow-hidden rounded-xl bg-white/10 p-6 shadow-lg backdrop-blur-md ring-1 ring-white/15">
                        <div class="absolute -right-3 -top-3 h-16 w-16 rounded-full bg-violet-400/10"></div>
                        <div class="relative">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-violet-400/15">
                                    <svg class="h-5 w-5 text-violet-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                    </svg>
                                </div>
                                <p class="text-sm font-medium text-sky-200/50">Anggota</p>
                            </div>
                            <p class="mt-3 text-2xl font-bold text-white">{{ stats.anggota.toLocaleString() }}</p>
                        </div>
                    </div>

                    <!-- AJK Cabang -->
                    <div class="relative overflow-hidden rounded-xl bg-white/10 p-6 shadow-lg backdrop-blur-md ring-1 ring-white/15">
                        <div class="absolute -right-3 -top-3 h-16 w-16 rounded-full bg-sky-400/10"></div>
                        <div class="relative">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-sky-400/15">
                                    <svg class="h-5 w-5 text-sky-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0M12 12.75h.008v.008H12v-.008z" />
                                    </svg>
                                </div>
                                <p class="text-sm font-medium text-sky-200/50">AJK Cabang</p>
                            </div>
                            <p class="mt-3 text-2xl font-bold text-white">{{ stats.ajk_cabang.toLocaleString() }}</p>
                        </div>
                    </div>

                    <!-- AMK -->
                    <div class="relative overflow-hidden rounded-xl bg-white/10 p-6 shadow-lg backdrop-blur-md ring-1 ring-white/15">
                        <div class="absolute -right-3 -top-3 h-16 w-16 rounded-full bg-emerald-400/10"></div>
                        <div class="relative">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-400/15">
                                    <svg class="h-5 w-5 text-emerald-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                    </svg>
                                </div>
                                <p class="text-sm font-medium text-sky-200/50">AMK</p>
                            </div>
                            <p class="mt-3 text-2xl font-bold text-white">{{ stats.amk.toLocaleString() }}</p>
                        </div>
                    </div>

                    <!-- Wanita -->
                    <div class="relative overflow-hidden rounded-xl bg-white/10 p-6 shadow-lg backdrop-blur-md ring-1 ring-white/15">
                        <div class="absolute -right-3 -top-3 h-16 w-16 rounded-full bg-rose-400/10"></div>
                        <div class="relative">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-rose-400/15">
                                    <svg class="h-5 w-5 text-rose-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                    </svg>
                                </div>
                                <p class="text-sm font-medium text-sky-200/50">Wanita</p>
                            </div>
                            <p class="mt-3 text-2xl font-bold text-white">{{ stats.wanita.toLocaleString() }}</p>
                        </div>
                    </div>
                </div>

                <!-- Chart -->
                <div class="mt-8 rounded-xl bg-white/10 p-6 shadow-lg backdrop-blur-md ring-1 ring-white/15">
                    <h3 class="text-base font-semibold text-white">Pendaftaran Bulanan</h3>
                    <p class="text-sm text-sky-200/50">12 bulan terakhir</p>
                    <div class="mt-4" style="height: 320px">
                        <Bar :data="chartData" :options="chartOptions" />
                    </div>
                </div>

                <!-- Members Table -->
                <div class="mt-8 rounded-xl bg-white/10 shadow-lg backdrop-blur-md ring-1 ring-white/15">
                    <div class="border-b border-white/10 px-6 py-5">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <h3 class="text-base font-semibold text-white">Senarai Daftar</h3>
                            <div class="flex flex-col gap-3 sm:flex-row">
                                <div class="relative">
                                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-sky-300/40" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                    </svg>
                                    <input
                                        v-model="search"
                                        type="text"
                                        placeholder="Cari nama..."
                                        class="w-full rounded-lg border-0 bg-white/10 py-2 pl-10 pr-4 text-sm text-white ring-1 ring-white/15 placeholder-sky-200/40 focus:ring-2 focus:ring-sky-400 sm:w-64"
                                    />
                                </div>
                                <select
                                    v-model="category"
                                    class="rounded-lg border-0 bg-white/10 py-2 pl-3 pr-8 text-sm text-white ring-1 ring-white/15 focus:ring-2 focus:ring-sky-400"
                                >
                                    <option v-for="opt in categoryOptions" :key="opt.value" :value="opt.value" class="bg-sky-900 text-white">
                                        {{ opt.label }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-white/10">
                            <thead>
                                <tr class="bg-white/5">
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-sky-200/60">#</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-sky-200/60">Nama</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-sky-200/60">Kategori</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-sky-200/60">Jawatan</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-sky-200/60">Telefon</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-sky-200/60">Tarikh Daftar</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                <tr v-for="(member, index) in members.data" :key="member.id" class="transition-colors hover:bg-white/5">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-sky-300/40">
                                        {{ (members.current_page - 1) * members.per_page + index + 1 }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-white/10 text-sm font-semibold text-sky-200">
                                                {{ member.name?.charAt(0)?.toUpperCase() }}
                                            </div>
                                            <span class="text-sm font-medium text-white">{{ member.name }}</span>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium" :class="categoryColors[member.category_type]">
                                            {{ categoryLabels[member.category_type] || member.category_type }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-sky-200/50">
                                        {{ member.position_type || '-' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-sky-200/50">
                                        {{ member.phone_number || '-' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-sky-200/50">
                                        {{ formatDate(member.created_at) }}
                                    </td>
                                </tr>
                                <tr v-if="!members.data.length">
                                    <td colspan="6" class="px-6 py-12 text-center text-sm text-sky-300/40">
                                        Tiada rekod dijumpai.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="members.last_page > 1" class="flex items-center justify-between border-t border-white/10 px-6 py-4">
                        <p class="text-sm text-sky-200/50">
                            {{ members.from }}–{{ members.to }} daripada {{ members.total }}
                        </p>
                        <div class="flex gap-1">
                            <template v-for="link in members.links" :key="link.label">
                                <button
                                    v-if="link.url"
                                    @click="router.get(link.url, {}, { preserveState: true })"
                                    v-html="link.label"
                                    class="rounded-lg px-3 py-1.5 text-sm transition-colors"
                                    :class="link.active
                                        ? 'bg-sky-500 text-white font-medium shadow-lg shadow-sky-500/30'
                                        : 'text-sky-200/60 hover:bg-white/10'"
                                />
                                <span
                                    v-else
                                    v-html="link.label"
                                    class="rounded-lg px-3 py-1.5 text-sm text-sky-300/30"
                                />
                            </template>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
