<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { 
    Plus, 
    Eye, 
    Calendar,
    Clock,
    FileText,
    Users,
    Shield,
    Activity,
    ChevronRight,
    Search,
    Filter
} from 'lucide-vue-next';

const props = defineProps({
    astekpams: {
        type: Array,
        default: () => [],
    },
});

// Helper untuk warna shift
const getShiftColor = (shift) => {
    const colors = {
        'Pagi': 'bg-emerald-100 text-emerald-700 border-emerald-200',
        'Siang': 'bg-orange-100 text-orange-700 border-orange-200',
        'Malam': 'bg-indigo-100 text-indigo-700 border-indigo-200',
    };
    return colors[shift] || 'bg-gray-100 text-gray-700 border-gray-200';
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric', month: 'short', year: 'numeric'
    });
};
</script>

<template>
    <Head title="Riwayat Astekpam" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="font-black text-2xl text-gray-800 leading-tight tracking-tight">
                        Riwayat Laporan <span class="text-blue-600">Astekpam</span>
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Pantau dokumentasi keamanan dan ketertiban harian.</p>
                </div>
                <Link
                    :href="route('astekpam.create')"
                    class="inline-flex items-center justify-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-blue-200 transition-all active:scale-95"
                >
                    <Plus class="w-5 h-5 mr-2" />
                    Buat Laporan
                </Link>
            </div>
        </template>

        <div class="py-6 pb-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-4 rounded-2xl text-white shadow-sm">
                        <Activity class="w-6 h-6 mb-2 opacity-80" />
                        <div class="text-2xl font-black">{{ astekpams.length }}</div>
                        <div class="text-xs opacity-80 uppercase font-bold tracking-wider">Total Laporan</div>
                    </div>
                    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
                        <Shield class="w-6 h-6 mb-2 text-emerald-500" />
                        <div class="text-2xl font-black text-gray-800">Aktif</div>
                        <div class="text-xs text-gray-400 uppercase font-bold tracking-wider">Status Keamanan</div>
                    </div>
                    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
                        <Users class="w-6 h-6 mb-2 text-orange-500" />
                        <div class="text-2xl font-black text-gray-800">{{ astekpams[0]?.total_wbp || 0 }}</div>
                        <div class="text-xs text-gray-400 uppercase font-bold tracking-wider">WBP Terakhir</div>
                    </div>
                    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
                        <Calendar class="w-6 h-6 mb-2 text-purple-500" />
                        <div class="text-sm font-black text-gray-800">Hari Ini</div>
                        <div class="text-xs text-gray-400 uppercase font-bold tracking-wider">{{ new Date().toLocaleDateString('id-ID', {weekday: 'long'}) }}</div>
                    </div>
                </div>

                <div class="bg-white shadow-xl shadow-gray-100 rounded-3xl overflow-hidden border border-gray-100">
                    
                    <div class="p-4 border-b border-gray-50 flex flex-col md:flex-row gap-4 justify-between bg-gray-50/50">
                        <div class="relative w-full md:w-64">
                            <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                            <input type="text" placeholder="Cari laporan..." class="w-full pl-10 pr-4 py-2 border-none rounded-xl bg-white focus:ring-2 focus:ring-blue-500 text-sm shadow-sm" />
                        </div>
                        <button class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 shadow-sm">
                            <Filter class="w-4 h-4 mr-2" /> Filter
                        </button>
                    </div>

                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-gray-400 uppercase bg-white">
                                <tr>
                                    <th class="px-6 py-4 font-bold tracking-widest">Detail Waktu</th>
                                    <th class="px-6 py-4 font-bold tracking-widest">Shift / Rupam</th>
                                    <th class="px-6 py-4 font-bold tracking-widest">Pimpinan</th>
                                    <th class="px-6 py-4 font-bold tracking-widest text-center">WBP</th>
                                    <th class="px-6 py-4 font-bold tracking-widest text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr v-for="item in astekpams" :key="item.id" class="hover:bg-blue-50/30 transition-colors group">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-bold text-gray-800">{{ formatDate(item.tanggal) }}</div>
                                        <div class="text-xs text-gray-400 flex items-center">
                                            <Clock class="w-3 h-3 mr-1" /> {{ item.pukul }} WIB
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <span :class="getShiftColor(item.dari_shift)" class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase border">
                                                {{ item.dari_shift }}
                                            </span>
                                            <span class="text-gray-600 font-medium capitalize">{{ item.dari_rupam }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-gray-700 font-medium">{{ item.pimpinan }}</div>
                                        <div class="text-[10px] text-gray-400 uppercase tracking-tighter">Penanggung Jawab</div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="inline-flex items-center px-2 py-1 bg-blue-50 text-blue-700 rounded-lg font-black tracking-tighter">
                                            {{ item.total_wbp }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <Link :href="route('astekpam.show', item.id)" class="inline-flex items-center justify-center w-10 h-10 bg-gray-100 text-gray-400 rounded-full hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                            <Eye class="w-5 h-5" />
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="md:hidden divide-y divide-gray-50">
                        <div v-for="item in astekpams" :key="item.id" class="p-4 active:bg-gray-50">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <div class="font-black text-gray-800 text-lg">{{ formatDate(item.tanggal) }}</div>
                                    <div class="text-xs text-gray-400 flex items-center">
                                        <Clock class="w-3 h-3 mr-1" /> {{ item.pukul }} WIB
                                    </div>
                                </div>
                                <span :class="getShiftColor(item.dari_shift)" class="px-3 py-1 rounded-full text-[10px] font-black uppercase border">
                                    {{ item.dari_shift }}
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4 mb-4 bg-gray-50 p-3 rounded-2xl">
                                <div>
                                    <div class="text-[10px] text-gray-400 uppercase font-bold">Rupam</div>
                                    <div class="text-sm font-bold text-gray-700 capitalize">{{ item.dari_rupam }}</div>
                                </div>
                                <div>
                                    <div class="text-[10px] text-gray-400 uppercase font-bold">Total WBP</div>
                                    <div class="text-sm font-bold text-blue-600">{{ item.total_wbp }} Orang</div>
                                </div>
                                <div class="col-span-2">
                                    <div class="text-[10px] text-gray-400 uppercase font-bold">Pimpinan</div>
                                    <div class="text-sm font-bold text-gray-700">{{ item.pimpinan }}</div>
                                </div>
                            </div>

                            <Link :href="route('astekpam.show', item.id)" class="w-full flex items-center justify-center py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-bold text-gray-600 shadow-sm">
                                Lihat Detail Laporan <ChevronRight class="w-4 h-4 ml-1" />
                            </Link>
                        </div>
                    </div>

                    <div v-if="astekpams.length === 0" class="p-12 text-center">
                        <div class="bg-gray-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <FileText class="w-10 h-10 text-gray-300" />
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Belum ada laporan</h3>
                        <p class="text-sm text-gray-500 mb-6">Mulai buat laporan Astekpam pertama Anda hari ini.</p>
                        <Link :href="route('astekpam.create')" class="px-6 py-2 bg-blue-600 text-white rounded-xl font-bold shadow-md">Buat Sekarang</Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Menghaluskan scrolling di mobile */
.overflow-x-auto {
    -webkit-overflow-scrolling: touch;
}
</style>