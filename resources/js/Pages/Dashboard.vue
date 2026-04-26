<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { 
    FileText, 
    CheckCircle2, 
    Clock, 
    ArrowRight, 
    Plus, 
    ShieldCheck,
    Zap,
    Activity
} from 'lucide-vue-next';
import { Card, CardContent } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';

// Menerima data dari DashboardController dengan default value untuk keamanan
const props = defineProps({
    stats: {
        type: Object,
        default: () => ({ total: '0', today: 0, efficiency: '0%' })
    },
    recentReports: {
        type: Array,
        default: () => []
    },
    activeDuty: {
        type: Object,
        default: () => ({ shift: '-', rupam: '-', pimpinan: '-', jam: '-' })
    }
});

// Helper untuk format tanggal singkat
const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short'
    });
};
</script>

<template>
    <Head title="Dashboard Utama" />

    <AuthenticatedLayout>
        <div class="max-w-6xl mx-auto space-y-10 pb-20 px-4 font-sans">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 mt-6">
                <div class="space-y-1">
                    <h1 class="text-3xl font-medium tracking-tighter text-zinc-900 italic font-sans">Ringkasan Operasional</h1>
                    <div class="flex items-center gap-2">
                        <div class="h-1.5 w-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></div>
                        <p class="text-zinc-500 text-[10px] font-medium uppercase tracking-[0.2em]">Sistem Astekpam Aktif</p>
                    </div>
                </div>
                <Link :href="route('astekpam.create')" class="w-full sm:w-auto">
                    <Button class="w-full sm:w-auto h-12 rounded-2xl bg-zinc-900 text-white shadow-xl shadow-zinc-200 hover:bg-zinc-800 transition-all px-6 font-medium text-[11px] uppercase tracking-widest active:scale-95">
                        <Plus class="w-4 h-4 mr-2" /> Buat Laporan Baru
                    </Button>
                </Link>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div class="p-6 rounded-[2.5rem] bg-blue-50/50 border border-blue-100/50 space-y-4 transition-all hover:shadow-lg hover:shadow-blue-500/5">
                    <div class="h-10 w-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-blue-600">
                        <FileText class="w-5 h-5" />
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Total Laporan</p>
                        <h3 class="text-3xl font-medium tracking-tighter text-zinc-900">{{ stats?.total ?? 0 }}</h3>
                    </div>
                </div>

                <div class="p-6 rounded-[2.5rem] bg-emerald-50/50 border border-emerald-100/50 space-y-4 transition-all hover:shadow-lg hover:shadow-emerald-500/5">
                    <div class="h-10 w-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-emerald-600">
                        <Zap class="w-5 h-5" />
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Laporan Hari Ini</p>
                        <h3 class="text-3xl font-medium tracking-tighter text-zinc-900">{{ stats?.today ?? 0 }}</h3>
                    </div>
                </div>

                <div class="p-6 rounded-[2.5rem] bg-amber-50/50 border border-amber-100/50 space-y-4 transition-all hover:shadow-lg hover:shadow-amber-500/5">
                    <div class="h-10 w-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-amber-600">
                        <CheckCircle2 class="w-5 h-5" />
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Tingkat Validasi</p>
                        <h3 class="text-3xl font-medium tracking-tighter text-zinc-900">{{ stats?.efficiency ?? '0%' }}</h3>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">
                
                <div class="lg:col-span-7 space-y-6">
                    <div class="flex items-center justify-between px-2">
                        <div class="flex items-center gap-2">
                            <Activity class="w-4 h-4 text-zinc-400" />
                            <h2 class="text-xs font-bold text-zinc-900 uppercase tracking-widest">Aktivitas Terkini</h2>
                        </div>
                        <Link :href="route('astekpam.index')" class="text-[10px] font-bold text-blue-600 uppercase tracking-widest hover:underline decoration-2 underline-offset-4">
                            Lihat Semua
                        </Link>
                    </div>

                    <div class="space-y-3">
                        <template v-if="recentReports.length > 0">
                            <Link v-for="report in recentReports" :key="report.id" :href="route('astekpam.show', report.id)"
                                class="group p-5 rounded-[2rem] bg-white border border-zinc-100 hover:border-blue-200 transition-all flex items-center justify-between">
                                <div class="flex items-center gap-5">
                                    <div class="h-11 w-11 rounded-2xl bg-zinc-50 flex items-center justify-center text-zinc-400 group-hover:bg-blue-50 group-hover:text-blue-500 transition-colors">
                                        <Clock class="w-5 h-5" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-zinc-800 tracking-tight group-hover:text-blue-700 transition-colors">
                                            {{ report.dari_rupam }} ke {{ report.ke_rupam }}
                                        </p>
                                        <p class="text-[10px] text-zinc-400 uppercase tracking-tighter font-medium">
                                            {{ formatDate(report.tanggal) }} • {{ report.pukul }} • {{ report.pimpinan }}
                                        </p>
                                    </div>
                                </div>
                                <ArrowRight class="w-4 h-4 text-zinc-300 opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all" />
                            </Link>
                        </template>
                        
                        <div v-else class="py-16 text-center border border-dashed border-zinc-200 rounded-[2.5rem] bg-zinc-50/30">
                            <p class="text-xs text-zinc-400 font-medium tracking-wide">Belum ada aktivitas pelaporan terbaru.</p>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-5 space-y-8">
                    <div class="space-y-6">
                        <h2 class="text-xs font-bold text-zinc-900 uppercase tracking-widest px-2">Informasi Regu Jaga</h2>
                        
                        <Card class="border-none shadow-2xl shadow-blue-900/10 bg-gradient-to-br from-zinc-900 to-zinc-800 rounded-[2.5rem] overflow-hidden relative">
                            <CardContent class="p-8 space-y-8 relative z-10 text-white">
                                <div class="flex items-center justify-between">
                                    <span class="text-[9px] font-bold uppercase tracking-[0.2em] text-blue-400">Shift {{ activeDuty?.shift }} Aktif</span>
                                    <div class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse shadow-[0_0_10px_#34d399]"></div>
                                </div>
                                
                                <div class="space-y-2">
                                    <h3 class="text-3xl font-medium tracking-tighter">{{ activeDuty?.rupam || 'Belum Terplot' }}</h3>
                                    <p class="text-[11px] text-zinc-400 font-normal tracking-wide">
                                        Wilayah tugas Lapas Kelas I Palembang pukul {{ activeDuty?.jam }}.
                                    </p>
                                </div>

                                <div class="flex items-center gap-4 pt-6 border-t border-white/5">
                                    <div class="h-10 w-10 rounded-2xl bg-white/5 flex items-center justify-center text-[10px] font-bold border border-white/5">
                                        KRP
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[9px] text-zinc-500 font-bold uppercase tracking-widest leading-none mb-1.5">Pimpinan Regu</span>
                                        <span class="text-sm font-medium tracking-tight leading-none text-white">{{ activeDuty?.pimpinan }}</span>
                                    </div>
                                </div>
                            </CardContent>
                            <div class="absolute -bottom-10 -right-10 h-32 w-32 rounded-full bg-blue-500/10 blur-3xl"></div>
                        </Card>

                        <div class="p-6 rounded-[2.5rem] bg-zinc-50 border border-zinc-100 space-y-3">
                            <div class="flex items-center gap-2 text-zinc-900">
                                <ShieldCheck class="w-4 h-4" />
                                <span class="text-[10px] font-bold uppercase tracking-widest">Pesan Integritas</span>
                            </div>
                            <p class="text-[11px] text-zinc-500 leading-relaxed italic font-normal">
                                "Waspada Jangan-Jangan! Laporkan setiap detil pergerakan demi menjamin keamanan dan ketertiban fasilitas."
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Smooth Transition untuk hover effect */
* {
    transition: all 300ms cubic-bezier(0.4, 0, 0.2, 1);
}
</style>