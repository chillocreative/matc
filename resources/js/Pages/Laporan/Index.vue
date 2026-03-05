<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    reports: {
        type: Object,
        default: () => ({ matc: null, amk: null, wanita: null }),
    },
    flash: Object,
});

const categories = [
    { key: 'matc',   label: 'MATC',    color: 'sky' },
    { key: 'amk',    label: 'MATAMKC', color: 'emerald' },
    { key: 'wanita', label: 'MATWC',   color: 'rose' },
];

const uploading = ref({ matc: false, amk: false, wanita: false });
const fileInputs = ref({ matc: null, amk: null, wanita: null });

function handleUpload(category) {
    const file = fileInputs.value[category]?.files?.[0];
    if (!file) return;

    uploading.value[category] = true;

    const formData = new FormData();
    formData.append('file', file);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content ?? '');

    router.post(route('laporan.upload', category), formData, {
        forceFormData: true,
        onFinish: () => {
            uploading.value[category] = false;
            if (fileInputs.value[category]) fileInputs.value[category].value = '';
        },
    });
}

function downloadQr(category) {
    window.location.href = route('laporan.qr', category);
}

const colorMap = {
    sky: {
        card: 'border-sky-500/30 bg-sky-500/5',
        badge: 'bg-sky-400/15 text-sky-300 ring-sky-400/20',
        btn: 'bg-sky-600 hover:bg-sky-500',
        outline: 'border-sky-500/50 text-sky-300 hover:bg-sky-500/10',
        icon: 'text-sky-400',
    },
    emerald: {
        card: 'border-emerald-500/30 bg-emerald-500/5',
        badge: 'bg-emerald-400/15 text-emerald-300 ring-emerald-400/20',
        btn: 'bg-emerald-600 hover:bg-emerald-500',
        outline: 'border-emerald-500/50 text-emerald-300 hover:bg-emerald-500/10',
        icon: 'text-emerald-400',
    },
    rose: {
        card: 'border-rose-500/30 bg-rose-500/5',
        badge: 'bg-rose-400/15 text-rose-300 ring-rose-400/20',
        btn: 'bg-rose-600 hover:bg-rose-500',
        outline: 'border-rose-500/50 text-rose-300 hover:bg-rose-500/10',
        icon: 'text-rose-400',
    },
};
</script>

<template>
    <Head title="Laporan" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-white">Laporan</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">

                <!-- Flash messages -->
                <div v-if="$page.props.flash?.success" class="mb-6 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
                    {{ $page.props.flash.success }}
                </div>
                <div v-if="$page.props.flash?.error" class="mb-6 rounded-lg border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-300">
                    {{ $page.props.flash.error }}
                </div>

                <div class="space-y-6">
                    <div
                        v-for="cat in categories"
                        :key="cat.key"
                        class="rounded-xl border backdrop-blur-sm p-6"
                        :class="colorMap[cat.color].card"
                    >
                        <!-- Header -->
                        <div class="mb-4 flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" :class="colorMap[cat.color].icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="text-lg font-semibold text-white">Laporan {{ cat.label }}</h3>
                        </div>

                        <!-- Current file info -->
                        <div v-if="reports[cat.key]" class="mb-4 flex items-center gap-2">
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1" :class="colorMap[cat.color].badge">
                                {{ reports[cat.key].original_filename }}
                            </span>
                            <span class="text-xs text-white/40">Fail semasa</span>
                        </div>
                        <div v-else class="mb-4 text-sm text-white/40 italic">
                            Tiada fail dimuat naik.
                        </div>

                        <!-- Upload + QR row -->
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                            <!-- File input -->
                            <div class="flex-1">
                                <label class="mb-1.5 block text-xs font-medium text-white/60">
                                    {{ reports[cat.key] ? 'Ganti Laporan (PDF)' : 'Muat Naik Laporan (PDF)' }}
                                </label>
                                <input
                                    :ref="el => fileInputs[cat.key] = el"
                                    type="file"
                                    accept="application/pdf"
                                    class="block w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-sm text-white/80 file:mr-3 file:rounded file:border-0 file:bg-white/10 file:px-3 file:py-1 file:text-xs file:text-white/80 file:cursor-pointer hover:file:bg-white/20 focus:outline-none"
                                />
                            </div>

                            <!-- Upload button -->
                            <button
                                type="button"
                                :disabled="uploading[cat.key]"
                                @click="handleUpload(cat.key)"
                                class="inline-flex items-center gap-2 rounded-lg px-4 py-2.5 text-sm font-medium text-white transition disabled:opacity-50"
                                :class="colorMap[cat.color].btn"
                            >
                                <svg v-if="uploading[cat.key]" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                                </svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                {{ uploading[cat.key] ? 'Memuat naik...' : 'Muat Naik' }}
                            </button>

                            <!-- QR Download button (only if file exists) -->
                            <button
                                v-if="reports[cat.key]"
                                type="button"
                                @click="downloadQr(cat.key)"
                                class="inline-flex items-center gap-2 rounded-lg border px-4 py-2.5 text-sm font-medium transition"
                                :class="colorMap[cat.color].outline"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                </svg>
                                Muat Turun QR
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
