<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    // Asumsi data yang dikirim dari controller berbentuk pagination atau array
    astekpams: {
        type: [Object, Array],
        default: () => ({ data: [] })
    }
});

// Normalisasi data agar tidak error jika controller mengirim array biasa (bukan paginate)
const astekpamData = computed(() => {
    return Array.isArray(props.astekpams) ? props.astekpams : (props.astekpams.data || []);
});

const searchQuery = ref('');
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Riwayat Laporan Astekpam" />

        <div class="py-6 md:py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                    <div>
                        <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">Laporan Astekpam</h2>
                        <p class="text-sm text-slate-500 mt-1 font-medium">Kelola dan pantau riwayat apel serah terima regu pengamanan.</p>
                    </div>
                    
                    <Link
                        :href="route('astekpam.create')"
                        class="inline-flex items-center justify-center px-4 py-2.5 bg-[#0f172a] hover:bg-[#1e293b] text-[#f59e0b] font-bold text-sm rounded-xl shadow-lg shadow-[#0f172a]/10 transition-all duration-200 transform active:scale-95 gap-2 w-full sm:w-auto"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Buat Laporan Baru
                    </Link>
                </div>

                <div class="bg-white p-3 rounded-2xl shadow-sm border border-slate-100 mb-6 flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-grow">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </div>
                        <input
                            type="text"
                            v-model="searchQuery"
                            placeholder="Cari berdasarkan pelapor atau regu..."
                            class="block w-full pl-10 pr-3 py-2.5 border-transparent bg-slate-50 rounded-xl text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#f59e0b]/50 focus:bg-white focus:border-transparent transition-all"
                        >
                    </div>
                    <button class="inline-flex items-center justify-center px-4 py-2.5 border border-slate-200 bg-white text-slate-600 font-semibold text-sm rounded-xl hover:bg-slate-50 hover:text-slate-900 transition-colors gap-2 w-full sm:w-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                        Filter Data
                    </button>
                </div>

                <div v-if="astekpamData.length === 0" class="bg-white rounded-3xl shadow-sm border border-slate-100 p-12 text-center flex flex-col items-center justify-center">
                    <div class="w-16 h-16 bg-amber-50 rounded-full flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-[#f59e0b]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">Belum Ada Laporan</h3>
                    <p class="text-slate-500 text-sm mt-1 max-w-sm mx-auto">Riwayat laporan Astekpam masih kosong. Silakan klik tombol "Buat Laporan Baru" untuk memulai pencatatan apel.</p>
                </div>

                <div v-else>
                    <div class="block md:hidden space-y-4">
                        <div v-for="item in astekpamData" :key="item.id" class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 relative overflow-hidden group">
                            
                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-[#f59e0b] to-[#d97706]"></div>
                            
                            <div class="flex justify-between items-start mb-4 pl-2">
                                <div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-700 mb-2 border border-slate-200">
                                        {{ item.regu_pengamanan || 'Regu Pengamanan' }}
                                    </span>
                                    <h3 class="font-bold text-slate-900 text-sm flex items-center gap-1.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                        {{ item.tanggal || item.created_at?.split('T')[0] || 'Tanpa Tanggal' }}
                                    </h3>
                                </div>
                                <span class="px-2 py-1 rounded-md text-[10px] font-bold tracking-wide shadow-sm border" 
                                      :class="(item.status === 'Selesai' || item.status === 'Terverifikasi') ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200'">
                                    {{ item.status || 'Terkirim' }}
                                </span>
                            </div>
                            
                            <div class="flex items-center gap-2 text-xs text-slate-500 mb-5 pl-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                <span>Pelapor: <strong class="text-slate-800">{{ item.user?.name || item.pelapor || '-' }}</strong></span>
                            </div>

                            <Link :href="route('astekpam.show', item.id)" class="block w-full text-center px-4 py-2.5 bg-slate-50 hover:bg-[#0f172a] hover:text-[#f59e0b] text-slate-700 font-bold text-xs rounded-xl transition-all duration-200 border border-slate-200 hover:border-transparent">
                                Buka Detail Laporan
                            </Link>
                        </div>
                    </div>

                    <div class="hidden md:block bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] uppercase tracking-widest text-slate-500 font-bold">
                                        <th class="px-6 py-4">Informasi Laporan</th>
                                        <th class="px-6 py-4">Regu</th>
                                        <th class="px-6 py-4">Petugas Pelapor</th>
                                        <th class="px-6 py-4">Status</th>
                                        <th class="px-6 py-4 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <tr v-for="item in astekpamData" :key="item.id" class="hover:bg-slate-50/50 transition-colors group">
                                        
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2.5 text-sm font-semibold text-slate-900">
                                                <div class="p-2 bg-slate-100 rounded-lg text-slate-500 group-hover:bg-[#f59e0b]/10 group-hover:text-[#f59e0b] transition-colors">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                                </div>
                                                {{ item.tanggal || item.created_at?.split('T')[0] || '-' }}
                                            </div>
                                        </td>

                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider bg-slate-100 text-slate-600 border border-slate-200">
                                                {{ item.regu_pengamanan || '-' }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 text-sm font-medium text-slate-700">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#1e293b] to-[#0f172a] text-[#f59e0b] flex items-center justify-center font-bold text-xs shadow-sm">
                                                    {{ (item.user?.name || item.pelapor || 'U').charAt(0) }}
                                                </div>
                                                {{ item.user?.name || item.pelapor || '-' }}
                                            </div>
                                        </td>

                                        <td class="px-6 py-4">
                                            <span class="px-3 py-1.5 rounded-lg text-xs font-bold border shadow-sm" 
                                                  :class="(item.status === 'Selesai' || item.status === 'Terverifikasi') ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200'">
                                                {{ item.status || 'Terkirim' }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 text-right">
                                            <Link :href="route('astekpam.show', item.id)" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-slate-200 text-slate-700 hover:bg-[#0f172a] hover:text-[#f59e0b] hover:border-transparent font-bold text-xs rounded-xl shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-[#f59e0b]/50">
                                                Detail
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div v-if="props.astekpams.links" class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex items-center justify-between">
                            <div class="text-xs text-slate-500 font-medium">
                                Menampilkan <span class="font-bold text-slate-800">{{ props.astekpams.from || 0 }}</span> sampai <span class="font-bold text-slate-800">{{ props.astekpams.to || 0 }}</span> dari total <span class="font-bold text-slate-800">{{ props.astekpams.total || 0 }}</span> laporan
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>