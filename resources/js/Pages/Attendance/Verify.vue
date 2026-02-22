<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link, usePage } from '@inertiajs/vue3';

const props = defineProps({
    meeting: Object,
    attendances: Array,
});

const page = usePage();

const form = useForm({
    meeting_id: props.meeting.id,
    ic_number: '',
});

function submit() {
    form.post(route('attendances.verify'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('ic_number');
        },
    });
}
</script>

<template>
    <Head title="Pengesahan Kehadiran" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-white">Pengesahan: {{ meeting.title }}</h2>
                <Link :href="route('meetings.show', meeting.id)" class="text-sm text-sky-300 hover:text-sky-200">Kembali ke mesyuarat</Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div v-if="page.props.flash.success" class="mb-4 rounded-md bg-emerald-400/15 p-4 ring-1 ring-emerald-400/20">
                    <p class="text-sm font-medium text-emerald-300">{{ page.props.flash.success }}</p>
                </div>
                <div v-if="page.props.flash.error" class="mb-4 rounded-md bg-red-400/15 p-4 ring-1 ring-red-400/20">
                    <p class="text-sm font-medium text-red-300">{{ page.props.flash.error }}</p>
                </div>

                <div class="overflow-hidden rounded-2xl bg-white/10 shadow-lg backdrop-blur-md ring-1 ring-white/15">
                    <form @submit.prevent="submit" class="p-6">
                        <h3 class="text-lg font-medium text-white mb-4">Imbas / Masukkan No. IC</h3>
                        <div class="flex gap-4">
                            <input
                                v-model="form.ic_number"
                                type="text"
                                placeholder="No. IC (cth: 880101145678)"
                                autofocus
                                class="flex-1 rounded-md border-0 bg-white/10 text-white ring-1 ring-white/15 focus:ring-2 focus:ring-sky-400 text-lg placeholder-sky-200/40"
                            />
                            <button
                                type="submit"
                                :disabled="form.processing || !form.ic_number"
                                class="inline-flex items-center rounded-md bg-emerald-500/80 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-500/20 hover:bg-emerald-400 disabled:opacity-50"
                            >
                                Sahkan
                            </button>
                        </div>
                        <p v-if="form.errors.ic_number" class="mt-2 text-sm text-red-300">{{ form.errors.ic_number }}</p>
                        <p v-if="form.errors.meeting_id" class="mt-2 text-sm text-red-300">{{ form.errors.meeting_id }}</p>
                    </form>
                </div>

                <div class="mt-8 overflow-hidden rounded-2xl bg-white/10 shadow-lg backdrop-blur-md ring-1 ring-white/15">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-white">
                            Senarai Pengesahan
                            <span class="text-sm font-normal text-sky-200/50">({{ attendances.length }} orang)</span>
                        </h3>
                        <table v-if="attendances.length" class="mt-4 min-w-full divide-y divide-white/10">
                            <thead class="bg-white/5">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-sky-200/60">#</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-sky-200/60">Nama</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-sky-200/60">No. IC</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-sky-200/60">Masa</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                                <tr v-for="(att, i) in attendances" :key="att.id" class="hover:bg-white/5">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-sky-200/50">{{ i + 1 }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-white">{{ att.member?.name }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-sky-200/50">{{ att.member?.ic_number }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-sky-200/50">{{ att.created_at }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <p v-else class="mt-4 text-sm text-sky-200/50">Belum ada pengesahan kehadiran.</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
