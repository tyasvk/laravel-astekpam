<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

defineProps({
    canLogin: { type: Boolean },
    canRegister: { type: Boolean },
});
</script>

<template>
    <Head title="ASTEKPAM - Lapas Kelas I Palembang" />

    <div class="min-h-screen bg-slate-50 text-slate-900 selection:bg-blue-600 selection:text-white font-sans">
        <nav class="sticky top-0 z-50 w-full border-b bg-white/80 backdrop-blur-md">
            <div class="container mx-auto flex h-20 items-center justify-between px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <ApplicationLogo class="h-10 w-10 fill-blue-800" />
                    <div class="flex flex-col border-l border-slate-200 pl-3">
                        <span class="text-xl font-black leading-none tracking-tighter text-slate-900 uppercase">Astekpam</span>
                        <span class="text-[10px] font-bold text-blue-700 uppercase tracking-widest mt-1">Lapas Kelas I Palembang</span>
                    </div>
                </div>

                <div v-if="canLogin" class="flex items-center gap-4">
                    <Link v-if="$page.props.auth.user" :href="route('dashboard')">
                        <Button class="rounded-full px-6 bg-blue-800 hover:bg-blue-900 text-white font-bold">Dashboard</Button>
                    </Link>
                    <template v-else>
                        <Link :href="route('login')">
                            <Button variant="ghost" class="text-sm font-semibold">Masuk</Button>
                        </Link>
                        <Link v-if="canRegister" :href="route('register')">
                            <Button class="rounded-full px-6 bg-blue-800 hover:bg-blue-900 shadow-lg shadow-blue-800/20 text-white font-bold">Daftar Akun</Button>
                        </Link>
                    </template>
                </div>
            </div>
        </nav>

        <main>
            <header class="relative overflow-hidden bg-white pt-20 pb-24 lg:pt-32 lg:pb-40">
                <div class="container relative z-10 mx-auto px-6 lg:px-8">
                    <div class="text-center max-w-4xl mx-auto">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-100 text-blue-700 text-[11px] font-bold uppercase tracking-wider mb-8">
                            Kemenimipas Republik Indonesia
                        </div>
                        <h1 class="text-5xl font-black tracking-tight text-slate-900 sm:text-7xl leading-[1.1] mb-6">
                            Apel Serah Terima <br/>
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-800 to-blue-600 font-black">Kepala Regu Pengamanan.</span>
                        </h1>
                        <p class="mx-auto mt-8 max-w-2xl text-lg text-slate-500 leading-relaxed">
                            Digitalisasi sistem <span class="font-bold text-slate-800 tracking-tight text-xl italic underline decoration-blue-500 underline-offset-4">ASTEKPAM</span> untuk meningkatkan kedisiplinan, koordinasi harian, dan akuntabilitas serah terima tugas antar regu di Lapas Kelas I Palembang.
                        </p>
                        <div class="mt-12 flex flex-col sm:flex-row justify-center gap-4">
                            <Link :href="route('login')">
                                <Button size="lg" class="h-14 px-10 text-md font-bold rounded-2xl bg-blue-800 hover:bg-blue-900 shadow-xl shadow-blue-800/20 transition-all hover:-translate-y-1 text-white">
                                    Akses Portal Petugas
                                </Button>
                            </Link>
                            <Button variant="outline" size="lg" class="h-14 px-10 text-md font-semibold rounded-2xl border-slate-200 hover:bg-slate-50 transition-all">
                                Pelajari Prosedur
                            </Button>
                        </div>
                    </div>
                </div>
                <div class="absolute inset-0 -z-10 bg-[radial-gradient(45rem_50rem_at_top,theme(colors.blue.100),white)] opacity-30"></div>
            </header>

            <section class="py-24 bg-white border-t border-slate-100">
                <div class="container mx-auto px-6 lg:px-8">
                    <div class="text-center mb-16">
                        <h2 class="text-3xl font-black tracking-tight text-slate-900 uppercase">Tata Nilai PASTI</h2>
                        <p class="mt-3 text-slate-500 max-w-xl mx-auto italic font-medium">"Menjadi landasan utama dalam setiap pelaksanaan serah terima tugas pengamanan."</p>
                    </div>
                    
                    <div class="container mx-auto grid grid-cols-1 gap-6 sm:grid-cols-3 lg:grid-cols-5">
                        <Card class="border-none shadow-sm bg-slate-50 hover:bg-blue-50 transition-colors group">
                            <CardHeader>
                                <CardTitle class="text-blue-800 font-bold tracking-widest uppercase text-xs">Professional</CardTitle>
                            </CardHeader>
                            <CardContent class="text-sm text-slate-500">Petugas melaksanakan tugas sesuai standar operasional pengamanan.</CardContent>
                        </Card>
                        <Card class="border-none shadow-sm bg-slate-50 hover:bg-blue-50 transition-colors group">
                            <CardHeader>
                                <CardTitle class="text-blue-800 font-bold tracking-widest uppercase text-xs">Akuntabel</CardTitle>
                            </CardHeader>
                            <CardContent class="text-sm text-slate-500">Setiap data serah terima terdokumentasi dan dapat dipertanggungjawabkan.</CardContent>
                        </Card>
                        <Card class="border-none shadow-sm bg-slate-50 hover:bg-blue-50 transition-colors group">
                            <CardHeader>
                                <CardTitle class="text-blue-800 font-bold tracking-widest uppercase text-xs">Sinergi</CardTitle>
                            </CardHeader>
                            <CardContent class="text-sm text-slate-500">Koordinasi harmonis antar regu pengamanan lama dan baru.</CardContent>
                        </Card>
                        <Card class="border-none shadow-sm bg-slate-50 hover:bg-blue-50 transition-colors group">
                            <CardHeader>
                                <CardTitle class="text-blue-800 font-bold tracking-widest uppercase text-xs">Transparan</CardTitle>
                            </CardHeader>
                            <CardContent class="text-sm text-slate-500">Keterbukaan informasi kondisi keamanan lapas secara real-time.</CardContent>
                        </Card>
                        <Card class="border-none shadow-sm bg-slate-50 hover:bg-blue-50 transition-colors group">
                            <CardHeader>
                                <CardTitle class="text-blue-800 font-bold tracking-widest uppercase text-xs">Inovatif</CardTitle>
                            </CardHeader>
                            <CardContent class="text-sm text-slate-500">Digitalisasi laporan untuk efisiensi birokrasi pengamanan.</CardContent>
                        </Card>
                    </div>
                </div>
            </section>
        </main>

        <footer class="bg-[#0f172a] pt-20 pb-10 text-slate-400">
            <div class="container mx-auto px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-12 lg:grid-cols-4 lg:gap-8">
                    <div class="lg:col-span-1">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2 bg-white/10 rounded-lg">
                                <ApplicationLogo class="h-8 w-8 fill-white" />
                            </div>
                            <span class="text-xl font-black text-white tracking-tighter uppercase">ASTEKPAM</span>
                        </div>
                        <p class="text-sm leading-relaxed mb-6 italic opacity-80 text-justify text-slate-400">
                            Sistem Informasi Apel Serah Terima Kepala Regu Pengamanan. Inovasi untuk efisiensi dan transparansi operasional di Lapas Kelas I Palembang.
                        </p>
                    </div>

                    <div>
                        <h3 class="mb-6 text-sm font-bold uppercase tracking-[0.2em] text-white">Navigasi</h3>
                        <ul class="space-y-4 text-sm font-medium">
                            <li><Link href="/" class="hover:text-blue-500 transition-colors">Beranda</Link></li>
                            <li><Link :href="route('login')" class="hover:text-blue-500 transition-colors">Akses Petugas</Link></li>
                            <li><a href="#" class="hover:text-blue-500 transition-colors">Panduan Sistem</a></li>
                            <li><a href="#" class="hover:text-blue-500 transition-colors">Pusat Bantuan</a></li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="mb-6 text-sm font-bold uppercase tracking-[0.2em] text-white">Hubungi Kami</h3>
                        <ul class="space-y-4 text-sm font-medium">
                            <li class="flex gap-3">
                                <svg class="w-5 h-5 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <span>Jl. Raya Merdeka, No. 1, <br/>Palembang, Sumsel</span>
                            </li>
                            <li class="flex items-center gap-3 font-mono">
                                <svg class="w-5 h-5 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                <span>lapas.palembang@kemenkumham.go.id</span>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="mb-6 text-sm font-bold uppercase tracking-[0.2em] text-white">Tautan Instansi</h3>
                        <div class="space-y-3">
                            <a href="https://kemenimipas.go.id" target="_blank" class="block p-3 rounded-lg bg-white/5 border border-white/10 hover:bg-white/10 hover:border-blue-500 transition-all group">
                                <p class="text-[10px] font-bold text-white mb-0.5 group-hover:text-blue-400">KEMENIMIPAS RI</p>
                                <p class="text-[9px] opacity-50 uppercase tracking-tighter leading-none text-slate-300">Imigrasi dan Pemasyarakatan</p>
                            </a>
                            <a href="#" target="_blank" class="block p-3 rounded-lg bg-white/5 border border-white/10 hover:bg-white/10 hover:border-blue-500 transition-all group">
                                <p class="text-[10px] font-bold text-white mb-0.5 group-hover:text-blue-400">DITJEN PAS</p>
                                <p class="text-[9px] opacity-50 uppercase tracking-tighter leading-none text-slate-300">Direktorat Jenderal Pemasyarakatan</p>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="mt-20 border-t border-white/5 pt-10 flex flex-col md:flex-row justify-between items-center gap-6">
                    <div class="text-center md:text-left">
                        <p class="text-[11px] font-black uppercase tracking-[0.3em] text-white mb-2">Waspada Jangan-Jangan!</p>
                        <p class="text-[10px] opacity-40 uppercase tracking-widest">&copy; {{ new Date().getFullYear() }} Lapas Kelas I Palembang. All rights reserved.</p>
                    </div>
                    
                    <div class="flex items-center gap-6 text-[10px] font-bold uppercase tracking-widest opacity-40">
                        <a href="#" class="hover:text-white transition-colors">Privasi</a>
                        <a href="#" class="hover:text-white transition-colors">Keamanan</a>
                        <a href="#" class="hover:text-white transition-colors">Audit</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>