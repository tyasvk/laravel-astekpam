<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import { 
    Download, Printer, User, Calendar, MessageSquare, 
    ChevronDown, ChevronUp, CheckCircle2, FileSpreadsheet, FileText 
} from 'lucide-vue-next';

const props = defineProps({ 
    responses: Array 
});

// State untuk melacak baris (kartu) mana yang sedang dibuka
const expandedRows = ref([]);

// Fungsi buka/tutup kartu jawaban
const toggleRow = (id) => {
    if (expandedRows.value.includes(id)) {
        expandedRows.value = expandedRows.value.filter(rowId => rowId !== id);
    } else {
        expandedRows.value.push(id);
    }
};

// Fungsi untuk membuka semua jawaban sekaligus sebelum di-print/PDF
const expandAll = () => {
    expandedRows.value = props.responses.map(r => r.id);
};

const downloadCsv = () => {
    window.location.href = route('admin.kuisioner.export');
};

const printPdf = () => {
    expandAll(); // Buka semua tab jawaban
    
    // Beri waktu 300ms agar animasi Vue selesai membuka tab sebelum layar Print muncul
    setTimeout(() => {
        window.print();
    }, 300);
};

// Format tanggal menggunakan Native JavaScript (tanpa library tambahan)
const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('id-ID', { 
        weekday: 'long', 
        day: '2-digit', 
        month: 'long', 
        year: 'numeric', 
        hour: '2-digit', 
        minute: '2-digit' 
    }) + ' WIB';
};
</script>

<template>
    <Head title="Hasil Kuisioner" />
    <AuthenticatedLayout>
        
        <div class="py-6 sm:py-10 bg-slate-50 min-h-screen print:bg-white print:py-0">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-5 mb-8 print:hidden">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-indigo-100 text-indigo-700 text-xs font-bold mb-3 uppercase tracking-widest">
                            <MessageSquare class="w-3.5 h-3.5" /> Evaluasi Astekpam
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Hasil Kuisioner Petugas</h2>
                        <p class="text-slate-500 text-sm font-medium mt-1">Daftar rekapitulasi jawaban kuisioner yang telah diisi oleh petugas lapangan.</p>
                    </div>
                    
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <button @click="downloadCsv" class="flex-1 sm:flex-none flex items-center justify-center gap-2 bg-white border border-slate-200 hover:border-emerald-300 hover:bg-emerald-50 text-emerald-600 px-5 py-2.5 rounded-xl font-bold text-sm shadow-sm transition-all">
                            <FileSpreadsheet class="w-4 h-4" /> Export CSV
                        </button>
                        <button @click="printPdf" class="flex-1 sm:flex-none flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-md shadow-indigo-200 transition-all hover:scale-105 active:scale-95">
                            <Printer class="w-4 h-4" /> Simpan PDF
                        </button>
                    </div>
                </div>

                <div class="hidden print:block mb-8 text-center border-b-2 border-slate-800 pb-4">
                    <h1 class="text-2xl font-black text-black uppercase tracking-widest">Laporan Hasil Kuisioner Astekpam</h1>
                    <p class="text-sm text-gray-600 mt-1">Dicetak pada: {{ new Date().toLocaleString('id-ID') }}</p>
                </div>

                <div v-if="!responses.length" class="bg-white border border-slate-200 rounded-3xl p-12 text-center shadow-sm print:hidden">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <FileText class="w-10 h-10 text-slate-300" />
                    </div>
                    <h3 class="text-lg font-bold text-slate-700">Belum Ada Data</h3>
                    <p class="text-slate-500 mt-2 text-sm">Belum ada petugas yang berpartisipasi mengisi form kuisioner ini.</p>
                </div>

                <div class="space-y-4 print:space-y-6">
                    <div v-for="res in responses" :key="res.id" class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden transition-all hover:shadow-md hover:border-indigo-200 print:border-b print:border-x-0 print:border-t-0 print:rounded-none print:shadow-none print:break-inside-avoid">
                        
                        <div @click="toggleRow(res.id)" class="px-5 py-4 cursor-pointer flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 group print:p-0 print:mb-3 print:cursor-default">
                            
                            <div class="flex items-center gap-4 w-full sm:w-auto">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-50 to-blue-50 text-indigo-600 flex items-center justify-center shrink-0 shadow-inner print:hidden">
                                    <User class="w-5 h-5" />
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-base font-bold text-slate-800 group-hover:text-indigo-700 transition-colors print:text-lg print:text-black">
                                        {{ res.user.name }}
                                    </h3>
                                    <div class="flex items-center gap-2 text-xs font-medium text-slate-500 mt-1 print:text-slate-600">
                                        <Calendar class="w-3.5 h-3.5 print:hidden" /> 
                                        {{ formatDate(res.created_at) }}
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-4 w-full sm:w-auto justify-between sm:justify-end">
                                <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 print:bg-transparent print:border-none print:p-0 print:text-black">
                                    <CheckCircle2 class="w-4 h-4 print:hidden" /> {{ res.answers.length }} Jawaban
                                </div>
                                <div class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-indigo-100 group-hover:text-indigo-600 transition-colors print:hidden">
                                    <ChevronUp v-if="expandedRows.includes(res.id)" class="w-5 h-5" />
                                    <ChevronDown v-else class="w-5 h-5" />
                                </div>
                            </div>
                        </div>

                        <div v-if="expandedRows.includes(res.id)" class="border-t border-slate-100 bg-slate-50/50 p-5 print:p-0 print:border-none print:bg-transparent print:block">
                            
                            <div v-if="res.answers.length > 0" class="space-y-5">
                                <div v-for="(ans, index) in res.answers" :key="ans.id" class="flex gap-4 group/item">
                                    <span class="w-6 h-6 rounded-full bg-slate-200 text-slate-600 text-xs font-bold flex items-center justify-center shrink-0 mt-0.5 print:bg-transparent print:text-black print:w-auto print:justify-start">
                                        {{ index + 1 }}
                                    </span>
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-slate-800 mb-1.5 leading-relaxed print:text-black">
                                            {{ ans.question.pertanyaan }}
                                        </p>
                                        <p class="text-sm text-slate-600 bg-white border border-slate-200 p-3 rounded-xl shadow-sm leading-relaxed whitespace-pre-wrap print:shadow-none print:border-none print:p-0 print:bg-transparent print:text-slate-800 print:pl-3 print:border-l-2 print:border-l-slate-300">
                                            {{ ans.jawaban }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div v-else class="text-sm text-slate-500 italic p-4 bg-white rounded-xl border border-dashed border-slate-200 text-center print:border-none print:p-0 print:text-left">
                                Tidak ada rincian jawaban yang direkam.
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Pengaturan Layout Khusus Mode Cetak (PDF) */
@media print {
    @page {
        margin: 1.5cm;
        size: A4 portrait;
    }
    
    body {
        background-color: white !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    /* Menyembunyikan sidebar navigasi bawaan AuthenticatedLayout Inertia */
    :deep(nav), :deep(aside), :deep(header) {
        display: none !important;
    }

    /* Memastikan konten mengisi seluruh kertas */
    :deep(main) {
        padding: 0 !important;
        margin: 0 !important;
        background: white !important;
    }
    
    .print\:hidden {
        display: none !important;
    }
}
</style>