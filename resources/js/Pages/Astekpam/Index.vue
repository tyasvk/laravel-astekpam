<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { 
    Plus, 
    Search, 
    MoreHorizontal, 
    Eye, 
    ShieldCheck, 
    Calendar,
    Clock,
    ChevronRight,
    Printer,
    FileText
} from 'lucide-vue-next';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/Components/ui/dropdown-menu';

// Menerima data dari Controller dengan default value untuk mencegah error undefined
const props = defineProps({
    reports: {
        type: Object,
        default: () => ({ data: [], links: [] })
    },
});

// Helper format tanggal Indonesia
const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    });
};
</script>

<template>
    <Head title="Riwayat Astekpam" />

    <AuthenticatedLayout>
        <div class="max-w-6xl mx-auto px-4 pb-24 font-sans">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 mb-10 mt-6">
                <div class="space-y-1">
                    <h1 class="text-3xl font-medium tracking-tighter text-zinc-900 italic font-sans">Riwayat Astekpam</h1>
                    <div class="flex items-center gap-2 text-zinc-400">
                        <div class="h-1.5 w-1.5 rounded-full bg-emerald-500"></div>
                        <p class="text-[10px] uppercase tracking-[0.2em] font-medium">Lapas Kelas I Palembang</p>
                    </div>
                </div>
                
                <Link :href="route('astekpam.create')">
                    <Button class="h-12 rounded-2xl bg-zinc-900 text-white shadow-xl shadow-zinc-200 hover:bg-zinc-800 transition-all px-6 font-medium text-[11px] uppercase tracking-widest active:scale-95">
                        <Plus class="w-4 h-4 mr-2" /> Buat Laporan
                    </Button>
                </Link>
            </div>

            <div class="space-y-4">
                
                <div class="hidden md:grid grid-cols-[1.2fr_1.5fr_1fr_1fr_80px] px-8 py-3 text-[10px] uppercase tracking-[0.2em] text-zinc-400 font-bold">
                    <div>Waktu & Jam</div>
                    <div>Serah Terima</div>
                    <div>Pimpinan</div>
                    <div>Status Keamanan</div>
                    <div class="text-right">Aksi</div>
                </div>

                <template v-if="reports?.data?.length > 0">
                    <div v-for="report in reports.data" :key="report.id" 
                        class="bg-white rounded-[2rem] md:rounded-[1.5rem] border border-zinc-100 shadow-sm hover:shadow-md transition-all group overflow-hidden">
                        
                        <div class="p-6 md:px-8 md:py-5 grid grid-cols-1 md:grid-cols-[1.2fr_1.5fr_1fr_1fr_80px] items-center gap-5 md:gap-0">
                            
                            <div class="flex items-center gap-4">
                                <div class="h-10 w-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 md:hidden">
                                    <Calendar class="w-4 h-4" />
                                </div>
                                <div class="space-y-0.5">
                                    <p class="text-sm font-medium text-zinc-900">{{ formatDate(report.tanggal) }}</p>
                                    <div class="flex items-center gap-2 text-zinc-400">
                                        <Clock class="w-3 h-3" />
                                        <p class="text-[11px] font-medium">{{ report.pukul }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col gap-1.5 md:flex-row md:items-center">
                                <span class="text-[9px] font-bold text-zinc-300 uppercase md:hidden tracking-widest">Serah Terima:</span>
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-1 bg-zinc-100 text-zinc-500 rounded-lg text-[10px] font-bold uppercase">
                                        {{ report.dari_rupam }}
                                    </span>
                                    <ChevronRight class="w-3 h-3 text-zinc-300" />
                                    <span class="px-2.5 py-1 bg-blue-50 text-blue-600 rounded-lg text-[10px] font-bold uppercase">
                                        {{ report.ke_rupam }}
                                    </span>
                                </div>
                            </div>

                            <div class="space-y-0.5">
                                <span class="text-[9px] font-bold text-zinc-300 uppercase md:hidden tracking-widest">Pimpinan:</span>
                                <p class="text-xs font-medium text-zinc-700">{{ report.pimpinan }}</p>
                                <p class="text-[10px] text-zinc-400 font-medium">Shift {{ report.ke_shift }}</p>
                            </div>

                            <div>
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-100/50">
                                    <ShieldCheck class="w-3.5 h-3.5" />
                                    <span class="text-[10px] font-medium uppercase tracking-tight">Aman & Tertib</span>
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <DropdownMenu>
                                    <DropdownMenuTrigger asChild>
                                        <Button variant="ghost" size="icon" class="h-9 w-9 rounded-xl hover:bg-zinc-100 transition-colors">
                                            <MoreHorizontal class="w-4 h-4 text-zinc-400" />
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end" class="w-48 rounded-2xl shadow-2xl border-zinc-100 p-2 font-sans">
                                        <Link :href="route('astekpam.show', report.id)">
                                            <DropdownMenuItem class="rounded-xl gap-3 py-2.5 cursor-pointer">
                                                <Eye class="w-4 h-4 text-zinc-400" />
                                                <span class="text-xs font-medium">Lihat Detail</span>
                                            </DropdownMenuItem>
                                        </Link>
                                        <DropdownMenuItem class="rounded-xl gap-3 py-2.5 cursor-pointer text-blue-600">
                                            <Printer class="w-4 h-4" />
                                            <span class="text-xs font-medium">Cetak Laporan</span>
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </div>
                        </div>
                    </div>
                </template>

                <div v-else class="py-24 text-center bg-white rounded-[2.5rem] border border-dashed border-zinc-200">
                    <div class="h-16 w-16 bg-zinc-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <FileText class="w-6 h-6 text-zinc-200" />
                    </div>
                    <p class="text-sm text-zinc-400 font-medium tracking-wide">Belum ada riwayat laporan yang tercatat.</p>
                </div>
            </div>

            <div class="mt-12 flex items-center justify-between px-4" v-if="reports?.links?.length > 3">
                <p class="text-[10px] font-medium text-zinc-400 uppercase tracking-widest">
                    Data ke {{ reports.from }} - {{ reports.to }}
                </p>
                <div class="flex gap-2">
                    <Link 
                        v-for="(link, k) in reports.links" 
                        :key="k"
                        :href="link.url || '#'"
                        v-html="link.label"
                        :class="[
                            'px-4 py-2 text-[10px] font-bold uppercase tracking-widest rounded-xl transition-all',
                            link.active ? 'bg-zinc-900 text-white shadow-lg shadow-zinc-200' : 'bg-white text-zinc-400 hover:bg-zinc-50 border border-zinc-100',
                            !link.url ? 'opacity-30 cursor-not-allowed' : ''
                        ]"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Transisi halus khas Modern SaaS */
* {
    transition: all 300ms cubic-bezier(0.4, 0, 0.2, 1);
}
</style>