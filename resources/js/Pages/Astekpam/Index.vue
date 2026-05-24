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
        <div class="py-6 bg-zinc-50/30 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                
                <div class="bg-white rounded-2xl p-6 border border-zinc-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-extrabold text-zinc-900 tracking-tight flex items-center gap-2">
                            <FileText class="w-6 h-6 text-blue-600" /> Riwayat Astekpam
                        </h1>
                        <p class="text-zinc-500 text-sm mt-0.5">Daftar laporan Apel Serah Terima Kepala Regu Pengamanan.</p>
                    </div>
                    <Link :href="route('astekpam.create')" class="w-full sm:w-auto">
                        <Button class="rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold shadow-md shadow-blue-200 h-11 px-5 w-full">
                            <Plus class="w-4 h-4 mr-2" /> Buat Laporan Baru
                        </Button>
                    </Link>
                </div>

                <Card class="rounded-2xl border border-zinc-200 shadow-sm bg-white p-4">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                        <div class="flex items-center gap-2 text-xs font-bold text-zinc-400 uppercase tracking-wider">
                            <Filter class="w-4 h-4 text-zinc-400" />
                            <span>Filter Data:</span>
                        </div>
                        
                        <div class="grid grid-cols-2 sm:flex gap-3 w-full sm:w-auto">
                            <Select v-model="filterRegu">
                                <SelectTrigger class="h-10 rounded-xl bg-zinc-50 border-0 focus:ring-1 focus:ring-blue-500 w-full sm:w-44 text-xs font-bold">
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
                                <SelectTrigger class="h-10 rounded-xl bg-zinc-50 border-0 focus:ring-1 focus:ring-blue-500 w-full sm:w-48 text-xs font-bold">
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

                <Card class="rounded-2xl border border-zinc-200 shadow-sm bg-white overflow-hidden">
                    <CardContent class="p-0">
                        
                        <div class="block md:hidden divide-y divide-zinc-100">
                            <div v-for="laporan in filteredAstekpams" :key="laporan.id" class="p-5 space-y-4 hover:bg-zinc-50 transition-colors">
                                <div class="flex justify-between items-start">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-zinc-900 flex items-center gap-1.5 text-sm">
                                            <Calendar class="w-4 h-4 text-zinc-400"/> {{ formatVal(laporan.tanggal) }}
                                        </span>
                                        <span class="text-xs text-zinc-500 flex items-center gap-1.5 mt-1">
                                            <Clock class="w-3.5 h-3.5 text-zinc-400"/> Pukul {{ formatVal(laporan.pukul) }}
                                        </span>
                                    </div>
                                </div>

                                <div class="bg-zinc-50 p-3.5 rounded-xl border border-zinc-100 space-y-3 text-sm">
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs text-zinc-500 font-bold uppercase tracking-wider">Serah Terima Regu</span>
                                        <div class="flex items-center gap-1.5">
                                            <span class="px-2 py-0.5 bg-white text-zinc-600 text-[10px] font-bold rounded-md border border-zinc-200 uppercase">{{ formatVal(laporan.dari_rupam) }}</span>
                                            <span class="text-zinc-300 text-[10px] font-bold">➔</span>
                                            <span class="px-2 py-0.5 bg-blue-50 text-blue-700 text-[10px] font-bold rounded-md border border-blue-200 uppercase">{{ formatVal(laporan.ke_rupam) }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="flex justify-between items-center border-t border-zinc-200/60 pt-2">
                                        <span class="text-xs text-zinc-500 font-semibold">Pimpinan Apel</span>
                                        <span class="font-semibold text-zinc-800 flex items-center gap-1.5 text-xs"><ShieldCheck class="w-3.5 h-3.5 text-zinc-400"/> {{ formatVal(laporan.pimpinan) }}</span>
                                    </div>

                                    <div class="flex justify-between items-center border-t border-zinc-200/60 pt-2">
                                        <span class="text-xs text-zinc-500 font-semibold">Pelapor</span>
                                        <span class="font-bold text-zinc-800 flex items-center gap-1.5 text-xs"><User class="w-3.5 h-3.5 text-amber-500"/> {{ getPetugasPelapor(laporan) }}</span>
                                    </div>
                                </div>

                                <Link :href="route('astekpam.show', laporan.id)" class="block w-full">
                                    <Button variant="outline" class="w-full text-blue-600 border-blue-100 hover:bg-blue-50 font-bold text-xs h-10 rounded-xl">
                                        <Eye class="w-4 h-4 mr-2" /> Buka Detail Laporan
                                    </Button>
                                </Link>
                            </div>
                            
                            <div v-if="filteredAstekpams.length === 0" class="text-center py-12 px-4">
                                <FileText class="w-10 h-10 text-zinc-300 mx-auto mb-3" />
                                <p class="text-sm font-semibold text-zinc-500">Tidak ada data laporan yang sesuai filter.</p>
                            </div>
                        </div>

                        <div class="hidden md:block">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-zinc-50 border-b border-zinc-100 text-zinc-400 font-bold text-[11px] tracking-wider uppercase">
                                        <th class="py-4 px-6">Tanggal & Waktu</th>
                                        <th class="py-4 px-6">Serah Terima Regu</th>
                                        <th class="py-4 px-6">Pimpinan Apel</th>
                                        <th class="py-4 px-6">Petugas Pelapor</th>
                                        <th class="py-4 px-6 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-100 text-sm font-medium text-zinc-800">
                                    <tr v-for="laporan in filteredAstekpams" :key="laporan.id" class="hover:bg-zinc-50/50 transition-colors">
                                        <td class="py-4 px-6">
                                            <div class="flex flex-col">
                                                <span class="font-bold text-zinc-900 flex items-center gap-1.5">
                                                    <Calendar class="w-3.5 h-3.5 text-zinc-400"/> {{ formatVal(laporan.tanggal) }}
                                                </span>
                                                <span class="text-xs text-zinc-500 flex items-center gap-1.5 mt-1">
                                                    <Clock class="w-3.5 h-3.5 text-zinc-400"/> Pukul {{ formatVal(laporan.pukul) }}
                                                </span>
                                            </div>
                                        </td>
                                        
                                        <td class="py-4 px-6">
                                            <div class="flex items-center gap-2">
                                                <span class="px-2 py-1 bg-zinc-100 text-zinc-600 text-[11px] font-bold rounded-md border border-zinc-200 uppercase tracking-wide">
                                                    {{ formatVal(laporan.dari_rupam) }}
                                                </span>
                                                <span class="text-zinc-300 text-xs font-bold">➔</span>
                                                <span class="px-2 py-1 bg-blue-50 text-blue-700 text-[11px] font-bold rounded-md border border-blue-100 uppercase tracking-wide">
                                                    {{ formatVal(laporan.ke_rupam) }}
                                                </span>
                                            </div>
                                        </td>
                                        
                                        <td class="py-4 px-6">
                                            <div class="flex items-center gap-2">
                                                <ShieldCheck class="w-4 h-4 text-zinc-400" />
                                                <span class="text-zinc-700 font-semibold">{{ formatVal(laporan.pimpinan) }}</span>
                                            </div>
                                        </td>
                                        
                                        <td class="py-4 px-6">
                                            <div class="flex items-center gap-2">
                                                <User class="w-4 h-4 text-amber-500" />
                                                <span class="text-zinc-800 font-bold">{{ getPetugasPelapor(laporan) }}</span>
                                            </div>
                                        </td>
                                        
                                        <td class="py-4 px-6 text-center">
                                            <Link :href="route('astekpam.show', laporan.id)">
                                                <Button variant="ghost" size="sm" class="text-blue-600 hover:bg-blue-50 font-bold text-xs h-8">
                                                    <Eye class="w-3.5 h-3.5 mr-1.5" /> Detail
                                                </Button>
                                            </Link>
                                        </td>
                                    </tr>
                                    
                                    <tr v-if="filteredAstekpams.length === 0">
                                        <td colspan="5" class="text-center py-12 text-zinc-400 italic">
                                            Tidak ada data laporan yang sesuai dengan filter yang dipilih.
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