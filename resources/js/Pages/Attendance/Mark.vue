<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    meeting: Object,
    members: Object,
    attendances: Array,
    statuses: Array,
});

const existingMap = {};
props.attendances.forEach(a => {
    existingMap[a.member_id] = a.status;
});

const form = useForm({
    meeting_id: props.meeting.id,
    attendances: props.members.data.map(member => ({
        member_id: member.id,
        member_name: member.name,
        member_ic: member.ic_number,
        status: existingMap[member.id] || 'present',
    })),
});

function submit() {
    form.post(route('attendances.store'));
}

const statusLabels = {
    present: 'Hadir',
    absent: 'Tidak Hadir',
    late: 'Lewat',
    excused: 'Dimaafkan',
};

const categoryLabels = {
    anggota: 'Anggota',
    ajk_cabang: 'AJK Cabang',
    amk: 'AMK',
    wanita: 'Wanita',
};
</script>

<template>
    <Head title="Rekod Kehadiran" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-white">Rekod Kehadiran: {{ meeting.title }}</h2>
                <Link :href="route('meetings.show', meeting.id)" class="text-sm text-sky-300 hover:text-sky-200">Kembali ke mesyuarat</Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-2xl bg-white/10 shadow-lg backdrop-blur-md ring-1 ring-white/15">
                    <form @submit.prevent="submit" class="p-6">
                        <table class="min-w-full divide-y divide-white/10">
                            <thead class="bg-white/5">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-sky-200/60">Nama</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-sky-200/60">No. IC</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-sky-200/60">Status Kehadiran</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                                <tr v-for="(item, index) in form.attendances" :key="item.member_id" class="hover:bg-white/5">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-white">{{ item.member_name }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-sky-200/50">{{ item.member_ic }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <select
                                            v-model="form.attendances[index].status"
                                            class="rounded-md border-0 bg-white/10 text-white ring-1 ring-white/15 focus:ring-2 focus:ring-sky-400 text-sm"
                                        >
                                            <option v-for="status in statuses" :key="status.value" :value="status.value" class="bg-sky-900 text-white">
                                                {{ statusLabels[status.value] || status.value }}
                                            </option>
                                        </select>
                                    </td>
                                </tr>
                                <tr v-if="!form.attendances.length">
                                    <td colspan="3" class="px-6 py-4 text-center text-sm text-sky-200/50">Tiada ahli berdaftar.</td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="mt-6 flex items-center gap-4">
                            <button
                                type="submit"
                                :disabled="form.processing || !form.attendances.length"
                                class="inline-flex items-center rounded-md bg-sky-500 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-sky-500/30 hover:bg-sky-400 disabled:opacity-50"
                            >
                                Simpan Kehadiran
                            </button>
                            <Link :href="route('meetings.show', meeting.id)" class="text-sm text-sky-200/60 hover:text-sky-100">Batal</Link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
