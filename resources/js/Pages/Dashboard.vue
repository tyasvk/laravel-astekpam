<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Card, CardContent } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { 
    Users, ShieldAlert, Zap, Clock, Calendar, ArrowRight, Activity, MapPin, ClipboardList, FileText, Image as ImageIcon
} from 'lucide-vue-next';

const props = defineProps({
    latestAstekpam: Object
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

// Pemetaan kelas Tailwind berdasarkan Tema Warna
const getThemeClasses = (theme) => {
    const themes = {
        blue: {
            card: 'bg-blue-50/60 border-blue-200 hover:border-blue-400 hover:shadow-blue-200',
            badge: 'bg-blue-200 text-blue-800',
            textLabel: 'text-blue-600',
            textVal: 'text-blue-950'
        },
        indigo: {
            card: 'bg-indigo-50/60 border-indigo-200 hover:border-indigo-400 hover:shadow-indigo-200',
            badge: 'bg-indigo-200 text-indigo-800',
            textLabel: 'text-indigo-600',
            textVal: 'text-indigo-950'
        },
        emerald: {
            card: 'bg-emerald-50/60 border-emerald-200 hover:border-emerald-400 hover:shadow-emerald-200',
            badge: 'bg-emerald-200 text-emerald-800',
            textLabel: 'text-emerald-600',
            textVal: 'text-emerald-950'
        },
        amber: {
            card: 'bg-amber-50/60 border-amber-200 hover:border-amber-400 hover:shadow-amber-200',
            badge: 'bg-amber-200 text-amber-800',
            textLabel: 'text-amber-700',
            textVal: 'text-amber-950'
        },
        rose: {
            card: 'bg-rose-50/60 border-rose-200 hover:border-rose-400 hover:shadow-rose-200',
            badge: 'bg-rose-200 text-rose-800',
            textLabel: 'text-rose-600',
            textVal: 'text-rose-950'
        },
        fuchsia: {
            card: 'bg-fuchsia-50/60 border-fuchsia-200 hover:border-fuchsia-400 hover:shadow-fuchsia-200',
            badge: 'bg-fuchsia-200 text-fuchsia-800',
            textLabel: 'text-fuchsia-600',
            textVal: 'text-fuchsia-950'
        },
        teal: {
            card: 'bg-teal-50/60 border-teal-200 hover:border-teal-400 hover:shadow-teal-200',
            badge: 'bg-teal-200 text-teal-800',
            textLabel: 'text-teal-600',
            textVal: 'text-teal-950'
        },
        cyan: {
            card: 'bg-cyan-50/60 border-cyan-200 hover:border-cyan-400 hover:shadow-cyan-200',
            badge: 'bg-cyan-200 text-cyan-800',
            textLabel: 'text-cyan-600',
            textVal: 'text-cyan-950'
        }
    };
    return themes[theme] || themes.blue; // Default biru jika tidak ditemukan
};

// Penambahan 'theme' pada setiap baris tugas
const taskRows = computed(() => {
    if (!props.latestAstekpam) return [];
    const t = props.latestAstekpam.tugas || props.latestAstekpam || {};
    
    return [
        { cols: 'md:grid-cols-2', theme: 'blue', items: [
            { letter: 'a', label: 'Ka. Rupam', value: t.ka_rupam },
            { letter: 'b', label: 'Wakarupam', value: t.wakarupam },
        ]},
        { cols: 'md:grid-cols-2', theme: 'indigo', items: [
            { letter: 'c', label: 'Kasatgas P2U', value: t.kasatgas_p2u },
            { letter: 'd', label: 'Wakasatgas P2U', value: t.wakasatgas_p2u },
        ]},
        { cols: 'md:grid-cols-2', theme: 'emerald', items: [
            { letter: 'e', label: 'Blok A', value: formatNamaTugas(t.blok_a) },
            { letter: 'f', label: 'Blok B', value: formatNamaTugas(t.blok_b) },
        ]},
        { cols: 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4', theme: 'amber', items: [
            { letter: 'g', label: 'Menara 1', value: formatNamaTugas(t.menara_1) },
            { letter: 'h', label: 'Menara 2', value: formatNamaTugas(t.menara_2) },
            { letter: 'i', label: 'Menara 3', value: formatNamaTugas(t.menara_3) },
            { letter: 'j', label: 'Menara 4', value: formatNamaTugas(t.menara_4) },
        ]},
        { cols: 'md:grid-cols-2', theme: 'rose', items: [
            { letter: 'k', label: 'Jaga RS', value: t.jaga_rs },
            { letter: 'l', label: 'Piket Dapur', value: t.piket_dapur },
        ]},
        { cols: 'md:grid-cols-2', theme: 'fuchsia', items: [
            { letter: 'm', label: 'Pengawas Piket', value: t.perwira_kontrol },
            { letter: 'n', label: 'Perwira Piket', value: t.perwira_piket },
        ]},
        { cols: 'grid-cols-1', theme: 'teal', items: [
            { letter: 'o', label: 'Banjaga', value: t.banjaga },
        ]},
        { cols: 'grid-cols-1', theme: 'cyan', items: [
            { letter: 'p', label: 'Staf KPLP', value: t.staff_kplp },
        ]},
        { cols: 'grid-cols-1', theme: 'blue', items: [
            { letter: 'q', label: 'Petugas Laporan', value: t.petugas_laporan },
        ]}
    ];
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="py-6 sm:py-10 bg-slate-50 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 sm:space-y-8">
                
                <!-- Welcome Banner (Lebih Berwarna & Elegan) -->
                <div class="bg-gradient-to-r from-blue-700 to-indigo-800 rounded-3xl p-6 sm:p-10 shadow-lg shadow-blue-900/20 flex flex-col md:flex-row md:items-center justify-between gap-6 text-white overflow-hidden relative">
                    <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-white opacity-5 blur-3xl pointer-events-none"></div>
                    
                    <div class="relative z-10 text-center md:text-left">
                        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight mb-2">Selamat Datang, {{ $page.props.auth.user.name }}!</h1>
                        <p class="text-blue-100 text-sm sm:text-base max-w-xl">
                            Pantau ringkasan laporan regu pengamanan Lapas Kelas I Palembang hari ini dengan mudah.
                        </p>
                    </div>
                    <Link :href="route('astekpam.create')" class="w-full md:w-auto relative z-10">
                        <Button class="w-full md:w-auto rounded-2xl bg-white hover:bg-slate-100 text-blue-700 font-bold shadow-xl h-14 px-8 text-base transition-transform active:scale-95">
                            <span class="mr-2 text-xl">+</span> Buat Laporan Baru
                        </Button>
                    </Link>
                </div>

                <div v-if="props.latestAstekpam" class="space-y-6 sm:space-y-8">
                    <!-- Title Section -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-2 border-b border-slate-200 pb-4">
                        <h2 class="text-xl sm:text-2xl font-black text-slate-800 flex items-center gap-3">
                            <Activity class="w-7 h-7 text-blue-600 p-1.5 bg-blue-100 rounded-lg" /> 
                            <span>Ringkasan Shift Terakhir</span>
                        </h2>
                        <Link :href="route('astekpam.index')" class="inline-flex items-center justify-center text-sm sm:text-base font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 px-4 py-2 rounded-xl transition-colors">
                            Lihat Semua Riwayat <ArrowRight class="ml-2 w-4 h-4" />
                        </Link>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        
                        <!-- Col Kiri: Info Shift -->
                        <div class="lg:col-span-4 space-y-6">
                            <Card class="rounded-3xl border-0 shadow-md bg-white overflow-hidden ring-1 ring-slate-200/50">
                                <CardContent class="p-0">
                                    <div class="bg-slate-900 text-white p-5 flex justify-between items-center">
                                        <div class="flex items-center gap-3">
                                            <Calendar class="w-5 h-5 text-blue-400" />
                                            <span class="font-bold text-base">{{ formatVal(props.latestAstekpam.tanggal) }}</span>
                                        </div>
                                        <span class="text-sm bg-slate-800 px-4 py-1.5 rounded-full font-bold border border-slate-700 text-blue-300">
                                            {{ formatVal(props.latestAstekpam.pukul) }}
                                        </span>
                                    </div>
                                    <div class="p-6 space-y-6">
                                        <!-- Timeline Serah Terima -->
                                        <div class="relative bg-slate-50 p-5 rounded-2xl border border-slate-100">
                                            <div class="absolute left-[31px] top-10 bottom-10 w-1 bg-slate-200 rounded-full"></div>
                                            <div class="space-y-6">
                                                <!-- Dari (Lama) -->
                                                <div class="flex items-start gap-4">
                                                    <div class="w-10 h-10 rounded-full bg-slate-200 border-4 border-white shadow-sm flex items-center justify-center flex-shrink-0 z-10 text-slate-600">
                                                        <Clock class="w-4 h-4" />
                                                    </div>
                                                    <div class="pt-1">
                                                        <p class="text-xs font-black text-slate-500 uppercase tracking-wider mb-1">Shift Sebelumnya</p>
                                                        <p class="font-extrabold text-slate-800 text-base">Regu {{ formatVal(props.latestAstekpam.dari_rupam) }}</p>
                                                        <p class="text-slate-600 font-medium text-sm">{{ formatVal(props.latestAstekpam.dari_shift) }}</p>
                                                    </div>
                                                </div>
                                                <!-- Ke (Baru) -->
                                                <div class="flex items-start gap-4">
                                                    <div class="w-10 h-10 rounded-full bg-blue-500 border-4 border-white shadow-sm flex items-center justify-center flex-shrink-0 z-10 text-white">
                                                        <Zap class="w-4 h-4" />
                                                    </div>
                                                    <div class="pt-1">
                                                        <p class="text-xs font-black text-blue-600 uppercase tracking-wider mb-1">Shift Saat Ini</p>
                                                        <p class="font-extrabold text-slate-800 text-base">Regu {{ formatVal(props.latestAstekpam.ke_rupam) }}</p>
                                                        <p class="text-slate-600 font-medium text-sm">{{ formatVal(props.latestAstekpam.ke_shift) }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Dipimpin -->
                                        <div class="bg-indigo-50/50 p-5 rounded-2xl border border-indigo-100 flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center">
                                                <Users class="w-6 h-6" />
                                            </div>
                                            <div>
                                                <p class="text-xs font-bold text-indigo-500 uppercase tracking-wider mb-1">Dipimpin Oleh</p>
                                                <p class="font-bold text-slate-800 text-base">{{ formatVal(props.latestAstekpam.pimpinan) }}</p>
                                            </div>
                                        </div>

                                        <!-- Foto Laporan -->
                                        <div v-if="props.latestAstekpam.foto_laporan" class="space-y-3">
                                            <p class="text-sm font-bold text-slate-600 flex items-center gap-2">
                                                <ImageIcon class="w-5 h-5 text-slate-400" /> Foto Bukti Laporan
                                            </p>
                                            <a :href="`/storage/${props.latestAstekpam.foto_laporan}`" target="_blank" class="block w-full rounded-2xl overflow-hidden border-2 border-slate-200 bg-slate-100 relative group">
                                                <img :src="`/storage/${props.latestAstekpam.foto_laporan}`" alt="Foto Laporan" class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-500" />
                                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                                    <span class="text-white font-bold bg-black/50 px-4 py-2 rounded-full text-sm">Lihat Penuh</span>
                                                </div>
                                            </a>
                                        </div>

                                        <Link :href="route('astekpam.show', props.latestAstekpam.id)" class="block w-full">
                                            <Button variant="outline" class="w-full rounded-2xl border-slate-300 text-slate-800 font-extrabold hover:bg-slate-100 text-sm h-14">
                                                Buka Detail Laporan
                                            </Button>
                                        </Link>
                                    </div>
                                </CardContent>
                            </Card>
                        </div>

                        <!-- Col Kanan: Data Summary -->
                        <div class="lg:col-span-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <!-- Card WBP -->
                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-3xl p-6 shadow-sm flex items-center gap-5">
                                <div class="w-16 h-16 rounded-2xl bg-blue-600 text-white flex items-center justify-center flex-shrink-0 shadow-lg shadow-blue-600/30">
                                    <Users class="w-8 h-8" />
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-blue-700 uppercase tracking-wide">Total WBP</p>
                                    <h3 class="text-4xl font-black text-slate-900 mt-1">{{ formatVal(props.latestAstekpam.total_wbp) }}</h3>
                                </div>
                            </div>

                            <!-- Card Narapidana -->
                            <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 border border-indigo-200 rounded-3xl p-6 shadow-sm flex items-center gap-5">
                                <div class="w-16 h-16 rounded-2xl bg-indigo-600 text-white flex items-center justify-center flex-shrink-0 shadow-lg shadow-indigo-600/30">
                                    <Users class="w-8 h-8" />
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-indigo-700 uppercase tracking-wide">Narapidana</p>
                                    <div class="flex items-baseline gap-2 mt-1 flex-wrap">
                                        <h3 class="text-4xl font-black text-slate-900">{{ formatVal(props.latestAstekpam.narapidana) }}</h3>
                                        <span class="text-sm text-indigo-600 font-bold bg-indigo-200/50 px-3 py-1 rounded-lg">Kapasitas {{ formatVal(props.latestAstekpam.kapasitas) }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Distribusi Blok -->
                            <div class="md:col-span-2 bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
                                <p class="text-sm font-extrabold text-slate-800 mb-5 flex items-center gap-2 uppercase tracking-wider">
                                    <MapPin class="w-5 h-5 text-emerald-500"/> Distribusi Penghuni
                                </p>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <div v-for="b in ['blok_a', 'blok_b', 'dapur', 'klinik']" :key="b" class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-center hover:bg-slate-100 transition-colors">
                                        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">{{ b.replace('_', ' ') }}</p>
                                        <p class="text-2xl font-black text-slate-800">{{ formatVal(props.latestAstekpam[b]) }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Kondisi RS & Luar -->
                            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
                                <p class="text-sm font-extrabold mb-5 flex items-center gap-2 uppercase tracking-wider text-slate-800">
                                    <ShieldAlert class="w-5 h-5 text-rose-500"/> Kondisi Luar Lapas
                                </p>
                                <div class="space-y-4 text-base font-bold text-slate-600">
                                    <div class="flex justify-between items-center bg-rose-50/50 p-3 rounded-xl border border-rose-100">
                                        <span>Rawat Inap RS</span>
                                        <span class="text-xl text-rose-700 bg-rose-200/50 px-3 py-1 rounded-lg">{{ parseJsonCount(props.latestAstekpam.rawat_inap_items) }} Org</span>
                                    </div>
                                    <div class="flex justify-between items-center bg-amber-50/50 p-3 rounded-xl border border-amber-100">
                                        <span>Berobat RS</span>
                                        <span class="text-xl text-amber-700 bg-amber-200/50 px-3 py-1 rounded-lg">{{ parseJsonCount(props.latestAstekpam.berobat_items) }} Org</span>
                                    </div>
                                    <div class="flex justify-between items-center bg-slate-50 p-3 rounded-xl border border-slate-200">
                                        <span>Bon Luar</span>
                                        <span class="text-xl text-slate-800 bg-slate-200 px-3 py-1 rounded-lg">{{ formatVal(props.latestAstekpam.luar_lapas) }} Org</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Kehadiran Personil -->
                            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm flex flex-col justify-center">
                                <p class="text-sm font-extrabold mb-5 flex items-center gap-2 uppercase tracking-wider text-slate-800">
                                    <Zap class="w-5 h-5 text-amber-500"/> Kehadiran Personil
                                </p>
                                <div class="space-y-5">
                                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                                        <div class="flex justify-between items-center mb-3">
                                            <span class="font-extrabold text-slate-600 uppercase text-sm">Regu Pengamanan</span>
                                            <span class="font-black text-slate-900 text-lg">{{ formatVal(props.latestAstekpam.rupam_hadir) }} / {{ formatVal(props.latestAstekpam.rupam_jumlah) }}</span>
                                        </div>
                                        <div class="w-full bg-slate-200 rounded-full h-3 overflow-hidden">
                                            <div class="bg-emerald-500 h-3 rounded-full" style="width: 100%"></div>
                                        </div>
                                    </div>
                                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                                        <div class="flex justify-between items-center mb-3">
                                            <span class="font-extrabold text-slate-600 uppercase text-sm">Satgas P2U</span>
                                            <span class="font-black text-slate-900 text-lg">{{ formatVal(props.latestAstekpam.p2u_hadir) }} / {{ formatVal(props.latestAstekpam.p2u_jumlah) }}</span>
                                        </div>
                                        <div class="w-full bg-slate-200 rounded-full h-3 overflow-hidden">
                                            <div class="bg-emerald-500 h-3 rounded-full" style="width: 100%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Pembagian Tugas (Colorful) -->
                    <Card class="rounded-3xl border border-slate-200 shadow-md bg-white overflow-hidden mt-8">
                        <div class="px-6 py-5 border-b border-slate-200 flex items-center gap-3 font-black text-lg text-slate-800 bg-slate-50">
                            <div class="bg-blue-100 p-2 rounded-xl text-blue-600">
                                <ClipboardList class="w-6 h-6" />
                            </div>
                            <span>PEMBAGIAN TUGAS JAGA PERSONIL</span>
                        </div>
                        <CardContent class="p-6 sm:p-8">
                            <div class="space-y-5">
                                <div v-for="(row, index) in taskRows" :key="index" :class="['grid gap-4 sm:gap-6', row.cols]">
                                    
                                    <!-- Kotak Tugas per Item -->
                                    <div 
                                        v-for="task in row.items" 
                                        :key="task.letter" 
                                        :class="[
                                            'flex items-center gap-4 p-4 sm:p-5 rounded-2xl border-2 transition-all hover:-translate-y-1 hover:shadow-lg', 
                                            getThemeClasses(row.theme).card
                                        ]"
                                    >
                                        <!-- Bulatan Abjad -->
                                        <div :class="[
                                            'w-12 h-12 sm:w-14 sm:h-14 rounded-xl text-lg sm:text-xl font-black flex items-center justify-center uppercase flex-shrink-0',
                                            getThemeClasses(row.theme).badge
                                        ]">
                                            {{ task.letter }}
                                        </div>
                                        
                                        <!-- Nama Tugas & Petugas -->
                                        <div class="min-w-0 flex-1">
                                            <p :class="[
                                                'text-xs sm:text-sm font-bold uppercase tracking-widest mb-1 truncate',
                                                getThemeClasses(row.theme).textLabel
                                            ]">
                                                {{ task.label }}
                                            </p>
                                            <p :class="[
                                                'text-base sm:text-lg font-black truncate',
                                                getThemeClasses(row.theme).textVal
                                            ]">
                                                {{ formatVal(task.value) }}
                                            </p>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Empty State -->
                <div v-else class="bg-white border-2 border-dashed border-slate-300 rounded-[2rem] p-10 sm:p-20 text-center shadow-sm">
                    <div class="bg-slate-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6">
                        <FileText class="w-12 h-12 text-slate-400" />
                    </div>
                    <h3 class="text-xl sm:text-2xl font-black text-slate-800">Belum Ada Data Laporan</h3>
                    <p class="text-sm sm:text-base text-slate-500 mt-3 max-w-md mx-auto">
                        Sistem belum menemukan data laporan serah terima regu pengamanan. Silakan buat laporan pertama Anda hari ini.
                    </p>
                    <Link :href="route('astekpam.create')">
                        <Button class="mt-8 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white text-base font-bold h-14 px-8 shadow-lg shadow-blue-200 transition-transform active:scale-95">
                            Buat Laporan Sekarang
                        </Button>
                    </Link>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>