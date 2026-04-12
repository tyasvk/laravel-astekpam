<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { 
    FilePlus, 
    History, 
    ShieldCheck, 
    ArrowRight,
    Users,
    ClipboardCheck,
    AlertCircle
} from 'lucide-vue-next';
import { usePermission } from '@/Composables/usePermission';

// Mengambil data autentikasi dari server
const { auth } = usePage().props;
const { hasRole, hasPermission } = usePermission();

// Logika tampilan: Munculkan tombol jika Admin atau Petugas yang punya izin
const canCreate = hasRole('admin') || hasPermission('create reports');
</script>

<template>
    <Head title="Dashboard Astekpam" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-1">
                <h2 class="font-bold text-2xl text-zinc-900 tracking-tight leading-none">
                    Dashboard Astekpam
                </h2>
                <p class="text-sm text-zinc-500 font-medium italic">
                    Lapas Kelas I Palembang
                </p>
            </div>
        </template>

        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <Card class="border-none shadow-sm bg-white">
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Status Keamanan</span>
                        <ShieldCheck class="h-4 w-4 text-green-500" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-black text-zinc-800">KONDUSIF</div>
                    </CardContent>
                </Card>

                <Card class="border-none shadow-sm bg-white">
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Total Penghuni</span>
                        <Users class="h-4 w-4 text-zinc-400" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-black text-zinc-800">1.650 <span class="text-xs font-normal text-zinc-400">Org</span></div>
                    </CardContent>
                </Card>

                <Card class="border-none shadow-sm bg-white">
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Laporan Masuk</span>
                        <ClipboardCheck class="h-4 w-4 text-zinc-400" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-black text-zinc-800">04 <span class="text-xs font-normal text-zinc-400">Shift Ini</span></div>
                    </CardContent>
                </Card>
            </div>

            <div class="space-y-4">
                <h3 class="text-xs font-black text-zinc-400 uppercase tracking-widest px-1">Layanan Utama</h3>
                
                <div class="grid grid-cols-1 gap-4">
                    <Link v-if="canCreate" :href="route('astekpam.create')" class="group outline-none">
                        <Card class="border-none shadow-md hover:shadow-xl transition-all bg-indigo-600 overflow-hidden relative active:scale-[0.98]">
                            <div class="absolute -right-10 -top-10 text-white/10 group-hover:rotate-12 transition-transform duration-500">
                                <FilePlus :size="180" />
                            </div>
                            
                            <CardContent class="p-8 flex items-center justify-between relative z-10">
                                <div class="flex items-center gap-6">
                                    <div class="p-4 bg-white/20 rounded-2xl text-white shadow-inner backdrop-blur-sm">
                                        <FilePlus :size="36" />
                                    </div>
                                    <div class="text-white">
                                        <h4 class="text-2xl font-black tracking-tighter">Buat Astekpam Baru</h4>
                                        <p class="text-indigo-100/70 text-sm font-medium">Input serah terima regu jaga</p>
                                    </div>
                                </div>
                                <div class="bg-white/20 p-3 rounded-full text-white group-hover:translate-x-2 transition-transform">
                                    <ArrowRight :size="24" />
                                </div>
                            </CardContent>
                        </Card>
                    </Link>

                    <Link :href="route('astekpam.index')" class="outline-none">
                        <Card class="border-none shadow-sm hover:bg-zinc-50 transition-colors bg-white group active:scale-[0.98]">
                            <CardContent class="p-6 flex items-center justify-between">
                                <div class="flex items-center gap-5">
                                    <div class="p-3 bg-zinc-100 rounded-2xl text-zinc-600 group-hover:bg-zinc-200 transition-colors">
                                        <History :size="28" />
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-zinc-800 text-lg leading-tight">Riwayat Laporan</h4>
                                        <p class="text-xs text-zinc-500 font-medium">Lihat data arsip laporan sebelumnya</p>
                                    </div>
                                </div>
                                <ArrowRight :size="20" class="text-zinc-300 group-hover:text-zinc-500 transition-colors" />
                            </CardContent>
                        </Card>
                    </Link>

                    <Link v-if="hasRole('admin')" :href="route('admin.users.index')" class="outline-none">
                        <Card class="border-none shadow-sm hover:bg-zinc-50 transition-colors bg-white group active:scale-[0.98]">
                            <CardContent class="p-6 flex items-center justify-between">
                                <div class="flex items-center gap-5">
                                    <div class="p-3 bg-zinc-100 rounded-2xl text-zinc-600 group-hover:bg-zinc-200 transition-colors">
                                        <Users :size="28" />
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-zinc-800 text-lg leading-tight">Manajemen Petugas</h4>
                                        <p class="text-xs text-zinc-500 font-medium">Kelola akun dan hak akses personil</p>
                                    </div>
                                </div>
                                <ArrowRight :size="20" class="text-zinc-300 group-hover:text-zinc-500 transition-colors" />
                            </CardContent>
                        </Card>
                    </Link>
                </div>
            </div>

            <div class="p-4 bg-amber-50 rounded-2xl border border-amber-100 flex gap-4 items-start shadow-sm">
                <div class="p-2 bg-white rounded-xl border border-amber-200 shadow-xs shrink-0">
                    <AlertCircle class="w-5 h-5 text-amber-500" />
                </div>
                <div class="space-y-1">
                    <h5 class="text-xs font-bold text-amber-900 uppercase tracking-wider">Instruksi Jaga</h5>
                    <p class="text-[11px] text-amber-800 leading-relaxed font-medium">
                        Wajib melakukan apel dan menghitung fisik narapidana di setiap blok sebelum menekan tombol <strong>Kirim Laporan</strong>.
                    </p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>