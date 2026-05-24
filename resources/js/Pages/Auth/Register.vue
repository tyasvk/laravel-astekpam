<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    nip: '', // State untuk menampung input NIP baru
    regu_pengamanan: '',
    password: '',
    password_confirmation: '',
});

// State pengaturan visibilitas kata sandi
const showPassword = ref(false);
const showConfirmPassword = ref(false);

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};

const toggleConfirmPasswordVisibility = () => {
    showConfirmPassword.value = !showConfirmPassword.value;
};

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Pendaftaran Akun" />

    <div class="min-h-screen flex flex-col md:flex-row bg-[#f8fafc] font-sans selection:bg-[#d97706] selection:text-white">
        <div class="hidden md:flex md:w-1/2 bg-gradient-to-br from-[#0f172a] to-[#1e293b] p-12 flex-col justify-between relative overflow-hidden text-white border-r border-[#d97706]/10">
            <div class="absolute inset-0 opacity-5 bg-[radial-gradient(#f59e0b_1px,transparent_1px)] [background-size:24px_24px]"></div>
            <div class="absolute -top-40 -left-40 w-96 h-96 bg-[#f59e0b]/5 rounded-full blur-3xl"></div>

            <div class="flex items-center space-x-3 relative z-10">
                <div class="w-10 h-10 bg-[#1e293b] rounded-lg flex items-center justify-center border border-[#f59e0b]/30 shadow-md">
                    <span class="text-[#f59e0b] font-bold text-xl tracking-wider">A</span>
                </div>
                <div>
                    <span class="font-bold text-lg tracking-wider uppercase block leading-none">Astekpam</span>
                    <span class="text-xs text-[#94a3b8] font-medium mt-0.5 block">Lapas Kelas I Palembang</span>
                </div>
            </div>

            <div class="relative z-10 max-w-md my-auto">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#f59e0b]/10 border border-[#f59e0b]/20 text-[#f59e0b] text-xs font-semibold uppercase tracking-wider mb-6">
                    Sistem Laporan Serah Terima Jaga
                </div>
                <h2 class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-[#f59e0b] via-[#fbbf24] to-[#d97706] mb-4 leading-tight">
                    SPARTA (Sistem Pelaporan Astekpam Responsif, Terpadu, dan Akuntabel)
                </h2>
                <p class="text-[#94a3b8] text-sm leading-relaxed">
                    Silakan daftarkan akun baru Anda untuk dapat memasukkan data serta melakukan validasi laporan Apel Serah Terima Kepala Regu Pengamanan secara terpusat dan akurat.
                </p>
            </div>

            <div class="text-xs text-[#475569] flex flex-col gap-1 relative z-10">
                <span class="font-medium text-slate-400">Kementerian Imigrasi dan Pemasyarakatan Republik Indonesia</span>
                <span class="text-amber-500 font-semibold uppercase tracking-wider text-[10px]">Smart Governance</span>
            </div>
        </div>

        <div class="flex-grow md:w-1/2 flex items-center justify-center p-4 sm:p-8 bg-white md:bg-[#f8fafc]">
            <div class="w-full max-w-xl bg-white p-6 md:p-8 md:rounded-2xl md:shadow-lg md:border md:border-[#e2e8f0]">
                
                <div class="md:hidden flex flex-col items-center mb-5">
                    <div class="w-10 h-10 bg-[#0f172a] rounded-xl flex items-center justify-center border border-[#f59e0b]/30 shadow-md mb-2">
                        <span class="text-[#f59e0b] font-bold text-xl">A</span>
                    </div>
                    <h2 class="text-lg font-bold uppercase tracking-tight text-[#0f172a]">Astekpam</h2>
                    <p class="text-xs text-[#64748b]">Lapas Kelas I Palembang</p>
                </div>

                <div class="mb-5 hidden md:block">
                    <h3 class="text-xl font-bold text-[#0f172a] tracking-tight">Daftar Akun Baru</h3>
                    <p class="text-xs text-[#64748b] mt-0.5">Lengkapi data Anda dengan benar untuk masuk ke sistem.</p>
                </div>

                <form @submit.prevent="submit" class="space-y-3.5">
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label for="name" class="block text-[11px] font-semibold uppercase tracking-wider text-[#475569] mb-1.5">
                                Nama Lengkap
                            </label>
                            <input
                                id="name"
                                type="text"
                                v-model="form.name"
                                required
                                autofocus
                                autocomplete="name"
                                class="w-full px-3.5 py-2.5 bg-[#f8fafc] border border-[#e2e8f0] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#f59e0b]/40 focus:border-[#f59e0b] focus:bg-white transition-all duration-200 text-[#0f172a]"
                                placeholder="Heri Darmawan"
                            />
                            <span v-if="form.errors.name" class="text-xs text-red-500 mt-1 block font-medium">
                                {{ form.errors.name }}
                            </span>
                        </div>

                        <div>
                            <label for="email" class="block text-[11px] font-semibold uppercase tracking-wider text-[#475569] mb-1.5">
                                Alamat Email
                            </label>
                            <input
                                id="email"
                                type="email"
                                v-model="form.email"
                                required
                                autocomplete="username"
                                class="w-full px-3.5 py-2.5 bg-[#f8fafc] border border-[#e2e8f0] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#f59e0b]/40 focus:border-[#f59e0b] focus:bg-white transition-all duration-200 text-[#0f172a]"
                                placeholder="nama@domain.com"
                            />
                            <span v-if="form.errors.email" class="text-xs text-red-500 mt-1 block font-medium">
                                {{ form.errors.email }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label for="nip" class="block text-[11px] font-semibold uppercase tracking-wider text-[#475569] mb-1.5">
                                NIP Pegawai
                            </label>
                            <input
                                id="nip"
                                type="text"
                                v-model="form.nip"
                                required
                                class="w-full px-3.5 py-2.5 bg-[#f8fafc] border border-[#e2e8f0] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#f59e0b]/40 focus:border-[#f59e0b] focus:bg-white transition-all duration-200 text-[#0f172a]"
                                placeholder="199508xxxxxxxxxxxx"
                            />
                            <span v-if="form.errors.nip" class="text-xs text-red-500 mt-1 block font-medium">
                                {{ form.errors.nip }}
                            </span>
                        </div>

                        <div>
                            <label for="regu_pengamanan" class="block text-[11px] font-semibold uppercase tracking-wider text-[#475569] mb-1.5">
                                Regu Pengamanan (Rupam)
                            </label>
                            <div class="relative">
                                <select
                                    id="regu_pengamanan"
                                    v-model="form.regu_pengamanan"
                                    required
                                    class="w-full px-3.5 py-2.5 bg-[#f8fafc] border border-[#e2e8f0] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#f59e0b]/40 focus:border-[#f59e0b] focus:bg-white transition-all duration-200 text-[#0f172a] appearance-none cursor-pointer"
                                >
                                    <option value="" disabled selected>Pilih Regu Pengamanan</option>
                                    <option value="Rupam I">Rupam I</option>
                                    <option value="Rupam II">Rupam II</option>
                                    <option value="Rupam III">Rupam III</option>
                                    <option value="Rupam IV">Rupam IV</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-[#64748b]">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                                    </svg>
                                </div>
                            </div>
                            <span v-if="form.errors.regu_pengamanan" class="text-xs text-red-500 mt-1 block font-medium">
                                {{ form.errors.regu_pengamanan }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label for="password" class="block text-[11px] font-semibold uppercase tracking-wider text-[#475569] mb-1.5">
                                Kata Sandi
                            </label>
                            <div class="relative">
                                <input
                                    id="password"
                                    :type="showPassword ? 'text' : 'password'"
                                    v-model="form.password"
                                    required
                                    autocomplete="new-password"
                                    class="w-full pl-3.5 pr-10 py-2.5 bg-[#f8fafc] border border-[#e2e8f0] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#f59e0b]/40 focus:border-[#f59e0b] focus:bg-white transition-all duration-200 text-[#0f172a]"
                                    placeholder="••••••••"
                                />
                                <button
                                    type="button"
                                    @click="togglePasswordVisibility"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-[#64748b] hover:text-[#0f172a] focus:outline-none select-none"
                                >
                                    <svg v-if="showPassword" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                                        <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                    <svg v-else xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                                        <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                                        <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
                                        <path d="M6.61 6.61A13.52 13.52 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
                                        <line x1="2" x2="22" y1="2" y2="22"/>
                                    </svg>
                                </button>
                            </div>
                            <span v-if="form.errors.password" class="text-xs text-red-500 mt-1 block font-medium">
                                {{ form.errors.password }}
                            </span>
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-[11px] font-semibold uppercase tracking-wider text-[#475569] mb-1.5">
                                Konfirmasi Sandi
                            </label>
                            <div class="relative">
                                <input
                                    id="password_confirmation"
                                    :type="showConfirmPassword ? 'text' : 'password'"
                                    v-model="form.password_confirmation"
                                    required
                                    autocomplete="new-password"
                                    class="w-full pl-3.5 pr-10 py-2.5 bg-[#f8fafc] border border-[#e2e8f0] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#f59e0b]/40 focus:border-[#f59e0b] focus:bg-white transition-all duration-200 text-[#0f172a]"
                                    placeholder="••••••••"
                                />
                                <button
                                    type="button"
                                    @click="toggleConfirmPasswordVisibility"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-[#64748b] hover:text-[#0f172a] focus:outline-none select-none"
                                >
                                    <svg v-if="showConfirmPassword" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                                        <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                    <svg v-else xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                                        <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                                        <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
                                        <path d="M6.61 6.61A13.52 13.52 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
                                        <line x1="2" x2="22" y1="2" y2="22"/>
                                    </svg>
                                </button>
                            </div>
                            <span v-if="form.errors.password_confirmation" class="text-xs text-red-500 mt-1 block font-medium">
                                {{ form.errors.password_confirmation }}
                            </span>
                        </div>
                    </div>

                    <div class="pt-1.5">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full py-2.5 px-4 bg-[#0f172a] hover:bg-[#1e293b] text-[#f59e0b] disabled:opacity-50 disabled:pointer-events-none font-bold text-sm rounded-xl shadow-md shadow-[#0f172a]/10 transition-all duration-200 transform active:scale-[0.99] flex justify-center items-center gap-2"
                        >
                            <svg v-if="form.processing" class="animate-spin h-4 w-4 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Daftarkan Akun
                        </button>
                    </div>
                </form>

                <div class="mt-6 text-center flex flex-col gap-2 border-t border-slate-100 pt-4">
                    <Link :href="route('login')" class="text-xs text-[#d97706] hover:text-[#f59e0b] font-semibold transition-colors">
                        Sudah Memiliki Akun? Masuk Log Di Sini
                    </Link>
                    <Link href="/" class="text-xs text-[#64748b] hover:text-[#0f172a] font-medium transition-colors">
                        ← Kembali ke Halaman Utama
                    </Link>
                </div>

            </div>
        </div>
    </div>
</template>