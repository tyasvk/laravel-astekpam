<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Card, CardContent } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { 
    FileText, Plus, Eye, Filter, Calendar, Clock, ShieldCheck, User 
} from 'lucide-vue-next';

const props = defineProps({
    astekpams: Array
});

// State untuk Filter
const filterRegu = ref('all');
const filterWaktu = ref('all');

// Helper untuk perhitungan Waktu
const isSameDay = (date1, date2) => {
    return date1.getFullYear() === date2.getFullYear() &&
           date1.getMonth() === date2.getMonth() &&
           date1.getDate() === date2.getDate();
};

const getWeekNumber = (d) => {
    const target = new Date(Date.UTC(d.getFullYear(), d.getMonth(), d.getDate()));
    const dayNr = (target.getUTCDay() + 6) % 7;
    target.setUTCDate(target.getUTCDate() - dayNr + 3);
    const firstThursday = target.valueOf();
    target.setUTCMonth(0, 1);
    if (target.getUTCDay() !== 4) {
        target.setUTCMonth(0, 1 + ((4 - target.getUTCDay()) + 7) % 7);
    }
    return 1 + Math.ceil((firstThursday - target) / 604800000);
};

// Logika Penyaringan (Filter) Otomatis
const filteredAstekpams = computed(() => {
    let result = props.astekpams || [];
    
    // 1. FILTER REGU
    if (filterRegu.value !== 'all') {
        result = result.filter(item => {
            const regu = item.ke_rupam || ''; 
            return regu.includes(filterRegu.value);
        });
    }

    // 2. FILTER WAKTU (Harian, Mingguan, Bulanan)
    if (filterWaktu.value !== 'all') {
        const today = new Date();
        
        result = result.filter(item => {
            if (!item.tanggal) return false;
            
            const itemDate = new Date(item.tanggal);
            
            if (filterWaktu.value === 'harian') {
                return isSameDay(itemDate, today);
            } 
            else if (filterWaktu.value === 'mingguan') {
                return itemDate.getFullYear() === today.getFullYear() && 
                       getWeekNumber(itemDate) === getWeekNumber(today);
            } 
            else if (filterWaktu.value === 'bulanan') {
                return itemDate.getFullYear() === today.getFullYear() && 
                       itemDate.getMonth() === today.getMonth();
            }
            
            return true;
        });
    }

    // Urutkan selalu dari yang terbaru
    return result.sort((a, b) => new Date(b.tanggal) - new Date(a.tanggal));
});

const formatVal = (val) => val ? val : '-';

// Membantu mengambil data petugas laporan
const getPetugasPelapor = (laporan) => {
    if (laporan.petugas_laporan) return laporan.petugas_laporan;
    if (laporan.tugas && laporan.tugas.petugas_laporan) return laporan.tugas.petugas_laporan;
    if (laporan.tugas && laporan.tugas.amanah) return laporan.tugas.amanah;
    return 'Belum Diisi';
};
</script>

<template>
    <Head title="Riwayat Astekpam" />

    <AuthenticatedLayout>
        <div class="py-6 sm:py-8 bg-zinc-50/30 min-h-screen">
            <div class="w-full max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 space-y-4 sm:space-y-6">
                
                <div class="bg-white rounded-2xl sm:rounded-xl p-5 border border-zinc-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="text-center sm:text-left">
                        <h1 class="text-xl font-extrabold text-zinc-900 tracking-tight flex items-center justify-center sm:justify-start gap-2">
                            <FileText class="w-5 h-5 text-blue-600" /> Riwayat Astekpam
                        </h1>
                        <p class="text-zinc-500 text-xs sm:text-[13px] mt-1 sm:mt-0.5">Daftar laporan Apel Serah Terima Kepala Regu Pengamanan.</p>
                    </div>
                    <Link :href="route('astekpam.create')" class="w-full sm:w-auto shrink-0 mt-1 sm:mt-0">
                        <Button class="rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold shadow-md shadow-blue-200 h-12 sm:h-9 px-4 w-full text-sm sm:text-[13px]">
                            <Plus class="w-4 h-4 sm:w-3.5 sm:h-3.5 mr-2 sm:mr-1.5" /> Buat Laporan Baru
                        </Button>
                    </Link>
                </div>

                <Card class="rounded-2xl sm:rounded-xl border border-zinc-200 shadow-sm bg-white p-4 sm:p-3">
                    <div class="flex flex-col md:flex-row items-start md:items-center gap-3">
                        <div class="flex items-center gap-2 text-xs sm:text-[11px] font-bold text-zinc-400 uppercase tracking-wider shrink-0">
                            <Filter class="w-4 h-4 sm:w-3.5 sm:h-3.5 text-zinc-400" />
                            <span>Filter Data:</span>
                        </div>
                        
                        <div class="grid grid-cols-2 sm:grid-cols-2 md:flex gap-3 sm:gap-2 w-full md:w-auto">
                            <Select v-model="filterRegu">
                                <SelectTrigger class="h-11 sm:h-9 rounded-xl sm:rounded-lg bg-zinc-50 border-zinc-200 focus:ring-1 focus:ring-blue-500 w-full sm:w-40 text-sm sm:text-[12px] font-bold">
                                    <SelectValue placeholder="Semua Regu" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">Semua Regu</SelectItem>
                                    <SelectItem value="I">Regu I</SelectItem>
                                    <SelectItem value="II">Regu II</SelectItem>
                                    <SelectItem value="III">Regu III</SelectItem>
                                    <SelectItem value="IV">Regu IV</SelectItem>
                                </SelectContent>
                            </Select>

                            <Select v-model="filterWaktu">
                                <SelectTrigger class="h-11 sm:h-9 rounded-xl sm:rounded-lg bg-zinc-50 border-zinc-200 focus:ring-1 focus:ring-blue-500 w-full sm:w-40 text-sm sm:text-[12px] font-bold">
                                    <SelectValue placeholder="Semua Waktu" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">Semua Waktu</SelectItem>
                                    <SelectItem value="harian">Hari Ini</SelectItem>
                                    <SelectItem value="mingguan">Minggu Ini</SelectItem>
                                    <SelectItem value="bulanan">Bulan Ini</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                    </div>
                </Card>

                <Card class="rounded-2xl sm:rounded-xl border border-zinc-200 shadow-sm bg-white overflow-hidden">
                    <CardContent class="p-0">
                        
                        <div class="block lg:hidden divide-y divide-zinc-100">
                            <div v-for="laporan in filteredAstekpams" :key="laporan.id" class="p-4 sm:p-5 space-y-4 hover:bg-zinc-50 transition-colors">
                                <div class="flex justify-between items-start">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-zinc-900 flex items-center gap-1.5 text-sm whitespace-nowrap">
                                            <Calendar class="w-4 h-4 text-zinc-400 shrink-0"/> {{ formatVal(laporan.tanggal) }}
                                        </span>
                                        <span class="text-xs text-zinc-500 flex items-center gap-1.5 mt-1 whitespace-nowrap">
                                            <Clock class="w-3.5 h-3.5 text-zinc-400 shrink-0"/> Pukul {{ formatVal(laporan.pukul) }}
                                        </span>
                                    </div>
                                </div>

                                <div class="bg-zinc-50 p-4 rounded-xl border border-zinc-100 space-y-3.5">
                                    <div class="flex flex-col gap-2 w-full">
                                        <span class="text-[10px] text-zinc-500 font-bold uppercase tracking-wider shrink-0">Serah Terima</span>
                                        <div class="grid grid-cols-[1fr_20px_1fr] gap-y-2 gap-x-2 items-center">
                                            <span class="px-2 py-1.5 bg-white text-zinc-600 text-[11px] font-bold rounded-md border border-zinc-200 uppercase text-center w-full shadow-sm">
                                                {{ formatVal(laporan.dari_rupam) }}
                                            </span>
                                            <span class="text-zinc-300 text-sm font-medium text-center">→</span>
                                            <span class="px-2 py-1.5 bg-blue-50 text-blue-700 text-[11px] font-bold rounded-md border border-blue-200 uppercase text-center w-full shadow-sm">
                                                {{ formatVal(laporan.ke_rupam) }}
                                            </span>
                                            
                                            <span class="px-2 py-1.5 bg-white text-zinc-600 text-[11px] font-bold rounded-md border border-zinc-200 uppercase text-center w-full shadow-sm">
                                                {{ formatVal(laporan.dari_shift) }}
                                            </span>
                                            <span class="text-zinc-300 text-sm font-medium text-center">→</span>
                                            <span class="px-2 py-1.5 bg-emerald-50 text-emerald-700 text-[11px] font-bold rounded-md border border-emerald-200 uppercase text-center w-full shadow-sm">
                                                {{ formatVal(laporan.ke_shift) }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="border-t border-zinc-200/60 pt-3 mt-1">
                                        <span class="text-[10px] text-zinc-500 font-bold uppercase tracking-wider block mb-1">Pimpinan Apel</span>
                                        <span class="font-semibold text-zinc-800 flex items-start gap-2 text-[13px] break-words leading-snug">
                                            <ShieldCheck class="w-4 h-4 text-zinc-400 shrink-0"/> 
                                            {{ formatVal(laporan.pimpinan) }}
                                        </span>
                                    </div>

                                    <div class="border-t border-zinc-200/60 pt-3">
                                        <span class="text-[10px] text-zinc-500 font-bold uppercase tracking-wider block mb-1">Pelapor</span>
                                        <span class="font-bold text-zinc-800 flex items-start gap-2 text-[13px] break-words leading-snug">
                                            <User class="w-4 h-4 text-amber-500 shrink-0"/> 
                                            {{ getPetugasPelapor(laporan) }}
                                        </span>
                                    </div>
                                </div>

                                <Link :href="route('astekpam.show', laporan.id)" class="block w-full pt-1">
                                    <Button variant="outline" class="w-full text-blue-600 border-blue-200 hover:bg-blue-50 font-bold text-[13px] h-11 rounded-xl shadow-sm">
                                        <Eye class="w-4 h-4 mr-2 shrink-0" /> Lihat Detail Lengkap
                                    </Button>
                                </Link>
                            </div>
                            
                            <div v-if="filteredAstekpams.length === 0" class="text-center py-10 px-4">
                                <FileText class="w-10 h-10 text-zinc-300 mx-auto mb-3" />
                                <p class="text-sm font-semibold text-zinc-500">Tidak ada riwayat laporan.</p>
                            </div>
                        </div>

                        <div class="hidden lg:block w-full overflow-x-auto custom-scrollbar">
                            <table class="w-full text-left border-collapse min-w-max">
                                <thead>
                                    <tr class="bg-zinc-50 border-b border-zinc-100 text-zinc-400 font-bold text-[10px] tracking-wider uppercase">
                                        <th class="py-3 px-4 whitespace-nowrap">Tanggal & Waktu</th>
                                        <th class="py-3 px-4 whitespace-nowrap">Serah Terima (Regu & Shift)</th>
                                        <th class="py-3 px-4 whitespace-nowrap">Pimpinan Apel</th>
                                        <th class="py-3 px-4 whitespace-nowrap">Petugas Pelapor</th>
                                        <th class="py-3 px-4 text-center whitespace-nowrap">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-100 text-[12px] font-medium text-zinc-800">
                                    <tr v-for="laporan in filteredAstekpams" :key="laporan.id" class="hover:bg-zinc-50/50 transition-colors align-top">
                                        
                                        <td class="py-3 px-4 pt-3.5 whitespace-nowrap">
                                            <div class="flex flex-col">
                                                <span class="font-bold text-zinc-900 flex items-center gap-1.5 text-[12px]">
                                                    <Calendar class="w-3.5 h-3.5 text-zinc-400 shrink-0"/> {{ formatVal(laporan.tanggal) }}
                                                </span>
                                                <span class="text-[11px] text-zinc-500 flex items-center gap-1.5 mt-0.5">
                                                    <Clock class="w-3 h-3 text-zinc-400 shrink-0"/> Pukul {{ formatVal(laporan.pukul) }}
                                                </span>
                                            </div>
                                        </td>
                                        
                                        <td class="py-3 px-4 pb-3.5 pt-3 whitespace-nowrap">
                                            <div class="grid grid-cols-[85px_16px_85px] gap-y-2 gap-x-2 items-center">
                                                <span class="px-2 py-1 bg-zinc-100 text-zinc-600 text-[10px] font-bold rounded border border-zinc-200 uppercase tracking-wide text-center w-full">
                                                    {{ formatVal(laporan.dari_rupam) }}
                                                </span>
                                                <span class="text-zinc-400 text-[11px] font-medium text-center">→</span>
                                                <span class="px-2 py-1 bg-blue-50 text-blue-700 text-[10px] font-bold rounded border border-blue-100 uppercase tracking-wide text-center w-full">
                                                    {{ formatVal(laporan.ke_rupam) }}
                                                </span>
                                                
                                                <span class="px-2 py-1 bg-zinc-100 text-zinc-600 text-[10px] font-bold rounded border border-zinc-200 uppercase tracking-wide text-center w-full">
                                                    {{ formatVal(laporan.dari_shift) }}
                                                </span>
                                                <span class="text-zinc-400 text-[11px] font-medium text-center">→</span>
                                                <span class="px-2 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-bold rounded border border-emerald-100 uppercase tracking-wide text-center w-full">
                                                    {{ formatVal(laporan.ke_shift) }}
                                                </span>
                                            </div>
                                        </td>
                                        
                                        <td class="py-3 px-4 align-top pt-3.5 whitespace-nowrap">
                                            <div class="flex items-center gap-1.5">
                                                <ShieldCheck class="w-3.5 h-3.5 text-zinc-400 shrink-0" />
                                                <span class="text-zinc-700 font-semibold text-[12px]">
                                                    {{ formatVal(laporan.pimpinan) }}
                                                </span>
                                            </div>
                                        </td>
                                        
                                        <td class="py-3 px-4 align-top pt-3.5 whitespace-nowrap">
                                            <div class="flex items-center gap-1.5">
                                                <User class="w-3.5 h-3.5 text-amber-500 shrink-0" />
                                                <span class="text-zinc-800 font-bold text-[12px]">
                                                    {{ getPetugasPelapor(laporan) }}
                                                </span>
                                            </div>
                                        </td>
                                        
                                        <td class="py-3 px-4 text-center align-top pt-3.5 whitespace-nowrap">
                                            <Link :href="route('astekpam.show', laporan.id)">
                                                <Button variant="ghost" size="sm" class="text-blue-600 hover:bg-blue-50 font-bold text-[11px] h-7 px-2">
                                                    <Eye class="w-3 h-3 mr-1 shrink-0" /> Detail
                                                </Button>
                                            </Link>
                                        </td>
                                    </tr>
                                    
                                    <tr v-if="filteredAstekpams.length === 0">
                                        <td colspan="5" class="text-center py-8 text-zinc-400 italic whitespace-nowrap text-[12px]">
                                            Tidak ada data laporan.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    height: 6px;
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f5f9; 
    border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1; 
    border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8; 
}
</style>