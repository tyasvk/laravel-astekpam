<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { 
    Landmark, HeartPulse, ListChecks, 
    ChevronRight, LayoutDashboard, AlertCircle, ArrowRight
} from 'lucide-vue-next';

const props = defineProps({
    latestReport: Object,
    stats: Object
});

// Pemetaan kunci database ke label yang rapi untuk Bagian Tugas
const taskLabels = {
    ka_rupam1: 'Karupam',
    wakarupam1: 'Wakarupam',
    kasatgas_p2u1: 'Kasatgas P2U',
    wakasatgas_p2u1: 'Wakasatgas P2U',
    blok_a1: 'Blok A',
    blok_b1: 'Blok B',
    menara_11: 'Menara 1',
    menara_21: 'Menara 2',
    menara_31: 'Menara 3',
    menara_41: 'Menara 4',
    jaga_rs1: 'Jaga RS',
    perwira_kontrol1: 'Perwira Kontrol',
    perwira_piket1: 'Perwira Piket',
    banjaga1: 'Banjaga',
    piket_dapur1: 'Piket Dapur',
    staff_kplp1: 'Staff KPLP',
    amanah1: 'Amanah',
    petugas_laporan1: 'Petugas Laporan'
};

// Fungsi untuk mengambil label, jika tidak ada di daftar, gunakan key aslinya
const getTaskLabel = (key) => taskLabels[key] || key;

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('id-ID', {
        weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
    });
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="min-h-screen bg-gray-50 pb-12">
            <div class="bg-white border-b border-gray-200 py-3 sticky top-0 z-20 shadow-sm">
                <div class="max-w-7xl mx-auto px-4 flex justify-between items-center">
                    <h2 class="text-sm font-bold text-gray-700 flex items-center gap-2">
                        <LayoutDashboard class="w-4 h-4 text-blue-600" /> Dashboard Monitoring
                    </h2>
                    <div class="bg-blue-50 px-3 py-1 rounded-full border border-blue-100 text-[10px] font-bold text-blue-600">
                        HARI INI: {{ stats.today }} Laporan
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 mt-4 space-y-4">
                
                <div v-if="latestReport" class="space-y-4">
                    
                    <div class="bg-slate-900 rounded-2xl p-5 text-white shadow-md relative overflow-hidden">
                        <div class="relative z-10 space-y-3">
                            <div class="flex justify-between items-start border-b border-white/10 pb-3">
                                <div>
                                    <p class="text-blue-400 text-[9px] font-bold uppercase tracking-widest">Update Terakhir</p>
                                    <h3 class="text-sm font-bold">{{ formatDate(latestReport.tanggal) }}</h3>
                                </div>
                                <div class="text-right">
                                    <p class="text-[9px] font-bold opacity-50 uppercase">Pukul</p>
                                    <p class="text-sm font-bold">{{ latestReport.pukul }} WIB</p>
                                </div>
                            </div>
                            <div class="flex items-center justify-center gap-4 py-1">
                                <div class="text-center flex-1">
                                    <span class="text-[8px] uppercase opacity-50 font-bold block">Dari Rupam</span>
                                    <span class="text-xs font-black text-blue-300">{{ latestReport.dari_rupam }}</span>
                                </div>
                                <ArrowRight class="w-4 h-4 text-white/20" />
                                <div class="text-center flex-1">
                                    <span class="text-[8px] uppercase opacity-50 font-bold block">Ke Rupam</span>
                                    <span class="text-xs font-black text-emerald-400">{{ latestReport.ke_rupam }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                        
                        <div class="lg:col-span-2 space-y-4">
                            <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
                                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-2 mb-4">
                                    <Landmark class="w-3 h-3 text-indigo-500" /> Data Blok & Hunian
                                </h4>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                    <div v-for="(val, lbl) in { 'Blok A': latestReport.blok_a, 'Blok B': latestReport.blok_b, 'Dapur': latestReport.dapur, 'Klinik': latestReport.klinik }" 
                                        :key="lbl" class="bg-gray-50 p-2 rounded-xl border border-gray-100 text-center">
                                        <p class="text-[8px] font-bold text-gray-400 uppercase">{{ lbl }}</p>
                                        <p class="text-sm font-bold text-gray-800">{{ val }}</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3 mt-4">
                                    <div class="bg-indigo-50 p-3 rounded-xl border border-indigo-100 flex justify-between items-center">
                                        <span class="text-[9px] font-bold text-indigo-500 uppercase">Dalam Lapas</span>
                                        <span class="text-lg font-black text-indigo-900">{{ latestReport.dalam_lapas }}</span>
                                    </div>
                                    <div class="bg-orange-50 p-3 rounded-xl border border-orange-100 flex justify-between items-center">
                                        <span class="text-[9px] font-bold text-orange-500 uppercase">Luar Lapas</span>
                                        <span class="text-lg font-black text-orange-900">{{ latestReport.luar_lapas }}</span>
                                    </div>
                                    <div class="col-span-2 bg-slate-800 p-3 rounded-xl flex justify-between items-center">
                                        <span class="text-[9px] font-bold text-gray-300 uppercase">Total Keseluruhan WBP</span>
                                        <span class="text-lg font-black text-white">{{ latestReport.total_wbp }} Orang</span>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
                                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-2 mb-4">
                                    <ListChecks class="w-3 h-3 text-blue-500" /> Pembagian Tugas Personil
                                </h4>
                                
                                <div v-if="latestReport.tugas" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div v-for="(personName, taskKey) in latestReport.tugas" :key="taskKey" 
                                        class="flex justify-between items-center p-3 bg-gray-50 rounded-xl border border-gray-100 hover:border-blue-100 transition">
                                        <span class="text-[9px] font-black text-gray-500 uppercase">{{ getTaskLabel(taskKey) }}</span>
                                        <span class="text-[11px] font-bold text-gray-800 truncate text-right ml-4">{{ personName || '-' }}</span>
                                    </div>
                                </div>
                                
                                <div v-else class="text-center py-6 text-gray-400 text-xs italic">
                                    Data pembagian tugas tidak ditemukan.
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
                                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-2 mb-4">
                                    <HeartPulse class="w-3 h-3 text-rose-500" /> Keterangan Diluar Lapas
                                </h4>
                                <div class="space-y-4">
                                    <div class="border-b border-gray-100 pb-3">
                                        <div class="flex justify-between text-[9px] font-bold uppercase mb-2">
                                            <span class="text-gray-400">Rawat Inap RS</span>
                                            <span class="text-rose-600">{{ latestReport.rawat_inap_rs }} Pasien</span>
                                        </div>
                                        <div class="flex flex-col gap-1.5">
                                            <span v-for="n in latestReport.rawat_inap_items" :key="n" class="text-[10px] font-bold text-gray-600 flex items-center gap-1.5"><div class="w-1.5 h-1.5 rounded-full bg-rose-400"></div> {{ n }}</span>
                                            <span v-if="!latestReport.rawat_inap_items?.length" class="text-[10px] text-gray-300 italic">Nihil</span>
                                        </div>
                                    </div>
                                    <div class="border-b border-gray-100 pb-3">
                                        <div class="flex justify-between text-[9px] font-bold uppercase mb-2">
                                            <span class="text-gray-400">Berobat Jalan</span>
                                            <span class="text-blue-600">{{ latestReport.berobat_rs }} Org</span>
                                        </div>
                                        <div class="flex flex-col gap-1.5">
                                            <span v-for="n in latestReport.berobat_items" :key="n" class="text-[10px] font-bold text-gray-600 flex items-center gap-1.5"><div class="w-1.5 h-1.5 rounded-full bg-blue-400"></div> {{ n }}</span>
                                            <span v-if="!latestReport.berobat_items?.length" class="text-[10px] text-gray-300 italic">Nihil</span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="flex justify-between text-[9px] font-bold uppercase mb-2">
                                            <span class="text-gray-400">Bon Luar</span>
                                            <span class="text-emerald-600">{{ latestReport.bon_luar }} Org</span>
                                        </div>
                                        <div class="flex flex-col gap-1.5">
                                            <span v-for="n in latestReport.bon_luar_items" :key="n" class="text-[10px] font-bold text-gray-600 flex items-center gap-1.5"><div class="w-1.5 h-1.5 rounded-full bg-emerald-400"></div> {{ n }}</span>
                                            <span v-if="!latestReport.bon_luar_items?.length" class="text-[10px] text-gray-300 italic">Nihil</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <Link :href="route('astekpam.show', latestReport.id)" class="w-full bg-gray-900 text-white p-3 rounded-xl flex items-center justify-between text-xs font-bold active:scale-95 transition shadow-sm">
                                    <span>Lihat Detail Laporan</span>
                                    <ChevronRight class="w-4 h-4" />
                                </Link>
                                <div class="grid grid-cols-2 gap-3">
                                    <Link :href="route('astekpam.create')" class="bg-blue-600 text-white py-3 rounded-xl font-bold text-[11px] text-center shadow-md active:scale-95 transition">Buat Baru</Link>
                                    <Link :href="route('astekpam.index')" class="bg-white border border-gray-200 text-gray-700 py-3 rounded-xl font-bold text-[11px] text-center active:scale-95 transition shadow-sm">Riwayat</Link>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div v-else class="bg-white rounded-2xl p-12 text-center border-2 border-dashed border-gray-200">
                    <AlertCircle class="w-8 h-8 text-gray-300 mx-auto mb-2" />
                    <p class="text-gray-500 text-xs font-bold">Laporan belum tersedia hari ini.</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>