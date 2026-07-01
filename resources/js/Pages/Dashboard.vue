<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Card, CardContent } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { 
    Users, ShieldAlert, Zap, Clock, Calendar, ArrowRight, Activity, MapPin, ClipboardList, FileText, Image as ImageIcon, CheckCircle2, ClipboardCheck
} from 'lucide-vue-next';

const props = defineProps({
    latestAstekpam: Object,
    isKuisionerActive: Boolean // <-- Tambahkan prop ini untuk menerima status kuisioner dari controller
});

const formatVal = (val) => {
    return val !== undefined && val !== null && val !== '' ? val : '-';
};

const parseJsonCount = (data) => {
    if (!data) return 0;
    if (Array.isArray(data)) return data.filter(i => i && i.ket && i.ket.trim() !== '').length;
    if (typeof data === 'string' && data.startsWith('[')) {
        try {
            return JSON.parse(data).filter(i => i && i.ket && i.ket.trim() !== '').length;
        } catch (e) {
            return 0;
        }
    }
    return 0;
};

const formatNamaTugas = (dataTugas) => {
    if (!dataTugas) return '-';
    
    let obj = dataTugas;
    if (typeof dataTugas === 'string') {
        try {
            obj = JSON.parse(dataTugas);
        } catch (e) {
            return dataTugas; 
        }
    }
    
    if (obj && typeof obj === 'object' && ('jam_1' in obj || 'jam_2' in obj || 'jam_3' in obj)) {
        const jams = [obj.jam_1, obj.jam_2, obj.jam_3].filter(j => j && j !== '-');
        const namaUnik = [...new Set(jams)];
        return namaUnik.length > 0 ? namaUnik.join(', ') : '-';
    }
    
    return dataTugas;
};

// Palet warna pastel untuk icon tugas
const colorClasses = [
    'bg-blue-100 text-blue-700', 'bg-emerald-100 text-emerald-700', 
    'bg-amber-100 text-amber-700', 'bg-purple-100 text-purple-700', 
    'bg-pink-100 text-pink-700', 'bg-rose-100 text-rose-700',
    'bg-indigo-100 text-indigo-700', 'bg-cyan-100 text-cyan-700', 
    'bg-teal-100 text-teal-700'
];

const getBadgeColor = (index) => colorClasses[index % colorClasses.length];

// Layout grid disesuaikan agar rapi
const taskRows = computed(() => {
    if (!props.latestAstekpam) return [];
    const t = props.latestAstekpam.tugas || props.latestAstekpam || {};
    
    return [
        { cols: 'md:grid-cols-2 lg:grid-cols-4', items: [
            { letter: 'a', label: 'Ka. Rupam', value: t.ka_rupam },
            { letter: 'b', label: 'Wakarupam', value: t.wakarupam },
            { letter: 'c', label: 'Kasatgas P2U', value: t.kasatgas_p2u },
            { letter: 'd', label: 'Wakasatgas P2U', value: t.wakasatgas_p2u },
        ]},
        { cols: 'md:grid-cols-2', items: [
            { letter: 'e', label: 'Blok A', value: formatNamaTugas(t.blok_a) },
            { letter: 'f', label: 'Blok B', value: formatNamaTugas(t.blok_b) },
        ]},
        { cols: 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4', items: [
            { letter: 'g', label: 'Menara 1', value: formatNamaTugas(t.menara_1) },
            { letter: 'h', label: 'Menara 2', value: formatNamaTugas(t.menara_2) },
            { letter: 'i', label: 'Menara 3', value: formatNamaTugas(t.menara_3) },
            { letter: 'j', label: 'Menara 4', value: formatNamaTugas(t.menara_4) },
        ]},
        { cols: 'md:grid-cols-2 lg:grid-cols-4', items: [
            { letter: 'k', label: 'Jaga RS', value: t.jaga_rs },
            { letter: 'l', label: 'Piket Dapur', value: t.piket_dapur },
            { letter: 'm', label: 'Pengawas Piket', value: t.perwira_kontrol },
            { letter: 'n', label: 'Perwira Piket', value: t.perwira_piket },
        ]},
        { cols: 'grid-cols-1 md:grid-cols-3', items: [
            { letter: 'o', label: 'Banjaga', value: t.banjaga },
            { letter: 'p', label: 'Staf KPLP', value: t.staff_kplp },
            { letter: 'q', label: 'Petugas Laporan', value: t.petugas_laporan },
        ]}
    ];
});

// Calculate Percentage for Progress Bars
const getPercentage = (hadir, jumlah) => {
    const h = parseInt(hadir) || 0;
    const j = parseInt(jumlah) || 1; // avoid division by zero
    return Math.min(Math.round((h / j) * 100), 100);
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="py-6 sm:py-8 bg-[#f8fafc] min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 sm:space-y-8">
                
                <!-- CARD WELCOME -->
                <div class="bg-gradient-to-br from-blue-600 via-indigo-600 to-violet-700 rounded-3xl p-6 sm:p-8 md:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6 shadow-lg shadow-indigo-500/20 relative overflow-hidden">
                    <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-white opacity-10 rounded-full blur-2xl pointer-events-none"></div>
                    <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-40 h-40 bg-white opacity-10 rounded-full blur-2xl pointer-events-none"></div>
                    
                    <div class="relative z-10">
                        <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight mb-2">
                            Selamat Datang, {{ $page.props.auth.user.name }}! 👋
                        </h1>
                        <p class="text-blue-100 text-sm sm:text-base font-medium max-w-xl">
                            Pantau ringkasan laporan regu pengamanan Lapas Kelas I Palembang hari ini dengan cepat dan mudah.
                        </p>
                    </div>
                    <Link :href="route('astekpam.create')" class="w-full md:w-auto relative z-10">
                        <Button class="w-full md:w-auto rounded-xl bg-white text-indigo-700 hover:bg-blue-50 font-bold h-12 px-6 text-sm sm:text-base shadow-xl transition-all hover:scale-105 active:scale-95">
                            + Buat Laporan Baru
                        </Button>
                    </Link>
                </div>

                <!-- CARD KUISIONER (MUNCUL JIKA AKTIF/ON) -->
                <div v-if="isKuisionerActive" class="bg-white border-2 border-indigo-100 rounded-3xl p-6 sm:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6 shadow-md shadow-indigo-200/50 relative overflow-hidden group hover:border-indigo-200 transition-colors">
                    <div class="absolute -right-10 -top-10 w-40 h-40 bg-indigo-50 rounded-full opacity-50 group-hover:scale-110 transition-transform duration-700 ease-out pointer-events-none"></div>
                    
                    <div class="relative z-10 flex items-start sm:items-center gap-5">
                        <div class="w-14 h-14 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0 shadow-inner">
                            <ClipboardCheck class="w-7 h-7" />
                        </div>
                        <div>
                            <h3 class="text-lg sm:text-xl font-extrabold text-slate-800 tracking-tight">Evaluasi & Kuisioner</h3>
                            <p class="text-sm text-slate-500 mt-1 font-medium max-w-xl">
                                Terdapat form evaluasi/kuisioner yang sedang aktif. Mohon kesediaannya untuk mengisi form tersebut.
                            </p>
                        </div>
                    </div>
                    
                    <Link :href="route('kuisioner.fill')" class="w-full md:w-auto relative z-10 shrink-0">
                        <Button class="w-full md:w-auto rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold h-12 px-8 text-sm sm:text-base shadow-lg shadow-indigo-200 transition-all hover:scale-105 active:scale-95">
                            Isi Kuisioner Sekarang
                        </Button>
                    </Link>
                </div>

                <div v-if="props.latestAstekpam" class="space-y-6 sm:space-y-8">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mt-2">
                        <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2.5">
                            <div class="p-2 bg-blue-100 text-blue-600 rounded-lg">
                                <Activity class="w-5 h-5" /> 
                            </div>
                            Ringkasan Shift Terakhir
                        </h2>
                        <Link :href="route('astekpam.index')" class="inline-flex items-center text-sm font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-4 py-2 rounded-full transition-colors">
                            Lihat Semua Riwayat <ArrowRight class="ml-1.5 w-4 h-4" />
                        </Link>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        
                        <div class="lg:col-span-4 space-y-6">
                            <Card class="rounded-3xl border-0 shadow-md shadow-slate-200/50 bg-white overflow-hidden h-full">
                                <div class="bg-gradient-to-r from-slate-50 to-slate-100 px-6 py-5 flex justify-between items-center border-b border-slate-100">
                                    <div class="flex items-center gap-3 text-slate-700">
                                        <div class="p-2 bg-white rounded-md shadow-sm">
                                            <Calendar class="w-4 h-4 text-indigo-500" />
                                        </div>
                                        <span class="font-bold text-sm">{{ formatVal(props.latestAstekpam.tanggal) }}</span>
                                    </div>
                                    <span class="text-xs bg-indigo-100 text-indigo-700 px-3 py-1.5 rounded-lg font-bold uppercase tracking-wider">
                                        {{ formatVal(props.latestAstekpam.pukul) }}
                                    </span>
                                </div>
                                
                                <CardContent class="p-6 space-y-7">
                                    <div class="relative space-y-6 before:absolute before:inset-0 before:ml-[17px] before:-translate-x-px before:h-full before:w-0.5 before:bg-gradient-to-b before:from-slate-200 before:via-indigo-200 before:to-transparent">
                                        
                                        <div class="relative flex items-center gap-4 group">
                                            <div class="flex items-center justify-center w-9 h-9 rounded-full border-4 border-white bg-slate-200 text-slate-500 shadow-sm shrink-0 z-10">
                                                <Clock class="w-4 h-4" />
                                            </div>
                                            <div class="flex-1 min-w-0 bg-white border border-slate-100 p-4 rounded-2xl shadow-sm hover:shadow-md transition-shadow">
                                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Shift Sebelumnya</p>
                                                <p class="font-bold text-slate-700 text-base truncate">Regu {{ formatVal(props.latestAstekpam.dari_rupam) }}</p>
                                                <p class="text-slate-500 text-xs mt-1 font-medium truncate">{{ formatVal(props.latestAstekpam.dari_shift) }}</p>
                                            </div>
                                        </div>
                                        
                                        <div class="relative flex items-center gap-4 group">
                                            <div class="flex items-center justify-center w-9 h-9 rounded-full border-4 border-white bg-indigo-500 text-white shadow-md shadow-indigo-200 shrink-0 z-10 animate-pulse">
                                                <CheckCircle2 class="w-5 h-5" />
                                            </div>
                                            <div class="flex-1 min-w-0 bg-gradient-to-br from-indigo-50 to-blue-50 border border-indigo-100 p-4 rounded-2xl shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
                                                <div class="absolute top-0 right-0 w-16 h-16 bg-indigo-500 opacity-5 rounded-bl-full pointer-events-none"></div>
                                                <p class="text-[10px] font-bold text-indigo-500 uppercase tracking-wider mb-1">Shift Saat Ini</p>
                                                <p class="font-bold text-indigo-900 text-base truncate">Regu {{ formatVal(props.latestAstekpam.ke_rupam) }}</p>
                                                <p class="text-indigo-700 text-xs mt-1 font-medium truncate">{{ formatVal(props.latestAstekpam.ke_shift) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 flex items-center gap-4 hover:bg-slate-100 transition-colors">
                                        <div class="w-12 h-12 rounded-xl bg-white border border-slate-200 text-indigo-600 flex items-center justify-center shadow-sm">
                                            <Users class="w-6 h-6" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Pimpinan Regu</p>
                                            <p class="font-bold text-slate-800 text-sm mt-0.5 truncate">{{ formatVal(props.latestAstekpam.pimpinan) }}</p>
                                        </div>
                                    </div>

                                    <div v-if="props.latestAstekpam.foto_laporan" class="space-y-3 pt-2">
                                        <p class="text-xs font-bold text-slate-500 flex items-center gap-2 uppercase tracking-wider">
                                            <ImageIcon class="w-4 h-4 text-slate-400" /> Bukti Foto Laporan
                                        </p>
                                        <a :href="`/storage/${props.latestAstekpam.foto_laporan}`" target="_blank" class="block w-full rounded-2xl overflow-hidden border-2 border-slate-100 group relative">
                                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors z-10 flex items-center justify-center">
                                                <span class="opacity-0 group-hover:opacity-100 bg-white/90 text-slate-800 text-xs font-bold px-3 py-1.5 rounded-lg transition-opacity">Lihat Penuh</span>
                                            </div>
                                            <img :src="`/storage/${props.latestAstekpam.foto_laporan}`" alt="Foto Laporan" class="w-full h-36 object-cover transform group-hover:scale-105 transition-transform duration-500" />
                                        </a>
                                    </div>

                                    <Link :href="route('astekpam.show', props.latestAstekpam.id)" class="block w-full pt-2">
                                        <Button variant="outline" class="w-full rounded-xl border-2 border-slate-200 text-slate-700 font-bold hover:bg-slate-50 hover:text-indigo-600 text-sm h-12 transition-all">
                                            Lihat Detail Lengkap Laporan
                                        </Button>
                                    </Link>
                                </CardContent>
                            </Card>
                        </div>

                        <div class="lg:col-span-8 flex flex-col gap-6">
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                                <div class="bg-white border-0 rounded-3xl p-6 shadow-md shadow-slate-200/50 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                                    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-bl-full -mr-10 -mt-10 transition-transform group-hover:scale-110"></div>
                                    <div class="relative flex items-center gap-5">
                                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 text-white flex items-center justify-center shrink-0 shadow-lg shadow-blue-500/30">
                                            <Users class="w-7 h-7" />
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total WBP</p>
                                            <h3 class="text-3xl font-extrabold text-slate-800 mt-1">{{ formatVal(props.latestAstekpam.total_wbp) }}</h3>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white border-0 rounded-3xl p-6 shadow-md shadow-slate-200/50 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                                    <div class="absolute top-0 right-0 w-32 h-32 bg-purple-50 rounded-bl-full -mr-10 -mt-10 transition-transform group-hover:scale-110"></div>
                                    <div class="relative flex items-center gap-5">
                                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-purple-500 to-fuchsia-600 text-white flex items-center justify-center shrink-0 shadow-lg shadow-purple-500/30">
                                            <Users class="w-7 h-7" />
                                        </div>
                                        <div class="w-full">
                                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Narapidana</p>
                                            <div class="flex justify-between items-end mt-1">
                                                <h3 class="text-3xl font-extrabold text-slate-800">{{ formatVal(props.latestAstekpam.narapidana) }}</h3>
                                                <span class="text-[10px] text-purple-700 font-bold bg-purple-100 px-2.5 py-1 rounded-lg">
                                                    Kapasitas: {{ formatVal(props.latestAstekpam.kapasitas) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white border-0 shadow-md shadow-slate-200/50 rounded-3xl p-6">
                                <p class="text-sm font-bold text-slate-700 mb-5 flex items-center gap-2 uppercase tracking-wide">
                                    <div class="p-1.5 bg-orange-100 text-orange-600 rounded-md"><MapPin class="w-4 h-4"/></div> 
                                    Distribusi Penghuni
                                </p>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <div v-for="b in ['blok_a', 'blok_b', 'dapur', 'klinik']" :key="b" 
                                         class="p-4 rounded-2xl bg-slate-50 border border-slate-100 flex flex-col justify-center items-center hover:bg-orange-50 hover:border-orange-100 transition-colors group">
                                        <p class="text-[11px] font-bold text-slate-400 group-hover:text-orange-500 uppercase tracking-wider mb-2">{{ b.replace('_', ' ') }}</p>
                                        <p class="text-2xl font-black text-slate-700 group-hover:text-orange-700">{{ formatVal(props.latestAstekpam[b]) }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                                
                                <div class="bg-white border-0 rounded-3xl p-6 shadow-md shadow-slate-200/50">
                                    <p class="text-sm font-bold text-slate-700 mb-5 flex items-center gap-2 uppercase tracking-wide">
                                        <div class="p-1.5 bg-rose-100 text-rose-600 rounded-md"><ShieldAlert class="w-4 h-4"/></div> 
                                        Kondisi Luar Lapas
                                    </p>
                                    <div class="space-y-4">
                                        <div class="flex justify-between items-center p-3 rounded-xl bg-slate-50 hover:bg-rose-50 transition-colors">
                                            <span class="text-slate-600 font-semibold text-sm">Rawat Inap RS</span>
                                            <span class="font-bold text-slate-800 bg-white px-3 py-1 rounded-lg shadow-sm">{{ parseJsonCount(props.latestAstekpam.rawat_inap_items) }} Org</span>
                                        </div>
                                        <div class="flex justify-between items-center p-3 rounded-xl bg-slate-50 hover:bg-rose-50 transition-colors">
                                            <span class="text-slate-600 font-semibold text-sm">Berobat RS</span>
                                            <span class="font-bold text-slate-800 bg-white px-3 py-1 rounded-lg shadow-sm">{{ parseJsonCount(props.latestAstekpam.berobat_items) }} Org</span>
                                        </div>
                                        <div class="flex justify-between items-center p-3 rounded-xl bg-slate-50 hover:bg-rose-50 transition-colors">
                                            <span class="text-slate-600 font-semibold text-sm">Bon Luar</span>
                                            <span class="font-bold text-slate-800 bg-white px-3 py-1 rounded-lg shadow-sm">{{ formatVal(props.latestAstekpam.luar_lapas) }} Org</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white border-0 rounded-3xl p-6 shadow-md shadow-slate-200/50">
                                    <p class="text-sm font-bold text-slate-700 mb-5 flex items-center gap-2 uppercase tracking-wide">
                                        <div class="p-1.5 bg-emerald-100 text-emerald-600 rounded-md"><Zap class="w-4 h-4"/></div> 
                                        Kehadiran Personil
                                    </p>
                                    <div class="space-y-6 mt-2">
                                        <div>
                                            <div class="flex justify-between items-end mb-2">
                                                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Regu Pengamanan</span>
                                                <span class="font-bold text-slate-700 text-sm bg-slate-100 px-2 py-0.5 rounded-md">{{ formatVal(props.latestAstekpam.rupam_hadir) }} / {{ formatVal(props.latestAstekpam.rupam_jumlah) }}</span>
                                            </div>
                                            <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                                                <div class="bg-gradient-to-r from-emerald-400 to-emerald-500 h-full rounded-full transition-all duration-1000" :style="{ width: getPercentage(props.latestAstekpam.rupam_hadir, props.latestAstekpam.rupam_jumlah) + '%' }"></div>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="flex justify-between items-end mb-2">
                                                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Satgas P2U</span>
                                                <span class="font-bold text-slate-700 text-sm bg-slate-100 px-2 py-0.5 rounded-md">{{ formatVal(props.latestAstekpam.p2u_hadir) }} / {{ formatVal(props.latestAstekpam.p2u_jumlah) }}</span>
                                            </div>
                                            <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                                                <div class="bg-gradient-to-r from-teal-400 to-teal-500 h-full rounded-full transition-all duration-1000" :style="{ width: getPercentage(props.latestAstekpam.p2u_hadir, props.latestAstekpam.p2u_jumlah) + '%' }"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <Card class="rounded-3xl border-0 shadow-md shadow-slate-200/50 bg-white overflow-hidden mt-2">
                        <div class="px-6 py-5 border-b border-slate-100 flex items-center gap-3 bg-white">
                            <div class="p-2 bg-indigo-100 text-indigo-600 rounded-lg">
                                <ClipboardList class="w-5 h-5" />
                            </div>
                            <span class="font-bold text-base text-slate-800 uppercase tracking-wide">Pembagian Tugas Jaga Personil</span>
                        </div>
                        <CardContent class="p-6 sm:p-8 bg-slate-50/30">
                            <div class="space-y-5">
                                <div v-for="(row, rowIndex) in taskRows" :key="rowIndex" :class="['grid gap-4 sm:gap-5', row.cols]">
                                    
                                    <div 
                                        v-for="(task, itemIndex) in row.items" 
                                        :key="task.letter" 
                                        class="group flex items-center gap-4 p-4 rounded-2xl border border-slate-200 bg-white hover:border-indigo-300 hover:shadow-lg hover:shadow-indigo-100 hover:-translate-y-1 transition-all duration-300 cursor-default"
                                    >
                                        <div :class="['w-10 h-10 rounded-xl text-sm font-black flex items-center justify-center uppercase shrink-0 transition-colors', getBadgeColor(rowIndex)]">
                                            {{ task.letter }}
                                        </div>
                                        
                                        <div class="min-w-0 flex-1">
                                            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1 truncate">
                                                {{ task.label }}
                                            </p>
                                            <p class="text-sm font-bold text-slate-700 truncate group-hover:text-indigo-700 transition-colors">
                                                {{ formatVal(task.value) }}
                                            </p>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <div v-else class="bg-white border-0 shadow-md shadow-slate-200/50 rounded-3xl p-10 sm:p-16 text-center mt-6">
                    <div class="bg-gradient-to-br from-indigo-50 to-blue-50 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                        <FileText class="w-10 h-10 text-indigo-400" />
                    </div>
                    <h3 class="text-xl font-bold text-slate-800">Belum Ada Data Laporan</h3>
                    <p class="text-slate-500 mt-3 max-w-md mx-auto text-sm sm:text-base leading-relaxed">
                        Sistem belum menemukan data laporan serah terima regu pengamanan hari ini. Mulai dengan membuat laporan baru.
                    </p>
                    <Link :href="route('astekpam.create')">
                        <Button class="mt-8 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm sm:text-base font-bold h-12 px-8 shadow-lg shadow-indigo-200 hover:scale-105 active:scale-95 transition-all">
                            Buat Laporan Sekarang
                        </Button>
                    </Link>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>