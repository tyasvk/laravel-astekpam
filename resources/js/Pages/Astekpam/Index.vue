<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
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

// Import library PDF
import jsPDF from 'jspdf';
import autoTable from 'jspdf-autotable';

const props = defineProps({
    astekpams: Array
});

// Cek hak akses (Role) User yang sedang login
const { hasRole } = usePermission();

// State untuk Filter & Pagination
const filterRegu = ref('all');
const filterStartDate = ref(''); 
const filterEndDate = ref('');  
const currentPage = ref(1);
const itemsPerPage = 10; 

// Reset pagination ke halaman 1 setiap kali filter diubah
watch([filterRegu, filterStartDate, filterEndDate], () => {
    currentPage.value = 1;
});

// Logika Penyaringan (Filter) Otomatis
const filteredAstekpams = computed(() => {
    let result = props.astekpams || [];
    
    // Filter Regu
    if (filterRegu.value !== 'all') {
        result = result.filter(item => {
            const regu = item.ke_rupam || ''; 
            return regu === `Rupam ${filterRegu.value}` || regu === filterRegu.value;
        });
    }

    // Filter Rentang Tanggal
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

// Logika Pagination (Memotong array sesuai halaman saat ini)
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

// Fungsi aman untuk memanggil path Foto (Mencegah double slash)
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

        const tableColumn = [
            "Waktu & Shift", 
            "Pimpinan & Pelapor", 
            "Rincian WBP", 
            "Kekuatan Pengamanan", 
            "Pembagian Tugas Lengkap",
            "Foto Laporan"
        ];

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
            styles: {
                fontSize: 7.5,
                cellPadding: 3,
                valign: 'top',
                overflow: 'linebreak'
            },
            headStyles: {
                fillColor: [30, 41, 59],
                textColor: [255, 255, 255],
                fontSize: 8,
                fontStyle: 'bold',
                halign: 'center'
            },
            columnStyles: {
                0: { cellWidth: 28 }, 
                1: { cellWidth: 28 }, 
                2: { cellWidth: 43 }, 
                3: { cellWidth: 43 }, 
                4: { cellWidth: 'auto' }, 
                5: { cellWidth: 35, halign: 'center', valign: 'middle' } 
            },
            alternateRowStyles: {
                fillColor: [248, 250, 252]
            },
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
// LOGIKA UNTUK COPY TEKS LAPORAN (WHATSAPP)
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
    
    const tglNum = dateObj.getDate();
    const tanggalIndo = `${days[dateObj.getDay()]}, ${tglNum} ${months[dateObj.getMonth()]} ${dateObj.getFullYear()}`;
    const salamWaktu = getGreeting(data.pukul);

    const formatOrg = (val) => (val && val !== '0' && val !== 0) ? `${val} Org` : '0 Org';
    const formatStr = (val) => (val && val !== '-') ? val : '-';

    let pesan = "";

    if (data.foto_laporan) {
        pesan += `${window.location.origin}/storage/${data.foto_laporan}\n\n`;
    }

    pesan += "ASTEKPAM LAPAS KELAS I PALEMBANG\n";
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
        await navigator.clipboard.writeText(teks);
        alert('Teks berhasil disalin! Silakan "Paste/Tempel" pesan tersebut di Grup WhatsApp.');

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
        <div class="py-6 sm:py-10 bg-slate-50 min-h-screen relative">
            
            <!-- Dekorasi Latar Belakang -->
            <div class="absolute top-0 inset-x-0 h-64 bg-gradient-to-b from-indigo-100/50 to-transparent pointer-events-none"></div>

            <!-- LEBAR PENUH: px-2 memberi gap kecil ke sidebar dan kanan -->
            <div class="w-full mx-auto px-2 lg:px-2 space-y-6 relative z-10">
                
                <!-- HEADER & CREATE BUTTON -->
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-2 px-2">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-indigo-100 text-indigo-700 text-xs font-bold mb-3 uppercase tracking-widest">
                            <FileText class="w-3.5 h-3.5" /> Arsip Laporan
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Riwayat Astekpam</h1>
                        <p class="text-slate-500 text-sm font-medium mt-2">Kelola dan pantau seluruh riwayat serah terima regu pengamanan.</p>
                    </div>
                    <Link :href="route('astekpam.create')" class="w-full sm:w-auto shrink-0">
                        <Button class="w-full sm:w-auto rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold shadow-lg shadow-indigo-200 h-12 px-6 transition-all hover:scale-105 active:scale-95">
                            <Plus class="w-5 h-5 mr-2" /> Buat Laporan Baru
                        </Button>
                    </Link>
                </div>

                <!-- FILTER CARD -->
                <Card class="rounded-2xl sm:rounded-3xl border-0 shadow-md shadow-slate-200/50 bg-white p-4 sm:p-6 w-full">
                    <div class="flex flex-col xl:flex-row items-start xl:items-center justify-between gap-4">
                        
                        <div class="flex flex-col md:flex-row items-start md:items-center gap-4 w-full xl:w-auto">
                            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-wider shrink-0 bg-slate-50 px-3 py-2 rounded-lg border border-slate-100">
                                <Filter class="w-4 h-4 text-indigo-500" />
                                <span>Filter Data</span>
                            </div>
                            
                            <div class="flex flex-col sm:flex-row flex-wrap gap-3 w-full xl:w-auto">
                                <Select v-model="filterRegu">
                                    <SelectTrigger class="h-11 rounded-xl bg-slate-50 border-slate-200 focus:ring-2 focus:ring-indigo-500 w-full sm:w-40 text-sm font-bold text-slate-700">
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

                                <div class="flex items-center gap-2 w-full sm:w-auto relative group">
                                    <Input 
                                        type="date" 
                                        v-model="filterStartDate" 
                                        class="h-11 rounded-xl bg-slate-50 border-slate-200 focus:ring-2 focus:ring-indigo-500 w-full sm:w-40 text-sm font-bold px-3 text-slate-700 pr-10"
                                        title="Tanggal Mulai"
                                    />
                                    <Button v-if="filterStartDate" @click="filterStartDate = ''" variant="ghost" size="icon" class="absolute right-1 w-8 h-8 text-slate-400 hover:text-rose-500 rounded-lg hover:bg-rose-50" title="Hapus Tanggal Mulai">
                                        <X class="w-4 h-4" />
                                    </Button>
                                </div>

                                <span class="hidden sm:flex text-slate-300 font-bold items-center">
                                    <ArrowRight class="w-4 h-4" />
                                </span>

                                <div class="flex items-center gap-2 w-full sm:w-auto relative group">
                                    <Input 
                                        type="date" 
                                        v-model="filterEndDate" 
                                        class="h-11 rounded-xl bg-slate-50 border-slate-200 focus:ring-2 focus:ring-indigo-500 w-full sm:w-40 text-sm font-bold px-3 text-slate-700 pr-10"
                                        title="Tanggal Selesai"
                                    />
                                    <Button v-if="filterEndDate" @click="filterEndDate = ''" variant="ghost" size="icon" class="absolute right-1 w-8 h-8 text-slate-400 hover:text-rose-500 rounded-lg hover:bg-rose-50" title="Hapus Tanggal Selesai">
                                        <X class="w-4 h-4" />
                                    </Button>
                                </div>
                            </div>
                        </div>

                        <div v-if="hasRole('admin')" class="w-full xl:w-auto mt-2 xl:mt-0 pt-4 xl:pt-0 border-t xl:border-t-0 border-slate-100 flex flex-col sm:flex-row gap-2">                            
                            <Button @click="downloadPDF" variant="outline" class="w-full sm:w-auto text-rose-700 bg-rose-50 border-rose-200 hover:bg-rose-100 hover:text-rose-800 h-11 text-sm font-bold rounded-xl flex items-center justify-center transition-all shadow-sm active:scale-95">
                                <Download class="w-4 h-4 mr-2" /> Cetak PDF
                            </Button>
                        </div>
                    </div>
                </Card>

                <!-- DATA TABLE & MOBILE LIST -->
                <Card class="rounded-2xl sm:rounded-3xl border-0 shadow-md shadow-slate-200/50 bg-white overflow-hidden w-full">
                    <CardContent class="p-0 w-full">
                        
                        <!-- VIEW MOBILE (Tumpukan Kartu) -->
                        <div class="block lg:hidden divide-y divide-slate-100">
                            <div v-for="laporan in paginatedAstekpams" :key="laporan.id" class="p-5 space-y-4 hover:bg-slate-50 transition-colors">
                                
                                <div class="flex flex-col">
                                    <span class="font-bold text-slate-900 flex items-center gap-2 text-sm whitespace-normal break-words">
                                        <div class="p-1.5 bg-indigo-50 text-indigo-500 rounded-lg shrink-0"><Calendar class="w-4 h-4" /></div>
                                        {{ formatVal(laporan.tanggal) }}
                                    </span>
                                    <span class="text-xs font-semibold text-slate-500 flex items-center gap-2 mt-2 whitespace-normal break-words">
                                        <div class="p-1.5 bg-slate-100 text-slate-500 rounded-lg shrink-0"><Clock class="w-3.5 h-3.5" /></div>
                                        Pukul {{ formatVal(laporan.pukul) }}
                                    </span>
                                </div>

                                <div class="bg-slate-50/50 p-4 rounded-2xl border border-slate-100 space-y-4 w-full">
                                    
                                    <!-- Flow Serah Terima Mobile -->
                                    <div class="flex flex-col gap-2 w-full">
                                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-1">Serah Terima Regu</span>
                                        <div class="grid grid-cols-[1fr_auto_1fr] gap-2 items-center">
                                            <div class="flex flex-col items-center bg-white p-2 rounded-xl border border-slate-200 shadow-sm text-center w-full">
                                                <span class="text-xs font-bold text-slate-700 uppercase break-words whitespace-normal">{{ formatVal(laporan.dari_rupam) }}</span>
                                                <span class="text-[10px] font-semibold text-slate-500 break-words whitespace-normal">{{ formatVal(laporan.dari_shift) }}</span>
                                            </div>
                                            <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-500 flex items-center justify-center shrink-0">
                                                <ArrowRight class="w-3.5 h-3.5" />
                                            </div>
                                            <div class="flex flex-col items-center bg-indigo-50 p-2 rounded-xl border border-indigo-100 shadow-sm text-center w-full">
                                                <span class="text-xs font-bold text-indigo-700 uppercase break-words whitespace-normal">{{ formatVal(laporan.ke_rupam) }}</span>
                                                <span class="text-[10px] font-semibold text-indigo-500 break-words whitespace-normal">{{ formatVal(laporan.ke_shift) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4 border-t border-slate-200/60 pt-4 w-full">
                                        <div>
                                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-1">Pimpinan Apel</span>
                                            <span class="font-bold text-slate-800 flex items-start gap-1.5 text-xs whitespace-normal break-words leading-snug">
                                                <ShieldCheck class="w-3.5 h-3.5 text-indigo-400 shrink-0 mt-0.5"/> 
                                                <span>{{ formatVal(laporan.pimpinan) }}</span>
                                            </span>
                                        </div>
                                        <div>
                                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-1">Pelapor</span>
                                            <span class="font-bold text-slate-800 flex items-start gap-1.5 text-xs whitespace-normal break-words leading-snug">
                                                <User class="w-3.5 h-3.5 text-amber-500 shrink-0 mt-0.5"/> 
                                                <span>{{ getPetugasPelapor(laporan) }}</span>
                                            </span>
                                        </div>
                                    </div>

                                    <div v-if="laporan.foto_laporan" class="border-t border-slate-200/60 pt-4 w-full">
                                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-2 flex items-center gap-1.5">
                                            <ImageIcon class="w-3.5 h-3.5" /> Foto Lampiran
                                        </span>
                                        <a :href="getImageUrl(laporan.foto_laporan)" target="_blank" class="block w-full rounded-xl overflow-hidden shadow-sm border border-slate-200">
                                            <img :src="getImageUrl(laporan.foto_laporan)" class="w-full h-32 object-cover hover:opacity-90 hover:scale-105 transition-all duration-500" alt="Foto Laporan" />
                                        </a>
                                    </div>
                                </div>

                                <!-- Action Buttons Mobile -->
                                <div class="grid grid-cols-2 gap-2 w-full pt-1">
                                    <Link :href="route('astekpam.show', laporan.id)" class="w-full block">
                                        <Button variant="outline" class="w-full text-indigo-600 bg-white border-slate-200 hover:bg-indigo-50 hover:border-indigo-200 font-bold text-xs h-11 rounded-xl shadow-sm">
                                            <Eye class="w-4 h-4 mr-1.5 shrink-0" /> Detail
                                        </Button>
                                    </Link>
                                    
                                    <Link v-if="hasRole('admin')" :href="route('admin.astekpam.edit', laporan.id)" class="w-full block">
                                        <Button variant="outline" class="w-full text-amber-600 bg-white border-slate-200 hover:bg-amber-50 hover:border-amber-200 font-bold text-xs h-11 rounded-xl shadow-sm">
                                            <Edit class="w-4 h-4 mr-1.5 shrink-0" /> Edit
                                        </Button>
                                    </Link>
                                    
                                    <Button @click="copyTeksLaporan(laporan)" variant="outline" class="w-full text-slate-600 bg-white border-slate-200 hover:bg-slate-50 font-bold text-xs h-11 rounded-xl shadow-sm">
                                        <Copy class="w-4 h-4 mr-1.5 shrink-0" /> Copy Teks
                                    </Button>

                                    <Button @click="shareKeWhatsAppGroup(laporan)" class="w-full text-white bg-emerald-500 border-0 hover:bg-emerald-600 font-bold text-xs h-11 rounded-xl shadow-md shadow-emerald-500/20 active:scale-95 transition-all">
                                        <MessageCircle class="w-4 h-4 mr-1.5 shrink-0" /> Share WA
                                    </Button>
                                </div>

                            </div>
                            
                            <!-- Empty State Mobile -->
                            <div v-if="paginatedAstekpams.length === 0" class="text-center py-16 px-4">
                                <div class="bg-indigo-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <FileText class="w-10 h-10 text-indigo-300" />
                                </div>
                                <h3 class="text-lg font-bold text-slate-800">Tidak ada laporan</h3>
                                <p class="text-sm font-medium text-slate-500 mt-1">Coba sesuaikan filter atau rentang tanggal.</p>
                            </div>
                        </div>

                        <!-- VIEW DESKTOP (Tabel table-fixed) -->
                        <div class="hidden lg:block w-full overflow-hidden">
                            <table class="w-full text-left border-collapse table-fixed">
                                <thead>
                                    <!-- Menyesuaikan lebar kolom Tanggal agar teks dijamin tidak terpotong -->
                                    <tr class="bg-slate-50/80 border-b border-slate-100 text-slate-400 font-extrabold text-[11px] tracking-widest uppercase">
                                        <th class="py-4 px-3 w-[20%]">Tanggal & Waktu</th>
                                        <th class="py-4 px-3 text-center w-[22%]">Serah Terima</th>
                                        <th class="py-4 px-3 w-[16%]">Pimpinan Apel</th>
                                        <th class="py-4 px-3 w-[16%]">Pelapor</th>
                                        <th class="py-4 px-3 text-center w-[8%]">Foto</th>
                                        <th class="py-4 px-3 text-center w-[18%]">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-sm font-semibold text-slate-800">
                                    <tr v-for="laporan in paginatedAstekpams" :key="laporan.id" class="hover:bg-indigo-50/30 transition-colors align-top group">
                                        
                                        <!-- Kolom 1: Tanggal & Waktu (Tanpa break-words, agar tidak memotong kata di tengah-tengah) -->
                                        <td class="py-5 px-3 align-middle">
                                            <div class="flex flex-col gap-1.5">
                                                <span class="text-slate-900 flex items-start gap-2 whitespace-normal leading-snug">
                                                    <Calendar class="w-4 h-4 text-indigo-400 shrink-0 mt-0.5"/> 
                                                    <span class="font-bold">{{ formatVal(laporan.tanggal) }}</span>
                                                </span>
                                                <span class="text-xs text-slate-500 flex items-start gap-2 whitespace-normal leading-snug">
                                                    <Clock class="w-3.5 h-3.5 text-slate-400 shrink-0 mt-0.5"/> 
                                                    <span class="font-medium">Pk. {{ formatVal(laporan.pukul) }}</span>
                                                </span>
                                            </div>
                                        </td>
                                        
                                        <!-- Kolom 2: Serah Terima -->
                                        <td class="py-5 px-3 align-middle">
                                            <div class="flex items-center justify-center gap-1.5 w-full">
                                                <div class="flex flex-col items-center bg-white px-2 py-2 rounded-xl border border-slate-200 shadow-sm flex-1 text-center w-full min-w-0">
                                                    <span class="text-[11px] font-bold text-slate-700 uppercase break-words w-full">{{ formatVal(laporan.dari_rupam) }}</span>
                                                    <span class="text-[9px] font-bold text-slate-500 break-words w-full">{{ formatVal(laporan.dari_shift) }}</span>
                                                </div>
                                                <div class="w-5 h-5 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center shrink-0">
                                                    <ArrowRight class="w-3 h-3" />
                                                </div>
                                                <div class="flex flex-col items-center bg-indigo-50 px-2 py-2 rounded-xl border border-indigo-100 shadow-sm flex-1 text-center w-full min-w-0">
                                                    <span class="text-[11px] font-bold text-indigo-700 uppercase break-words w-full">{{ formatVal(laporan.ke_rupam) }}</span>
                                                    <span class="text-[9px] font-bold text-indigo-500 break-words w-full">{{ formatVal(laporan.ke_shift) }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <!-- Kolom 3: Pimpinan -->
                                        <td class="py-5 px-3 align-middle">
                                            <div class="flex items-start gap-1.5">
                                                <ShieldCheck class="w-4 h-4 text-slate-400 mt-0.5 shrink-0" />
                                                <span class="whitespace-normal leading-snug">{{ formatVal(laporan.pimpinan) }}</span>
                                            </div>
                                        </td>
                                        
                                        <!-- Kolom 4: Pelapor -->
                                        <td class="py-5 px-3 align-middle">
                                            <div class="flex items-start gap-1.5">
                                                <User class="w-4 h-4 text-amber-500 mt-0.5 shrink-0" />
                                                <span class="whitespace-normal leading-snug">{{ getPetugasPelapor(laporan) }}</span>
                                            </div>
                                        </td>

                                        <!-- Kolom 5: Foto -->
                                        <td class="py-5 px-3 text-center align-middle">
                                            <div v-if="laporan.foto_laporan" class="flex justify-center">
                                                <a :href="getImageUrl(laporan.foto_laporan)" target="_blank" title="Klik untuk memperbesar">
                                                    <img :src="getImageUrl(laporan.foto_laporan)" alt="Foto Laporan" class="w-10 h-10 object-cover rounded-lg border border-slate-200 shadow-sm hover:opacity-80 transition-all cursor-pointer hover:scale-110" />
                                                </a>
                                            </div>
                                            <div v-else class="text-slate-300 text-[11px] italic font-medium whitespace-normal break-words">
                                                Tidak Ada
                                            </div>
                                        </td>
                                        
                                        <!-- Kolom 6: Aksi -->
                                        <td class="py-5 px-3 align-middle">
                                            <div class="grid grid-cols-2 gap-2 w-full mx-auto opacity-80 group-hover:opacity-100 transition-opacity">
                                                <Link :href="route('astekpam.show', laporan.id)" class="w-full">
                                                    <Button variant="outline" class="w-full text-indigo-600 bg-white border-slate-200 hover:bg-indigo-50 hover:border-indigo-200 font-bold text-[10px] h-8 px-1 rounded-lg shadow-sm">
                                                        <Eye class="w-3.5 h-3.5 mr-1 shrink-0" /> Detail
                                                    </Button>
                                                </Link>
                                                
                                                <Link v-if="hasRole('admin')" :href="route('admin.astekpam.edit', laporan.id)" class="w-full">
                                                    <Button variant="outline" class="w-full text-amber-600 bg-white border-slate-200 hover:bg-amber-50 hover:border-amber-200 font-bold text-[10px] h-8 px-1 rounded-lg shadow-sm">
                                                        <Edit class="w-3.5 h-3.5 mr-1 shrink-0" /> Edit
                                                    </Button>
                                                </Link>

                                                <Button @click="copyTeksLaporan(laporan)" variant="outline" class="w-full text-slate-600 bg-white border-slate-200 hover:bg-slate-50 font-bold text-[10px] h-8 px-1 rounded-lg shadow-sm">
                                                    <Copy class="w-3.5 h-3.5 mr-1 shrink-0" /> Copy
                                                </Button>

                                                <Button @click="shareKeWhatsAppGroup(laporan)" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-[10px] h-8 px-1 rounded-lg shadow-sm active:scale-95 transition-transform border-0">
                                                    <MessageCircle class="w-3.5 h-3.5 mr-1 shrink-0" /> WA
                                                </Button>
                                            </div>
                                        </td>
                                    </tr>
                                    
                                    <!-- Empty State Desktop -->
                                    <tr v-if="paginatedAstekpams.length === 0">
                                        <td colspan="6" class="text-center py-20">
                                            <div class="flex flex-col items-center justify-center">
                                                <div class="bg-indigo-50 w-16 h-16 rounded-full flex items-center justify-center mb-4">
                                                    <FileText class="w-8 h-8 text-indigo-300" />
                                                </div>
                                                <h3 class="text-base font-bold text-slate-800">Laporan Tidak Ditemukan</h3>
                                                <p class="text-sm font-medium text-slate-500 mt-1">Coba sesuaikan filter atau rentang tanggal.</p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- PAGINATION -->
                        <div v-if="totalPages > 1" class="flex flex-col sm:flex-row items-center justify-between gap-4 p-5 border-t border-slate-100 bg-slate-50/50">
                            <span class="text-xs text-slate-500 font-bold tracking-wide">
                                Menampilkan {{ ((currentPage - 1) * itemsPerPage) + 1 }} - 
                                {{ Math.min(currentPage * itemsPerPage, filteredAstekpams.length) }} 
                                dari {{ filteredAstekpams.length }} Laporan
                            </span>
                            
                            <div class="flex items-center gap-2">
                                <Button @click="prevPage" :disabled="currentPage === 1" variant="outline" class="h-10 px-4 rounded-xl border-slate-200 text-slate-600 text-xs font-bold flex items-center hover:bg-slate-100 bg-white shadow-sm">
                                    <ChevronLeft class="w-4 h-4 mr-1" /> Prev
                                </Button>
                                
                                <div class="flex items-center px-4 text-xs font-extrabold text-slate-700 bg-white h-10 rounded-xl border border-slate-200 shadow-sm">
                                    Hal {{ currentPage }} / {{ totalPages }}
                                </div>

                                <Button @click="nextPage" :disabled="currentPage === totalPages" variant="outline" class="h-10 px-4 rounded-xl border-slate-200 text-slate-600 text-xs font-bold flex items-center hover:bg-slate-100 bg-white shadow-sm">
                                    Next <ChevronRight class="w-4 h-4 ml-1" />
                                </Button>
                            </div>
                        </div>

                    </CardContent>
                </Card>

            </div>
        </div>
    </AuthenticatedLayout>
</template>