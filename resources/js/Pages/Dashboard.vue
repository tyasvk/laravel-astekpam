<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardContent } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { 
    FileText, 
    CheckCircle2, 
    Clock, 
    ArrowRight, 
    Plus, 
    ShieldCheck,
    Zap,
    LayoutGrid
} from 'lucide-vue-next';

const stats = [
    { name: 'Laporan Masuk', value: '1,284', icon: FileText, color: 'text-blue-600', bg: 'bg-blue-50/50', border: 'border-blue-100' },
    { name: 'Verifikasi', value: '98.2%', icon: CheckCircle2, color: 'text-emerald-600', bg: 'bg-emerald-50/50', border: 'border-emerald-100' },
    { name: 'Waktu Rata-rata', value: '14m 20s', icon: Clock, color: 'text-amber-600', bg: 'bg-amber-50/50', border: 'border-amber-100' },
];
</script>

<template>
    <Head title="Dashboard Overview" />

    <AuthenticatedLayout>
        <div class="max-w-6xl mx-auto space-y-10 pb-10">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                <div class="space-y-1">
                    <h1 class="text-3xl font-medium tracking-tight text-zinc-900">Dashboard</h1>
                    <div class="flex items-center gap-2">
                        <div class="flex h-2 w-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.4)]"></div>
                        <p class="text-zinc-500 text-[11px] font-medium uppercase tracking-[0.2em]">Sistem Beroperasi Normal</p>
                    </div>
                </div>
                <Link :href="route('astekpam.create')" class="w-full sm:w-auto">
                    <Button class="w-full sm:w-auto rounded-xl px-6 h-12 bg-zinc-900 hover:bg-zinc-800 text-white font-medium shadow-xl shadow-zinc-200 transition-all active:scale-95">
                        <Plus class="mr-2 h-4 w-4" /> Buat Laporan
                    </Button>
                </Link>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <div v-for="stat in stats" :key="stat.name" 
                    :class="['p-6 rounded-[2rem] border transition-all hover:shadow-md cursor-default', stat.bg, stat.border]">
                    <div class="flex items-center justify-between mb-6">
                        <div :class="['p-2.5 rounded-xl bg-white shadow-sm', stat.color]">
                            <component :is="stat.icon" class="h-5 w-5" />
                        </div>
                        <Zap class="h-3 w-3 text-zinc-300" />
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-medium text-zinc-500 uppercase tracking-widest">{{ stat.name }}</p>
                        <h3 class="text-3xl font-normal tracking-tight text-zinc-900">{{ stat.value }}</h3>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">
                
                <div class="lg:col-span-7 space-y-6">
                    <div class="flex items-center justify-between px-2">
                        <div class="flex items-center gap-2">
                            <LayoutGrid class="h-4 w-4 text-zinc-400" />
                            <h2 class="text-sm font-medium text-zinc-900 uppercase tracking-widest">Aktivitas Terkini</h2>
                        </div>
                        <Link :href="route('astekpam.index')" class="text-xs text-blue-600 hover:text-blue-700 font-medium">
                            Lihat semua
                        </Link>
                    </div>

                    <div class="space-y-3">
                        <div v-for="i in 4" :key="i" class="group p-5 rounded-3xl bg-white border border-zinc-100 hover:border-blue-200 hover:shadow-sm transition-all flex items-center justify-between">
                            <div class="flex items-center gap-5">
                                <div class="h-11 w-11 rounded-2xl bg-zinc-50 flex items-center justify-center group-hover:bg-blue-50 transition-colors">
                                    <FileText class="h-5 w-5 text-zinc-400 group-hover:text-blue-500 transition-colors" />
                                </div>
                                <div class="space-y-1">
                                    <p class="text-sm font-medium text-zinc-800 tracking-tight">Serah Terima Regu Bravo — Charlie</p>
                                    <p class="text-[10px] text-zinc-400 font-normal uppercase tracking-tighter">08:00 WIB • KRP Sudirman</p>
                                </div>
                            </div>
                            <div class="h-8 w-8 rounded-full flex items-center justify-center text-zinc-300 group-hover:text-blue-500 group-hover:bg-blue-50 transition-all">
                                <ArrowRight class="h-4 w-4" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-5 space-y-8">
                    <div class="space-y-6">
                        <h2 class="text-sm font-medium text-zinc-900 uppercase tracking-widest px-2">Regu Aktif</h2>
                        
                        <Card class="border-none shadow-xl shadow-blue-900/10 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-[2.5rem] overflow-hidden relative">
                            <CardContent class="p-8 space-y-8 relative z-10 text-white">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-medium uppercase tracking-[0.2em] opacity-80">Shift Pagi Bertugas</span>
                                    <div class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></div>
                                </div>
                                
                                <div class="space-y-3">
                                    <h3 class="text-3xl font-normal tracking-tight">Regu Charlie</h3>
                                    <p class="text-xs opacity-80 font-normal leading-relaxed max-w-[240px]">
                                        Unit reaksi cepat dan pengamanan blok hunian sektor A & B.
                                    </p>
                                </div>

                                <div class="flex items-center gap-4 pt-6 border-t border-white/10">
                                    <div class="h-10 w-10 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center text-xs font-medium border border-white/10">
                                        BP
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[10px] opacity-60 font-medium uppercase tracking-widest leading-none mb-1.5">Kepala Regu</span>
                                        <span class="text-sm font-medium tracking-tight leading-none">Bpk. Pramono</span>
                                    </div>
                                </div>
                            </CardContent>
                            <div class="absolute -bottom-10 -right-10 h-40 w-40 rounded-full bg-white/5"></div>
                        </Card>

                        <div class="p-6 rounded-[2rem] bg-emerald-50 border border-emerald-100 flex gap-4">
                            <ShieldCheck class="h-5 w-5 text-emerald-600 shrink-0 mt-0.5" />
                            <div class="space-y-1">
                                <span class="text-[11px] font-medium text-emerald-700 uppercase tracking-widest">Integritas</span>
                                <p class="text-[11px] text-emerald-600/80 leading-relaxed font-normal">
                                    Pastikan berita acara serah terima telah divalidasi oleh kedua belah pihak KRP sebelum sistem dikunci.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Smooth transitions for mobile */
* {
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
}
</style>