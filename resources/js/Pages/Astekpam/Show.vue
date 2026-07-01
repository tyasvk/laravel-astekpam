<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Card } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
// [TAMBAHAN] Import ikon Edit
import { ChevronLeft, Printer, Copy, Check, Image as ImageIcon, Edit } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const props = defineProps({
    astekpam: Object
});

const isCopied = ref(false);

const displayHari = computed(() => {
    if (!props.astekpam.tanggal) return '';
    return new Intl.DateTimeFormat('id-ID', { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    }).format(new Date(props.astekpam.tanggal));
});

const salamWaktu = computed(() => {
    const pukul = props.astekpam.pukul || '';
    if (pukul.includes('07.30-13.00')) return 'Pagi';
    if (pukul.includes('13.00-19.30')) return 'Siang';
    if (pukul.includes('19.30-07.30')) return 'Malam';
    return 'Pagi';
});

const formatOrg = (val) => {
    return (val !== null && val !== undefined && val !== '') ? `${val} Org` : '-';
};

// Fungsi untuk membersihkan dan menampilkan nama petugas saja tanpa format jam
const formatNamaTugas = (dataTugas) => {
    if (!dataTugas) return '-';
    if (typeof dataTugas === 'string') return dataTugas;
    
    const jams = [dataTugas.jam_1, dataTugas.jam_2, dataTugas.jam_3].filter(j => j && j !== '-');
    const namaUnik = [...new Set(jams)];
    return namaUnik.length > 0 ? namaUnik.join(', ') : '-';
};

// Fungsi baru untuk memformat data array Keterangan Luar Lapas (Rawat Inap, Berobat, Bon Luar)
const formatItems = (items) => {
    if (!items || !Array.isArray(items)) return '-';
    // Filter item yang memiliki keterangan valid dan tidak kosong
    const validItems = items.filter(item => item && item.ket && String(item.ket).trim() !== '');
    return validItems.length > 0 ? validItems.map(item => item.get ? item.get : item.ket).join(', ') : '-';
};

// FITUR COPY TEXT DENGAN SPASI PRESISI
const copyToClipboard = () => {
    const pad = (str, length = 25) => str.padEnd(length, ' ');
    
    // Tambahkan URL Foto jika ada
    const urlFoto = props.astekpam.foto_laporan ? `${window.location.origin}/storage/${props.astekpam.foto_laporan}\n\n` : '';
    
    const text = `${urlFoto}ASTEKPAM LAPAS KELAS I PALEMBANG

Assalamu’alaikum Warahmatullahi Wabarakatuh
Selamat ${salamWaktu.value}....

Hari/Tgl      : ${displayHari.value}
Pukul         : ${props.astekpam.pukul} WIB

Berikut, ASTEKPAM dari ${props.astekpam.dari_rupam} (Shift ${props.astekpam.dari_shift}) ke ${props.astekpam.ke_rupam} (Shift ${props.astekpam.ke_shift}) Dipimpin oleh ${props.astekpam.pimpinan || '-'} berjalan aman dan tertib.

Dengan rincian sebagai berikut :

A. JUMLAH PENGHUNI
${pad('1. Kapasitas')} : ${formatOrg(props.astekpam.kapasitas)}
${pad('2. Narapidana')} : ${formatOrg(props.astekpam.narapidana)}
3. Isi Blok Hunian        :
${pad('   * Blok A')} : ${formatOrg(props.astekpam.blok_a)}
${pad('   * Blok B')} : ${formatOrg(props.astekpam.blok_b)}
${pad('   * Dapur')} : ${formatOrg(props.astekpam.dapur)}
${pad('   * Klinik')} : ${formatOrg(props.astekpam.klinik)}
Jumlah                    :
${pad('   * Didalam Lapas')} : ${formatOrg(props.astekpam.dalam_lapas)}
${pad('   * Diluar Lapas')} : ${formatOrg(props.astekpam.luar_lapas)}

4. Keterangan di luar Lapas :
${pad('   * Rawat Inap RS')} : ${formatItems(props.astekpam.rawat_inap_items)}
${pad('   * Berobat RS')} : ${formatItems(props.astekpam.berobat_items)}
${pad('   * Lain-lain (bon luar)')} : ${formatItems(props.astekpam.bon_luar_items)}

${pad('5. Total Jumlah WBP')} : ${formatOrg(props.astekpam.total_wbp)}

B. PERSONIL PENGAMANAN
1. ${props.astekpam.rupam_pilihan}
${pad('   Jumlah')} : ${props.astekpam.rupam_jumlah ? props.astekpam.rupam_jumlah + ' Org' : '-'}
${pad('   Hadir')} : ${props.astekpam.rupam_hadir ? props.astekpam.rupam_hadir + ' Org' : '-'}
${pad('   Tidak Hadir')} : ${props.astekpam.rupam_tidak_hadir ? props.astekpam.rupam_tidak_hadir + ' Org' : '-'}
${pad('   Keterangan')} : ${props.astekpam.rupam_keterangan || '-'}

2. SATGAS P2U (${props.astekpam.p2u_rupam || '-'})
${pad('   Jumlah')} : ${props.astekpam.p2u_jumlah ? props.astekpam.p2u_jumlah + ' Org' : '-'}
${pad('   Hadir')} : ${props.astekpam.p2u_hadir ? props.astekpam.p2u_hadir + ' Org' : '-'}
${pad('   Keterangan')} : ${props.astekpam.p2u_keterangan || '-'}

3. Pembagian Tugas :
${pad('a. Ka. Rupam')} : ${props.astekpam.tugas.ka_rupam || '-'}
${pad('   Wakarupam')} : ${props.astekpam.tugas.wakarupam || '-'}
b. Petugas P2U            :
${pad('   Kasatgas')} : ${props.astekpam.tugas.kasatgas_p2u || '-'}
${pad('   Wakasatgas')} : ${props.astekpam.tugas.wakasatgas_p2u || '-'}
c. Petugas Blok           :
${pad('   Blok A')} : ${formatNamaTugas(props.astekpam.tugas.blok_a)}
${pad('   Blok B')} : ${formatNamaTugas(props.astekpam.tugas.blok_b)}
d. Petugas Pos Atas       :
${pad('   * Menara 1')} : ${formatNamaTugas(props.astekpam.tugas.menara_1)}
${pad('   * Menara 2')} : ${formatNamaTugas(props.astekpam.tugas.menara_2)}
${pad('   * Menara 3')} : ${formatNamaTugas(props.astekpam.tugas.menara_3)}
${pad('   * Menara 4')} : ${formatNamaTugas(props.astekpam.tugas.menara_4)}

${pad('e. Petugas Jaga RS')} : ${props.astekpam.tugas.jaga_rs || '-'}
${pad('f. Piket Dapur')} : ${props.astekpam.tugas.piket_dapur || '-'}
${pad('g. Pengawas Piket')} : ${props.astekpam.tugas.pengawas_piket || '-'}
${pad('h. Perwira Piket')} : ${props.astekpam.tugas.perwira_piket || '-'}
${pad('i. Banja')} : ${props.astekpam.tugas.banja || '-'}
${pad('j. Staff KPLP')} : ${props.astekpam.tugas.staff_kplp || '-'}
${pad('k. Amanah')} : ${props.astekpam.tugas.amanah || '-'}
${pad('l. Petugas Laporan')} : ${props.astekpam.tugas.petugas_laporan || '-'}

Demikian Laporan ini, kami sampaikan dan diucapkan terima kasih.

Wassalamu’alaikum Warahmatullaahi wabarakaatuh
Salam Sejahtera
Salam Sehat Selalu…🙏`;

    navigator.clipboard.writeText(text).then(() => {
        isCopied.value = true;
        setTimeout(() => isCopied.value = false, 2000);
    });
};

const printPage = () => window.print();
</script>

<template>
    <Head :title="'Detail Astekpam - ' + astekpam.tanggal" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between font-sans print:hidden">
                <Link :href="route('astekpam.index')">
                    <Button variant="outline" size="sm" class="rounded-full">
                        <ChevronLeft class="w-4 h-4 mr-1" /> Kembali
                    </Button>
                </Link>
                <div class="flex gap-2">
                    
                    <!-- [FITUR BARU] Tombol Edit Khusus Admin -->
                    <Link v-if="$page.props.auth.user.roles?.includes('admin')" :href="route('astekpam.edit', astekpam.id)">
                        <Button class="bg-amber-500 hover:bg-amber-600 font-sans shadow-sm text-white">
                            <Edit class="w-4 h-4 mr-2" /> Edit
                        </Button>
                    </Link>

                    <!-- Tombol Salin Teks -->
                    <Button @click="copyToClipboard" :variant="isCopied ? 'default' : 'outline'" class="font-sans transition-all">
                        <component :is="isCopied ? Check : Copy" class="w-4 h-4 mr-2" />
                        {{ isCopied ? 'Berhasil Disalin' : 'Salin Teks' }}
                    </Button>
                    
                    <!-- Tombol Cetak -->
                    <Button @click="printPage" class="bg-zinc-800 hover:bg-zinc-900 font-sans shadow-lg text-white">
                        <Printer class="w-4 h-4 mr-2" /> Cetak
                    </Button>
                </div>
            </div>
        </template>

        <div class="max-w-4xl mx-auto py-10 px-4 font-sans print:p-0 print:max-w-full">
            <Card class="border-none shadow-2xl bg-white p-8 md:p-16 rounded-xl text-zinc-900 font-sans text-[15px] leading-relaxed print:shadow-none print:p-0">
                
                <!-- TAMPILAN FOTO LAPORAN (Paling Atas) -->
                <div v-if="astekpam.foto_laporan" class="mb-8 flex flex-col items-center bg-zinc-50/80 p-4 rounded-xl border border-zinc-200 print:hidden">
                    <span class="text-xs font-bold text-zinc-500 mb-3 w-full text-center uppercase tracking-wider flex items-center justify-center gap-2">
                        <ImageIcon class="w-4 h-4" /> Lampiran Foto Bukti
                    </span>
                    <a :href="`/storage/${astekpam.foto_laporan}`" target="_blank" title="Klik untuk memperbesar">
                        <img :src="`/storage/${astekpam.foto_laporan}`" alt="Foto Laporan" class="w-full max-w-sm rounded-lg shadow-sm border border-zinc-200 object-contain hover:opacity-90 transition-opacity" />
                    </a>
                </div>
                
                <!-- JUDUL -->
                <div class="font-bold text-center mb-8 text-base uppercase font-sans">
                    ASTEKPAM LAPAS KELAS I PALEMBANG
                </div>
                
                <p>Assalamu’alaikum Warahmatullahi Wabarakatuh</p>
                <p>Selamat {{ salamWaktu }}....</p>
                <br>
                
                <div class="grid grid-cols-[180px_15px_1fr] gap-y-1 font-sans">
                    <div>Hari/Tgl</div><div>:</div><div>{{ displayHari }}</div>
                    <div>Pukul</div><div>:</div><div>{{ astekpam.pukul }} WIB</div>
                </div>
                <br>
                
                <p>Berikut, ASTEKPAM dari <span class="font-bold">{{ astekpam.dari_rupam }}</span> (Shift {{ astekpam.dari_shift }}) ke <span class="font-bold">{{ astekpam.ke_rupam }}</span> (Shift {{ astekpam.ke_shift }}) Dipimpin oleh <span class="font-bold">{{ astekpam.pimpinan || '-' }}</span> berjalan aman dan tertib.</p>
                <br>
                
                <p>Dengan rincian sebagai berikut :</p>
                
                <p class="font-bold">A. JUMLAH PENGHUNI</p>
                <div class="grid grid-cols-[180px_15px_1fr] gap-y-1 font-sans">
                    <div>1. Kapasitas</div><div>:</div><div>{{ formatOrg(astekpam.kapasitas) }}</div>
                    <div>2. Narapidana</div><div>:</div><div>{{ formatOrg(astekpam.narapidana) }}</div>
                    <div>3. Isi Blok Hunian</div><div>:</div><div></div>
                    
                    <div class="pl-5">* Blok A</div><div>:</div><div>{{ formatOrg(astekpam.blok_a) }}</div>
                    <div class="pl-5">* Blok B</div><div>:</div><div>{{ formatOrg(astekpam.blok_b) }}</div>
                    <div class="pl-5">* Dapur</div><div>:</div><div>{{ formatOrg(astekpam.dapur) }}</div>
                    <div class="pl-5">* Klinik</div><div>:</div><div>{{ formatOrg(astekpam.klinik) }}</div>
                    
                    <div>Jumlah</div><div>:</div><div></div>
                    <div class="pl-5">* Didalam Lapas</div><div>:</div><div>{{ formatOrg(astekpam.dalam_lapas) }}</div>
                    <div class="pl-5">* Diluar Lapas</div><div>:</div><div>{{ formatOrg(astekpam.luar_lapas) }}</div>
                    
                    <div class="mt-2">4. Keterangan di luar Lapas</div><div>:</div><div></div>
                    <div class="pl-5">* Rawat Inap RS</div><div>:</div><div>{{ formatItems(astekpam.rawat_inap_items) }}</div>
                    <div class="pl-5">* Berobat RS</div><div>:</div><div>{{ formatItems(astekpam.berobat_items) }}</div>
                    <div class="pl-5">* Lain-lain (bon luar)</div><div>:</div><div>{{ formatItems(astekpam.bon_luar_items) }}</div>
                    
                    <div class="font-bold mt-2">5. Total Jumlah WBP</div><div>:</div><div class="font-bold">{{ formatOrg(astekpam.total_wbp) }}</div>
                </div>
                <br>

                <p class="font-bold">B. PERSONIL PENGAMANAN</p>
                <div class="grid grid-cols-[180px_15px_1fr] gap-y-1 font-sans">
                    <div class="font-semibold">1. {{ astekpam.rupam_pilihan }}</div><div></div><div></div>
                    <div class="pl-5">Jumlah</div><div>:</div><div>{{ astekpam.rupam_jumlah ? astekpam.rupam_jumlah + ' Org' : '-' }}</div>
                    <div class="pl-5">Hadir</div><div>:</div><div>{{ astekpam.rupam_hadir ? astekpam.rupam_hadir + ' Org' : '-' }}</div>
                    <div class="pl-5">Tidak Hadir</div><div>:</div><div>{{ astekpam.rupam_tidak_hadir ? astekpam.rupam_tidak_hadir + ' Org' : '-' }}</div>
                    <div class="pl-5">Keterangan</div><div>:</div><div>{{ astekpam.rupam_keterangan || '-' }}</div>
                    
                    <div class="font-semibold mt-2">2. SATGAS P2U ({{ astekpam.p2u_rupam || '-' }})</div><div></div><div></div>
                    <div class="pl-5">Jumlah</div><div>:</div><div>{{ astekpam.p2u_jumlah ? astekpam.p2u_jumlah + ' Org' : '-' }}</div>
                    <div class="pl-5">Hadir</div><div>:</div><div>{{ astekpam.p2u_hadir ? astekpam.p2u_hadir + ' Org' : '-' }}</div>
                    <div class="pl-5">Keterangan</div><div>:</div><div>{{ astekpam.p2u_keterangan || '-' }}</div>
                </div>
                <br>

                <p class="font-bold">3. Pembagian Tugas :</p>
                <div class="grid grid-cols-[180px_15px_1fr] gap-y-1 font-sans">
                    <div class="font-semibold">a. Ka. Rupam</div><div>:</div><div>{{ astekpam.tugas.ka_rupam || '-' }}</div>
                    
                    <div class="pl-5">Wakarupam</div><div>:</div><div>{{ astekpam.tugas.wakarupam || '-' }}</div>
                    
                    <div class="font-semibold mt-2">b. Petugas P2U</div><div>:</div><div></div>
                    <div class="pl-5">Kasatgas</div><div>:</div><div>{{ astekpam.tugas.kasatgas_p2u || '-' }}</div>
                    <div class="pl-5">Wakasatgas</div><div>:</div><div>{{ astekpam.tugas.wakasatgas_p2u || '-' }}</div>
                    
                    <div class="font-semibold mt-2">c. Petugas Blok</div><div>:</div><div></div>
                    <div class="pl-5">Blok A</div><div>:</div><div>{{ formatNamaTugas(astekpam.tugas.blok_a) }}</div>
                    <div class="pl-5">Blok B</div><div>:</div><div>{{ formatNamaTugas(astekpam.tugas.blok_b) }}</div>
                    
                    <div class="font-semibold mt-2">d. Petugas Pos Atas</div><div>:</div><div></div>
                    <div class="pl-5">* Menara 1</div><div>:</div><div>{{ formatNamaTugas(astekpam.tugas.menara_1) }}</div>
                    <div class="pl-5">* Menara 2</div><div>:</div><div>{{ formatNamaTugas(astekpam.tugas.menara_2) }}</div>
                    <div class="pl-5">* Menara 3</div><div>:</div><div>{{ formatNamaTugas(astekpam.tugas.menara_3) }}</div>
                    <div class="pl-5">* Menara 4</div><div>:</div><div>{{ formatNamaTugas(astekpam.tugas.menara_4) }}</div>
                    
                    <div class="mt-2">e. Petugas Jaga RS</div><div>:</div><div>{{ astekpam.tugas.jaga_rs || '-' }}</div>
                    <div>f. Piket Dapur</div><div>:</div><div>{{ astekpam.tugas.piket_dapur || '-' }}</div>
                    <div>g. Pengawas Piket</div><div>:</div><div>{{ astekpam.tugas.pengawas_piket || '-' }}</div>
                    <div>h. Perwira Piket</div><div>:</div><div>{{ astekpam.tugas.perwira_piket || '-' }}</div>
                    <div>i. Banja</div><div>:</div><div>{{ astekpam.tugas.banja || '-' }}</div>
                    <div>j. Staff KPLP</div><div>:</div><div>{{ astekpam.tugas.staff_kplp || '-' }}</div>
                    <div>k. Amanah</div><div>:</div><div>{{ astekpam.tugas.amanah || '-' }}</div>
                    <div class="font-bold border-t pt-1 mt-1">l. Petugas Laporan</div><div>:</div><div class="font-bold pt-1 uppercase">{{ astekpam.tugas.petugas_laporan || '-' }}</div>
                </div>
                <br>

                <p>Demikian Laporan ini, kami sampaikan dan diucapkan terima kasih.</p>
                <br>
                <p>Wassalamu'alaikum Warahmatullaahi wabarakaatuh</p>
                <p>Salam Sejahtera</p>
                <p>Salam Sehat Selalu…🙏</p>

                <div class="hidden print:flex justify-between mt-20 text-center font-bold italic">
                    <div class="w-1/3">
                        <p>Dibuat Oleh,</p>
                        <br><br><br>
                        <p>( {{ astekpam.tugas.petugas_laporan || '................' }} )</p>
                    </div>
                    <div class="w-1/3">
                        <p>Mengetahui,</p>
                        <br><br><br>
                        <p>( {{ astekpam.pimpinan || '................' }} )</p>
                    </div>
                </div>
            </Card>
        </div>
    </AuthenticatedLayout>
</template>

<style>
@media print {
    body { background: white !important; }
    nav, .print\:hidden { display: none !important; }
    .max-w-4xl { max-width: 100% !important; width: 100% !important; padding: 0 !important; }
}
</style>