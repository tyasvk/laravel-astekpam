<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed, watch, ref } from 'vue';
import { Card, CardContent } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Button } from '@/Components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { 
    ChevronLeft, 
    Eye, 
    Users, 
    Shield, 
    MapPin, 
    ArrowRightLeft,
    FileText,
    ClipboardCheck,
    LayoutGrid,
    CalendarDays,
    Plus,
    Trash2
} from 'lucide-vue-next';

const props = defineProps({
    lastReport: Object
});

const isPreviewActive = ref(false);

const form = useForm({
    tanggal: new Date().toISOString().split('T')[0],
    pukul: '(13.00-19.30)',
    dari_rupam: '',
    dari_shift: '',
    ke_rupam: '',
    ke_shift: '',
    pimpinan: 'STAF KPLP',
    
    // A. JUMLAH PENGHUNI
    kapasitas: props.lastReport?.kapasitas ?? 813,
    narapidana: props.lastReport?.narapidana ?? 0,
    blok_a: props.lastReport?.blok_a ?? 0,
    blok_b: props.lastReport?.blok_b ?? 0,
    dapur: props.lastReport?.dapur ?? 0,
    klinik: props.lastReport?.klinik ?? 0,
    dalam_lapas: props.lastReport?.dalam_lapas ?? 0,
    luar_lapas: props.lastReport?.luar_lapas ?? 0,
    
    // Keterangan Luar Lapas (Dynamic Arrays)
    rawat_inap_items: [{ ket: '' }],
    berobat_items: [{ ket: '' }],
    bon_luar_items: [{ ket: '' }],
    
    total_wbp: props.lastReport?.total_wbp ?? 0,

    // B. PERSONIL PENGAMANAN
    rupam_pilihan: '',
    rupam_jumlah: '',
    rupam_hadir: '',
    rupam_keterangan: '',
    p2u_jumlah: '',
    p2u_hadir: '',
    p2u_keterangan: '',

    // 3. PEMBAGIAN TUGAS
    tugas: {
        ka_rupam: '', wakarupam: '', kasatgas_p2u: '', wakasatgas_p2u: '',
        blok_a: '', blok_b: '', menara_1: '', menara_2: '', menara_3: '', menara_4: '',
        jaga_rs: '-', perwira_kontrol: '**', perwira_piket: '**', banjaga: '**', 
        piket_dapur: '', staff_kplp: '', amanah: '-', petugas_laporan: ''
    }
});

// Fungsi Dynamic Fields
const addItem = (type) => form[type].push({ ket: '' });
const removeItem = (type, index) => {
    if (form[type].length > 1) form[type].splice(index, 1);
    else form[type][0].ket = '';
};

// Auto-fill Petugas Laporan
watch(() => form.tugas.ka_rupam, (val) => form.tugas.petugas_laporan = val);

const displayHari = computed(() => {
    if (!form.tanggal) return '';
    return new Intl.DateTimeFormat('id-ID', { weekday: 'long' }).format(new Date(form.tanggal));
});

const salamWaktu = computed(() => {
    if (form.pukul.includes('07.30-13.00')) return 'Pagi';
    if (form.pukul.includes('13.00-19.30')) return 'Siang';
    if (form.pukul.includes('19.30-07.30')) return 'Malam';
    return 'Siang';
});

const formatOrg = (val) => {
    if (!val || val === 0) return '- Org';
    return `${val} Org`;
};

watch(() => [form.dalam_lapas, form.luar_lapas], ([dalam, luar]) => {
    form.total_wbp = Number(dalam) + Number(luar);
});

const togglePreview = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
    isPreviewActive.value = !isPreviewActive.value;
};

const submitLaporan = () => form.post(route('astekpam.store'));
</script>

<template>
    <Head title="Laporan Astekpam" />

    <AuthenticatedLayout>
        <div class="max-w-4xl mx-auto mb-8 px-4 font-sans">
            <div class="flex flex-col gap-4">
                <button v-if="isPreviewActive" @click="togglePreview" class="flex items-center gap-2 text-zinc-400 hover:text-blue-600 transition-colors w-fit text-[11px] font-medium uppercase tracking-[0.2em]">
                    <ChevronLeft class="w-4 h-4" /> Kembali Edit
                </button>
                <h1 class="text-3xl font-medium tracking-tighter text-zinc-900 italic font-sans">
                    {{ isPreviewActive ? 'Pratinjau Dokumen' : 'Input Astekpam' }}
                </h1>
            </div>
        </div>

        <div v-show="!isPreviewActive" class="max-w-4xl mx-auto px-4 space-y-8 pb-32 animate-in fade-in duration-500 font-sans">
            <form @submit.prevent="togglePreview" class="space-y-8">
                <Card class="border-none shadow-xl shadow-zinc-200/50 rounded-[2rem] bg-white">
                    <CardContent class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-3"><Label class="text-[10px] font-medium uppercase tracking-[0.2em] text-zinc-400">Hari / Tanggal</Label><Input type="date" v-model="form.tanggal" class="h-12 rounded-2xl border-zinc-100 bg-zinc-50/50" /></div>
                        <div class="space-y-3"><Label class="text-[10px] font-medium uppercase tracking-[0.2em] text-zinc-400">Pukul</Label>
                            <Select v-model="form.pukul"><SelectTrigger class="h-12 rounded-2xl border-zinc-100 bg-zinc-50/50"><SelectValue /></SelectTrigger><SelectContent class="rounded-2xl"><SelectItem value="(07.30-13.00)">(07.30-13.00)</SelectItem><SelectItem value="(13.00-19.30)">(13.00-19.30)</SelectItem><SelectItem value="(19.30-07.30)">(19.30-07.30)</SelectItem></SelectContent></Select>
                        </div>
                    </CardContent>
                </Card>

                <Card class="border-none shadow-xl shadow-zinc-200/50 rounded-[2rem] bg-white overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-700 to-indigo-700 px-8 py-4 text-white text-[11px] font-medium uppercase tracking-widest">Detail Serah Terima</div>
                    <CardContent class="p-8 space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-4 p-6 bg-blue-50/30 rounded-3xl border border-blue-100/50">
                                <Label class="text-[10px] font-medium text-blue-700 uppercase font-sans">Dari (Lama):</Label>
                                <div class="grid grid-cols-2 gap-2">
                                    <Select v-model="form.dari_rupam"><SelectTrigger class="bg-white border-none h-10 rounded-xl"><SelectValue placeholder="Rupam" /></SelectTrigger><SelectContent><SelectItem v-for="r in ['I','II','III','IV']" :key="r" :value="'RUPAM '+r">RUPAM {{r}}</SelectItem></SelectContent></Select>
                                    <Select v-model="form.dari_shift"><SelectTrigger class="bg-white border-none h-10 rounded-xl"><SelectValue placeholder="Shift" /></SelectTrigger><SelectContent><SelectItem value="Pagi">Pagi</SelectItem><SelectItem value="Siang">Siang</SelectItem><SelectItem value="Malam">Malam</SelectItem></SelectContent></Select>
                                </div>
                            </div>
                            <div class="space-y-4 p-6 bg-indigo-50/30 rounded-3xl border border-indigo-100/50 font-sans">
                                <Label class="text-[10px] font-medium text-indigo-700 uppercase font-sans">Ke (Baru):</Label>
                                <div class="grid grid-cols-2 gap-2">
                                    <Select v-model="form.ke_rupam"><SelectTrigger class="bg-white border-none h-10 rounded-xl"><SelectValue placeholder="Rupam" /></SelectTrigger><SelectContent><SelectItem v-for="r in ['I','II','III','IV']" :key="r" :value="'RUPAM '+r">RUPAM {{r}}</SelectItem></SelectContent></Select>
                                    <Select v-model="form.ke_shift"><SelectTrigger class="bg-white border-none h-10 rounded-xl"><SelectValue placeholder="Shift" /></SelectTrigger><SelectContent><SelectItem value="Pagi">Pagi</SelectItem><SelectItem value="Siang">Siang</SelectItem><SelectItem value="Malam">Malam</SelectItem></SelectContent></Select>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-3"><Label class="text-[10px] font-medium text-zinc-400 uppercase tracking-widest font-sans">Dipimpin Oleh</Label><Input v-model="form.pimpinan" class="h-12 rounded-2xl border-zinc-100 bg-zinc-50/50 font-sans" /></div>
                    </CardContent>
                </Card>

                <Card class="border-none shadow-xl shadow-zinc-200/50 rounded-[2rem] bg-white overflow-hidden font-sans">
                    <div class="bg-zinc-900 px-8 py-4 text-white text-[11px] font-medium uppercase tracking-widest font-sans">A. Jumlah Penghuni</div>
                    <CardContent class="p-8 space-y-10 font-sans">
                        <div class="grid grid-cols-2 gap-6">
                            <div class="space-y-3"><Label class="text-[10px] text-zinc-400 uppercase font-sans">Kapasitas</Label><Input type="number" v-model="form.kapasitas" class="h-12 rounded-2xl border-zinc-100 font-sans" /></div>
                            <div class="space-y-3"><Label class="text-[10px] text-blue-600 uppercase font-sans">Narapidana</Label><Input type="number" v-model="form.narapidana" class="h-12 rounded-2xl bg-blue-50/30 border-blue-100 font-medium text-blue-700 font-sans" /></div>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 font-sans">
                            <div v-for="b in ['blok_a', 'blok_b', 'dapur', 'klinik']" :key="b" class="space-y-2 font-sans"><Label class="text-[10px] text-zinc-400 uppercase font-sans tracking-tight">{{ b.replace('_', ' ') }}</Label><Input type="number" v-model="form[b]" class="h-11 rounded-xl bg-zinc-50/50 font-sans" /></div>
                        </div>
                        <div class="grid grid-cols-2 gap-6 pt-6 border-t border-dashed">
                            <div class="space-y-3"><Label class="text-[10px] text-emerald-600 uppercase font-bold font-sans">Didalam Lapas</Label><Input type="number" v-model="form.dalam_lapas" class="h-12 rounded-2xl bg-emerald-50/50 border-emerald-100 font-sans" /></div>
                            <div class="space-y-3"><Label class="text-[10px] text-orange-600 uppercase font-bold font-sans">Diluar Lapas</Label><Input type="number" v-model="form.luar_lapas" class="h-12 rounded-2xl bg-orange-50/50 border-orange-100 font-sans" /></div>
                        </div>

                        <div class="space-y-8 p-6 bg-zinc-50/50 rounded-[2rem] border border-zinc-100">
                            <div v-for="(items, key) in { rawat_inap_items: 'Rawat Inap RS', berobat_items: 'Berobat RS', bon_luar_items: 'Lain-lain (Bon Luar)' }" :key="key" class="space-y-4 font-sans">
                                <div class="flex items-center justify-between font-sans">
                                    <Label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest font-sans">{{ items }}</Label>
                                    <Button type="button" @click="addItem(key)" variant="ghost" size="sm" class="h-8 rounded-full text-blue-600 hover:text-blue-700 hover:bg-blue-50 font-sans">
                                        <Plus class="w-3 h-3 mr-1" /> Tambah Kolom
                                    </Button>
                                </div>
                                <div v-for="(item, index) in form[key]" :key="index" class="flex gap-2 font-sans">
                                    <Input v-model="item.ket" placeholder="Ketik keterangan..." class="h-11 rounded-xl bg-white border-zinc-200 font-sans" />
                                    <Button v-if="form[key].length > 1" type="button" @click="removeItem(key, index)" variant="ghost" size="icon" class="h-11 w-11 rounded-xl text-red-500 hover:bg-red-50 font-sans">
                                        <Trash2 class="w-4 h-4" />
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card class="border-none shadow-xl shadow-zinc-200/50 rounded-[2rem] bg-white overflow-hidden">
                    <div class="bg-indigo-900 px-8 py-4 text-white text-[11px] font-medium uppercase tracking-widest font-sans">B. Personil Pengamanan</div>
                    <CardContent class="p-8 space-y-12">
                        <div class="space-y-6">
                            <Label class="text-xs font-bold text-zinc-800 ml-1">1. RUPAM</Label>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 p-7 bg-zinc-50/50 rounded-3xl border border-zinc-100 font-sans">
                                <div class="space-y-3"><Label class="text-[10px] text-zinc-400 uppercase">Pilihan</Label><Select v-model="form.rupam_pilihan"><SelectTrigger class="h-11 bg-white"><SelectValue placeholder="Rupam" /></SelectTrigger><SelectContent><SelectItem v-for="i in ['RUPAM I','RUPAM II','RUPAM III','RUPAM IV']" :key="i" :value="i">{{i}}</SelectItem></SelectContent></Select></div>
                                <div class="space-y-3 font-sans"><Label class="text-[10px] text-zinc-400 uppercase font-sans">Jumlah</Label><Input type="number" v-model="form.rupam_jumlah" class="h-11 bg-white font-sans" /></div>
                                <div class="space-y-3 font-sans"><Label class="text-[10px] text-zinc-400 uppercase font-sans">Hadir</Label><Input type="number" v-model="form.rupam_hadir" class="h-11 bg-white text-emerald-600 font-medium font-sans" /></div>
                                <div class="space-y-3 font-sans"><Label class="text-[10px] text-zinc-400 uppercase font-sans">Keterangan</Label><Input type="text" v-model="form.rupam_keterangan" class="h-11 bg-white font-sans" /></div>
                            </div>
                        </div>
                        <div class="space-y-6 pt-6 border-t border-dashed font-sans">
                            <Label class="text-xs font-bold text-zinc-800 ml-1">2. SATGAS P2U</Label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-7 bg-indigo-50/30 rounded-3xl border border-indigo-100/50">
                                <div class="space-y-3 font-sans"><Label class="text-[10px] text-indigo-400 uppercase font-sans">Jumlah</Label><Input type="number" v-model="form.p2u_jumlah" class="h-11 bg-white font-sans" /></div>
                                <div class="space-y-3 font-sans"><Label class="text-[10px] text-indigo-400 uppercase font-sans">Hadir</Label><Input type="number" v-model="form.p2u_hadir" class="h-11 bg-white font-medium text-indigo-600 font-sans" /></div>
                                <div class="space-y-3 font-sans"><Label class="text-[10px] text-indigo-400 uppercase font-sans">Keterangan</Label><Input type="text" v-model="form.p2u_keterangan" class="h-11 bg-white font-sans" /></div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card class="border-none shadow-xl shadow-zinc-200/50 rounded-[2rem] bg-white overflow-hidden pb-10">
                    <div class="bg-zinc-100 px-8 py-4 text-zinc-600 text-[11px] font-medium uppercase tracking-widest font-sans">3. Pembagian Tugas</div>
                    <CardContent class="p-8 lg:p-10 space-y-12">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 font-sans">
                            <div class="space-y-6"><Label class="text-[11px] font-bold uppercase text-blue-600 tracking-widest ml-1">a. Ka.Rupam & Wakarupam</Label>
                                <div class="space-y-4 pl-5 border-l-2 border-zinc-100 font-sans">
                                    <div class="flex items-center gap-4"><Label class="w-24 text-[10px] text-zinc-400 uppercase font-sans">Ka. Rupam</Label><Input v-model="form.tugas.ka_rupam" class="h-10 rounded-xl bg-zinc-50 border-none font-sans" /></div>
                                    <div class="flex items-center gap-4"><Label class="w-24 text-[10px] text-zinc-400 uppercase font-sans">Wakarupam</Label><Input v-model="form.tugas.wakarupam" class="h-10 rounded-xl bg-zinc-50 border-none font-sans" /></div>
                                </div>
                            </div>
                            <div class="space-y-6"><Label class="text-[11px] font-bold uppercase text-blue-600 tracking-widest ml-1">b. Petugas P2U</Label>
                                <div class="space-y-4 pl-5 border-l-2 border-zinc-100">
                                    <div class="flex items-center gap-4 font-sans"><Label class="w-24 text-[10px] text-zinc-400 uppercase">Kasatgas</Label><Input v-model="form.tugas.kasatgas_p2u" class="h-10 rounded-xl bg-zinc-50 border-none" /></div>
                                    <div class="flex items-center gap-4 font-sans"><Label class="w-24 text-[10px] text-zinc-400 uppercase">Wakasatgas</Label><Input v-model="form.tugas.wakasatgas_p2u" class="h-10 rounded-xl bg-zinc-50 border-none" /></div>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-10 border-t">
                            <div v-for="label, key in { blok_a: 'Blok A', blok_b: 'Blok B', menara_1: 'Menara 1', menara_2: 'Menara 2', menara_3: 'Menara 3', menara_4: 'Menara 4', jaga_rs: 'e. Jaga RS', perwira_kontrol: 'f. Perwira Kontrol', perwira_piket: 'g. Perwira Piket', banjaga: 'h. Piket Banjaga', piket_dapur: 'i. Piket Dapur', staff_kplp: 'j. Staf KPLP', amanah: 'k. Amanah', petugas_laporan: 'l. Petugas Laporan' }" :key="key" class="p-4 rounded-2xl bg-zinc-50/50 border border-zinc-50">
                                <Label class="text-[9px] uppercase text-zinc-400 font-bold block mb-2">{{label}}</Label><Input v-model="form.tugas[key]" class="h-9 rounded-xl bg-white border-zinc-100 text-[11px]" />
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <div class="fixed bottom-0 left-0 right-0 p-4 bg-white/90 backdrop-blur-md border-t md:relative md:bg-transparent md:border-none md:p-0 z-40">
                    <Button type="submit" class="w-full h-14 rounded-2xl text-[11px] font-medium uppercase tracking-[0.3em] bg-zinc-900 text-white shadow-2xl active:scale-95 font-sans">Pratinjau Laporan</Button>
                </div>
            </form>
        </div>

        <div v-show="isPreviewActive" class="max-w-3xl mx-auto px-4 pb-32 animate-in slide-in-from-bottom duration-700 font-sans">
            <Card class="border-none shadow-2xl bg-white p-8 md:p-16 rounded-[3rem] text-zinc-800 leading-relaxed font-sans relative">
                <div class="relative z-10 space-y-8 text-[14px] md:text-[15px] font-normal text-zinc-900 leading-snug">
                    
                    <div class="text-center space-y-1 pb-6 border-b border-zinc-100 uppercase font-sans">
                        <div class="text-[16px] font-bold tracking-tight">ASTEKPAM</div>
                        <div class="text-[16px] font-bold tracking-tight">LAPAS KELAS I PALEMBANG</div>
                    </div>

                    <div class="space-y-1">
                        <p>Assalamu’alaikum Warahmatullahi Wabarakatuh</p>
                        <p>Selamat {{ salamWaktu }}</p>
                    </div>

                    <div class="grid grid-cols-[100px_10px_1fr] gap-y-1 font-medium font-sans">
                        <div class="text-zinc-500">Hari/Tgl</div><div>:</div><div>{{ displayHari }}, {{ form.tanggal }}</div>
                        <div class="text-zinc-500">Pukul</div><div>:</div><div>{{ form.pukul }} WIB</div>
                    </div>

                    <p>Berikut, ASTEKPAM dari <span class="font-bold">{{ form.dari_rupam }} (Shift {{ form.dari_shift }})</span> ke <span class="font-bold">{{ form.ke_rupam }} (Shift {{ form.ke_shift }})</span>. Dipimpin oleh <span class="font-bold">{{ form.pimpinan || '-' }}</span> berjalan aman dan tertib.</p>

                    <p>Dengan rincian sebagai berikut :</p>

                    <div class="space-y-1 pt-2 font-sans">
                        <p class="font-bold">A. JUMLAH PENGHUNI</p>
                        <div class="grid grid-cols-[150px_15px_1fr] gap-y-1 pl-2 font-sans">
                            <div>1. Kapasitas</div><div>:</div><div>{{ formatOrg(form.kapasitas) }}</div>
                            <div>2. Narapidana</div><div>:</div><div class="font-bold">{{ formatOrg(form.narapidana) }}</div>
                            <div>3. Isi Blok Hunian</div><div>:</div><div></div>
                            <div class="pl-4">* Blok A</div><div>:</div><div>{{ formatOrg(form.blok_a) }}</div>
                            <div class="pl-4">* Blok B</div><div>:</div><div>{{ formatOrg(form.blok_b) }}</div>
                            <div class="pl-4">* Dapur</div><div>:</div><div>{{ formatOrg(form.dapur) }}</div>
                            <div class="pl-4">* Klinik</div><div>:</div><div>{{ formatOrg(form.klinik) }}</div>
                            <div class="font-bold mt-1">Jumlah</div><div>:</div><div></div>
                            <div class="pl-4 font-bold">* Didalam Lapas</div><div>:</div><div class="font-bold">{{ formatOrg(form.dalam_lapas) }}</div>
                            <div class="pl-4 font-bold">* Diluar Lapas</div><div>:</div><div class="font-bold">{{ formatOrg(form.luar_lapas) }}</div>
                        </div>
                    </div>

                    <div class="space-y-2 pt-2">
                        <p class="font-bold font-sans">4. Keterangan di luar Lapas :</p>
                        <div class="space-y-3 pl-2">
                            <div v-if="form.rawat_inap_items.some(i => i.ket)" class="font-sans">
                                <p class="font-medium">* Rawat Inap RS :</p>
                                <ul class="pl-6 italic text-zinc-600 text-sm list-disc">
                                    <li v-for="item in form.rawat_inap_items.filter(i => i.ket)">{{ item.ket }}</li>
                                </ul>
                            </div>
                            <div v-if="form.berobat_items.some(i => i.ket)" class="font-sans">
                                <p class="font-medium">* Berobat RS :</p>
                                <ul class="pl-6 italic text-zinc-600 text-sm list-disc font-sans">
                                    <li v-for="item in form.berobat_items.filter(i => i.ket)">{{ item.ket }}</li>
                                </ul>
                            </div>
                            <div v-if="form.bon_luar_items.some(i => i.ket)" class="font-sans">
                                <p class="font-medium font-sans">* Lain-lain (bon luar) :</p>
                                <ul class="pl-6 italic text-zinc-600 text-sm list-disc font-sans">
                                    <li v-for="item in form.bon_luar_items.filter(i => i.ket)">{{ item.ket }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="pt-2 font-bold font-sans">
                        <p>5. Total Jumlah WBP : {{ formatOrg(form.total_wbp) }}</p>
                    </div>

                    <div class="space-y-4 pt-4 font-sans">
                        <p class="font-bold">B. PERSONIL PENGAMANAN</p>
                        <div class="space-y-1">
                            <p class="font-bold">1. {{ form.rupam_pilihan || 'RUPAM' }}</p>
                            <div class="grid grid-cols-[150px_15px_1fr] gap-y-0.5 pl-6 font-sans">
                                <div>Jumlah</div><div>:</div><div>{{ formatOrg(form.rupam_jumlah) }}</div>
                                <div>Hadir</div><div>:</div><div>{{ formatOrg(form.rupam_hadir) }}</div>
                                <div>Keterangan</div><div>:</div><div>{{ form.rupam_keterangan || '-' }}</div>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <p class="font-bold">2. SATGAS P2U</p>
                            <div class="grid grid-cols-[150px_15px_1fr] gap-y-0.5 pl-6 font-sans">
                                <div>Jumlah</div><div>:</div><div>{{ formatOrg(form.p2u_jumlah) }}</div>
                                <div>Hadir</div><div>:</div><div>{{ formatOrg(form.p2u_hadir) }}</div>
                                <div>Keterangan</div><div>:</div><div>{{ form.p2u_keterangan || '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2 pt-4 font-sans">
                        <p class="font-bold font-sans">3. Pembagian Tugas :</p>
                        <div class="space-y-1 pl-6">
                            <p>a. <span class="font-bold">Ka.Rupam</span> : {{ form.tugas.ka_rupam || '-' }}</p>
                            <p class="pl-4"><span class="font-bold">Wakarupam</span> : {{ form.tugas.wakarupam || '-' }}</p>
                            <p class="pt-1 font-bold">b. Petugas P2U :</p>
                            <p class="pl-4"><span class="font-bold font-sans">Kasatgas</span> : {{ form.tugas.kasatgas_p2u || '-' }}</p>
                            <p class="pl-4 font-sans"><span class="font-bold">Wakasatgas</span> : {{ form.tugas.wakasatgas_p2u || '-' }}</p>
                            <p class="pt-1 font-bold font-sans">c. Petugas Blok :</p>
                            <p class="pl-4">Blok A : {{ form.tugas.blok_a || '-' }}</p>
                            <p class="pl-4">Blok B : {{ form.tugas.blok_b || '-' }}</p>
                            <p class="pt-1 font-bold font-sans">d. Petugas Pos Atas :</p>
                            <div class="pl-4 grid grid-cols-1 gap-0.5 font-sans">
                                <p>* Menara 1 : {{ form.tugas.menara_1 || '-' }}</p>
                                <p>* Menara 2 : {{ form.tugas.menara_2 || '-' }}</p>
                                <p>* Menara 3 : {{ form.tugas.menara_3 || '-' }}</p>
                                <p>* Menara 4 : {{ form.tugas.menara_4 || '-' }}</p>
                            </div>
                            <div class="pt-3 grid grid-cols-1 gap-1 font-sans">
                                <p>e. Petugas jaga RS : {{ form.tugas.jaga_rs || '-' }}</p>
                                <p>f. Perwira Kontrol : {{ form.tugas.perwira_kontrol || '-' }}</p>
                                <p>g. Perwira Piket : {{ form.tugas.perwira_piket || '-' }}</p>
                                <p>h. Piket Banjaga : {{ form.tugas.banjaga || '-' }}</p>
                                <p>i. Piket Dapur : {{ form.tugas.piket_dapur || '-' }}</p>
                                <p>j. Staf KPLP : {{ form.tugas.staff_kplp || '-' }}</p>
                                <p>k. Amanah : {{ form.tugas.amanah || '-' }}</p>
                                <p>l. <span class="font-bold italic">Petugas Laporan Astekpam :</span> {{ form.tugas.petugas_laporan || '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-zinc-100 font-sans">
                        <p>Demikian Laporan ini kami sampaikan dan diucapkan terima kasih.</p>
                        <br>
                        <div class="space-y-1 font-sans">
                            <p>Wassalamu'alaikum Warahmatullaahi wabarakaatuh</p>
                            <p>Salam Sejahtera</p>
                            <p>Salam Sehat Selalu…🙏</p>
                        </div>
                    </div>
                </div>
            </Card>

            <div class="mt-12 px-4 font-sans">
                <Button @click="submitLaporan" class="w-full h-16 rounded-[2.5rem] text-[11px] font-medium tracking-[0.3em] bg-emerald-600 hover:bg-emerald-700 shadow-2xl shadow-emerald-600/20 text-white transition-all active:scale-95" :disabled="form.processing">
                    <ClipboardCheck class="mr-3 h-5 w-5" /> KIRIM SEKARANG
                </Button>
            </div>
        </div>
    </AuthenticatedLayout>
</template>