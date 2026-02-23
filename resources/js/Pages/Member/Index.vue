<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    members: Object,
    filters: Object,
});

const page = usePage();
const search = ref(props.filters.search || '');
const selectedCategory = ref(props.filters.category || '');

function applyFilters() {
    const params = {};
    if (search.value) params.search = search.value;
    if (selectedCategory.value) params.category = selectedCategory.value;
    router.get(route('members.index'), params, {
        preserveState: true,
        replace: true,
    });
}

watch(search, () => applyFilters());

function onCategoryChange() {
    applyFilters();
}

function destroy(id) {
    if (confirm('Adakah anda pasti mahu memadam ahli ini?')) {
        router.delete(route('members.destroy', id));
    }
}

const categoryLabels = {
    matc: 'MATC',
    amk: 'MATAMKC',
    wanita: 'MATWC',
};
</script>

<template>
    <Head title="Ahli" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-white">Senarai Ahli</h2>
                <Link
                    v-if="page.props.auth.user.is_admin"
                    :href="route('members.create')"
                    class="inline-flex items-center rounded-md bg-sky-500 px-3 py-2 text-sm font-semibold text-white shadow-lg shadow-sky-500/30 hover:bg-sky-400"
                >
                    Tambah Ahli
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div v-if="page.props.flash.success" class="mb-4 rounded-md bg-emerald-400/15 p-4 ring-1 ring-emerald-400/20">
                    <p class="text-sm text-emerald-300">{{ page.props.flash.success }}</p>
                </div>

                <div class="mb-4 flex flex-wrap items-end gap-4">
                    <div>
                        <label class="block text-sm font-medium text-sky-100/80 mb-1">Cari</label>
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Cari ahli..."
                            class="w-full rounded-md border-0 bg-white/10 text-white ring-1 ring-white/15 placeholder-sky-200/40 focus:ring-2 focus:ring-sky-400 sm:w-64"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-sky-100/80 mb-1">Kategori</label>
                        <select
                            v-model="selectedCategory"
                            @change="onCategoryChange"
                            class="w-full rounded-md border-0 bg-white/10 text-white ring-1 ring-white/15 focus:ring-2 focus:ring-sky-400 sm:w-48"
                        >
                            <option value="" class="bg-sky-900 text-white">Semua</option>
                            <option value="matc" class="bg-sky-900 text-white">MATC</option>
                            <option value="amk" class="bg-sky-900 text-white">MATAMKC</option>
                            <option value="wanita" class="bg-sky-900 text-white">MATWC</option>
                        </select>
                    </div>
                    <a
                        v-if="selectedCategory"
                        :href="route('export.members.pdf', { category: selectedCategory })"
                        class="inline-flex items-center rounded-md bg-emerald-600 px-3 py-2 text-sm font-semibold text-white shadow-lg shadow-emerald-600/30 hover:bg-emerald-500"
                    >
                        Muat Turun PDF
                    </a>
                </div>

                <div class="overflow-hidden rounded-2xl bg-white/10 shadow-lg backdrop-blur-md ring-1 ring-white/15">
                    <table class="min-w-full divide-y divide-white/10">
                        <thead class="bg-white/5">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-sky-200/60">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-sky-200/60">No. IC</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-sky-200/60">Kategori</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-sky-200/60">Telefon</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-sky-200/60">Alamat</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-sky-200/60">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            <tr v-for="member in members.data" :key="member.id" class="hover:bg-white/5">
                                <td class="whitespace-nowrap px-6 py-4 uppercase">
                                    <Link :href="route('members.show', member.id)" class="text-sky-300 hover:text-sky-200">
                                        {{ member.name }}
                                    </Link>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-sky-200/50 uppercase">{{ member.ic_number }}</td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="inline-flex items-center rounded-full bg-sky-400/15 px-2.5 py-0.5 text-xs font-medium text-sky-300 ring-1 ring-sky-400/20 uppercase">
                                        {{ categoryLabels[member.category_type] || member.category_type }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-sky-200/50 uppercase">{{ member.phone_number }}</td>
                                <td class="px-6 py-4 text-sm text-sky-200/50 uppercase">{{ member.address || '-' }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                    <template v-if="page.props.auth.user.is_admin">
                                        <Link :href="route('members.edit', member.id)" class="text-sky-300 hover:text-sky-200 mr-3">Ubah</Link>
                                        <button @click="destroy(member.id)" class="text-red-300 hover:text-red-200">Padam</button>
                                    </template>
                                </td>
                            </tr>
                            <tr v-if="!members.data.length">
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-sky-200/50">Tiada ahli dijumpai.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="members.links.length > 3" class="mt-4 flex justify-center">
                    <template v-for="link in members.links" :key="link.label">
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
