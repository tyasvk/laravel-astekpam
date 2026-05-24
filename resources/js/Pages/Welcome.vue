<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});
</script>

<template>
    <Head title="Selamat Datang" />

    <div class="min-h-screen bg-[#f8fafc] flex flex-col justify-between text-[#0f172a] font-sans selection:bg-[#d97706] selection:text-white">
        <header class="bg-white/95 backdrop-blur-md sticky top-0 z-50 border-b border-[#e2e8f0] shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-[#0f172a] rounded-lg flex items-center justify-center shadow-md border border-[#d97706]/30">
                        <span class="text-[#f59e0b] font-bold text-xl tracking-wider">A</span>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold tracking-tight text-[#0f172a] leading-tight uppercase">
                            Astekpam
                        </h1>
                        <p class="text-xs text-[#64748b] font-medium tracking-wide">
                            Lapas Kelas I Palembang
                        </p>
                    </div>
                </div>

                <nav v-if="canLogin" class="flex items-center space-x-3">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                    >
                        <Button variant="default" class="bg-[#0f172a] hover:bg-[#1e293b] text-[#f59e0b] font-semibold shadow">
                            Dashboard
                        </Button>
                    </Link>

                    <template v-else>
                        <Link :href="route('login')">
                            <Button variant="ghost" class="text-[#475569] hover:text-[#0f172a] hover:bg-[#f1f5f9] font-medium">
                                Masuk
                            </Button>
                        </Link>

                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                        >
                            <Button class="bg-[#0f172a] hover:bg-[#1e293b] text-[#f59e0b] font-semibold shadow-sm">
                                Daftar Akun
                            </Button>
                        </Link>
                    </template>
                </nav>
            </div>
        </header>

        <main class="flex-grow">
            <div class="relative overflow-hidden bg-gradient-to-b from-[#0f172a] via-[#0f172a] to-[#1e293b] py-24 sm:py-32 text-white border-b border-[#d97706]/10">
                <div class="absolute inset-0 opacity-5 bg-[radial-gradient(#f59e0b_1px,transparent_1px)] [background-size:20px_20px]"></div>
                
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#f59e0b]/10 border border-[#f59e0b]/30 text-[#f59e0b] text-xs font-semibold uppercase tracking-wider mb-6">
                        <span class="flex h-2 w-2 rounded-full bg-[#f59e0b] animate-pulse"></span>
                        Smart Governance
                    </div>
                    
                    <h2 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white mb-6 max-w-5xl mx-auto leading-[1.15]">
                        SPARTA <br class="hidden sm:inline" />
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#f59e0b] via-[#fbbf24] to-[#d97706] text-3xl sm:text-4xl lg:text-5xl block mt-2 font-bold tracking-normal">
                            (Sistem Pelaporan Astekpam Responsif, Terpadu, dan Akuntabel)
                        </span>
                    </h2>
                    
                    <p class="text-[#94a3b8] text-base sm:text-lg max-w-3xl mx-auto mb-10 leading-relaxed">
                        Sistem terpusat untuk memasukkan data serta melakukan validasi laporan Apel Serah Terima Kepala Regu Pengamanan secara akurat dan terintegrasi di lingkungan Lapas Kelas I Palembang.
                    </p>

                    <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
                        <Link :href="route('login')">
                            <Button size="lg" class="w-full sm:w-auto px-8 bg-[#f59e0b] hover:bg-[#d97706] text-[#0f172a] font-bold text-base shadow-lg shadow-[#f59e0b]/20 transition-all duration-200 transform hover:-translate-y-0.5">
                                Masuk ke Aplikasi
                            </Button>
                        </Link>
                        <a href="#fitur">
                            <Button size="lg" variant="outline" class="w-full sm:w-auto px-8 border-[#334155] text-[#cbd5e1] hover:bg-[#1e293b] hover:text-white text-base">
                                Pelajari Fitur
                            </Button>
                        </a>
                    </div>
                </div>
            </div>

            <section id="fitur" class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <h3 class="text-3xl font-bold tracking-tight text-[#0f172a] mb-4">
                        Pilar Utama Layanan SPARTA
                    </h3>
                    <p class="text-[#64748b] text-base">
                        Mendukung akuntabilitas kinerja pengamanan melalui pengelolaan administrasi pelaporan yang sistematis dan tervalidasi.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <Card class="border border-[#e2e8f0] shadow-sm hover:shadow-md transition-shadow bg-white">
                        <CardHeader>
                            <div class="p-3 bg-[#f1f5f9] rounded-lg w-fit text-[#0f172a] mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6"><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/></svg>
                            </div>
                            <CardTitle class="text-xl font-bold text-[#0f172a]">Pelaporan Digital Terpusat</CardTitle>
                            <CardDescription class="text-[#64748b] pt-2 leading-relaxed">
                                Penyusunan data laporan Apel Serah Terima Kepala Regu Pengamanan secara digital untuk memastikan validitas informasi jurnalan tugas.
                            </CardDescription>
                        </CardHeader>
                    </Card>

                    <Card class="border border-[#e2e8f0] shadow-sm hover:shadow-md transition-shadow bg-white">
                        <CardHeader>
                            <div class="p-3 bg-[#f59e0b]/10 rounded-lg w-fit text-[#d97706] mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6"><path d="M20 13c0 5-3.5 7.5-7.66 9.7a1 1 0 0 1-.68 0C7.5 20.5 4 18 4 13V6a1 1 0 0 1 .76-.97l8-2a1 1 0 0 1 .48 0l8 2A1 1 0 0 1 20 6z"/></svg>
                            </div>
                            <CardTitle class="text-xl font-bold text-[#0f172a]">Validasi Serah Terima</CardTitle>
                            <CardDescription class="text-[#64748b] pt-2 leading-relaxed">
                                Proses serah terima Kepala Regu Pengamanan (Karupam) terverifikasi secara sistematis demi menjaga akuntabilitas pelaksanaan tugas.
                            </CardDescription>
                        </CardHeader>
                    </Card>

                    <Card class="border border-[#e2e8f0] shadow-sm hover:shadow-md transition-shadow bg-white">
                        <CardHeader>
                            <div class="p-3 bg-[#f1f5f9] rounded-lg w-fit text-[#0f172a] mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6"><line x1="18" x2="18" y1="20" y2="10"/><line x1="12" x2="12" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="14"/></svg>
                            </div>
                            <CardTitle class="text-xl font-bold text-[#0f172a]">Arsip Responsif</CardTitle>
                            <CardDescription class="text-[#64748b] pt-2 leading-relaxed">
                                Mempermudah peninjauan dan rekapitulasi berkas laporan oleh pihak manajerial kapan saja secara transparan dan teratur.
                            </CardDescription>
                        </CardHeader>
                    </Card>
                </div>
            </section>
        </main>

        <footer class="bg-[#0f172a] text-[#94a3b8] border-t border-[#1e293b] py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="text-center md:text-left">
                    <p class="text-sm font-semibold text-[#f1f5f9] uppercase tracking-wider">
                        SPARTA Platform
                    </p>
                    <p class="text-xs text-[#475569] mt-1">
                        Hak Cipta © 2026 Lapas Kelas I Palembang. Seluruh hak cipta dilindungi undang-undang.
                    </p>
                </div>
                <div class="flex flex-col md:flex-row items-center md:items-end gap-2 text-center md:text-right text-xs font-medium">
                    <span class="text-slate-400">Kementerian Imigrasi dan Pemasyarakatan Republik Indonesia</span>
                    <span class="hidden md:inline text-[#334155]">|</span>
                    <span class="text-[#f59e0b] uppercase tracking-wider text-[10px]">Smart Governance</span>
                </div>
            </div>
        </footer>
    </div>
</template>