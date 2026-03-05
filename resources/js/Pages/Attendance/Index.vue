<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, nextTick } from 'vue';
import axios from 'axios';

// Drag-to-scroll
const tableWrapper = ref(null);
let isDragging = false;
let startX = 0;
let scrollLeft = 0;

function onMouseDown(e) {
    isDragging = true;
    startX = e.pageX - tableWrapper.value.offsetLeft;
    scrollLeft = tableWrapper.value.scrollLeft;
    tableWrapper.value.style.cursor = 'grabbing';
}
function onMouseLeave() {
    isDragging = false;
    if (tableWrapper.value) tableWrapper.value.style.cursor = 'grab';
}
function onMouseUp() {
    isDragging = false;
    if (tableWrapper.value) tableWrapper.value.style.cursor = 'grab';
}
function onMouseMove(e) {
    if (!isDragging) return;
    e.preventDefault();
    const x = e.pageX - tableWrapper.value.offsetLeft;
    const walk = (x - startX) * 1.5;
    tableWrapper.value.scrollLeft = scrollLeft - walk;
}

const props = defineProps({
    meetings: Object,
    meeting: Object,
    attendances: Array,
    filters: Object,
});

const page = usePage();
const selectedMeeting = ref(props.filters.meeting_id || '');
const selectedCategory = ref(props.filters.category || '');

function applyFilters() {
    const params = { meeting_id: selectedMeeting.value };
    if (selectedCategory.value) {
        params.category = selectedCategory.value;
    }
    router.get(route('attendances.index'), params, {
        preserveState: true,
        replace: true,
    });
}

function onMeetingChange() {
    selectedCategory.value = '';
    applyFilters();
}

function onCategoryChange() {
    applyFilters();
}

const categoryLabels = {
    matc: 'MATC',
    amk: 'MATAMKC',
    wanita: 'MATWC',
};

const statusColors = {
    present: 'bg-emerald-400/15 text-emerald-300 ring-1 ring-emerald-400/20',
    absent: 'bg-red-400/15 text-red-300 ring-1 ring-red-400/20',
    late: 'bg-yellow-400/15 text-yellow-300 ring-1 ring-yellow-400/20',
    excused: 'bg-white/10 text-sky-200 ring-1 ring-white/15',
};

// PDF download
const downloading = ref(false);
const downloadError = ref('');

async function downloadPdf() {
    downloading.value = true;
    downloadError.value = '';

    try {
        const response = await axios.get(route('export.attendance.pdf', {
            meeting_id: selectedMeeting.value,
            category: selectedCategory.value,
        }), { responseType: 'blob' });

        const contentType = response.headers['content-type'] || '';
        if (!contentType.includes('application/pdf')) {
            throw new Error('Respons bukan PDF.');
        }

        const disposition = response.headers['content-disposition'] || '';
        const match = disposition.match(/filename="?([^";\n]+)"?/);
        const filename = match ? match[1] : 'kehadiran.pdf';

        const url = window.URL.createObjectURL(response.data);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        a.remove();
    } catch (error) {
        const status = error.response?.status;
        if (status === 401 || status === 419) {
            downloadError.value = 'Sesi anda telah tamat. Sila muat semula halaman.';
        } else if (status === 403) {
            downloadError.value = 'Anda tidak mempunyai kebenaran untuk memuat turun PDF.';
        } else if (status === 422) {
            downloadError.value = 'Data tidak sah. Sila pilih mesyuarat dan kategori.';
        } else {
            downloadError.value = error.message || 'Gagal memuat turun PDF. Sila cuba lagi.';
        }
    } finally {
        downloading.value = false;
    }
}

// Edit modal
const showEditModal = ref(false);
const editForm = ref({ id: null, status: '', absence_reason: '' });
const editErrors = ref({});

function openEdit(attendance) {
    editForm.value = {
        id: attendance.id,
        status: attendance.status,
        absence_reason: attendance.absence_reason || '',
    };
    editErrors.value = {};
    showEditModal.value = true;
}

function saveEdit() {
    router.put(route('attendances.update', editForm.value.id), {
        status: editForm.value.status,
        absence_reason: editForm.value.absence_reason,
    }, {
        preserveState: true,
        onSuccess: () => { showEditModal.value = false; },
        onError: (errors) => { editErrors.value = errors; },
    });
}

// Delete
const showDeleteModal = ref(false);
const deleteTarget = ref(null);

function confirmDelete(attendance) {
    deleteTarget.value = attendance;
    showDeleteModal.value = true;
}

function executeDelete() {
    router.delete(route('attendances.destroy', deleteTarget.value.id), {
        preserveState: true,
        onSuccess: () => { showDeleteModal.value = false; },
    });
}
</script>

<template>
    <Head title="Kehadiran" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-white">Rekod Kehadiran</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div v-if="page.props.flash.success" class="mb-4 rounded-md bg-emerald-400/15 p-4 ring-1 ring-emerald-400/20">
                    <p class="text-sm text-emerald-300">{{ page.props.flash.success }}</p>
                </div>

                <div v-if="downloadError" class="mb-4 rounded-md bg-red-400/15 p-4 ring-1 ring-red-400/20">
                    <p class="text-sm text-red-300">{{ downloadError }}</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-sky-100/80 mb-1">Pilih Mesyuarat</label>
                    <select
                        v-model="selectedMeeting"
                        @change="onMeetingChange"
                        class="w-full rounded-md border-0 bg-white/10 text-white ring-1 ring-white/15 focus:ring-2 focus:ring-sky-400 sm:max-w-md"
                    >
                        <option value="" class="bg-sky-900 text-white">-- Pilih Mesyuarat --</option>
                        <option v-for="m in meetings.data" :key="m.id" :value="m.id" class="bg-sky-900 text-white">
                            {{ m.title }} ({{ m.date }})
                        </option>
                    </select>
                </div>

                <div v-if="selectedMeeting" class="mb-4">
                    <label class="block text-sm font-medium text-sky-100/80 mb-1">Kategori</label>
                    <select
                        v-model="selectedCategory"
                        @change="onCategoryChange"
                        class="w-full rounded-md border-0 bg-white/10 text-white ring-1 ring-white/15 focus:ring-2 focus:ring-sky-400 sm:max-w-md"
                    >
                        <option value="" class="bg-sky-900 text-white">Semua</option>
                        <option value="matc" class="bg-sky-900 text-white">MATC</option>
                        <option value="amk" class="bg-sky-900 text-white">MATAMKC</option>
                        <option value="wanita" class="bg-sky-900 text-white">MATWC</option>
                    </select>
                </div>

                <div v-if="meeting" class="overflow-hidden rounded-2xl bg-white/10 shadow-lg backdrop-blur-md ring-1 ring-white/15">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-white">{{ meeting.title }}</h3>
                            <div class="flex items-center gap-2">
                                <button
                                    v-if="selectedCategory"
                                    @click="downloadPdf"
                                    :disabled="downloading"
                                    class="inline-flex items-center rounded-md bg-emerald-600 px-3 py-2 text-sm font-semibold text-white shadow-lg shadow-emerald-600/30 hover:bg-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    {{ downloading ? 'Memuat turun...' : 'Muat Turun PDF' }}
                                </button>
                                <Link
                                    v-if="page.props.auth.user.is_admin"
                                    :href="route('attendances.mark', meeting.id)"
                                    class="inline-flex items-center rounded-md bg-sky-500 px-3 py-2 text-sm font-semibold text-white shadow-lg shadow-sky-500/30 hover:bg-sky-400"
                                >
                                    Rekod Kehadiran
                                </Link>
                            </div>
                        </div>

                        <div
                            v-if="attendances.length"
                            ref="tableWrapper"
                            class="overflow-x-auto select-none"
                            style="cursor: grab;"
                            @mousedown="onMouseDown"
                            @mouseleave="onMouseLeave"
                            @mouseup="onMouseUp"
                            @mousemove="onMouseMove"
                        >
                        <table class="min-w-full divide-y divide-white/10">
                            <thead class="bg-white/5">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-sky-200/60">Nama Ahli</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-sky-200/60">No. IC</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-sky-200/60">Kategori</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-sky-200/60">Alamat</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-sky-200/60">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-sky-200/60">Sebab</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-sky-200/60">Usul</th>
                                    <th v-if="page.props.auth.user.is_admin" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-sky-200/60">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                                <tr v-for="attendance in attendances" :key="attendance.id" class="hover:bg-white/5">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-white uppercase">{{ attendance.member?.name }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-sky-200/50 uppercase">{{ attendance.member?.ic_number }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-sky-200/80 uppercase">{{ categoryLabels[attendance.member?.category_type] || '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-sky-200/80 uppercase">{{ attendance.member?.address || '-' }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span
                                            class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                            :class="statusColors[attendance.status] || 'bg-white/10 text-sky-200 ring-1 ring-white/15'"
                                        >
                                            {{ attendance.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-sky-200/80 uppercase">{{ attendance.absence_reason || '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-sky-200/80">{{ attendance.suggestion || '-' }}</td>
                                    <td v-if="page.props.auth.user.is_admin" class="whitespace-nowrap px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <button
                                                @click="openEdit(attendance)"
                                                class="text-sky-400 hover:text-sky-300 text-xs font-medium"
                                            >
                                                Edit
                                            </button>
                                            <button
                                                @click="confirmDelete(attendance)"
                                                class="text-red-400 hover:text-red-300 text-xs font-medium"
                                            >
                                                Padam
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        </div>
                        <p v-else class="text-sm text-sky-200/50">Tiada rekod kehadiran untuk mesyuarat ini.</p>
                    </div>
                </div>

                <div v-else class="overflow-hidden rounded-2xl bg-white/10 shadow-lg backdrop-blur-md ring-1 ring-white/15">
                    <div class="p-6 text-center text-sm text-sky-200/50">
                        Sila pilih mesyuarat untuk melihat rekod kehadiran.
                    </div>
                </div>
            </div>
        </div>
        <!-- Edit Modal -->
        <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60" @click.self="showEditModal = false">
            <div class="w-full max-w-md rounded-2xl bg-sky-950 p-6 ring-1 ring-white/15 shadow-2xl">
                <h3 class="text-lg font-semibold text-white mb-4">Edit Kehadiran</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-sky-100/80 mb-1">Status</label>
                        <select
                            v-model="editForm.status"
                            class="w-full rounded-md border-0 bg-white/10 text-white ring-1 ring-white/15 focus:ring-2 focus:ring-sky-400"
                        >
                            <option value="present" class="bg-sky-900 text-white">Present</option>
                            <option value="absent" class="bg-sky-900 text-white">Absent</option>
                            <option value="late" class="bg-sky-900 text-white">Late</option>
                            <option value="excused" class="bg-sky-900 text-white">Excused</option>
                        </select>
                        <p v-if="editErrors.status" class="mt-1 text-xs text-red-300">{{ editErrors.status }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-sky-100/80 mb-1">Sebab Tidak Hadir</label>
                        <textarea
                            v-model="editForm.absence_reason"
                            rows="3"
                            class="w-full rounded-md border-0 bg-white/10 text-white ring-1 ring-white/15 focus:ring-2 focus:ring-sky-400"
                        ></textarea>
                        <p v-if="editErrors.absence_reason" class="mt-1 text-xs text-red-300">{{ editErrors.absence_reason }}</p>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button @click="showEditModal = false" class="rounded-md px-3 py-2 text-sm font-medium text-sky-200 hover:text-white">Batal</button>
                        <button @click="saveEdit" class="rounded-md bg-sky-500 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-sky-500/30 hover:bg-sky-400">Simpan</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60" @click.self="showDeleteModal = false">
            <div class="w-full max-w-sm rounded-2xl bg-sky-950 p-6 ring-1 ring-white/15 shadow-2xl">
                <h3 class="text-lg font-semibold text-white mb-2">Padam Rekod</h3>
                <p class="text-sm text-sky-200/70 mb-4">Adakah anda pasti ingin memadam rekod kehadiran <strong class="text-white">{{ deleteTarget?.member?.name }}</strong>?</p>
                <div class="flex justify-end gap-3">
                    <button @click="showDeleteModal = false" class="rounded-md px-3 py-2 text-sm font-medium text-sky-200 hover:text-white">Batal</button>
                    <button @click="executeDelete" class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-red-600/30 hover:bg-red-500">Padam</button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
