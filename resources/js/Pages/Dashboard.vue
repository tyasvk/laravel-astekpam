<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Card, CardContent } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { 
    Users, ShieldAlert, Zap, Clock, Calendar, ArrowRight, Activity, MapPin, ClipboardList
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

// Pengelompokan Pembagian Tugas berdasarkan Baris yang diminta
const taskRows = computed(() => {
    if (!props.latestAstekpam) return [];
    const t = props.latestAstekpam.tugas || props.latestAstekpam || {};
    
    return [
        // Baris 1: Karupam, Wakarupam
        { cols: 'sm:grid-cols-2', items: [
            { letter: 'a', label: 'Ka. Rupam', value: t.ka_rupam },
            { letter: 'b', label: 'Wakarupam', value: t.wakarupam },
        ]},
        // Baris 2: Kasatgas, Wakasatgas
        { cols: 'sm:grid-cols-2', items: [
            { letter: 'c', label: 'Kasatgas P2U', value: t.kasatgas_p2u },
            { letter: 'd', label: 'Wakasatgas P2U', value: t.wakasatgas_p2u },
        ]},
        // Baris 3: Blok A, Blok B
        { cols: 'sm:grid-cols-2', items: [
            { letter: 'e', label: 'Blok A', value: t.blok_a },
            { letter: 'f', label: 'Blok B', value: t.blok_b },
        ]},
        // Baris 4: Menara 1 s.d 4 (4 Kolom di desktop, 2 kolom di HP)
        { cols: 'grid-cols-2 sm:grid-cols-4', items: [
            { letter: 'g', label: 'Menara 1', value: t.menara_1 },
            { letter: 'h', label: 'Menara 2', value: t.menara_2 },
            { letter: 'i', label: 'Menara 3', value: t.menara_3 },
            { letter: 'j', label: 'Menara 4', value: t.menara_4 },
        ]},
        // Baris 5: Jaga RS, Piket Dapur
        { cols: 'sm:grid-cols-2', items: [
            { letter: 'k', label: 'Jaga RS', value: t.jaga_rs },
            { letter: 'l', label: 'Piket Dapur', value: t.piket_dapur },
        ]},
        // Baris 6: Pengawas Piket, Perwira Piket
        { cols: 'sm:grid-cols-2', items: [
            { letter: 'm', label: 'Pengawas Piket', value: t.perwira_kontrol },
            { letter: 'n', label: 'Perwira Piket', value: t.perwira_piket },
        ]},
        // Baris 7: Banjaga
        { cols: 'grid-cols-1', items: [
            { letter: 'o', label: 'Banjaga', value: t.banjaga },
        ]},
        // Baris 8: Staf KPLP
        { cols: 'grid-cols-1', items: [
            { letter: 'p', label: 'Staf KPLP', value: t.staff_kplp },
        ]},
        // Baris 9: Petugas Laporan
        { cols: 'grid-cols-1', items: [
            { letter: 'q', label: 'Petugas Laporan', value: t.petugas_laporan },
        ]}
    ];
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="py-8 bg-zinc-50/30 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                
                <div class="bg-white rounded-2xl p-6 md:p-8 border border-zinc-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-extrabold text-zinc-900 tracking-tight">Selamat Datang, Admin</h1>
                        <p class="text-zinc-500 text-sm mt-1">Pantau ringkasan laporan regu pengamanan Lapas Kelas I Palembang hari ini.</p>
                    </div>
                    <Link :href="route('astekpam.create')" class="w-full sm:w-auto">
                        <Button class="w-full sm:w-auto rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold shadow-md shadow-blue-200 h-11 px-5">
                            + Buat Laporan Baru
                        </Button>
                    </Link>
                </div>

                <div v-if="props.latestAstekpam" class="space-y-6">
                    <div class="flex items-center justify-between pt-2">
                        <h2 class="text-lg font-bold text-zinc-800 flex items-center gap-2">
                            <Activity class="w-5 h-5 text-blue-600" /> Ringkasan Shift Terakhir
                        </h2>
                        <Link :href="route('astekpam.index')" class="text-sm font-semibold text-blue-600 hover:underline">
                            Lihat Semua
                        </Link>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        <div class="lg:col-span-4 space-y-6">
                            <Card class="rounded-2xl border-zinc-200 shadow-sm bg-white overflow-hidden">
                                <CardContent class="p-0">
                                    <div class="bg-zinc-900 text-white p-5 flex justify-between items-center">
                                        <div class="flex items-center gap-3">
                                            <Calendar class="w-5 h-5 text-zinc-400" />
                                            <span class="font-semibold text-sm">{{ formatVal(props.latestAstekpam.tanggal) }}</span>
                                        </div>
                                        <span class="text-xs bg-zinc-800 px-3 py-1.5 rounded-lg font-medium border border-zinc-700">
                                            {{ formatVal(props.latestAstekpam.pukul) }}
                                        </span>
                                    </div>
                                    <div class="p-6 space-y-6">
                                        <div class="relative">
                                            <div class="absolute left-[11px] top-6 bottom-6 w-0.5 bg-zinc-100"></div>
                                            <div class="space-y-4">
                                                <div class="flex items-start gap-4">
                                                    <div class="w-6 h-6 rounded-full bg-zinc-100 border-2 border-white shadow-sm flex-shrink-0 z-10"></div>
                                                    <div>
                                                        <p class="text-xs font-bold text-zinc-400 uppercase">Dari (Lama)</p>
                                                        <p class="font-bold text-zinc-800 text-sm">{{ formatVal(props.latestAstekpam.dari_rupam) }} <span class="text-zinc-500 font-normal text-xs">({{ formatVal(props.latestAstekpam.dari_shift) }})</span></p>
                                                    </div>
                                                </div>
                                                <div class="flex items-start gap-4">
                                                    <div class="w-6 h-6 rounded-full bg-blue-100 border-2 border-white shadow-sm flex-shrink-0 z-10"></div>
                                                    <div>
                                                        <p class="text-xs font-bold text-blue-400 uppercase">Ke (Baru)</p>
                                                        <p class="font-bold text-zinc-800 text-sm">{{ formatVal(props.latestAstekpam.ke_rupam) }} <span class="text-zinc-500 font-normal text-xs">({{ formatVal(props.latestAstekpam.ke_shift) }})</span></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bg-zinc-50 p-4 rounded-xl border border-zinc-100">
                                            <p class="text-xs font-bold text-zinc-400 uppercase mb-1">Dipimpin</p>
                                            <p class="font-semibold text-zinc-800 text-sm">{{ formatVal(props.latestAstekpam.pimpinan) }}</p>
                                        </div>
                                        <Link :href="route('astekpam.show', props.latestAstekpam.id)" class="block w-full">
                                            <Button variant="outline" class="w-full rounded-xl border-zinc-200 text-zinc-700 font-bold hover:bg-zinc-50 text-xs h-10">Lihat Detail Laporan</Button>
                                        </Link>
                                    </div>
                                </CardContent>
                            </Card>
                        </div>

                        <div class="lg:col-span-8 grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="col-span-2 bg-white border border-zinc-200 rounded-2xl p-5 shadow-sm flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                                    <Users class="w-6 h-6" />
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-zinc-500">Total WBP</p>
                                    <h3 class="text-2xl font-black text-zinc-900 mt-0.5">{{ formatVal(props.latestAstekpam.total_wbp) }}</h3>
                                </div>
                            </div>

                            <div class="col-span-2 bg-white border border-zinc-200 rounded-2xl p-5 shadow-sm flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0">
                                    <Users class="w-6 h-6" />
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-zinc-500">Narapidana</p>
                                    <div class="flex items-baseline gap-1.5 mt-0.5">
                                        <h3 class="text-2xl font-black text-zinc-900">{{ formatVal(props.latestAstekpam.narapidana) }}</h3>
                                        <span class="text-[10px] text-zinc-400 font-medium">/ Kapasitas {{ formatVal(props.latestAstekpam.kapasitas) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-span-2 md:col-span-4 bg-white border border-zinc-200 rounded-2xl p-5 shadow-sm">
                                <p class="text-xs font-bold text-zinc-800 mb-3 flex items-center gap-2 uppercase tracking-wider text-zinc-400"><MapPin class="w-3.5 h-3.5"/> Distribusi Penghuni</p>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                    <div v-for="b in ['blok_a', 'blok_b', 'dapur', 'klinik']" :key="b" class="p-3 rounded-xl bg-zinc-50 border border-zinc-100">
                                        <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-tight">{{ b.replace('_', ' ') }}</p>
                                        <p class="text-lg font-bold text-zinc-800 mt-0.5">{{ formatVal(props.latestAstekpam[b]) }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-span-2 bg-white border border-zinc-200 rounded-2xl p-5 shadow-sm">
                                <p class="text-xs font-bold mb-3 flex items-center gap-2 uppercase tracking-wider text-zinc-400"><ShieldAlert class="w-3.5 h-3.5 text-rose-500"/> Kondisi Luar Lapas</p>
                                <div class="space-y-2.5 text-xs font-medium">
                                    <div class="flex justify-between items-center border-b border-zinc-100 pb-2">
                                        <span class="text-zinc-600">Rawat Inap RS</span>
                                        <span class="font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-md">{{ parseJsonCount(props.latestAstekpam.rawat_inap_items) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center border-b border-zinc-100 pb-2">
                                        <span class="text-zinc-600">Berobat RS</span>
                                        <span class="font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-md">{{ parseJsonCount(props.latestAstekpam.berobat_items) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-zinc-600">Bon Luar</span>
                                        <span class="font-bold text-zinc-800 bg-zinc-100 px-2 py-0.5 rounded-md">{{ formatVal(props.latestAstekpam.luar_lapas) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-span-2 bg-white border border-zinc-200 rounded-2xl p-5 shadow-sm">
                                <p class="text-xs font-bold mb-3 flex items-center gap-2 uppercase tracking-wider text-zinc-400"><Zap class="w-3.5 h-3.5 text-amber-500"/> Kehadiran Personil</p>
                                <div class="space-y-3.5">
                                    <div class="bg-zinc-50 rounded-xl p-2.5 border border-zinc-100">
                                        <div class="flex justify-between items-center mb-1 text-xs">
                                            <span class="font-bold text-zinc-500 uppercase text-[10px]">Rupam</span>
                                            <span class="font-black text-zinc-800">{{ formatVal(props.latestAstekpam.rupam_hadir) }}/{{ formatVal(props.latestAstekpam.rupam_jumlah) }}</span>
                                        </div>
                                        <div class="w-full bg-zinc-200 rounded-full h-1.5"><div class="bg-emerald-500 h-1.5 rounded-full" style="width: 100%"></div></div>
                                    </div>
                                    <div class="bg-zinc-50 rounded-xl p-2.5 border border-zinc-100">
                                        <div class="flex justify-between items-center mb-1 text-xs">
                                            <span class="font-bold text-zinc-500 uppercase text-[10px]">Satgas P2U</span>
                                            <span class="font-black text-zinc-800">{{ formatVal(props.latestAstekpam.p2u_hadir) }}/{{ formatVal(props.latestAstekpam.p2u_jumlah) }}</span>
                                        </div>
                                        <div class="w-full bg-zinc-200 rounded-full h-1.5"><div class="bg-emerald-500 h-1.5 rounded-full" style="width: 100%"></div></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <Card class="rounded-2xl border border-zinc-200 shadow-sm bg-white overflow-hidden">
                        <div class="px-6 py-4 border-b border-zinc-100 flex items-center gap-2 font-bold text-sm text-zinc-800 bg-zinc-50">
                            <ClipboardList class="w-4 h-4 text-blue-600" />
                            <span>PEMBAGIAN TUGAS JAGA (a s.d q)</span>
                        </div>
                        <CardContent class="p-6">
                            <div class="space-y-4">
                                <div v-for="(row, index) in taskRows" :key="index" :class="['grid gap-4 grid-cols-1', row.cols]">
                                    <div 
                                        v-for="task in row.items" 
                                        :key="task.letter" 
                                        class="flex items-center gap-3 bg-zinc-50/60 p-3.5 rounded-xl border border-zinc-100 hover:bg-zinc-50 transition-colors"
                                    >
                                        <div class="w-6 h-6 rounded-full bg-zinc-200 text-zinc-700 text-xs font-black flex items-center justify-center uppercase flex-shrink-0 shadow-sm">
                                            {{ task.letter }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-wide truncate">{{ task.label }}</p>
                                            <p class="text-sm font-bold text-zinc-800 mt-0.5 truncate">{{ formatVal(task.value) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <div v-else class="bg-white border border-zinc-200 rounded-3xl p-16 text-center shadow-sm">
                    <FileText class="w-10 h-10 text-zinc-300 mx-auto mb-4" />
                    <h3 class="text-base font-bold text-zinc-900">Belum Ada Data Laporan</h3>
                    <p class="text-xs text-zinc-500 mt-2 max-w-sm mx-auto">Sistem belum menemukan data laporan serah terima regu pengamanan. Silakan buat laporan pertama Anda.</p>
                    <Link :href="route('astekpam.create')">
                        <Button class="mt-6 rounded-xl bg-zinc-900 text-white text-xs font-bold h-11 px-5">Buat Laporan Sekarang</Button>
                    </Link>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>