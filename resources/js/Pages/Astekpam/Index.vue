<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Card, CardContent } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { 
    FileText, Plus, Eye, Filter, Calendar, Clock, ShieldCheck, User, Copy, MessageCircle 
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
    
    if (filterRegu.value !== 'all') {
        result = result.filter(item => {
            const regu = item.ke_rupam || ''; 
            return regu.includes(filterRegu.value);
        });
    }

    if (filterWaktu.value !== 'all') {
        const today = new Date();
        result = result.filter(item => {
            if (!item.tanggal) return false;
            const itemDate = new Date(item.tanggal);
            
            if (filterWaktu.value === 'harian') return isSameDay(itemDate, today);
            if (filterWaktu.value === 'mingguan') return itemDate.getFullYear() === today.getFullYear() && getWeekNumber(itemDate) === getWeekNumber(today);
            if (filterWaktu.value === 'bulanan') return itemDate.getFullYear() === today.getFullYear() && itemDate.getMonth() === today.getMonth();
            return true;
        });
    }

    return result.sort((a, b) => new Date(b.tanggal) - new Date(a.tanggal));
});

const formatVal = (val) => val ? val : '-';

const getPetugasPelapor = (laporan) => {
    if (laporan.petugas_laporan) return laporan.petugas_laporan;
    if (laporan.tugas && laporan.tugas.petugas_laporan) return laporan.tugas.petugas_laporan;
    if (laporan.tugas && laporan.tugas.amanah) return laporan.tugas.amanah;
    return 'Belum Diisi';
};

// =========================================================================
// LOGIKA UNTUK COPY TEKS LAPORAN
// =========================================================================
const formatJsonArray = (data) => {
    if (!data) return null;
    let parsed = data;
    if (typeof data === 'string') {
        try { parsed = JSON.parse(data); } catch (e) { return null; }
    }
    if (!Array.isArray(parsed)) return null;
    
    const validItems = parsed.filter(item => item.ket && item.ket.trim() !== '');
    if (validItems.length === 0) return null;
    
    return validItems.map(item => item.ket.trim()).join(', ');
};

const formatJamTugas = (jamArray) => {
    if (!jamArray || typeof jamArray !== 'object') return '-';
    const validJams = [jamArray.jam_1, jamArray.jam_2, jamArray.jam_3].filter(val => val && val !== '-');
    if (validJams.length === 0) return '-';
    return validJams.join('/');
};

const getGreeting = (timeStr) => {
    if (!timeStr) return 'Selamat Bertugas....';
    const hour = parseInt(timeStr.replace('.', ':').split(':')[0]);
    if (hour >= 4 && hour < 11) return 'Selamat Pagi....';
    if (hour >= 11 && hour < 15) return 'Selamat Siang....';
    if (hour >= 15 && hour < 18) return 'Selamat Sore....';
    return 'Selamat Malam....';
};

const generatePesanLaporan = (data) => {
    const dateObj = new Date(data.tanggal);
    const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    
    // Menghilangkan angka 0 di depan tanggal jika ada (contoh: 04 jadi 4)
    const tglNum = dateObj.getDate();
    const tanggalIndo = `${days[dateObj.getDay()]}, ${tglNum} ${months[dateObj.getMonth()]} ${dateObj.getFullYear()}`;
    
    const salamWaktu = getGreeting(data.pukul);

    // Helpers untuk mengatasi null atau 0
    const formatOrg = (val) => (val && val !== '0' && val !== 0) ? `${val} Org` : '0 Org';
    const formatStr = (val) => (val && val !== '-') ? val : '-';

    let pesan = "ASTEKPAM LAPAS KELAS I PALEMBANG\n";
    pesan += "Assalamu’alaikum Warahmatullahi Wabarakatuh\n";
    pesan += `${salamWaktu}\n\n`;
    
    pesan += `Hari/Tgl : ${tanggalIndo}\n`;
    pesan += `Pukul : ${formatStr(data.pukul)} WIB\n\n`;
    
    pesan += `Berikut, ASTEKPAM dari ${formatStr(data.dari_rupam)} (Shift ${formatStr(data.dari_shift)}) ke ${formatStr(data.ke_rupam)} (Shift ${formatStr(data.ke_shift)}) Dipimpin oleh ${formatStr(data.pimpinan)} berjalan aman dan tertib.\n\n`;
    
    pesan += "Dengan rincian sebagai berikut :\n";
    pesan += "A. JUMLAH PENGHUNI\n";
    pesan += `1. Kapasitas : ${formatOrg(data.kapasitas)}\n`;
    pesan += `2. Narapidana : ${formatOrg(data.narapidana)}\n`;
    pesan += "3. Isi Blok Hunian :\n";
    pesan += `* Blok A : ${formatOrg(data.blok_a)}\n`;
    pesan += `* Blok B : ${formatOrg(data.blok_b)}\n`;
    pesan += `* Dapur : ${formatOrg(data.dapur)}\n`;
    pesan += `* Klinik : ${formatOrg(data.klinik)}\n`;
    pesan += "Jumlah :\n";
    pesan += `* Didalam Lapas : ${formatOrg(data.dalam_lapas)}\n`;
    pesan += `* Diluar Lapas : ${formatOrg(data.luar_lapas)}\n`;

    const rawatInap = formatJsonArray(data.rawat_inap_items);
    const berobat = formatJsonArray(data.berobat_items);
    const bonLuar = formatJsonArray(data.bon_luar_items);

    pesan += "4. Keterangan di luar Lapas :\n";
    pesan += `* Rawat Inap RS : ${rawatInap ? rawatInap : '-'}\n`;
    pesan += `* Berobat RS : ${berobat ? berobat : '-'}\n`;
    pesan += `* Lain-lain (bon luar) : ${bonLuar ? bonLuar : '-'}\n`;
    
    pesan += `5. Total Jumlah WBP : ${formatOrg(data.total_wbp)}\n\n`;

    pesan += "B. PERSONIL PENGAMANAN\n";
    pesan += `1. ${data.rupam_pilihan ? data.rupam_pilihan : '-'}\n`;
    pesan += `Jumlah : ${formatOrg(data.rupam_jumlah)}\n`;
    pesan += `Hadir : ${formatOrg(data.rupam_hadir)}\n`;
    const tHadir = (parseInt(data.rupam_jumlah) || 0) - (parseInt(data.rupam_hadir) || 0);
    pesan += `Tidak Hadir : ${tHadir > 0 ? tHadir + ' Org' : '-'}\n`;
    pesan += `Keterangan : ${formatStr(data.rupam_keterangan)}\n`;

    pesan += `2. SATGAS P2U\n`;
    pesan += `Jumlah : ${formatOrg(data.p2u_jumlah)}\n`;
    pesan += `Hadir : ${formatOrg(data.p2u_hadir)}\n`;
    pesan += `Keterangan : ${formatStr(data.p2u_keterangan)}\n\n`;

    let tugas = typeof data.tugas === 'string' ? JSON.parse(data.tugas) : data.tugas;
    
    if (tugas && typeof tugas === 'object') {
        pesan += "3. Pembagian Tugas :\n";
        pesan += `a. Ka. Rupam : ${formatStr(tugas.ka_rupam)}\n`;
        pesan += `   Wakarupam : ${formatStr(tugas.wakarupam)}\n`;
        pesan += `b. Petugas P2U :\n`;
        pesan += `   Kasatgas : ${formatStr(tugas.kasatgas_p2u)}\n`;
        pesan += `   Wakasatgas : ${formatStr(tugas.wakasatgas_p2u)}\n`;
        pesan += `c. Petugas Blok :\n`;
        pesan += `   Blok A : ${formatJamTugas(tugas.blok_a)}\n`;
        pesan += `   Blok B : ${formatJamTugas(tugas.blok_b)}\n`;
        pesan += `d. Petugas Pos Atas :\n`;
        pesan += `   * Menara 1 : ${formatJamTugas(tugas.menara_1)}\n`;
        pesan += `   * Menara 2 : ${formatJamTugas(tugas.menara_2)}\n`;
        pesan += `   * Menara 3 : ${formatJamTugas(tugas.menara_3)}\n`;
        pesan += `   * Menara 4 : ${formatJamTugas(tugas.menara_4)}\n`;
        pesan += `e. Petugas Jaga RS : ${formatStr(tugas.jaga_rs)}\n`;
        pesan += `f. Piket Dapur : ${formatStr(tugas.piket_dapur)}\n`;
        pesan += `g. Pengawas Piket : ${formatStr(tugas.perwira_piket)}\n`;
        pesan += `h. Perwira Piket : ${formatStr(tugas.perwira_kontrol)}\n`;
        pesan += `i. Banja : ${formatStr(tugas.banjaga)}\n`;
        pesan += `j. Staff KPLP : ${formatStr(tugas.staff_kplp)}\n`;
        pesan += `k. Amanah : ${formatStr(tugas.amanah)}\n`;
        pesan += `l. Petugas Laporan : ${formatStr(tugas.petugas_laporan)}\n\n`;
    } else {
        pesan += "3. Pembagian Tugas :\n(Data belum diisi)\n\n";
    }

    pesan += "Demikian Laporan ini, kami sampaikan dan diucapkan terima kasih.\n\n";
    pesan += "Wassalamu'alaikum Warahmatullaahi wabarakaatuh\n";
    pesan += "Salam Sejahtera\n";
    pesan += "Salam Sehat Selalu…🙏\n";

    return pesan;
};

const copyTeksLaporan = async (laporan) => {
    const teks = generatePesanLaporan(laporan);
    try {
        await navigator.clipboard.writeText(teks);
        alert('Teks Laporan berhasil disalin!');
    } catch (err) {
        alert('Gagal menyalin teks. Silakan coba lagi.');
        console.error('Failed to copy text: ', err);
    }
};

const shareKeWhatsAppGroup = async (laporan) => {
    const teks = generatePesanLaporan(laporan);
    try {
        // Copy teks ke clipboard
        await navigator.clipboard.writeText(teks);
        
        alert('Teks berhasil disalin! Silakan "Paste/Tempel" pesan tersebut di Grup WhatsApp.');

        // GANTI LINK DI BAWAH INI DENGAN LINK INVITE GRUP WHATSAPP ANDA
        const linkGrupWA = 'https://chat.whatsapp.com/CehSunQDnfiFmrNOVJy3CK'; 
        
        window.open(linkGrupWA, '_blank');
    } catch (err) {
        alert('Gagal menyalin teks. Silakan coba lagi.');
        console.error('Failed to copy text: ', err);
    }
};
</script>

<template>
    <Head title="Riwayat Astekpam" />

    <AuthenticatedLayout>
        <div class="py-6 bg-zinc-50/30 min-h-screen">
            <div class="w-full max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                
                <div class="bg-white rounded-xl p-5 border border-zinc-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-xl font-extrabold text-zinc-900 tracking-tight flex items-center gap-2">
                            <FileText class="w-5 h-5 text-blue-600" /> Riwayat Astekpam
                        </h1>
                        <p class="text-zinc-500 text-[13px] mt-0.5">Daftar laporan Apel Serah Terima Kepala Regu Pengamanan.</p>
                    </div>
                    <Link :href="route('astekpam.create')" class="w-full sm:w-auto shrink-0">
                        <Button class="rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold shadow-md shadow-blue-200 h-10 px-5 w-full text-[13px] transition-all">
                            <Plus class="w-4 h-4 mr-1.5" /> Buat Laporan Baru
                        </Button>
                    </Link>
                </div>

                <Card class="rounded-xl border border-zinc-200 shadow-sm bg-white p-3">
                    <div class="flex flex-col md:flex-row items-start md:items-center gap-3">
                        <div class="flex items-center gap-2 text-[11px] font-bold text-zinc-400 uppercase tracking-wider shrink-0 mb-1 md:mb-0">
                            <Filter class="w-4 h-4 text-zinc-400" />
                            <span>Filter Data:</span>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-2 w-full md:flex md:w-auto">
                            <Select v-model="filterRegu">
                                <SelectTrigger class="h-10 md:h-9 rounded-lg bg-zinc-50 border-0 focus:ring-1 focus:ring-blue-500 w-full md:w-40 text-[12px] font-bold">
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
                                <SelectTrigger class="h-10 md:h-9 rounded-lg bg-zinc-50 border-0 focus:ring-1 focus:ring-blue-500 w-full md:w-40 text-[12px] font-bold">
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

                <Card class="rounded-xl border border-zinc-200 shadow-sm bg-white overflow-hidden">
                    <CardContent class="p-0">
                        
                        <div class="block lg:hidden divide-y divide-zinc-100">
                            <div v-for="laporan in filteredAstekpams" :key="laporan.id" class="p-4 space-y-4 hover:bg-zinc-50 transition-colors">
                                
                                <div class="flex flex-col">
                                    <span class="font-bold text-zinc-900 flex items-center gap-1.5 text-[13px]">
                                        <Calendar class="w-4 h-4 text-zinc-400 shrink-0"/> {{ formatVal(laporan.tanggal) }}
                                    </span>
                                    <span class="text-[12px] font-medium text-zinc-500 flex items-center gap-1.5 mt-1">
                                        <Clock class="w-3.5 h-3.5 text-zinc-400 shrink-0"/> Pukul {{ formatVal(laporan.pukul) }}
                                    </span>
                                </div>

                                <div class="bg-zinc-50 p-3.5 rounded-xl border border-zinc-100 space-y-3">
                                    
                                    <div class="flex flex-col gap-1.5 w-full">
                                        <span class="text-[10px] text-zinc-400 font-bold uppercase tracking-wider shrink-0 mb-0.5">Serah Terima</span>
                                        <div class="grid grid-cols-[1fr_auto_1fr] gap-2 items-center">
                                            <span class="px-2 py-1.5 bg-white text-zinc-600 text-[11px] font-bold rounded-md border border-zinc-200 uppercase text-center truncate shadow-sm">
                                                {{ formatVal(laporan.dari_rupam) }}
                                            </span>
                                            <span class="text-zinc-400 text-sm font-medium text-center flex-shrink-0">→</span>
                                            <span class="px-2 py-1.5 bg-blue-50 text-blue-700 text-[11px] font-bold rounded-md border border-blue-200 uppercase text-center truncate shadow-sm">
                                                {{ formatVal(laporan.ke_rupam) }}
                                            </span>
                                            
                                            <span class="px-2 py-1.5 bg-white text-zinc-600 text-[11px] font-bold rounded-md border border-zinc-200 uppercase text-center truncate shadow-sm">
                                                {{ formatVal(laporan.dari_shift) }}
                                            </span>
                                            <span class="text-zinc-400 text-sm font-medium text-center flex-shrink-0">→</span>
                                            <span class="px-2 py-1.5 bg-emerald-50 text-emerald-700 text-[11px] font-bold rounded-md border border-emerald-200 uppercase text-center truncate shadow-sm">
                                                {{ formatVal(laporan.ke_shift) }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="border-t border-zinc-200/60 pt-2.5">
                                        <span class="text-[10px] text-zinc-400 font-bold uppercase tracking-wider block mb-1">Pimpinan Apel</span>
                                        <span class="font-semibold text-zinc-800 flex items-start gap-2 text-[12px] whitespace-normal leading-snug">
                                            <ShieldCheck class="w-4 h-4 text-zinc-400 shrink-0 mt-0.5"/> 
                                            <span class="break-words">{{ formatVal(laporan.pimpinan) }}</span>
                                        </span>
                                    </div>

                                    <div class="border-t border-zinc-200/60 pt-2.5">
                                        <span class="text-[10px] text-zinc-400 font-bold uppercase tracking-wider block mb-1">Pelapor</span>
                                        <span class="font-bold text-zinc-800 flex items-start gap-2 text-[12px] whitespace-normal leading-snug">
                                            <User class="w-4 h-4 text-amber-500 shrink-0 mt-0.5"/> 
                                            <span class="break-words">{{ getPetugasPelapor(laporan) }}</span>
                                        </span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-3 w-full pt-1">
                                    <Link :href="route('astekpam.show', laporan.id)" class="w-full block">
                                        <Button variant="outline" class="w-full text-blue-600 border-blue-200 hover:bg-blue-50 font-bold text-[12px] h-10 rounded-xl flex items-center justify-center">
                                            <Eye class="w-4 h-4 mr-1.5 shrink-0" /> Detail
                                        </Button>
                                    </Link>
                                    <Button @click="copyTeksLaporan(laporan)" variant="outline" class="w-full text-zinc-600 border-zinc-200 hover:bg-zinc-50 font-bold text-[12px] h-10 rounded-xl flex items-center justify-center">
                                        <Copy class="w-4 h-4 mr-1.5 shrink-0" /> Copy Teks
                                    </Button>

                                    <Button @click="shareKeWhatsAppGroup(laporan)" variant="outline" class="w-full text-emerald-600 border-emerald-200 hover:bg-emerald-50 font-bold text-[12px] h-10 rounded-xl flex items-center justify-center col-span-2">
                                        <MessageCircle class="w-4 h-4 mr-1.5 shrink-0" /> Copy & Buka Grup WA
                                    </Button>
                                </div>

                            </div>
                            
                            <div v-if="filteredAstekpams.length === 0" class="text-center py-10 px-4">
                                <FileText class="w-10 h-10 text-zinc-300 mx-auto mb-3" />
                                <p class="text-sm font-semibold text-zinc-500">Tidak ada data laporan ditemukan.</p>
                            </div>
                        </div>

                        <div class="hidden lg:block w-full">
                            <table class="w-full text-left border-collapse table-fixed">
                                <thead>
                                    <tr class="bg-zinc-50 border-b border-zinc-100 text-zinc-400 font-bold text-[11px] tracking-wider uppercase">
                                        <th class="py-3.5 px-4 w-[15%]">Tanggal & Waktu</th>
                                        <th class="py-3.5 px-4 w-[25%]">Serah Terima</th>
                                        <th class="py-3.5 px-4 w-[15%]">Pimpinan Apel</th>
                                        <th class="py-3.5 px-4 w-[15%]">Petugas Pelapor</th>
                                        <th class="py-3.5 px-4 w-[30%] text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-100 text-[13px] font-medium text-zinc-800">
                                    <tr v-for="laporan in filteredAstekpams" :key="laporan.id" class="hover:bg-zinc-50/50 transition-colors align-top">
                                        
                                        <td class="py-4 px-4 align-middle">
                                            <div class="flex flex-col">
                                                <span class="font-bold text-zinc-900 flex items-center gap-1.5 leading-snug">
                                                    <Calendar class="w-4 h-4 text-zinc-400 shrink-0"/> {{ formatVal(laporan.tanggal) }}
                                                </span>
                                                <span class="text-[12px] text-zinc-500 flex items-center gap-1.5 mt-1 leading-snug">
                                                    <Clock class="w-3.5 h-3.5 text-zinc-400 shrink-0"/> Pukul {{ formatVal(laporan.pukul) }}
                                                </span>
                                            </div>
                                        </td>
                                        
                                        <td class="py-4 px-4 align-middle">
                                            <div class="flex flex-col gap-2 w-full">
                                                <div class="flex items-center gap-1.5 w-full">
                                                    <span class="flex-1 px-1.5 py-1 bg-zinc-100 text-zinc-600 text-[11px] font-bold rounded border border-zinc-200 uppercase tracking-wide text-center truncate">
                                                        {{ formatVal(laporan.dari_rupam) }}
                                                    </span>
                                                    <span class="text-zinc-400 text-xs font-bold shrink-0">→</span>
                                                    <span class="flex-1 px-1.5 py-1 bg-blue-50 text-blue-700 text-[11px] font-bold rounded border border-blue-100 uppercase tracking-wide text-center truncate">
                                                        {{ formatVal(laporan.ke_rupam) }}
                                                    </span>
                                                </div>
                                                <div class="flex items-center gap-1.5 w-full">
                                                    <span class="flex-1 px-1.5 py-1 bg-zinc-100 text-zinc-600 text-[11px] font-bold rounded border border-zinc-200 uppercase tracking-wide text-center truncate">
                                                        {{ formatVal(laporan.dari_shift) }}
                                                    </span>
                                                    <span class="text-zinc-400 text-xs font-bold shrink-0">→</span>
                                                    <span class="flex-1 px-1.5 py-1 bg-emerald-50 text-emerald-700 text-[11px] font-bold rounded border border-emerald-100 uppercase tracking-wide text-center truncate">
                                                        {{ formatVal(laporan.ke_shift) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <td class="py-4 px-4 align-middle">
                                            <div class="flex items-start gap-1.5">
                                                <ShieldCheck class="w-4 h-4 text-zinc-400 shrink-0 mt-0.5" />
                                                <span class="text-zinc-700 font-semibold whitespace-normal break-words leading-snug">
                                                    {{ formatVal(laporan.pimpinan) }}
                                                </span>
                                            </div>
                                        </td>
                                        
                                        <td class="py-4 px-4 align-middle">
                                            <div class="flex items-start gap-1.5">
                                                <User class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" />
                                                <span class="text-zinc-800 font-bold whitespace-normal break-words leading-snug">
                                                    {{ getPetugasPelapor(laporan) }}
                                                </span>
                                            </div>
                                        </td>
                                        
                                        <td class="py-4 px-4 text-center align-middle">
                                            <div class="flex items-center justify-center gap-2 flex-wrap">
                                                <Link :href="route('astekpam.show', laporan.id)">
                                                    <Button variant="outline" class="text-blue-600 border-blue-200 hover:bg-blue-50 font-bold text-[11px] h-8 px-2.5 rounded-lg flex items-center">
                                                        <Eye class="w-3.5 h-3.5 mr-1 shrink-0" /> Detail
                                                    </Button>
                                                </Link>
                                                <Button @click="copyTeksLaporan(laporan)" variant="outline" class="text-zinc-600 border-zinc-200 hover:bg-zinc-50 font-bold text-[11px] h-8 px-2.5 rounded-lg flex items-center">
                                                    <Copy class="w-3.5 h-3.5 mr-1 shrink-0" /> Copy
                                                </Button>

                                                <Button @click="shareKeWhatsAppGroup(laporan)" variant="outline" class="text-emerald-600 border-emerald-200 hover:bg-emerald-50 font-bold text-[11px] h-8 px-2.5 rounded-lg flex items-center">
                                                    <MessageCircle class="w-3.5 h-3.5 mr-1 shrink-0" /> Buka WA
                                                </Button>
                                            </div>
                                        </td>
                                    </tr>
                                    
                                    <tr v-if="filteredAstekpams.length === 0">
                                        <td colspan="5" class="text-center py-10 text-zinc-400 italic text-[13px]">
                                            Tidak ada data laporan ditemukan.
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