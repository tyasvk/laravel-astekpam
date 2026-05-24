<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

// Menerima data asli dari database melalui Controller Laravel
const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            total_laporan: 0,
            menunggu_validasi: 0,
            regu_aktif: 'Belum Ada'
        })
    },
    recent_reports: {
        type: Array,
        default: () => []
    }
});

// Fungsi pembantu untuk memformat tanggal
const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'short', // Disingkat agar hemat ruang di layar HP (misal: "24 Mei 2026")
        year: 'numeric'
    }).format(date);
};
</script>

<template>
    <Head title="Dasbor SPARTA" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
                <div>
                    <h2 class="font-bold text-xl sm:text-2xl text-slate-900 tracking-tight leading-tight">
                        Dasbor Utama
                    </h2>
                    <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5 leading-snug">
                        Sistem Pelaporan Astekpam Responsif, Terpadu, dan Akuntabel
                    </p>
                </div>
                <div class="inline-flex w-fit items-center gap-2 px-3 py-1.5 rounded-full bg-slate-100 border border-slate-200">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse shrink-0"></span>
                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-slate-700">Smart Governance</span>
                </div>
            </div>
        </template>

        <div class="py-4 sm:py-8 font-sans">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4 sm:space-y-6">
                
                <div class="bg-slate-900 rounded-2xl p-5 sm:p-8 text-white shadow-md relative overflow-hidden flex flex-col lg:flex-row items-start lg:items-center justify-between gap-5 sm:gap-6 border border-slate-800">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-amber-500 opacity-10 rounded-full blur-2xl"></div>
                    
                    <div class="relative z-10 w-full lg:max-w-2xl">
                        <span class="text-amber-500 text-[10px] font-bold uppercase tracking-widest mb-2 block">
                            Kemenimipas RI
                        </span>
                        <h3 class="text-xl sm:text-2xl font-bold tracking-tight mb-2">
                            Selamat Datang, {{ $page.props.auth.user.name }}
                        </h3>
                        <p class="text-slate-400 text-xs sm:text-sm leading-relaxed">
                            Kelola arsip dan input dokumen digitalisasi Apel Serah Terima Kepala Regu Pengamanan (Astekpam) secara real-time dan terpusat.
                        </p>
                    </div>

                    <div class="relative z-10 w-full lg:w-auto shrink-0 mt-2 lg:mt-0">
                        <Link :href="route('astekpam.create')" class="block w-full">
                            <button class="w-full lg:w-auto bg-amber-500 hover:bg-amber-600 text-slate-900 font-bold text-sm px-6 py-3.5 sm:py-3 rounded-xl shadow-lg transition-colors flex items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                                Buat Laporan Baru
                            </button>
                        </Link>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-6">
                    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-3.5 sm:gap-4">
                        <div class="p-2.5 sm:p-3 bg-slate-100 text-slate-700 rounded-xl shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="sm:w-6 sm:h-6"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-slate-500 truncate">Total Laporan</p>
                            <h4 class="text-xl sm:text-2xl font-black text-slate-900 leading-none mt-1 truncate">{{ stats.total_laporan }} <span class="text-xs sm:text-sm font-medium text-slate-400">Berkas</span></h4>
                        </div>
                    </div>

                    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-3.5 sm:gap-4">
                        <div class="p-2.5 sm:p-3 bg-amber-50 text-amber-600 rounded-xl shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="sm:w-6 sm:h-6"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-slate-500 truncate">Belum Divalidasi</p>
                            <h4 class="text-xl sm:text-2xl font-black text-amber-600 leading-none mt-1 truncate">{{ stats.menunggu_validasi }} <span class="text-xs sm:text-sm font-medium text-amber-600/60">Berkas</span></h4>
                        </div>
                    </div>

                    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-3.5 sm:gap-4 sm:col-span-2 lg:col-span-1">
                        <div class="p-2.5 sm:p-3 bg-emerald-50 text-emerald-600 rounded-xl shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="sm:w-6 sm:h-6"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><path d="m9 12 2 2 4-4"/></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-slate-500 truncate">Aktivitas Terakhir</p>
                            <h4 class="text-lg sm:text-xl font-black text-emerald-600 leading-tight mt-1 truncate">{{ stats.regu_aktif || 'Belum Ada' }}</h4>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
                    <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <h4 class="text-sm sm:text-base font-bold text-slate-900">Pemantauan Aktivitas Astekpam</h4>
                            <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5">Daftar laporan serah terima jaga terbaru.</p>
                        </div>
                        <Link :href="route('astekpam.index')" class="w-full sm:w-auto">
                            <button class="w-full sm:w-auto text-xs sm:text-sm font-semibold text-slate-600 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 px-4 py-2 sm:py-2.5 rounded-lg border border-slate-200 transition-colors flex items-center justify-center gap-1.5">
                                Lihat Semua Arsip
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                            </button>
                        </Link>
                    </div>

                    <div class="overflow-x-auto w-full pb-1">
                        <table class="w-full text-left text-sm whitespace-nowrap min-w-max">
                            <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] sm:text-[11px] font-bold tracking-wider">
                                <tr>
                                    <th class="px-4 sm:px-6 py-3 sm:py-4 border-b border-slate-200">No. Berkas</th>
                                    <th class="px-4 sm:px-6 py-3 sm:py-4 border-b border-slate-200">Waktu Pelaksanaan</th>
                                    <th class="px-4 sm:px-6 py-3 sm:py-4 border-b border-slate-200">Regu Jaga</th>
                                    <th class="px-4 sm:px-6 py-3 sm:py-4 border-b border-slate-200">Nama Karupam</th>
                                    <th class="px-4 sm:px-6 py-3 sm:py-4 border-b border-slate-200 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700 text-xs sm:text-sm">
                                
                                <template v-if="recent_reports && recent_reports.length > 0">
                                    <tr v-for="item in recent_reports" :key="item.id" class="hover:bg-slate-50 transition-colors">
                                        <td class="px-4 sm:px-6 py-3 sm:py-4 font-mono font-bold text-slate-500">
                                            #{{ item.id }}
                                        </td>
                                        <td class="px-4 sm:px-6 py-3 sm:py-4">
                                            <div class="font-semibold text-slate-900">{{ formatDate(item.tanggal || item.created_at) }}</div>
                                            <div class="text-[10px] sm:text-xs text-slate-500 mt-0.5">{{ item.waktu || '-' }} WIB</div>
                                        </td>
                                        <td class="px-4 sm:px-6 py-3 sm:py-4 font-medium">
                                            {{ item.regu || '-' }}
                                        </td>
                                        <td class="px-4 sm:px-6 py-3 sm:py-4 font-medium">
                                            {{ item.user?.name || item.karupam_name || '-' }}
                                        </td>
                                        <td class="px-4 sm:px-6 py-3 sm:py-4 text-center">
                                            <span 
                                                class="inline-flex items-center px-2 py-1 rounded-full text-[9px] sm:text-[10px] uppercase font-bold tracking-wider"
                                                :class="item.status === 'validasi' || item.status === 'tervalidasi' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'"
                                            >
                                                {{ item.status || 'Pending' }}
                                            </span>
                                        </td>
                                    </tr>
                                </template>
                                
                                <template v-else>
                                    <tr>
                                        <td colspan="5" class="px-4 py-12 sm:py-16 text-center">
                                            <div class="inline-flex flex-col items-center justify-center text-slate-400">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" class="mb-3 sm:mb-4"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/><path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/><path d="M8 18h.01"/><path d="M12 18h.01"/><path d="M16 18h.01"/></svg>
                                                <span class="text-xs sm:text-sm font-medium text-slate-500">Belum ada laporan Astekpam.</span>
                                            </div>
                                        </td>
                                    </tr>
                                </template>

                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>