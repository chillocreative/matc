<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    meetings: Object,
    filters: Object,
});

const page = usePage();
const search = ref(props.filters.search || '');

watch(search, (value) => {
    router.get(route('meetings.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
});

function destroy(id) {
    if (confirm('Adakah anda pasti mahu memadam mesyuarat ini?')) {
        router.delete(route('meetings.destroy', id));
    }
}

const statusColors = {
    scheduled: 'bg-sky-400/15 text-sky-300 ring-1 ring-sky-400/20',
    ongoing: 'bg-yellow-400/15 text-yellow-300 ring-1 ring-yellow-400/20',
    completed: 'bg-emerald-400/15 text-emerald-300 ring-1 ring-emerald-400/20',
    cancelled: 'bg-red-400/15 text-red-300 ring-1 ring-red-400/20',
};
</script>

<template>
    <Head title="Mesyuarat" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-white">Senarai Mesyuarat</h2>
                <Link
                    v-if="page.props.auth.user.is_admin"
                    :href="route('meetings.create')"
                    class="inline-flex items-center rounded-md bg-sky-500 px-3 py-2 text-sm font-semibold text-white shadow-lg shadow-sky-500/30 hover:bg-sky-400"
                >
                    Tambah Mesyuarat
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div v-if="page.props.flash.success" class="mb-4 rounded-md bg-emerald-400/15 p-4 ring-1 ring-emerald-400/20">
                    <p class="text-sm text-emerald-300">{{ page.props.flash.success }}</p>
                </div>

                <div class="mb-4">
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari mesyuarat..."
                        class="w-full rounded-md border-0 bg-white/10 text-white ring-1 ring-white/15 placeholder-sky-200/40 focus:ring-2 focus:ring-sky-400 sm:max-w-xs"
                    />
                </div>

                <div class="overflow-hidden rounded-2xl bg-white/10 shadow-lg backdrop-blur-md ring-1 ring-white/15">
                    <table class="min-w-full divide-y divide-white/10">
                        <thead class="bg-white/5">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-sky-200/60">Tajuk</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-sky-200/60">Tarikh</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-sky-200/60">Lokasi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-sky-200/60">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-sky-200/60">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            <tr v-for="meeting in meetings.data" :key="meeting.id" class="hover:bg-white/5">
                                <td class="whitespace-nowrap px-6 py-4">
                                    <Link :href="route('meetings.show', meeting.id)" class="text-sky-300 hover:text-sky-200">
                                        {{ meeting.title }}
                                    </Link>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-sky-200/50">{{ meeting.date }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-sky-200/50">{{ meeting.location }}</td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium" :class="statusColors[meeting.status] || 'bg-white/10 text-sky-200 ring-1 ring-white/15'">
                                        {{ meeting.status }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                    <Link :href="route('attendances.mark', meeting.id)" class="text-emerald-300 hover:text-emerald-200 mr-3" v-if="page.props.auth.user.is_admin">Kehadiran</Link>
                                    <template v-if="page.props.auth.user.is_admin">
                                        <Link :href="route('meetings.edit', meeting.id)" class="text-sky-300 hover:text-sky-200 mr-3">Ubah</Link>
                                        <button @click="destroy(meeting.id)" class="text-red-300 hover:text-red-200">Padam</button>
                                    </template>
                                </td>
                            </tr>
                            <tr v-if="!meetings.data.length">
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-sky-200/50">Tiada mesyuarat dijumpai.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="meetings.links.length > 3" class="mt-4 flex justify-center">
                    <template v-for="link in meetings.links" :key="link.label">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            v-html="link.label"
                            class="mx-1 rounded border px-3 py-1 text-sm"
                            :class="link.active ? 'bg-sky-500 text-white border-sky-500' : 'bg-white/10 text-sky-100 border-white/15 hover:bg-white/20'"
                        />
                        <span v-else v-html="link.label" class="mx-1 rounded border border-white/10 bg-white/5 px-3 py-1 text-sm text-sky-300/40" />
                    </template>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
