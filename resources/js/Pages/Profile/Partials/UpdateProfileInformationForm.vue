<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
    nip: user.nip || '',
    jabatan: user.jabatan || '',
    regu_pengamanan: user.regu_pengamanan || '',
    regu: user.regu || '',
    no_hp: user.no_hp || '',
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">Informasi Profil</h2>
            <p class="mt-1 text-sm text-gray-600">
                Perbarui informasi profil akun, kepegawaian, dan alamat email Anda.
            </p>
        </header>

        <form @submit.prevent="form.patch(route('profile.update'))" class="mt-6 space-y-6">
            
            <!-- NAMA LENGKAP -->
            <div>
                <InputLabel for="name" value="Nama Lengkap" />
                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full bg-white"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <!-- NIP & JABATAN -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <InputLabel for="nip" value="NIP" />
                    <TextInput
                        id="nip"
                        type="text"
                        class="mt-1 block w-full bg-white"
                        v-model="form.nip"
                    />
                    <InputError class="mt-2" :message="form.errors.nip" />
                </div>
                <div>
                    <InputLabel for="jabatan" value="Jabatan" />
                    <TextInput
                        id="jabatan"
                        type="text"
                        class="mt-1 block w-full bg-white"
                        v-model="form.jabatan"
                    />
                    <InputError class="mt-2" :message="form.errors.jabatan" />
                </div>
            </div>

            <!-- REGU PENGAMANAN -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <InputLabel for="regu_pengamanan" value="Regu Pengamanan (Rupam)" />
                    <TextInput
                        id="regu_pengamanan"
                        type="text"
                        class="mt-1 block w-full bg-white"
                        v-model="form.regu_pengamanan"
                        placeholder="Contoh: Rupam I"
                    />
                    <InputError class="mt-2" :message="form.errors.regu_pengamanan" />
                </div>
                <div>
                    <InputLabel for="regu" value="Singkatan Regu" />
                    <TextInput
                        id="regu"
                        type="text"
                        class="mt-1 block w-full bg-white"
                        v-model="form.regu"
                        placeholder="Contoh: I"
                    />
                    <InputError class="mt-2" :message="form.errors.regu" />
                </div>
            </div>

            <!-- NO HP -->
            <div>
                <InputLabel for="no_hp" value="No. WhatsApp (Awali dengan 08 atau 628)" />
                <TextInput
                    id="no_hp"
                    type="text"
                    class="mt-1 block w-full bg-white"
                    v-model="form.no_hp"
                />
                <InputError class="mt-2" :message="form.errors.no_hp" />
            </div>

            <!-- EMAIL -->
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full bg-white"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="text-sm mt-2 text-gray-800">
                    Alamat email Anda belum diverifikasi.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Klik di sini untuk mengirim ulang email verifikasi.
                    </Link>
                </p>

                <div v-show="status === 'verification-link-sent'" class="mt-2 font-medium text-sm text-green-600">
                    Tautan verifikasi baru telah dikirim ke alamat email Anda.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Simpan Perubahan</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600 font-semibold text-emerald-600">Berhasil Disimpan!</p>
                </Transition>
            </div>
        </form>
    </section>
</template>