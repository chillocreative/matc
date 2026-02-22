<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';

const props = defineProps({
    meeting: Object,
});

const page = usePage();

const statusColors = {
    present: 'bg-emerald-400/15 text-emerald-300 ring-1 ring-emerald-400/20',
    absent: 'bg-red-400/15 text-red-300 ring-1 ring-red-400/20',
    late: 'bg-yellow-400/15 text-yellow-300 ring-1 ring-yellow-400/20',
    excused: 'bg-white/10 text-sky-200 ring-1 ring-white/15',
};
</script>

<template>
    <Head :title="meeting.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-white">{{ meeting.title }}</h2>
                <div class="flex gap-3">
                    <Link
                        v-if="page.props.auth.user.is_admin"
                        :href="route('attendances.mark', meeting.id)"
                        class="inline-flex items-center rounded-md bg-emerald-500/80 px-3 py-2 text-sm font-semibold text-white shadow-lg shadow-emerald-500/20 hover:bg-emerald-400"
                    >
                        Rekod Kehadiran
                    </Link>
                    <Link :href="route('meetings.index')" class="text-sm text-sky-300 hover:text-sky-200 self-center">Kembali ke senarai</Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-2xl bg-white/10 shadow-lg backdrop-blur-md ring-1 ring-white/15">
                    <div class="p-6">
                        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-sky-200/50">Tajuk</dt>
                                <dd class="mt-1 text-sm text-white">{{ meeting.title }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-sky-200/50">Status</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-sky-400/15 text-sky-300 ring-1 ring-sky-400/20">
                                        {{ meeting.status }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-sky-200/50">Tarikh</dt>
                                <dd class="mt-1 text-sm text-white">{{ meeting.date }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-sky-200/50">Masa</dt>
                                <dd class="mt-1 text-sm text-white">{{ meeting.time || '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-sky-200/50">Lokasi</dt>
                                <dd class="mt-1 text-sm text-white">{{ meeting.location || '-' }}</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-sky-200/50">Penerangan</dt>
                                <dd class="mt-1 text-sm text-white">{{ meeting.description || '-' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <div class="mt-8 overflow-hidden rounded-2xl bg-white/10 shadow-lg backdrop-blur-md ring-1 ring-white/15">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-white">Senarai Kehadiran</h3>
                        <table v-if="meeting.attendances?.length" class="mt-4 min-w-full divide-y divide-white/10">
                            <thead class="bg-white/5">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-sky-200/60">Nama Ahli</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-sky-200/60">No. IC</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-sky-200/60">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                                <tr v-for="attendance in meeting.attendances" :key="attendance.id" class="hover:bg-white/5">
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
                        <p v-else class="mt-4 text-sm text-sky-200/50">Tiada rekod kehadiran untuk mesyuarat ini.</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
