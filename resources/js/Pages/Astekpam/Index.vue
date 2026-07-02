<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import { Card, CardContent } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { Input } from '@/Components/ui/input'; 
import { 
    FileText, Plus, Eye, Filter, Calendar, Clock, ShieldCheck, User, Copy, MessageCircle, Image as ImageIcon,
    ChevronLeft, ChevronRight, Download, X, Edit, ArrowRight
} from 'lucide-vue-next';
import { usePermission } from '@/Composables/usePermission';

import jsPDF from 'jspdf';
import autoTable from 'jspdf-autotable';

const props = defineProps({
    astekpams: Array
});

const { hasRole } = usePermission();

const filterRegu = ref('all');
const filterStartDate = ref(''); 
const filterEndDate = ref('');  
const currentPage = ref(1);
const itemsPerPage = 10; 

watch([filterRegu, filterStartDate, filterEndDate], () => {
    currentPage.value = 1;
});

const filteredAstekpams = computed(() => {
    let result = props.astekpams || [];
    
    if (filterRegu.value !== 'all') {
        result = result.filter(item => {
            const regu = item.ke_rupam || ''; 
            return regu === `Rupam ${filterRegu.value}` || regu === filterRegu.value;
        });
    }

    if (filterStartDate.value || filterEndDate.value) {
        result = result.filter(item => {
            const itemDate = new Date(item.tanggal);
            let isValid = true;

            if (filterStartDate.value) {
                isValid = isValid && itemDate >= new Date(filterStartDate.value);
            }
            if (filterEndDate.value) {
                isValid = isValid && itemDate <= new Date(filterEndDate.value);
            }
            
            return isValid;
        });
    }

    return result.sort((a, b) => new Date(b.tanggal) - new Date(a.tanggal));
});

const hapusLaporan = (id) => {
    if (confirm('⚠️ PERINGATAN: Apakah Anda yakin ingin menghapus laporan ini secara permanen? Data dan foto tidak dapat dikembalikan.')) {
        router.delete(route('admin.astekpam.destroy', id), {
            preserveScroll: true,
        });
    }
};

const paginatedAstekpams = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return filteredAstekpams.value.slice(start, end);
});

const totalPages = computed(() => {
    return Math.ceil(filteredAstekpams.value.length / itemsPerPage);
});

const nextPage = () => { if (currentPage.value < totalPages.value) currentPage.value++; };
const prevPage = () => { if (currentPage.value > 1) currentPage.value--; };

const formatVal = (val) => val ? val : '-';

const getPetugasPelapor = (laporan) => {
    if (laporan.petugas_laporan) return laporan.petugas_laporan;
    if (laporan.tugas && laporan.tugas.petugas_laporan) return laporan.tugas.petugas_laporan;
    if (laporan.tugas && laporan.tugas.amanah) return laporan.tugas.amanah;
    return 'Belum Diisi';
};

const getImageUrl = (path) => {
    if (!path) return '';
    if (path.startsWith('http')) return path;
    const cleanPath = path.replace(/^\/?(storage\/)?/, '');
    return `/storage/${cleanPath}`;
};

// =========================================================================
// LOGIKA DOWNLOAD LAPORAN KE PDF
// =========================================================================
const downloadPDF = async () => {
    const data = filteredAstekpams.value;
    if (data.length === 0) {
        alert('Tidak ada data laporan untuk diunduh pada filter ini.');
        return;
    }
    document.body.style.cursor = 'wait';

    try {
        const doc = new jsPDF('l', 'mm', 'a4');
        doc.setFontSize(14);
        doc.setFont("helvetica", "bold");
        doc.text('REKAPITULASI LAPORAN LENGKAP ASTEKPAM', 14, 15);
        
        doc.setFontSize(9);
        doc.setFont("helvetica", "normal");
        const infoRegu = filterRegu.value === 'all' ? 'Semua Regu' : `Regu ${filterRegu.value}`;
        
        let infoWaktu = 'Semua Waktu';
        if (filterStartDate.value && filterEndDate.value) {
            infoWaktu = `${filterStartDate.value} s.d ${filterEndDate.value}`;
        } else if (filterStartDate.value) {
            infoWaktu = `Mulai ${filterStartDate.value}`;
        } else if (filterEndDate.value) {
            infoWaktu = `Sampai ${filterEndDate.value}`;
        }

        doc.text(`Filter: ${infoRegu} | Waktu: ${infoWaktu} | Total: ${data.length} Laporan`, 14, 22);

        const tableColumn = ["Waktu & Shift", "Pimpinan & Pelapor", "Rincian WBP", "Kekuatan Pengamanan", "Pembagian Tugas Lengkap", "Foto Laporan"];
        const tableRows = [];
        const base64Images = []; 

        for (const item of data) {
            const col1 = `Tgl: ${item.tanggal || '-'}\nPk. ${item.pukul || '-'} WIB\n\nSerah Terima:\n${item.dari_rupam || '-'} (${item.dari_shift || '-'}) \nMenuju \n${item.ke_rupam || '-'} (${item.ke_shift || '-'})`;
            const col2 = `Pimpinan Apel:\n${item.pimpinan || '-'}\n\nPetugas Pelapor:\n${getPetugasPelapor(item)}`;

            const rawatInap = formatJsonArray(item.rawat_inap_items);
            const berobat = formatJsonArray(item.berobat_items);
            const bonLuar = formatJsonArray(item.bon_luar_items);
            
            const col3 = `Total WBP: ${item.total_wbp || 0} Org\nKapasitas: ${item.kapasitas || 0} Org\nNarapidana: ${item.narapidana || 0} Org\n\nIsi Blok Hunian:\n- Blok A: ${item.blok_a || 0} Org\n- Blok B: ${item.blok_b || 0} Org\n- Dapur: ${item.dapur || 0} Org\n- Klinik: ${item.klinik || 0} Org\n\nPosisi:\n- Dalam Lapas: ${item.dalam_lapas || 0} Org\n- Luar Lapas: ${item.luar_lapas || 0} Org\n\nKet Luar:\n- Rawat Inap: ${rawatInap || '-'}\n- Berobat: ${berobat || '-'}\n- Bon Luar: ${bonLuar || '-'}`;

            const tHadir = (parseInt(item.rupam_jumlah) || 0) - (parseInt(item.rupam_hadir) || 0);
            const col4 = `REKAP RUPAM:\n(${item.rupam_pilihan || '-'})\n- Jumlah: ${item.rupam_jumlah || 0} Org\n- Hadir: ${item.rupam_hadir || 0} Org\n- Kurang: ${tHadir > 0 ? tHadir + ' Org' : '-'}\n- Ket: ${item.rupam_keterangan || '-'}\n\nREKAP P2U:\n- Jumlah: ${item.p2u_jumlah || 0} Org\n- Hadir: ${item.p2u_hadir || 0} Org\n- Ket: ${item.p2u_keterangan || '-'}`;

            let tugasStr = "(Data belum diisi)";
            let tugas = typeof item.tugas === 'string' ? JSON.parse(item.tugas) : item.tugas;
            
            if (tugas && typeof tugas === 'object') {
                tugasStr = `Ka. Rupam: ${tugas.ka_rupam || '-'}\n` +
                           `Wakarupam: ${tugas.wakarupam || '-'}\n\n` +
                           `P2U (Kasatgas): ${tugas.kasatgas_p2u || '-'}\n` +
                           `P2U (Wakasatgas): ${tugas.wakasatgas_p2u || '-'}\n\n` +
                           `Petugas Blok:\n` +
                           `- Blok A: ${formatJamTugas(tugas.blok_a)}\n` +
                           `- Blok B: ${formatJamTugas(tugas.blok_b)}\n\n` +
                           `Pos Menara Atas:\n` +
                           `- Menara 1: ${formatJamTugas(tugas.menara_1)}\n` +
                           `- Menara 2: ${formatJamTugas(tugas.menara_2)}\n` +
                           `- Menara 3: ${formatJamTugas(tugas.menara_3)}\n` +
                           `- Menara 4: ${formatJamTugas(tugas.menara_4)}\n\n` +
                           `Lainnya:\n` +
                           `- Jaga RS: ${tugas.jaga_rs || '-'}\n` +
                           `- Piket Dapur: ${tugas.piket_dapur || '-'}\n` +
                           `- Pengawas Piket: ${tugas.perwira_piket || '-'}\n` +
                           `- Perwira Kontrol: ${tugas.perwira_kontrol || '-'}\n` +
                           `- Banjaga: ${tugas.banjaga || '-'}\n` +
                           `- Staff KPLP: ${tugas.staff_kplp || '-'}\n` +
                           `- Amanah: ${tugas.amanah || '-'}`;
            }

            let base64Img = null;
            if (item.foto_laporan) {
                try {
                    const response = await fetch(`/storage/${item.foto_laporan}`);
                    if (response.ok) {
                        const blob = await response.blob();
                        if (blob.type.startsWith('image/')) {
                            base64Img = await new Promise(resolve => {
                                const reader = new FileReader();
                                reader.onloadend = () => resolve(reader.result);
                                reader.readAsDataURL(blob);
                            });
                        }
                    }
                } catch (e) {
                    console.error('Gagal load gambar untuk ID:', item.id, e);
                }
            }
            base64Images.push(base64Img); 

            tableRows.push([
                col1, 
                col2, 
                col3, 
                col4, 
                tugasStr, 
                { content: '', styles: { minCellHeight: 35 } } 
            ]);
        }

        autoTable(doc, {
            head: [tableColumn],
            body: tableRows,
            startY: 28, 
            theme: 'grid',
            styles: { fontSize: 7.5, cellPadding: 3, valign: 'top', overflow: 'linebreak' },
            headStyles: { fillColor: [30, 41, 59], textColor: [255, 255, 255], fontSize: 8, fontStyle: 'bold', halign: 'center' },
            columnStyles: {
                0: { cellWidth: 28 }, 
                1: { cellWidth: 28 }, 
                2: { cellWidth: 43 }, 
                3: { cellWidth: 43 }, 
                4: { cellWidth: 'auto' }, 
                5: { cellWidth: 35, halign: 'center', valign: 'middle' } 
            },
            alternateRowStyles: { fillColor: [248, 250, 252] },
            didDrawCell: function (data) {
                if (data.column.index === 5 && data.cell.section === 'body') {
                    const base64Img = base64Images[data.row.index];
                    if (base64Img) {
                        doc.addImage(base64Img, data.cell.x + 2.5, data.cell.y + 2.5, 30, 30);
                    } else {
                        doc.setFontSize(7);
                        doc.text("Tidak Ada Foto", data.cell.x + 17.5, data.cell.y + 17.5, { align: 'center' });
                    }
                }
            }
        });

        let fileName = 'Laporan_Lengkap_Astekpam';
        if (filterRegu.value !== 'all') fileName += `_Regu_${filterRegu.value}`;
        if (filterStartDate.value) fileName += `_Dari_${filterStartDate.value}`;
        if (filterEndDate.value) fileName += `_Sampai_${filterEndDate.value}`;
        fileName += '.pdf';

        doc.save(fileName);
    } catch (error) {
        console.error("Terjadi kesalahan saat generate PDF: ", error);
        alert("Gagal men-download PDF. Silakan coba lagi.");
    } finally {
        document.body.style.cursor = 'default';
    }
};

// =========================================================================
// LOGIKA UNTUK COPY TEKS LAPORAN (WHATSAPP) - FORMAT BARU
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
    if (!timeStr) return 'Bertugas';
    const hour = parseInt(timeStr.replace('.', ':').split(':')[0]);
    if (hour >= 4 && hour < 11) return 'Pagi';
    if (hour >= 11 && hour < 15) return 'Siang';
    if (hour >= 15 && hour < 18) return 'Sore';
    return 'Malam';
};

const generatePesanLaporan = (data) => {
    const dateObj = new Date(data.tanggal);
    const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    
    const tglNum = dateObj.getDate();
    const tanggalIndo = `${days[dateObj.getDay()]}, ${tglNum} ${months[dateObj.getMonth()]} ${dateObj.getFullYear()}`;
    const salamWaktu = getGreeting(data.pukul);

    const formatOrg = (val) => (val && val !== '0' && val !== 0) ? `${val} Org` : '0 Org';
    const formatStr = (val) => (val && val !== '-') ? val : '-';

    let pesan = "";

    // 1. Link Foto di Paling Atas
    if (data.foto_laporan) {
        pesan += `${window.location.origin}/storage/${data.foto_laporan}\n\n`;
    }

    pesan += "*ASTEKPAM LAPAS KELAS I PALEMBANG*\n\n";
    pesan += "Assalamu’alaikum Warahmatullahi Wabarakatuh\n";
    pesan += `Selamat ${data.ke_shift ? data.ke_shift : '-'}....\n\n`;
    
    pesan += `Hari/Tgl : *${tanggalIndo}*\n`;
    pesan += `Pukul    : *${formatStr(data.pukul)} WIB*\n\n`;
    
    pesan += `Berikut, ASTEKPAM dari *${formatStr(data.dari_rupam)}* (Shift ${formatStr(data.dari_shift)}) ke *${formatStr(data.ke_rupam)}* (Shift ${formatStr(data.ke_shift)}) Dipimpin oleh *${formatStr(data.pimpinan)}* berjalan aman dan tertib.\n\n`;
    
    pesan += "Dengan rincian sebagai berikut :\n\n";
    
    // 2. A. Jumlah Penghuni (Blok A-B Dapur Klinik Lurus)
    pesan += "*A. JUMLAH PENGHUNI*\n";
    pesan += `1. Kapasitas : ${formatOrg(data.kapasitas)}\n`;
    pesan += `2. Narapidana : ${formatOrg(data.narapidana)}\n`;
    pesan += "3. Isi Blok Hunian :\n";
    pesan += `   - Blok A         : ${formatOrg(data.blok_a)}\n`;
    pesan += `   - Blok B         : ${formatOrg(data.blok_b)}\n`;
    pesan += `   - Dapur          : ${formatOrg(data.dapur)}\n`;
    pesan += `   - Klinik         : ${formatOrg(data.klinik)}\n`;
    pesan += `   - Dalam Lapas : ${formatOrg(data.dalam_lapas)}\n`;
    pesan += `   - Luar Lapas    : ${formatOrg(data.luar_lapas)}\n\n`;

    const rawatInap = formatJsonArray(data.rawat_inap_items);
    const berobat = formatJsonArray(data.berobat_items);
    const bonLuar = formatJsonArray(data.bon_luar_items);

    pesan += "4. Keterangan di luar Lapas :\n";
    pesan += `   - Rawat Inap RS : ${rawatInap ? rawatInap : '-'}\n`;
    pesan += `   - Berobat RS      : ${berobat ? berobat : '-'}\n`;
    pesan += `   - Lain-lain (Bon): ${bonLuar ? bonLuar : '-'}\n\n`;
    
    pesan += `*5. Total Jumlah WBP : ${formatOrg(data.total_wbp)}*\n\n`;

    // 3. B. Personil (Rupam lurus, P2U Lurus)
    pesan += "*B. PERSONIL PENGAMANAN*\n";
    pesan += `1. *${data.rupam_pilihan ? data.rupam_pilihan : '-'}*\n`;
    pesan += `   - Jumlah          : ${formatOrg(data.rupam_jumlah)}\n`;
    pesan += `   - Hadir             : ${formatOrg(data.rupam_hadir)}\n`;
    const tHadir = (parseInt(data.rupam_jumlah) || 0) - (parseInt(data.rupam_hadir) || 0);
    pesan += `   - Tidak Hadir  : ${tHadir > 0 ? tHadir + ' Org' : '-'}\n`;
    pesan += `   - Keterangan   : ${formatStr(data.rupam_keterangan)}\n\n`;

    pesan += "2. *SATGAS P2U*\n";
    pesan += `   - Jumlah         : ${formatOrg(data.p2u_jumlah)}\n`;
    pesan += `   - Hadir            : ${formatOrg(data.p2u_hadir)}\n`;
    pesan += `   - Keterangan  : ${formatStr(data.p2u_keterangan)}\n\n`;

    let tugas = typeof data.tugas === 'string' ? JSON.parse(data.tugas) : data.tugas;
    
    if (tugas && typeof tugas === 'object') {
        pesan += "*3. Pembagian Tugas :*\n";
        pesan += `a. Ka. Rupam : ${formatStr(tugas.ka_rupam)}\n`;
        pesan += `   Wakarupam : ${formatStr(tugas.wakarupam)}\n\n`;
        
        pesan += `b. Petugas P2U :\n`;
        pesan += `   - Kasatgas    : ${formatStr(tugas.kasatgas_p2u)}\n`;
        pesan += `   - Wakasatgas : ${formatStr(tugas.wakasatgas_p2u)}\n\n`;
        
        pesan += `c. Petugas Blok :\n`;
        pesan += `   - Blok A : ${formatJamTugas(tugas.blok_a)}\n`;
        pesan += `   - Blok B : ${formatJamTugas(tugas.blok_b)}\n\n`;
        
        pesan += `d. Petugas Pos Atas :\n`;
        pesan += `   - Menara 1 : ${formatJamTugas(tugas.menara_1)}\n`;
        pesan += `   - Menara 2 : ${formatJamTugas(tugas.menara_2)}\n`;
        pesan += `   - Menara 3 : ${formatJamTugas(tugas.menara_3)}\n`;
        pesan += `   - Menara 4 : ${formatJamTugas(tugas.menara_4)}\n\n`;
        
        pesan += `e. Jaga RS         : ${formatStr(tugas.jaga_rs)}\n\n`;
        pesan += `f. Piket Dapur     : ${formatStr(tugas.piket_dapur)}\n\n`;
        pesan += `g. Pengawas Piket  : ${formatStr(tugas.perwira_piket)}\n\n`;
        pesan += `h. Perwira Piket   : ${formatStr(tugas.perwira_kontrol)}\n\n`;
        pesan += `i. Banjaga         : ${formatStr(tugas.banjaga)}\n\n`;
        pesan += `j. Staff KPLP      : ${formatStr(tugas.staff_kplp)}\n\n`;
        pesan += `k. Amanah          : ${formatStr(tugas.amanah)}\n\n`;
        pesan += `*l. Petugas Laporan : ${formatStr(tugas.petugas_laporan).toUpperCase()}*\n`;
    } else {
        pesan += "*3. Pembagian Tugas :*\n(Data belum diisi)\n\n";
    }

    pesan += "\nDemikian Laporan ini, kami sampaikan dan diucapkan terima kasih.\n\n";
    pesan += "Wassalamu'alaikum Warahmatullaahi wabarakaatuh\n";
    pesan += "Salam Sejahtera\n";
    pesan += "Salam Sehat Selalu…🙏\n\n";

    // 4. Link detail laporan
    pesan += "*Link Detail Laporan (Website):*\n";
    pesan += `${window.location.origin}/astekpam/${data.id}\n`;

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
        await navigator.clipboard.writeText(teks);
        alert('Teks berhasil disalin! Silakan "Paste/Tempel" pesan tersebut di Grup WhatsApp.');
        const linkGrupWA = '[https://chat.whatsapp.com/CehSunQDnfiFmrNOVJy3CK](https://chat.whatsapp.com/CehSunQDnfiFmrNOVJy3CK)'; 
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
        <div class="py-6 sm:py-8 bg-slate-50 min-h-screen relative overflow-x-hidden">
            
            <div class="absolute top-0 inset-x-0 h-48 bg-gradient-to-b from-indigo-100/40 to-transparent pointer-events-none"></div>

            <div class="w-full max-w-3xl mx-auto px-4 sm:px-6 space-y-4 sm:space-y-6 relative z-10">
                
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-indigo-100 text-indigo-700 text-[10px] font-bold mb-1.5 uppercase tracking-widest">
                            <FileText class="w-3.5 h-3.5" /> Arsip Laporan
                        </div>
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Riwayat Astekpam</h1>
                        <p class="text-slate-500 text-xs sm:text-sm font-medium mt-1">Kelola dan pantau seluruh riwayat pengamanan.</p>
                    </div>
                    <Link v-if="!hasRole('pejabat')" :href="route('astekpam.create')" class="w-full sm:w-auto shrink-0">
                        <Button class="w-full sm:w-auto rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold shadow-md shadow-indigo-200 h-10 px-4 transition-all active:scale-95 text-xs sm:text-sm">
                            <Plus class="w-4 h-4 mr-1.5" /> Buat Laporan Baru
                        </Button>
                    </Link>
                </div>

                <Card class="rounded-2xl border border-slate-200 shadow-sm bg-white p-3 sm:p-4 w-full relative z-20">
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-3">
                        
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 w-full md:w-auto">
                            
                            <div class="grid grid-cols-2 sm:flex sm:flex-row flex-wrap gap-2 w-full sm:w-auto">
                                <Select v-model="filterRegu">
                                    <SelectTrigger class="h-9 rounded-lg bg-slate-50 border-slate-200 focus:ring-2 focus:ring-indigo-500 w-full sm:w-32 text-xs font-bold text-slate-700 transition-all">
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

                                <div class="col-span-2 sm:col-span-1 flex items-center gap-2 w-full sm:w-auto relative group">
                                    <Input 
                                        type="date" 
                                        v-model="filterStartDate" 
                                        class="h-9 rounded-lg bg-slate-50 border-slate-200 focus:ring-2 focus:ring-indigo-500 w-full sm:w-36 text-xs font-bold px-3 text-slate-700 pr-8 transition-all"
                                        title="Tanggal Mulai"
                                    />
                                    <Button v-if="filterStartDate" @click="filterStartDate = ''" variant="ghost" size="icon" class="absolute right-1 w-6 h-6 text-slate-400 hover:text-rose-500 rounded-md hover:bg-rose-50">
                                        <X class="w-3.5 h-3.5" />
                                    </Button>
                                </div>

                                <span class="hidden sm:flex text-slate-300 font-bold items-center">
                                    <ArrowRight class="w-3.5 h-3.5" />
                                </span>

                                <div class="col-span-2 sm:col-span-1 flex items-center gap-2 w-full sm:w-auto relative group">
                                    <Input 
                                        type="date" 
                                        v-model="filterEndDate" 
                                        class="h-9 rounded-lg bg-slate-50 border-slate-200 focus:ring-2 focus:ring-indigo-500 w-full sm:w-36 text-xs font-bold px-3 text-slate-700 pr-8 transition-all"
                                        title="Tanggal Selesai"
                                    />
                                    <Button v-if="filterEndDate" @click="filterEndDate = ''" variant="ghost" size="icon" class="absolute right-1 w-6 h-6 text-slate-400 hover:text-rose-500 rounded-md hover:bg-rose-50">
                                        <X class="w-3.5 h-3.5" />
                                    </Button>
                                </div>
                            </div>
                        </div>

                        <div v-if="hasRole('admin')" class="w-full md:w-auto mt-1 md:mt-0 pt-2 md:pt-0 border-t md:border-t-0 border-slate-100 shrink-0">                            
                            <Button @click="downloadPDF" variant="outline" class="w-full md:w-auto text-rose-700 bg-rose-50 border-rose-200 hover:bg-rose-100 hover:text-rose-800 h-9 text-xs font-bold rounded-lg flex items-center justify-center transition-all shadow-sm active:scale-95">
                                <Download class="w-3.5 h-3.5 mr-1.5" /> Cetak PDF
                            </Button>
                        </div>
                    </div>
                </Card>

                <div v-if="paginatedAstekpams.length > 0" class="flex flex-col gap-4 sm:gap-5 pt-1">
                    
                    <div v-for="laporan in paginatedAstekpams" :key="laporan.id" class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-indigo-200 transition-all duration-300 overflow-hidden flex flex-col group relative">
                        
                        <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between gap-2">
                            <span class="font-bold text-slate-800 flex items-center gap-1.5 text-xs sm:text-sm">
                                <Calendar class="w-4 h-4 text-indigo-500 shrink-0" />
                                {{ formatVal(laporan.tanggal) }}
                            </span>
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-white border border-slate-200 text-slate-600 text-[10px] sm:text-xs font-bold shadow-sm">
                                <Clock class="w-3 h-3 text-slate-400" /> Pukul {{ formatVal(laporan.pukul) }}
                            </span>
                        </div>

                        <div class="p-4 flex-1 space-y-3.5 sm:space-y-4">
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                                
                                <div class="w-full bg-slate-50 p-2.5 rounded-xl border border-slate-100 flex flex-col justify-center">
                                    <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider block mb-1 text-center">Serah Terima Regu</span>
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="text-center min-w-0 flex-1">
                                            <p class="text-xs font-bold text-slate-700 uppercase truncate">{{ formatVal(laporan.dari_rupam) }}</p>
                                            <p class="text-[9px] font-bold text-slate-500 truncate">Shift {{ formatVal(laporan.dari_shift) }}</p>
                                        </div>
                                        <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-500 flex items-center justify-center shrink-0 shadow-inner group-hover:scale-110 transition-transform">
                                            <ArrowRight class="w-3.5 h-3.5" />
                                        </div>
                                        <div class="text-center min-w-0 flex-1">
                                            <p class="text-xs font-bold text-indigo-700 uppercase truncate">{{ formatVal(laporan.ke_rupam) }}</p>
                                            <p class="text-[9px] font-bold text-indigo-500 truncate">Shift {{ formatVal(laporan.ke_shift) }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col justify-center space-y-2.5">
                                    <div class="flex items-start gap-2">
                                        <ShieldCheck class="w-4 h-4 text-indigo-400 shrink-0 mt-0.5"/> 
                                        <div class="min-w-0">
                                            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider block leading-none mb-0.5">Pimpinan Apel</span>
                                            <span class="font-bold text-slate-800 text-xs truncate block">{{ formatVal(laporan.pimpinan) }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <User class="w-4 h-4 text-amber-500 shrink-0 mt-0.5"/> 
                                        <div class="min-w-0">
                                            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider block leading-none mb-0.5">Petugas Pelapor</span>
                                            <span class="font-bold text-slate-800 text-xs truncate block">{{ getPetugasPelapor(laporan) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-if="laporan.foto_laporan" class="w-full relative group/foto rounded-xl overflow-hidden shadow-sm border border-slate-200 bg-slate-100">
                                <span class="absolute top-2 left-2 z-10 bg-black/60 text-white text-[9px] font-bold px-2 py-1 rounded backdrop-blur-sm flex items-center gap-1">
                                    <ImageIcon class="w-3 h-3" /> Lampiran
                                </span>
                                <a :href="getImageUrl(laporan.foto_laporan)" target="_blank" class="block w-full h-24 sm:h-32 flex items-center justify-center p-2">
                                    <img :src="getImageUrl(laporan.foto_laporan)" class="max-w-full max-h-full object-contain rounded-md group-hover/foto:scale-105 transition-transform duration-500" alt="Foto Laporan" />
                                </a>
                            </div>

                        </div>

                        <div class="p-3 border-t border-slate-100 bg-slate-50/30">
                            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                                
                                <Link :href="route('astekpam.show', laporan.id)" class="flex-1 min-w-[70px]">
                                    <Button variant="outline" class="w-full text-indigo-700 bg-white border-indigo-100 hover:bg-indigo-50 hover:border-indigo-300 font-bold text-[10px] sm:text-xs h-9 rounded-lg shadow-sm">
                                        <Eye class="w-3.5 h-3.5 sm:mr-1.5 shrink-0" /> <span class="hidden sm:inline">Detail</span>
                                    </Button>
                                </Link>
                                
                                <Link v-if="hasRole('admin')" :href="route('admin.astekpam.edit', laporan.id)" class="flex-1 min-w-[70px]">
                                    <Button variant="outline" class="w-full text-amber-700 bg-amber-50 border-amber-100 hover:bg-amber-100 hover:border-amber-300 font-bold text-[10px] sm:text-xs h-9 rounded-lg shadow-sm">
                                        <Edit class="w-3.5 h-3.5 sm:mr-1.5 shrink-0" /> <span class="hidden sm:inline">Edit</span>
                                    </Button>
                                </Link>

                                <Button @click="copyTeksLaporan(laporan)" variant="outline" class="flex-1 min-w-[70px] text-slate-700 bg-white border-slate-200 hover:bg-slate-100 font-bold text-[10px] sm:text-xs h-9 rounded-lg shadow-sm">
                                    <Copy class="w-3.5 h-3.5 sm:mr-1.5 shrink-0" /> <span class="hidden sm:inline">Copy</span>
                                </Button>

                                <Button @click="shareKeWhatsAppGroup(laporan)" class="flex-1 min-w-[70px] bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-[10px] sm:text-xs h-9 rounded-lg shadow-sm border-0">
                                    <MessageCircle class="w-3.5 h-3.5 sm:mr-1.5 shrink-0" /> <span class="hidden sm:inline">WA</span>
                                </Button>
                                
                                <!-- Tombol Hapus Khusus Admin -->
<button 
    v-if="hasRole('admin')"
    @click="hapusLaporan(laporan.id)" 
    title="Hapus Laporan"
    class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-rose-500 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-100 transition-colors"
>
    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M3 6h18"></path>
        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
        <line x1="10" y1="11" x2="10" y2="17"></line>
        <line x1="14" y1="11" x2="14" y2="17"></line>
    </svg>
</button>
                            </div>
                        </div>

                    </div>
                </div>

                <div v-if="paginatedAstekpams.length === 0" class="bg-white border border-slate-200 shadow-sm rounded-2xl p-10 sm:p-14 text-center w-full">
                    <div class="bg-indigo-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3">
                        <FileText class="w-8 h-8 text-indigo-300" />
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Laporan Tidak Ditemukan</h3>
                    <p class="text-slate-500 mt-1 text-xs max-w-sm mx-auto">
                        Sistem tidak menemukan data laporan yang cocok. Sesuaikan filter regu atau rentang tanggal.
                    </p>
                </div>

                <div v-if="totalPages > 1" class="flex flex-col sm:flex-row items-center justify-between gap-3 p-4 bg-white border border-slate-200 shadow-sm rounded-2xl mt-6 w-full">
                    <span class="text-[10px] sm:text-xs text-slate-500 font-bold tracking-wide text-center sm:text-left">
                        Menampilkan <span class="text-indigo-600">{{ ((currentPage - 1) * itemsPerPage) + 1 }} - {{ Math.min(currentPage * itemsPerPage, filteredAstekpams.length) }}</span> 
                        dari <span class="text-slate-800">{{ filteredAstekpams.length }}</span> Laporan
                    </span>
                    
                    <div class="flex items-center gap-1.5">
                        <Button @click="prevPage" :disabled="currentPage === 1" variant="outline" class="h-9 px-3 rounded-lg border-slate-200 text-slate-700 text-[10px] sm:text-xs font-bold flex items-center hover:bg-slate-50 bg-white shadow-sm transition-all active:scale-95">
                            <ChevronLeft class="w-3.5 h-3.5 mr-1" /> Prev
                        </Button>
                        
                        <div class="flex items-center px-3 text-[10px] sm:text-xs font-extrabold text-slate-700 bg-slate-50 h-9 rounded-lg border border-slate-200 shadow-inner">
                            {{ currentPage }} / {{ totalPages }}
                        </div>

                        <Button @click="nextPage" :disabled="currentPage === totalPages" variant="outline" class="h-9 px-3 rounded-lg border-slate-200 text-slate-700 text-[10px] sm:text-xs font-bold flex items-center hover:bg-slate-50 bg-white shadow-sm transition-all active:scale-95">
                            Next <ChevronRight class="w-3.5 h-3.5 ml-1" />
                        </Button>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* CSS styles (Kosong atau sesuai kebutuhan Anda) */
</style>