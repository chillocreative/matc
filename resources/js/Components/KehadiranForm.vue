<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
    category: {
        type: String,
        required: true,
    },
    meetingId: {
        type: Number,
        required: true,
    },
    verifyUrl: {
        type: String,
        required: true,
    },
    formToken: {
        type: String,
        default: '',
    },
    recaptchaSiteKey: {
        type: String,
        default: '',
    },
    dark: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['verified']);

const name = ref('');
const ic_number = ref('');
const phone_number = ref('');
const address = ref('');
const position_type = ref('');
const website = ref('');

const loading = ref(false);
const errors = ref({});
const successMessage = ref('');
const errorMessage = ref('');

const recaptchaReady = ref(false);

const positionOptions = computed(() => {
    const positions = {
        matc: [
            'Anggota Biasa',
            'Ketua Cabang',
            'Timbalan Ketua Cabang',
            'Naib Ketua Cabang',
            'Setiausaha',
            'Setiausaha Pengelola',
            'Ketua Penerangan',
            'AJK',
        ],
        amk: [
            'Anggota Biasa',
            'Ketua AMK',
            'Timbalan Ketua AMK',
            'Naib Ketua AMK',
            'Setiausaha',
            'Ketua Penerangan',
            'AJK',
        ],
        wanita: [
            'Anggota Biasa',
            'Ketua Wanita',
            'Timbalan Ketua Wanita',
            'Naib Ketua Wanita',
            'Setiausaha',
            'Ketua Penerangan',
            'AJK',
        ],
    };

    return (positions[props.category] || []).map(p => ({ value: p, label: p }));
});

onMounted(() => {
    if (props.recaptchaSiteKey) {
        const script = document.createElement('script');
        script.src = `https://www.google.com/recaptcha/api.js?render=${props.recaptchaSiteKey}`;
        script.onload = () => { recaptchaReady.value = true; };
        document.head.appendChild(script);
    }
});

function validateIcFormat(ic) {
    if (ic.length !== 12) return false;

    const month = parseInt(ic.substring(2, 4), 10);
    const day = parseInt(ic.substring(4, 6), 10);
    const state = parseInt(ic.substring(6, 8), 10);

    if (month < 1 || month > 12) return false;
    if (day < 1 || day > 31) return false;

    return (state >= 1 && state <= 16)
        || (state >= 21 && state <= 59)
        || (state >= 60 && state <= 76)
        || (state >= 82 && state <= 93)
        || state === 98 || state === 99;
}

function validateForm() {
    const e = {};

    if (!name.value.trim()) {
        e.name = 'Nama wajib diisi.';
    }

    const icDigits = ic_number.value.replace(/\D/g, '');
    if (!icDigits) {
        e.ic_number = 'No. IC wajib diisi.';
    } else if (icDigits.length !== 12) {
        e.ic_number = 'No. IC mestilah 12 digit.';
    } else if (!validateIcFormat(icDigits)) {
        e.ic_number = 'Format No. IC tidak sah.';
    }

    if (!position_type.value) {
        e.position_type = 'Sila pilih jenis jawatan.';
    }

    errors.value = e;
    return Object.keys(e).length === 0;
}

async function getRecaptchaToken() {
    if (!props.recaptchaSiteKey || !recaptchaReady.value) return null;

    return new Promise((resolve) => {
        window.grecaptcha.ready(() => {
            window.grecaptcha
                .execute(props.recaptchaSiteKey, { action: 'verify_attendance' })
                .then(resolve);
        });
    });
}

async function submit() {
    if (!validateForm()) return;

    loading.value = true;
    successMessage.value = '';
    errorMessage.value = '';

    try {
        const recaptchaToken = await getRecaptchaToken();

        const payload = {
            name: name.value.trim(),
            ic_number: ic_number.value.replace(/\D/g, ''),
            phone_number: phone_number.value.trim() || null,
            address: address.value.trim() || null,
            position_type: position_type.value,
            website: website.value,
            _ft: props.formToken,
        };

        if (recaptchaToken) {
            payload.recaptcha_token = recaptchaToken;
        }

        const response = await axios.post(props.verifyUrl, payload);

        successMessage.value = response.data.message;
        resetForm();
        emit('verified');
    } catch (error) {
        if (error.response?.status === 422) {
            const serverErrors = error.response.data.errors || {};
            if (Object.keys(serverErrors).length) {
                errors.value = {};
                for (const [field, messages] of Object.entries(serverErrors)) {
                    errors.value[field] = Array.isArray(messages) ? messages[0] : messages;
                }
            } else if (error.response.data?.message) {
                errorMessage.value = error.response.data.message;
            }
        } else if (error.response?.data?.message) {
            errorMessage.value = error.response.data.message;
        } else {
            errorMessage.value = 'Ralat berlaku. Sila cuba lagi.';
        }
    } finally {
        loading.value = false;
    }
}

function resetForm() {
    name.value = '';
    ic_number.value = '';
    phone_number.value = '';
    address.value = '';
    position_type.value = '';
    errors.value = {};
}

function clearFieldError(field) {
    if (errors.value[field]) {
        const { [field]: _, ...rest } = errors.value;
        errors.value = rest;
    }
}

function forceUppercase(ref) {
    ref.value = ref.value.toUpperCase();
}

function forceDigits(ref) {
    ref.value = ref.value.replace(/\D/g, '');
}
</script>

<template>
    <div>
        <!-- Success -->
        <div v-if="successMessage" class="mb-4 rounded-md p-4" :class="dark ? 'bg-green-500/20 ring-1 ring-green-400/30' : 'bg-green-50'">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                    </svg>
                </div>
                <p class="ml-3 text-sm font-medium" :class="dark ? 'text-green-300' : 'text-green-800'">{{ successMessage }}</p>
            </div>
        </div>

        <!-- Error -->
        <div v-if="errorMessage" class="mb-4 rounded-md p-4" :class="dark ? 'bg-red-500/20 ring-1 ring-red-400/30' : 'bg-red-50'">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                    </svg>
                </div>
                <p class="ml-3 text-sm font-medium" :class="dark ? 'text-red-300' : 'text-red-800'">{{ errorMessage }}</p>
            </div>
        </div>

        <form @submit.prevent="submit" class="space-y-4 sm:space-y-5">
            <!-- Honeypot -->
            <div class="absolute -left-[9999px]" aria-hidden="true" tabindex="-1">
                <label for="website">Website</label>
                <input id="website" v-model="website" type="text" name="website" tabindex="-1" autocomplete="off" />
            </div>

            <!-- Nama -->
            <div>
                <label for="name" class="block text-xs font-medium sm:text-sm" :class="dark ? 'text-sky-100/80' : 'text-gray-700'">Nama Penuh</label>
                <input
                    id="name"
                    v-model="name"
                    type="text"
                    autocomplete="name"
                    @input="forceUppercase(name); clearFieldError('name')"
                    class="mt-1 block w-full rounded-lg shadow-sm text-base sm:text-lg uppercase"
                    :class="[
                        dark
                            ? 'border-0 bg-white/10 text-white placeholder-sky-300/40 ring-1 focus:ring-2 ' + (errors.name ? 'ring-red-400/50 focus:ring-red-400' : 'ring-white/15 focus:ring-sky-400')
                            : (errors.name ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500')
                    ]"
                />
                <p v-if="errors.name" class="mt-1 text-xs sm:text-sm" :class="dark ? 'text-red-300' : 'text-red-600'">{{ errors.name }}</p>
            </div>

            <!-- No. IC -->
            <div>
                <label for="ic_number" class="block text-xs font-medium sm:text-sm" :class="dark ? 'text-sky-100/80' : 'text-gray-700'">No. Kad Pengenalan</label>
                <input
                    id="ic_number"
                    v-model="ic_number"
                    type="text"
                    inputmode="numeric"
                    placeholder="880101145678"
                    autocomplete="off"
                    @input="clearFieldError('ic_number')"
                    class="mt-1 block w-full rounded-lg shadow-sm text-base sm:text-lg"
                    :class="[
                        dark
                            ? 'border-0 bg-white/10 text-white placeholder-sky-300/40 ring-1 focus:ring-2 ' + (errors.ic_number ? 'ring-red-400/50 focus:ring-red-400' : 'ring-white/15 focus:ring-sky-400')
                            : (errors.ic_number ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500')
                    ]"
                />
                <p v-if="errors.ic_number" class="mt-1 text-xs sm:text-sm" :class="dark ? 'text-red-300' : 'text-red-600'">{{ errors.ic_number }}</p>
            </div>

            <!-- Telefon -->
            <div>
                <label for="phone_number" class="block text-xs font-medium sm:text-sm" :class="dark ? 'text-sky-100/80' : 'text-gray-700'">No. Telefon</label>
                <input
                    id="phone_number"
                    v-model="phone_number"
                    type="tel"
                    inputmode="numeric"
                    placeholder="0121234567"
                    autocomplete="tel"
                    @input="forceDigits(phone_number)"
                    class="mt-1 block w-full rounded-lg shadow-sm"
                    :class="dark ? 'border-0 bg-white/10 text-white placeholder-sky-300/40 ring-1 ring-white/15 focus:ring-2 focus:ring-sky-400' : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'"
                />
            </div>

            <!-- Alamat -->
            <div>
                <label for="address" class="block text-xs font-medium sm:text-sm" :class="dark ? 'text-sky-100/80' : 'text-gray-700'">Alamat</label>
                <textarea
                    id="address"
                    v-model="address"
                    rows="2"
                    @input="forceUppercase(address)"
                    class="mt-1 block w-full rounded-lg shadow-sm uppercase"
                    :class="dark ? 'border-0 bg-white/10 text-white placeholder-sky-300/40 ring-1 ring-white/15 focus:ring-2 focus:ring-sky-400' : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'"
                ></textarea>
            </div>

            <!-- Jenis Jawatan -->
            <div>
                <label for="position_type" class="block text-xs font-medium sm:text-sm" :class="dark ? 'text-sky-100/80' : 'text-gray-700'">Jenis Jawatan</label>
                <select
                    id="position_type"
                    v-model="position_type"
                    @change="clearFieldError('position_type')"
                    class="mt-1 block w-full rounded-lg shadow-sm"
                    :class="[
                        dark
                            ? 'border-0 bg-white/10 text-white ring-1 focus:ring-2 ' + (errors.position_type ? 'ring-red-400/50 focus:ring-red-400' : 'ring-white/15 focus:ring-sky-400')
                            : (errors.position_type ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500')
                    ]"
                >
                    <option value="" :class="dark ? 'bg-sky-900 text-white' : ''">-- Pilih jawatan --</option>
                    <option v-for="opt in positionOptions" :key="opt.value" :value="opt.value" :class="dark ? 'bg-sky-900 text-white' : ''">
                        {{ opt.label }}
                    </option>
                </select>
                <p v-if="errors.position_type" class="mt-1 text-xs sm:text-sm" :class="dark ? 'text-red-300' : 'text-red-600'">{{ errors.position_type }}</p>
            </div>

            <!-- Submit -->
            <button
                type="submit"
                :disabled="loading"
                :class="dark
                    ? 'w-full rounded-xl bg-sky-500 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-sky-500/30 transition hover:bg-sky-400 disabled:opacity-50 disabled:cursor-not-allowed'
                    : 'w-full rounded-md bg-green-600 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-green-500 disabled:opacity-50 disabled:cursor-not-allowed'"
            >
                <span v-if="loading" class="inline-flex items-center gap-2">
                    <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Mengesahkan...
                </span>
                <span v-else>Sahkan Kehadiran</span>
            </button>
        </form>
    </div>
</template>
