<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    member: Object,
    categories: Array,
});

const categoryLabels = {
    anggota: 'Anggota',
    ajk_cabang: 'AJK Cabang',
    amk: 'AMK',
    wanita: 'Wanita',
};

const form = useForm({
    category_type: props.member.category_type,
    name: props.member.name,
    ic_number: props.member.ic_number,
    phone_number: props.member.phone_number || '',
    address: props.member.address || '',
    position_type: props.member.position_type || '',
    position_name: props.member.position_name || '',
});

function submit() {
    form.put(route('members.update', props.member.id));
}
</script>

<template>
    <Head title="Ubah Ahli" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-white">Ubah Ahli</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-2xl bg-white/10 shadow-lg backdrop-blur-md ring-1 ring-white/15">
                    <form @submit.prevent="submit" class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-sky-100/80">Kategori</label>
                            <select v-model="form.category_type" class="mt-1 block w-full rounded-md border-0 bg-white/10 text-white ring-1 ring-white/15 focus:ring-2 focus:ring-sky-400">
                                <option value="" class="bg-sky-900 text-white">-- Pilih Kategori --</option>
                                <option v-for="cat in categories" :key="cat.value" :value="cat.value" class="bg-sky-900 text-white">
                                    {{ categoryLabels[cat.value] || cat.value }}
                                </option>
                            </select>
                            <p v-if="form.errors.category_type" class="mt-1 text-sm text-red-300">{{ form.errors.category_type }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-sky-100/80">Nama</label>
                            <input v-model="form.name" type="text" class="mt-1 block w-full rounded-md border-0 bg-white/10 text-white ring-1 ring-white/15 focus:ring-2 focus:ring-sky-400 placeholder-sky-200/40" />
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-300">{{ form.errors.name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-sky-100/80">No. IC</label>
                            <input v-model="form.ic_number" type="text" class="mt-1 block w-full rounded-md border-0 bg-white/10 text-white ring-1 ring-white/15 focus:ring-2 focus:ring-sky-400 placeholder-sky-200/40" />
                            <p v-if="form.errors.ic_number" class="mt-1 text-sm text-red-300">{{ form.errors.ic_number }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-sky-100/80">Telefon</label>
                            <input v-model="form.phone_number" type="text" class="mt-1 block w-full rounded-md border-0 bg-white/10 text-white ring-1 ring-white/15 focus:ring-2 focus:ring-sky-400 placeholder-sky-200/40" />
                            <p v-if="form.errors.phone_number" class="mt-1 text-sm text-red-300">{{ form.errors.phone_number }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-sky-100/80">Alamat</label>
                            <textarea v-model="form.address" rows="3" class="mt-1 block w-full rounded-md border-0 bg-white/10 text-white ring-1 ring-white/15 focus:ring-2 focus:ring-sky-400 placeholder-sky-200/40"></textarea>
                            <p v-if="form.errors.address" class="mt-1 text-sm text-red-300">{{ form.errors.address }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-sky-100/80">Jenis Jawatan</label>
                                <input v-model="form.position_type" type="text" class="mt-1 block w-full rounded-md border-0 bg-white/10 text-white ring-1 ring-white/15 focus:ring-2 focus:ring-sky-400 placeholder-sky-200/40" />
                                <p v-if="form.errors.position_type" class="mt-1 text-sm text-red-300">{{ form.errors.position_type }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-sky-100/80">Nama Jawatan</label>
                                <input v-model="form.position_name" type="text" class="mt-1 block w-full rounded-md border-0 bg-white/10 text-white ring-1 ring-white/15 focus:ring-2 focus:ring-sky-400 placeholder-sky-200/40" />
                                <p v-if="form.errors.position_name" class="mt-1 text-sm text-red-300">{{ form.errors.position_name }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <button type="submit" :disabled="form.processing" class="inline-flex items-center rounded-md bg-sky-500 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-sky-500/30 hover:bg-sky-400 disabled:opacity-50">
                                Kemaskini
                            </button>
                            <Link :href="route('members.index')" class="text-sm text-sky-200/60 hover:text-sky-100">Batal</Link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
