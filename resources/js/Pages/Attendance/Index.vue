<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    meetings: Object,
    meeting: Object,
    attendances: Array,
    filters: Object,
});

const page = usePage();
const selectedMeeting = ref(props.filters.meeting_id || '');

function onMeetingChange() {
    router.get(route('attendances.index'), { meeting_id: selectedMeeting.value }, {
        preserveState: true,
        replace: true,
    });
}

const statusColors = {
    present: 'bg-emerald-400/15 text-emerald-300 ring-1 ring-emerald-400/20',
    absent: 'bg-red-400/15 text-red-300 ring-1 ring-red-400/20',
    late: 'bg-yellow-400/15 text-yellow-300 ring-1 ring-yellow-400/20',
    excused: 'bg-white/10 text-sky-200 ring-1 ring-white/15',
};
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

                <div v-if="meeting" class="overflow-hidden rounded-2xl bg-white/10 shadow-lg backdrop-blur-md ring-1 ring-white/15">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-white">{{ meeting.title }}</h3>
                            <Link
                                v-if="page.props.auth.user.is_admin"
                                :href="route('attendances.mark', meeting.id)"
                                class="inline-flex items-center rounded-md bg-sky-500 px-3 py-2 text-sm font-semibold text-white shadow-lg shadow-sky-500/30 hover:bg-sky-400"
                            >
                                Rekod Kehadiran
                            </Link>
                        </div>

                        <table v-if="attendances.length" class="min-w-full divide-y divide-white/10">
                            <thead class="bg-white/5">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-sky-200/60">Nama Ahli</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-sky-200/60">No. IC</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-sky-200/60">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                                <tr v-for="attendance in attendances" :key="attendance.id" class="hover:bg-white/5">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-white">{{ attendance.member?.name }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-sky-200/50">{{ attendance.member?.ic_number }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span
                                            class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                            :class="statusColors[attendance.status] || 'bg-white/10 text-sky-200 ring-1 ring-white/15'"
                                        >
                                            {{ attendance.status }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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
    </AuthenticatedLayout>
</template>
