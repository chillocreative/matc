<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    member: Object,
});

const categoryLabels = {
    anggota: 'Anggota',
    ajk_cabang: 'AJK Cabang',
    amk: 'AMK',
    wanita: 'Wanita',
};
</script>

<template>
    <Head :title="member.name" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-white">{{ member.name }}</h2>
                <Link :href="route('members.index')" class="text-sm text-sky-300 hover:text-sky-200">Kembali ke senarai</Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-2xl bg-white/10 shadow-lg backdrop-blur-md ring-1 ring-white/15">
                    <div class="p-6">
                        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-sky-200/50">Nama</dt>
                                <dd class="mt-1 text-sm text-white">{{ member.name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-sky-200/50">No. IC</dt>
                                <dd class="mt-1 text-sm text-white">{{ member.ic_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-sky-200/50">Kategori</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center rounded-full bg-sky-400/15 px-2.5 py-0.5 text-xs font-medium text-sky-300 ring-1 ring-sky-400/20">
                                        {{ categoryLabels[member.category_type] || member.category_type }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-sky-200/50">Telefon</dt>
                                <dd class="mt-1 text-sm text-white">{{ member.phone_number || '-' }}</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-sky-200/50">Alamat</dt>
                                <dd class="mt-1 text-sm text-white">{{ member.address || '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-sky-200/50">Jenis Jawatan</dt>
                                <dd class="mt-1 text-sm text-white">{{ member.position_type || '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-sky-200/50">Nama Jawatan</dt>
                                <dd class="mt-1 text-sm text-white">{{ member.position_name || '-' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <div class="mt-8 overflow-hidden rounded-2xl bg-white/10 shadow-lg backdrop-blur-md ring-1 ring-white/15">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-white">Rekod Kehadiran</h3>
                        <table v-if="member.attendances?.length" class="mt-4 min-w-full divide-y divide-white/10">
                            <thead class="bg-white/5">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-sky-200/60">Mesyuarat</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-sky-200/60">Tarikh</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-sky-200/60">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                                <tr v-for="attendance in member.attendances" :key="attendance.id" class="hover:bg-white/5">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-white">{{ attendance.meeting?.title }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-sky-200/50">{{ attendance.meeting?.date }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span
                                            class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                            :class="{
                                                'bg-emerald-400/15 text-emerald-300 ring-1 ring-emerald-400/20': attendance.status === 'present',
                                                'bg-red-400/15 text-red-300 ring-1 ring-red-400/20': attendance.status === 'absent',
                                                'bg-yellow-400/15 text-yellow-300 ring-1 ring-yellow-400/20': attendance.status === 'late',
                                                'bg-white/10 text-sky-200 ring-1 ring-white/15': attendance.status === 'excused',
                                            }"
                                        >
                                            {{ attendance.status }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <p v-else class="mt-4 text-sm text-sky-200/50">Tiada rekod kehadiran.</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
