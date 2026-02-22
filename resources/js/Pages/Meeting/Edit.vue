<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    meeting: Object,
    statuses: Array,
});

const form = useForm({
    title: props.meeting.title,
    description: props.meeting.description || '',
    date: props.meeting.date,
    time: props.meeting.time || '',
    location: props.meeting.location || '',
    status: props.meeting.status,
});

function submit() {
    form.put(route('meetings.update', props.meeting.id));
}
</script>

<template>
    <Head title="Ubah Mesyuarat" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-white">Ubah Mesyuarat</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-2xl bg-white/10 shadow-lg backdrop-blur-md ring-1 ring-white/15">
                    <form @submit.prevent="submit" class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-sky-100/80">Tajuk</label>
                            <input v-model="form.title" type="text" class="mt-1 block w-full rounded-md border-0 bg-white/10 text-white ring-1 ring-white/15 focus:ring-2 focus:ring-sky-400 placeholder-sky-200/40" />
                            <p v-if="form.errors.title" class="mt-1 text-sm text-red-300">{{ form.errors.title }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-sky-100/80">Penerangan</label>
                            <textarea v-model="form.description" rows="3" class="mt-1 block w-full rounded-md border-0 bg-white/10 text-white ring-1 ring-white/15 focus:ring-2 focus:ring-sky-400 placeholder-sky-200/40"></textarea>
                            <p v-if="form.errors.description" class="mt-1 text-sm text-red-300">{{ form.errors.description }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-sky-100/80">Tarikh</label>
                                <input v-model="form.date" type="date" class="mt-1 block w-full rounded-md border-0 bg-white/10 text-white ring-1 ring-white/15 focus:ring-2 focus:ring-sky-400" />
                                <p v-if="form.errors.date" class="mt-1 text-sm text-red-300">{{ form.errors.date }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-sky-100/80">Masa</label>
                                <input v-model="form.time" type="time" class="mt-1 block w-full rounded-md border-0 bg-white/10 text-white ring-1 ring-white/15 focus:ring-2 focus:ring-sky-400" />
                                <p v-if="form.errors.time" class="mt-1 text-sm text-red-300">{{ form.errors.time }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-sky-100/80">Lokasi</label>
                            <input v-model="form.location" type="text" class="mt-1 block w-full rounded-md border-0 bg-white/10 text-white ring-1 ring-white/15 focus:ring-2 focus:ring-sky-400 placeholder-sky-200/40" />
                            <p v-if="form.errors.location" class="mt-1 text-sm text-red-300">{{ form.errors.location }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-sky-100/80">Status</label>
                            <select v-model="form.status" class="mt-1 block w-full rounded-md border-0 bg-white/10 text-white ring-1 ring-white/15 focus:ring-2 focus:ring-sky-400">
                                <option v-for="status in statuses" :key="status.value" :value="status.value" class="bg-sky-900 text-white">{{ status.value }}</option>
                            </select>
                            <p v-if="form.errors.status" class="mt-1 text-sm text-red-300">{{ form.errors.status }}</p>
                        </div>

                        <div class="flex items-center gap-4">
                            <button type="submit" :disabled="form.processing" class="inline-flex items-center rounded-md bg-sky-500 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-sky-500/30 hover:bg-sky-400 disabled:opacity-50">
                                Kemaskini
                            </button>
                            <Link :href="route('meetings.index')" class="text-sm text-sky-200/60 hover:text-sky-100">Batal</Link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
