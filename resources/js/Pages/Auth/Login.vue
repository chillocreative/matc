<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log Masuk" />

        <h2 class="text-center text-lg font-semibold text-white mb-1">Log Masuk</h2>
        <p class="text-center text-sm text-sky-200/60 mb-6">Sistem Pengurusan MATC</p>

        <div v-if="status" class="mb-4 rounded-md bg-green-500/20 p-3 text-sm font-medium text-green-300 ring-1 ring-green-400/30">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <label for="email" class="block text-sm font-medium text-sky-100/80">Emel</label>
                <input
                    id="email"
                    type="email"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    class="mt-1 block w-full rounded-lg border-0 bg-white/10 px-4 py-2.5 text-white placeholder-sky-300/40 shadow-sm ring-1 ring-white/15 focus:ring-2 focus:ring-sky-400 sm:text-sm"
                    placeholder="admin@matc.local"
                />
                <p v-if="form.errors.email" class="mt-1.5 text-sm text-red-300">{{ form.errors.email }}</p>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-sky-100/80">Kata Laluan</label>
                <input
                    id="password"
                    type="password"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    class="mt-1 block w-full rounded-lg border-0 bg-white/10 px-4 py-2.5 text-white placeholder-sky-300/40 shadow-sm ring-1 ring-white/15 focus:ring-2 focus:ring-sky-400 sm:text-sm"
                />
                <p v-if="form.errors.password" class="mt-1.5 text-sm text-red-300">{{ form.errors.password }}</p>
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <input
                        type="checkbox"
                        v-model="form.remember"
                        class="h-4 w-4 rounded border-white/20 bg-white/10 text-sky-500 focus:ring-sky-400 focus:ring-offset-0"
                    />
                    <span class="ms-2 text-sm text-sky-200/60">Ingat saya</span>
                </label>

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm text-sky-300/70 hover:text-sky-200 transition"
                >
                    Lupa kata laluan?
                </Link>
            </div>

            <button
                type="submit"
                :disabled="form.processing"
                class="w-full rounded-xl bg-sky-500 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-sky-500/30 transition hover:bg-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-400 focus:ring-offset-2 focus:ring-offset-sky-950 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <span v-if="form.processing" class="inline-flex items-center gap-2">
                    <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Sedang log masuk...
                </span>
                <span v-else>Log Masuk</span>
            </button>
        </form>
    </GuestLayout>
</template>
