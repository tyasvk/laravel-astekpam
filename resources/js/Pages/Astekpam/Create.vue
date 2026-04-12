<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed, watch, ref } from 'vue';
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Button } from '@/Components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { ChevronLeft, CheckCircle2, Eye, Users, Shield, MapPin, Clock, ArrowRightLeft } from 'lucide-vue-next';

const isPreviewActive = ref(false);

const form = useForm({
    tanggal: new Date().toISOString().split('T')[0],
    pukul: '(13.00-19.30)',
    dari_rupam: 'Rupam IV',
    dari_shift: 'Pagi',
    ke_rupam: 'Rupam II',
    ke_shift: 'Siang',
    pimpinan: 'STAF KPLP',
    
    // A. JUMLAH PENGHUNI
    kapasitas: 813,
    narapidana: 1650,
    blok_a: 993,
    blok_b: 636,
    dapur: 11,
    klinik: 10,
    dalam_lapas: 1650,
    luar_lapas: 0,
    rawat_inap_rs: 0,
    berobat_rs: 0,
    bon_luar: 0,
    total_wbp: 1650,

    // B. PERSONIL PENGAMANAN
    rupam_pilihan: 'RUPAM II',
    rupam_jumlah: 11,
    rupam_hadir: 10,
    rupam_tidak_hadir: 1,
    rupam_keterangan: 'Lengkap',

    p2u_rupam: 'RUPAM II',
    p2u_jumlah: 2,
    p2u_hadir: 2,
    p2u_keterangan: '-',

    // 3. PEMBAGIAN TUGAS
    tugas: {
        ka_rupam: '', wakarupam: '', kasatgas_p2u: '', wakasatgas_p2u: '',
        blok_a: '', blok_b: '', menara_1: '', menara_2: '', menara_3: '', menara_4: '',
        jaga_rs: '-', piket_dapur: '-', pengawas_piket: '-', perwira_piket: '-',
        banja: '-', staff_kplp: '', amanah: '-', petugas_laporan: ''
    }
});

const displayHari = computed(() => {
    if (!form.tanggal) return '';
    return new Intl.DateTimeFormat('id-ID', { weekday: 'long' }).format(new Date(form.tanggal));
});

// Salam dinamis berdasarkan pilihan pukul
const salamWaktu = computed(() => {
    if (form.pukul.includes('07.30-13.00')) return 'Pagi';
    if (form.pukul.includes('13.00-19.30')) return 'Siang';
    if (form.pukul.includes('19.30-07.30')) return 'Malam';
    return 'Pagi';
});

// Helper untuk menampilkan jumlah atau strip di read only
const formatOrg = (val) => {
    return (val !== null && val !== undefined && val !== '') ? `${val} Org` : '-';
};

watch(() => [form.dalam_lapas, form.luar_lapas], ([dalam, luar]) => {
    form.total_wbp = Number(dalam) + Number(luar);
});

const togglePreview = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
    isPreviewActive.value = !isPreviewActive.value;
};

const submitLaporan = () => {
    form.post(route('astekpam.store'), {
        onBefore: () => {
            // Opsional: Tambahkan konfirmasi sebelum kirim
            return confirm('Apakah Anda yakin data sudah benar? Laporan yang dikirim tidak dapat diubah.');
        },
        onSuccess: () => {
            // Aksi jika berhasil (misal: munculkan notifikasi sukses)
            alert('Laporan Astekpam Berhasil Terkirim!');
            isPreviewActive.value = false;
        },
        onError: (errors) => {
            // Aksi jika ada validasi yang gagal
            alert('Gagal mengirim laporan. Periksa kembali pengisian Anda.');
            console.error(errors);
            isPreviewActive.value = false; // Kembali ke mode edit untuk perbaikan
        }
    });
};
</script>

<template>
    <Head title="Laporan Astekpam" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between font-sans">
                <h2 class="font-bold text-xl text-zinc-800 tracking-tight font-sans">
                    {{ isPreviewActive ? 'Pratinjau Astekpam' : 'Buat Laporan Astekpam' }}
                </h2>
                <Button v-if="isPreviewActive" variant="outline" size="sm" @click="togglePreview" class="rounded-full shadow-sm font-sans">
                    <ChevronLeft class="w-4 h-4 mr-1" /> Kembali Edit
                </Button>
            </div>
        </template>

        <div v-show="!isPreviewActive" class="max-w-4xl mx-auto py-6 px-4 space-y-8 animate-in fade-in duration-300 font-sans">
            <form @submit.prevent="togglePreview" class="space-y-8 font-sans">
                
                <Card class="border-none shadow-sm">
                    <CardContent class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 font-sans">
                        <div class="space-y-2 font-sans"><Label class="font-sans">Hari / Tgl</Label><Input type="date" v-model="form.tanggal" class="h-12 font-sans" /></div>
                        <div class="space-y-2 font-sans"><Label class="font-sans">Pukul</Label>
                            <Select v-model="form.pukul" class="font-sans">
                                <SelectTrigger class="h-12 font-sans"><SelectValue class="font-sans" /></SelectTrigger>
                                <SelectContent class="font-sans">
                                    <SelectItem value="(07.30-13.00)">(07.30-13.00)</SelectItem>
                                    <SelectItem value="(13.00-19.30)">(13.00-19.30)</SelectItem>
                                    <SelectItem value="(19.30-07.30)">(19.30-07.30)</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                    </CardContent>
                </Card>

                <Card class="border-none shadow-sm overflow-hidden font-sans">
                    <CardHeader class="bg-zinc-900 text-white p-4 font-bold tracking-wider font-sans uppercase flex items-center gap-2">
                        <ArrowRightLeft class="w-4 h-4" /> DETAIL SERAH TERIMA
                    </CardHeader>
                    <CardContent class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 font-sans">
                        <div class="space-y-3 p-4 bg-zinc-50 rounded-xl border font-sans">
                            <Label class="font-bold uppercase font-sans text-[11px]">DARI :</Label>
                            <Select v-model="form.dari_rupam" class="font-sans"><SelectTrigger class="font-sans"><SelectValue /></SelectTrigger><SelectContent class="font-sans"><SelectItem v-for="r in ['I','II','III','IV']" :key="r" :value="'Rupam '+r">Rupam {{r}}</SelectItem></SelectContent></Select>
                            <Select v-model="form.dari_shift" class="font-sans"><SelectTrigger class="font-sans"><SelectValue /></SelectTrigger><SelectContent class="font-sans"><SelectItem value="Pagi">Pagi</SelectItem><SelectItem value="Siang">Siang</SelectItem><SelectItem value="Malam">Malam</SelectItem></SelectContent></Select>
                        </div>
                        <div class="space-y-3 p-4 bg-zinc-50 rounded-xl border font-sans">
                            <Label class="font-bold uppercase font-sans text-[11px]">KE :</Label>
                            <Select v-model="form.ke_rupam" class="font-sans"><SelectTrigger class="font-sans"><SelectValue /></SelectTrigger><SelectContent class="font-sans"><SelectItem v-for="r in ['I','II','III','IV']" :key="r" :value="'Rupam '+r">Rupam {{r}}</SelectItem></SelectContent></Select>
                            <Select v-model="form.ke_shift" class="font-sans"><SelectTrigger class="font-sans"><SelectValue /></SelectTrigger><SelectContent class="font-sans"><SelectItem value="Pagi">Pagi</SelectItem><SelectItem value="Siang">Siang</SelectItem><SelectItem value="Malam">Malam</SelectItem></SelectContent></Select>
                        </div>
                        <div class="md:col-span-2 space-y-1 font-sans"><Label class="font-sans">Dipimpin Oleh</Label><Input v-model="form.pimpinan" class="font-sans h-11" /></div>
                    </CardContent>
                </Card>

                <Card class="border-none shadow-sm overflow-hidden font-sans">
                    <CardHeader class="bg-zinc-900 text-white p-4 font-bold uppercase tracking-wider font-sans">A. JUMLAH PENGHUNI</CardHeader>
                    <CardContent class="p-6 space-y-6 font-sans">
                        <div class="grid grid-cols-2 gap-4 font-sans">
                            <div class="space-y-1"><Label class="font-sans">1. Kapasitas</Label><Input type="number" v-model="form.kapasitas" class="font-sans" /></div>
                            <div class="space-y-1"><Label class="font-sans">2. Narapidana</Label><Input type="number" v-model="form.narapidana" class="font-sans font-bold" /></div>
                        </div>
                        <div class="space-y-3 font-sans">
                            <Label class="font-bold font-sans">3. Isi Blok Hunian :</Label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 pl-2 border-l-2 font-sans border-zinc-200">
                                <div v-for="b in ['blok_a', 'blok_b', 'dapur', 'klinik']" :key="b" class="space-y-1 font-sans">
                                    <Label class="font-sans">{{ b.replace('_', ' ').toUpperCase() }}</Label>
                                    <Input type="number" v-model="form[b]" class="font-sans" />
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 font-sans border-t pt-4 border-dashed">
                            <div class="space-y-1"><Label class="font-sans font-bold text-green-700">Didalam Lapas</Label><Input type="number" v-model="form.dalam_lapas" class="bg-green-50 font-sans font-bold border-green-200 text-green-800" /></div>
                            <div class="space-y-1"><Label class="font-sans font-bold text-orange-700">Diluar Lapas</Label><Input type="number" v-model="form.luar_lapas" class="bg-orange-50 font-sans font-bold border-orange-200 text-orange-800" /></div>
                        </div>
                    </CardContent>
                </Card>

                <Card class="border-none shadow-sm overflow-hidden font-sans">
                    <CardHeader class="bg-indigo-900 text-white p-4 font-bold tracking-wider font-sans uppercase">B. PERSONIL PENGAMANAN</CardHeader>
                    <CardContent class="p-6 space-y-8 font-sans">
                        <div class="space-y-4 font-sans"><Label class="font-black text-zinc-800 font-sans">1. RUPAM</Label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pl-4 border-l-2 border-indigo-200 font-sans">
                                <div class="col-span-2 md:col-span-1 space-y-1 font-sans"><Label class="font-sans">Pilihan</Label>
                                    <Select v-model="form.rupam_pilihan" class="font-sans"><SelectTrigger class="font-sans"><SelectValue /></SelectTrigger><SelectContent class="font-sans"><SelectItem v-for="i in ['RUPAM I','RUPAM II','RUPAM III','RUPAM IV']" :key="i" :value="i">{{i}}</SelectItem></SelectContent></Select>
                                </div>
                                <div class="space-y-1"><Label class="font-sans">Jumlah</Label><Input type="number" v-model="form.rupam_jumlah" class="font-sans" /></div>
                                <div class="space-y-1"><Label class="font-sans">Hadir</Label><Input type="number" v-model="form.rupam_hadir" class="font-sans font-bold text-green-700" /></div>
                                <div class="space-y-1"><Label class="font-sans">Tidak Hadir</Label><Input type="number" v-model="form.rupam_tidak_hadir" class="font-sans font-bold text-red-600" /></div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card class="border-none shadow-sm overflow-hidden font-sans pb-12">
                    <CardHeader class="bg-zinc-800 text-white p-4 font-bold tracking-wider font-sans uppercase">3. PEMBAGIAN TUGAS</CardHeader>
                    <CardContent class="p-6 space-y-10 font-sans">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 font-sans">
                            <div class="space-y-3 font-sans">
                                <Label class="font-bold font-sans">a. Ka Rupam</Label>
                                <div class="space-y-3 pl-2 border-l-2 font-sans border-zinc-200">
                                    <div class="flex items-center gap-3 font-sans"><Label class="w-24 font-sans">Ka. Rupam</Label><Input v-model="form.tugas.ka_rupam" class="h-10 font-sans" /></div>
                                    <div class="flex items-center gap-3 font-sans"><Label class="w-24 font-sans">Wakarupam</Label><Input v-model="form.tugas.wakarupam" class="h-10 font-sans" /></div>
                                </div>
                            </div>
                            <div class="space-y-3 font-sans">
                                <Label class="font-bold font-sans">b. P2U</Label>
                                <div class="space-y-3 pl-2 border-l-2 font-sans border-zinc-200">
                                    <div class="flex items-center gap-3 font-sans"><Label class="w-24 font-sans">Kasatgas</Label><Input v-model="form.tugas.kasatgas_p2u" class="h-10 font-sans" /></div>
                                    <div class="flex items-center gap-3 font-sans"><Label class="w-24 font-sans">Wakasatgas</Label><Input v-model="form.tugas.wakasatgas_p2u" class="h-10 font-sans" /></div>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 font-sans">
                            <div class="space-y-3 font-sans">
                                <Label class="font-bold font-sans">c. Blok</Label>
                                <div class="space-y-3 pl-2 border-l-2 font-sans border-zinc-200">
                                    <div class="flex items-center gap-3 font-sans"><Label class="w-20 font-sans">Blok A</Label><Input v-model="form.tugas.blok_a" class="h-10 font-sans" /></div>
                                    <div class="flex items-center gap-3 font-sans"><Label class="w-20 font-sans">Blok B</Label><Input v-model="form.tugas.blok_b" class="h-10 font-sans" /></div>
                                </div>
                            </div>
                            <div class="space-y-3 font-sans">
                                <Label class="font-bold font-sans">d. Pos Atas</Label>
                                <div class="grid grid-cols-2 gap-2 pl-2 border-l-2 font-sans border-zinc-200">
                                    <div v-for="n in [1,2,3,4]" :key="n" class="space-y-1 font-sans">
                                        <Label class="font-sans">Menara {{n}}</Label>
                                        <Input v-model="form.tugas['menara_'+n]" class="h-10 font-sans" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 pt-6 border-t font-sans">
                            <div v-for="label, key in { jaga_rs: 'e. Jaga RS', piket_dapur: 'f. Piket Dapur', pengawas_piket: 'g. Pengawas', perwira_piket: 'h. Perwira', banja: 'i. Banja', staff_kplp: 'j. Staff KPLP', amanah: 'k. Amanah', petugas_laporan: 'l. Petugas Laporan' }" :key="key" class="space-y-1 font-sans">
                                <Label class="font-sans">{{label}}</Label>
                                <Input v-model="form.tugas[key]" class="h-10 bg-zinc-50/50 font-sans border-zinc-200" />
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <div class="fixed bottom-0 left-0 right-0 p-4 bg-white/95 border-t md:relative md:bg-transparent md:border-none md:p-0">
                    <Button type="submit" class="w-full h-14 text-lg font-black uppercase tracking-widest bg-indigo-600 shadow-2xl font-sans"><Eye class="mr-2" /> Pratinjau Astekpam</Button>
                </div>
            </form>
        </div>

        <div v-show="isPreviewActive" class="min-h-screen bg-zinc-100 py-10 px-4 animate-in slide-in-from-bottom duration-500 font-sans">
            <div class="max-w-3xl mx-auto space-y-6">
                
                <Card class="border-none shadow-2xl bg-white p-8 md:p-16 rounded-xl text-zinc-900 font-sans text-[15px] leading-relaxed">
                    <div class="font-bold text-center mb-8 text-base font-sans">ASTEKPAM LAPAS KELAS I PALEMBANG</div>
                    <p class="font-sans">Assalamu’alaikum Warahmatullahi Wabarakatuh</p>
                    <p class="font-sans">Selamat {{ salamWaktu }}....</p>
                    <br>
                    <div class="grid grid-cols-[120px_10px_1fr] gap-y-1 font-sans">
                        <div>Hari/Tgl</div><div>:</div><div>{{ displayHari }}, {{ form.tanggal }}</div>
                        <div>Pukul</div><div>:</div><div>{{ form.pukul }} WIB</div>
                    </div>
                    <br>
                    <p class="font-sans">Berikut, ASTEKPAM dari {{ form.dari_rupam }} (Shift {{ form.dari_shift }}) ke {{ form.ke_rupam }} (Shift {{ form.ke_shift }}) Dipimpin oleh {{ form.pimpinan || '-' }} berjalan aman dan tertib.</p>
                    <br>
                    <p class="font-sans">Dengan rincian sebagai berikut :</p>
                    <p class="font-bold font-sans">A. JUMLAH PENGHUNI</p>
                    <div class="grid grid-cols-[180px_15px_1fr] gap-y-1 font-sans">
                        <div>1. Kapasitas</div><div>:</div><div>{{ formatOrg(form.kapasitas) }}</div>
                        <div>2. Narapidana</div><div>:</div><div>{{ formatOrg(form.narapidana) }}</div>
                        <div>3. Isi Blok Hunian</div><div>:</div><div></div>
                        <div class="pl-4 font-sans">* Blok A</div><div>:</div><div>{{ formatOrg(form.blok_a) }}</div>
                        <div class="pl-4 font-sans">* Blok B</div><div>:</div><div>{{ formatOrg(form.blok_b) }}</div>
                        <div class="pl-4 font-sans">* Dapur</div><div>:</div><div>{{ formatOrg(form.dapur) }}</div>
                        <div class="pl-4 font-sans">* Klinik</div><div>:</div><div>{{ formatOrg(form.klinik) }}</div>
                        <div>Jumlah</div><div>:</div><div></div>
                        <div class="pl-4 font-sans">* Didalam Lapas</div><div>:</div><div>{{ formatOrg(form.dalam_lapas) }}</div>
                        <div class="pl-4 font-sans">* Diluar Lapas</div><div>:</div><div>{{ formatOrg(form.luar_lapas) }}</div>
                        <div class="mt-2 font-sans">4. Keterangan di luar Lapas</div><div>:</div><div></div>
                        <div class="pl-4 font-sans">* Rawat Inap RS</div><div>:</div><div>{{ form.rawat_inap_rs ? form.rawat_inap_rs + ' Org' : '-' }}</div>
                        <div class="pl-4 font-sans">* Berobat RS</div><div>:</div><div>{{ form.berobat_rs ? form.berobat_rs + ' Org' : '-' }}</div>
                        <div class="pl-4 font-sans">* Lain-lain (bon luar)</div><div>:</div><div>{{ form.bon_luar ? form.bon_luar + ' Org' : '-' }}</div>
                        <div class="font-bold mt-2 font-sans">5. Total Jumlah WBP</div><div>:</div><div class="font-bold font-sans">{{ formatOrg(form.total_wbp) }}</div>
                    </div>
                    <br>
                    <p class="font-bold font-sans">B. PERSONIL PENGAMANAN</p>
                    <div class="grid grid-cols-[180px_15px_1fr] gap-y-1 font-sans">
                        <div class="font-semibold font-sans">1. {{ form.rupam_pilihan }}</div><div></div><div></div>
                        <div class="pl-4 font-sans">Jumlah</div><div>:</div><div>{{ form.rupam_jumlah ? form.rupam_jumlah + ' Org' : '-' }}</div>
                        <div class="pl-4 font-sans">Hadir</div><div>:</div><div>{{ form.rupam_hadir ? form.rupam_hadir + ' Org' : '-' }}</div>
                        <div class="pl-4 font-sans">Tidak Hadir</div><div>:</div><div>{{ form.rupam_tidak_hadir ? form.rupam_tidak_hadir + ' Org' : '-' }}</div>
                        <div class="pl-4 font-sans">Keterangan</div><div>:</div><div>{{ form.rupam_keterangan || '-' }}</div>
                    </div>
                    <br>
                    <p class="font-bold font-sans">3. Pembagian Tugas :</p>
                    <div class="grid grid-cols-[180px_15px_1fr] gap-y-1 font-sans">
                        <div class="font-semibold font-sans">a. Ka. Rupam</div><div>:</div><div>{{ form.tugas.ka_rupam || '-' }}</div>
                        <div class="pl-4 font-sans">Wakarupam</div><div>:</div><div>{{ form.tugas.wakarupam || '-' }}</div>
                        <div class="font-semibold mt-2 font-sans">b. Petugas P2U</div><div>:</div><div></div>
                        <div class="pl-4 font-sans">Kasatgas</div><div>:</div><div>{{ form.tugas.kasatgas_p2u || '-' }}</div>
                        <div class="pl-4 font-sans">Wakasatgas</div><div>:</div><div>{{ form.tugas.wakasatgas_p2u || '-' }}</div>
                        <div class="font-semibold mt-2 font-sans">c. Petugas Blok</div><div>:</div><div></div>
                        <div class="pl-4 font-sans">Blok A</div><div>:</div><div>{{ form.tugas.blok_a || '-' }}</div>
                        <div class="pl-4 font-sans">Blok B</div><div>:</div><div>{{ form.tugas.blok_b || '-' }}</div>
                        <div class="font-semibold mt-2 font-sans">d. Petugas Pos Atas</div><div>:</div><div></div>
                        <div class="pl-4 font-sans">* Menara 1</div><div>:</div><div>{{ form.tugas.menara_1 || '-' }}</div>
                        <div class="pl-4 font-sans">* Menara 2</div><div>:</div><div>{{ form.tugas.menara_2 || '-' }}</div>
                        <div class="pl-4 font-sans">* Menara 3</div><div>:</div><div>{{ form.tugas.menara_3 || '-' }}</div>
                        <div class="pl-4 font-sans">* Menara 4</div><div>:</div><div>{{ form.tugas.menara_4 || '-' }}</div>
                        <div class="mt-2 font-sans">e. Petugas Jaga RS</div><div>:</div><div>{{ form.tugas.jaga_rs || '-' }}</div>
                        <div class="font-sans">f. Piket Dapur</div><div>:</div><div>{{ form.tugas.piket_dapur || '-' }}</div>
                        <div class="font-sans">g. Pengawas Piket</div><div>:</div><div>{{ form.tugas.pengawas_piket || '-' }}</div>
                        <div class="font-sans">h. Perwira Piket</div><div>:</div><div>{{ form.tugas.perwira_piket || '-' }}</div>
                        <div class="font-sans">i. Banja</div><div>:</div><div>{{ form.tugas.banja || '-' }}</div>
                        <div class="font-sans">j. Staff KPLP</div><div>:</div><div>{{ form.tugas.staff_kplp || '-' }}</div>
                        <div class="font-sans">k. Amanah</div><div>:</div><div>{{ form.tugas.amanah || '-' }}</div>
                        <div class="font-bold font-sans">l. Petugas Laporan</div><div>:</div><div>{{ form.tugas.petugas_laporan || '-' }}</div>
                    </div>
                    <br>
                    <p class="font-sans">Demikian Laporan ini, kami sampaikan dan diucapkan terima kasih.</p>
                    <br>
                    <p class="font-sans">Wassalamu'alaikum Warahmatullaahi wabarakaatuh</p>
                    <p class="font-sans">Salam Sejahtera</p>
                    <p class="font-sans">Salam Sehat Selalu…🙏</p>
                </Card>

                <div class="py-4">
                    <Button @click="submitLaporan" class="w-full h-16 text-xl font-black bg-green-600 hover:bg-green-700 shadow-xl font-sans" :disabled="form.processing">
                        <CheckCircle2 class="mr-2 h-6 w-6" /> KIRIM LAPORAN SEKARANG
                    </Button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>