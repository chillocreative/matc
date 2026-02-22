<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    category: String,
    categoryLabel: String,
    categorySlug: String,
    meeting: Object,
    meetings: Array,
    qrCode: String,
    signedUrl: String,
});

const selectedMeeting = ref(props.meeting?.id || '');

function onMeetingChange() {
    router.get(
        window.location.pathname,
        { meeting_id: selectedMeeting.value },
        { preserveState: true }
    );
}

function printQr() {
    window.print();
}
</script>

<template>
    <Head :title="`QR ${categoryLabel}`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-white">
                QR Kehadiran: {{ categoryLabel }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-2xl bg-white/10 shadow-lg backdrop-blur-md ring-1 ring-white/15">
                    <div class="p-6">
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-sky-100/80 mb-2">Pilih Mesyuarat</label>
                            <select
                                v-model="selectedMeeting"
                                @change="onMeetingChange"
                                class="w-full rounded-md border-0 bg-white/10 text-white ring-1 ring-white/15 focus:ring-2 focus:ring-sky-400"
                            >
                                <option value="" class="bg-sky-900 text-white">-- Pilih mesyuarat --</option>
                                <option v-for="m in meetings" :key="m.id" :value="m.id" class="bg-sky-900 text-white">
                                    {{ m.title }} ({{ m.date }})
                                </option>
                            </select>
                        </div>

                        <div v-if="meeting && qrCode" class="text-center">
                            <div class="mb-4 rounded-lg bg-white/5 p-4 ring-1 ring-white/10">
                                <h3 class="text-lg font-semibold text-white">{{ meeting.title }}</h3>
                                <p class="text-sm text-sky-200/50">{{ meeting.date }}</p>
                                <span class="mt-2 inline-flex items-center rounded-full bg-sky-400/15 px-3 py-1 text-sm font-medium text-sky-300 ring-1 ring-sky-400/20">
                                    {{ categoryLabel }}
                                </span>
                            </div>

                            <div class="mx-auto my-8 inline-block rounded-2xl border-4 border-white/15 bg-white p-6" v-html="qrCode"></div>

                            <p class="mt-4 text-xs text-sky-300/40 break-all">{{ signedUrl }}</p>

                            <button
                                @click="printQr"
                                class="mt-6 inline-flex items-center rounded-md bg-sky-500 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-sky-500/30 hover:bg-sky-400"
                            >
                                Cetak QR
                            </button>
                        </div>

                        <div v-else-if="!meeting" class="text-center py-12">
                            <p class="text-sky-200/50">Sila pilih mesyuarat untuk menjana QR code.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
@media print {
    nav, header, select, button, .no-print { display: none !important; }
    body { background: white !important; }
}
</style>
