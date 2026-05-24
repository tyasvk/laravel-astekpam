<script setup>
import { ref } from 'vue';
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
    nip: '', // Diubah dari email menjadi nip
    password: '',
    remember: false,
});

// State untuk mengatur visibilitas kata sandi
const showPassword = ref(false);

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Masuk Log" />

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
                    Silakan otentikasi akun Anda untuk memasukkan data serta melakukan validasi laporan Apel Serah Terima Kepala Regu Pengamanan secara terpusat dan akurat.
                </p>
            </div>

            <div class="text-xs text-[#475569] flex flex-col gap-1 relative z-10">
                <span class="font-medium text-slate-400">Kementerian Imigrasi dan Pemasyarakatan Republik Indonesia</span>
                <span class="text-amber-500 font-semibold uppercase tracking-wider text-[10px]">Smart Governance</span>
            </div>
        </div>

        <div class="flex-grow md:w-1/2 flex items-center justify-center p-6 sm:p-12 bg-white md:bg-[#f8fafc]">
            <div class="w-full max-w-md bg-white p-8 md:p-10 md:rounded-2xl md:shadow-xl md:border md:border-[#e2e8f0]">
                
                <div class="md:hidden flex flex-col items-center mb-8">
                    <div class="w-12 h-12 bg-[#0f172a] rounded-xl flex items-center justify-center border border-[#f59e0b]/30 shadow-lg mb-3">
                        <span class="text-[#f59e0b] font-bold text-2xl">A</span>
                    </div>
                    <h2 class="text-xl font-bold uppercase tracking-tight text-[#0f172a]">Astekpam</h2>
                    <p class="text-xs text-[#64748b]">Lapas Kelas I Palembang</p>
                </div>

                <div class="mb-8 hidden md:block">
                    <h3 class="text-2xl font-bold text-[#0f172a] tracking-tight">Selamat Datang</h3>
                    <p class="text-sm text-[#64748b] mt-1">Masukkan kredensial Anda untuk mengakses dasbor pelaporan.</p>
                </div>

                <div v-if="status" class="mb-4 font-medium text-sm text-emerald-600 bg-emerald-50 border border-emerald-200 p-3 rounded-lg">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-5">
                    <div>
                        <label for="nip" class="block text-xs font-semibold uppercase tracking-wider text-[#475569] mb-2">
                            Nomor Induk Pegawai (NIP)
                        </label>
                        <input
                            id="nip"
                            type="text"
                            v-model="form.nip"
                            required
                            autofocus
                            class="w-full px-4 py-3 bg-[#f8fafc] border border-[#e2e8f0] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#f59e0b]/40 focus:border-[#f59e0b] focus:bg-white transition-all duration-200 text-[#0f172a]"
                            placeholder="Masukkan NIP Anda"
                        />
                        <span v-if="form.errors.nip" class="text-xs text-red-500 mt-1.5 block font-medium">
                            {{ form.errors.nip }}
                        </span>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-[#475569]">
                                Kata Sandi
                            </label>
                            <Link
                                v-if="canResetPassword"
                                :href="route('password.request')"
                                class="text-xs font-semibold text-[#d97706] hover:text-[#f59e0b] transition-colors"
                            >
                                Lupa Sandi?
                            </Link>
                        </div>
                        
                        <div class="relative">
                            <input
                                id="password"
                                :type="showPassword ? 'text' : 'password'"
                                v-model="form.password"
                                required
                                autocomplete="current-password"
                                class="w-full pl-4 pr-12 py-3 bg-[#f8fafc] border border-[#e2e8f0] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#f59e0b]/40 focus:border-[#f59e0b] focus:bg-white transition-all duration-200 text-[#0f172a]"
                                placeholder="••••••••"
                            />
                            
                            <button
                                type="button"
                                @click="togglePasswordVisibility"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-[#64748b] hover:text-[#0f172a] focus:outline-none select-none touch-none"
                                title="Tampilkan/Sembunyikan Kata Sandi"
                            >
                                <svg v-if="showPassword" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                                    <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                
                                <svg v-else xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                                    <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                                    <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
                                    <path d="M6.61 6.61A13.52 13.52 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
                                    <line x1="2" x2="22" y1="2" y2="22"/>
                                </svg>
                            </button>
                        </div>
                        
                        <span v-if="form.errors.password" class="text-xs text-red-500 mt-1.5 block font-medium">
                            {{ form.errors.password }}
                        </span>
                    </div>

                    <div class="flex items-center">
                        <label class="flex items-center cursor-pointer select-none">
                            <input
                                type="checkbox"
                                v-model="form.remember"
                                class="rounded border-[#e2e8f0] text-[#f59e0b] focus:ring-[#f59e0b] focus:ring-offset-0 w-4 h-4 accent-[#f59e0b]"
                            />
                            <span class="ms-2.5 text-sm text-[#475569] font-medium">Ingat sesi login saya</span>
                        </label>
                    </div>

                    <div class="pt-2">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full py-3 px-4 bg-[#0f172a] hover:bg-[#1e293b] text-[#f59e0b] disabled:opacity-50 disabled:pointer-events-none font-bold text-sm rounded-xl shadow-lg shadow-[#0f172a]/10 transition-all duration-200 transform active:scale-[0.99] flex justify-center items-center gap-2"
                        >
                            <svg v-if="form.processing" class="animate-spin h-4 w-4 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Masuk Ke Dasbor
                        </button>
                    </div>
                </form>

                <div class="mt-8 text-center">
                    <Link href="/" class="text-xs text-[#64748b] hover:text-[#0f172a] font-medium transition-colors">
                        ← Kembali ke Halaman Utama
                    </Link>
                </div>

            </div>
        </div>
    </div>
</template>