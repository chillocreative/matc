<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import KehadiranForm from '@/Components/KehadiranForm.vue';

const props = defineProps({
    category: String,
    categoryLabel: String,
    meeting: Object,
    attendances: Array,
    verifyUrl: String,
    formToken: String,
    recaptchaSiteKey: String,
});

const categoryColors = {
    matc: { text: 'text-sky-300', badge: 'bg-sky-400/15 text-sky-300 ring-sky-400/20' },
    wanita: { text: 'text-rose-300', badge: 'bg-rose-400/15 text-rose-300 ring-rose-400/20' },
    amk: { text: 'text-emerald-300', badge: 'bg-emerald-400/15 text-emerald-300 ring-emerald-400/20' },
};

const colors = categoryColors[props.category] || categoryColors.matc;

function onVerified() {
    router.reload({ only: ['attendances'] });
}
</script>

<template>
    <Head :title="`Kehadiran ${categoryLabel}`" />

    <div class="min-h-screen bg-gradient-to-br from-sky-950 via-sky-900 to-blue-950 relative overflow-hidden">
        <!-- Background pattern -->
        <div class="absolute inset-0 opacity-[0.04]" style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;1&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')"></div>

        <!-- Decorative circles -->
        <div class="absolute -top-40 -right-40 h-96 w-96 rounded-full bg-sky-400/10 blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 h-96 w-96 rounded-full bg-blue-400/10 blur-3xl"></div>

        <!-- Nav -->
        <nav class="relative z-10 flex items-center justify-between px-4 py-3 sm:px-8 sm:py-4">
            <Link href="/" class="inline-flex items-center gap-1.5 text-xs text-sky-300/60 transition hover:text-sky-200 sm:gap-2 sm:text-sm">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Kembali
            </Link>
            <span :class="[colors.badge, 'inline-flex items-center rounded-full px-2.5 py-0.5 text-[10px] font-medium uppercase tracking-wider backdrop-blur-sm ring-1 sm:px-3 sm:py-1 sm:text-xs']">
                {{ categoryLabel }}
            </span>
        </nav>

        <!-- Content -->
        <div class="relative z-10 mx-auto max-w-lg px-4 pb-8 sm:px-6 sm:pb-12">
            <!-- Header -->
            <div class="mb-6 text-center sm:mb-8">
                <img src="/logo-keadilan.png" alt="Logo Keadilan" class="mx-auto mb-3 w-20 sm:w-24 object-contain drop-shadow-2xl" />
                <h1 class="text-xl font-bold text-white sm:text-2xl lg:text-3xl">Pengesahan Kehadiran</h1>
                <div v-if="meeting" class="mt-3 flex flex-col items-center gap-1.5 text-xs text-sky-200/60 sm:mt-4 sm:text-sm">
                    <p class="font-medium text-sky-100/80">{{ meeting.title }}</p>
                    <div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-1">
                        <span class="inline-flex items-center gap-1">
                            <svg class="h-3.5 w-3.5 text-sky-300/60" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                            {{ meeting.date }}
                        </span>
                        <span class="inline-flex items-center gap-1">
                            <svg class="h-3.5 w-3.5 text-sky-300/60" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            11:30 pagi
                        </span>
                        <span class="inline-flex items-center gap-1">
                            <svg class="h-3.5 w-3.5 text-sky-300/60" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                            {{ meeting.location }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- No Meeting -->
            <div v-if="!meeting" class="rounded-2xl bg-white/10 p-6 shadow-xl backdrop-blur-md ring-1 ring-white/20 text-center sm:p-8">
                <svg class="mx-auto h-10 w-10 text-sky-300/40 sm:h-12 sm:w-12" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                </svg>
                <h2 class="mt-3 text-base font-semibold text-white sm:mt-4 sm:text-lg">Tiada Mesyuarat Aktif</h2>
                <p class="mt-1.5 text-xs text-sky-200/50 sm:mt-2 sm:text-sm">Sila hubungi pentadbir untuk maklumat lanjut.</p>
            </div>

            <!-- Form Card -->
            <template v-else>
                <div class="rounded-2xl bg-white/10 p-4 shadow-xl backdrop-blur-md ring-1 ring-white/20 sm:p-6 lg:p-8">
                    <h2 class="text-base font-semibold text-white mb-4 sm:text-lg sm:mb-5">Maklumat Kehadiran</h2>
                    <KehadiranForm
                        :category="category"
                        :meeting-id="meeting.id"
                        :verify-url="verifyUrl"
                        :form-token="formToken || ''"
                        :recaptcha-site-key="recaptchaSiteKey || ''"
                        :dark="true"
                        @verified="onVerified"
                    />
                </div>

            </template>
        </div>
    </div>
</template>
