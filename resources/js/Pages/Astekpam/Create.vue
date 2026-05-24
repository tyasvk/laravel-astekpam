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
    ChevronLeft, Plus, Trash2, ClipboardCheck, History, FileText, Users, ShieldAlert, Zap
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
        
        <div class="max-w-7xl mx-auto px-4 py-10 bg-zinc-50 min-h-screen">
            <div class="flex items-center justify-between mb-10">
                <div>
                    <h1 class="text-3xl font-extrabold text-zinc-950 tracking-tighter">Laporan Astekpam</h1>
                    <p class="text-zinc-500 font-medium">Sistem Informasi Pengamanan Terpadu</p>
                </div>
                <Button v-if="isPreviewActive" @click="togglePreview" variant="outline" class="rounded-xl">
                    <ChevronLeft class="w-4 h-4 mr-2" /> Edit Kembali
                </Button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <div class="lg:col-span-8 space-y-6">
                    <form @submit.prevent="togglePreview" class="space-y-6" v-show="!isPreviewActive">
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1.5"><Label class="text-xs font-bold text-zinc-500">TANGGAL</Label><Input type="date" v-model="form.tanggal" class="h-12 rounded-xl shadow-sm" /></div>
                            <div class="space-y-1.5"><Label class="text-xs font-bold text-zinc-500">PUKUL</Label>
                                <Select v-model="form.pukul"><SelectTrigger class="h-12 rounded-xl shadow-sm"><SelectValue /></SelectTrigger><SelectContent><SelectItem value="(07.30-13.00)">(07.30-13.00)</SelectItem><SelectItem value="(13.00-19.30)">(13.00-19.30)</SelectItem><SelectItem value="(19.30-07.30)">(19.30-07.30)</SelectItem></SelectContent></Select>
                            </div>
                        </div>

                        <Card class="rounded-3xl border-0 shadow-lg shadow-zinc-200/50">
                            <div class="px-6 py-4 border-b border-zinc-100 flex items-center gap-2 font-bold text-sm text-zinc-700 uppercase tracking-wider"><FileText class="w-4 h-4 text-blue-600"/> Serah Terima</div>
                            <CardContent class="p-6 grid grid-cols-2 gap-4">
                                <div class="space-y-2"><Label class="text-[10px] text-zinc-500">DARI (LAMA)</Label><div class="flex gap-2"><Input v-model="form.dari_rupam" placeholder="Rupam"/><Input v-model="form.dari_shift" placeholder="Shift"/></div></div>
                                <div class="space-y-2"><Label class="text-[10px] text-zinc-500">KE (BARU)</Label><div class="flex gap-2"><Input v-model="form.ke_rupam" placeholder="Rupam"/><Input v-model="form.ke_shift" placeholder="Shift"/></div></div>
                                <div class="col-span-2 space-y-2"><Label class="text-[10px] text-zinc-500">PIMPINAN</Label><Input v-model="form.pimpinan" class="h-11 rounded-xl" /></div>
                            </CardContent>
                        </Card>

                        <Card class="rounded-3xl border-0 shadow-lg shadow-zinc-200/50">
                            <div class="px-6 py-4 border-b border-zinc-100 flex items-center gap-2 font-bold text-sm text-zinc-700 uppercase tracking-wider bg-zinc-900 rounded-t-3xl text-white"><Users class="w-4 h-4 text-emerald-400"/> A. Jumlah Penghuni</div>
                            <CardContent class="p-6 space-y-6">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-2"><Label>Kapasitas</Label><Input v-model="form.kapasitas" /></div>
                                    <div class="space-y-2"><Label>Narapidana</Label><Input v-model="form.narapidana" class="border-indigo-200 bg-indigo-50/50" /></div>
                                </div>
                                <div class="grid grid-cols-4 gap-2">
                                    <div v-for="b in ['blok_a', 'blok_b', 'dapur', 'klinik']" :key="b" class="space-y-1"><Label class="capitalize text-[10px] text-zinc-500">{{ b.replace('_', ' ') }}</Label><Input v-model="form[b]" /></div>
                                </div>
                                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-zinc-100">
                                    <div class="space-y-2"><Label>Dalam Lapas</Label><Input v-model="form.dalam_lapas" /></div>
                                    <div class="space-y-2"><Label>Luar Lapas</Label><Input v-model="form.luar_lapas" /></div>
                                </div>
                                <div v-for="(items, key) in { rawat_inap_items: 'Rawat Inap RS', berobat_items: 'Berobat RS', bon_luar_items: 'Lain-lain (Bon Luar)' }" :key="key" class="space-y-2">
                                    <div class="flex justify-between items-center"><Label class="text-[10px] font-bold uppercase">{{ items }}</Label><Button type="button" @click="addItem(key)" size="sm" variant="ghost" class="h-7 text-[10px]"><Plus class="w-3 h-3 mr-1"/> Tambah</Button></div>
                                    <div v-for="(item, i) in form[key]" :key="i" class="flex gap-2"><Input v-model="item.ket" class="h-10"/><Button v-if="form[key].length > 1" type="button" @click="removeItem(key, i)" variant="ghost" size="icon" class="h-10 w-10 text-rose-500"><Trash2 class="w-4 h-4"/></Button></div>
                                </div>
                            </CardContent>
                        </Card>

                        <Card class="rounded-3xl border-0 shadow-lg shadow-zinc-200/50">
                            <div class="px-6 py-4 border-b border-zinc-100 flex items-center gap-2 font-bold text-sm text-zinc-700 uppercase tracking-wider"><ShieldAlert class="w-4 h-4 text-rose-600"/> B. Personil Pengamanan</div>
                            <CardContent class="p-6 space-y-6">
                                <div class="grid grid-cols-3 gap-3">
                                    <div class="col-span-3 text-[10px] font-bold text-zinc-400 uppercase">1. Rupam</div>
                                    <Input v-model="form.rupam_pilihan" placeholder="Pilihan" class="bg-zinc-50"/>
                                    <Input v-model="form.rupam_jumlah" placeholder="Jumlah" class="bg-zinc-50"/>
                                    <Input v-model="form.rupam_hadir" placeholder="Hadir" class="bg-zinc-50"/>
                                    <Input class="col-span-3 bg-zinc-50" v-model="form.rupam_keterangan" placeholder="Keterangan"/>
                                </div>
                                <div class="grid grid-cols-3 gap-3 pt-4 border-t">
                                    <div class="col-span-3 text-[10px] font-bold text-zinc-400 uppercase">2. Satgas P2U</div>
                                    <Input v-model="form.p2u_jumlah" placeholder="Jumlah" class="bg-zinc-50"/>
                                    <Input v-model="form.p2u_hadir" placeholder="Hadir" class="bg-zinc-50"/>
                                    <Input v-model="form.p2u_keterangan" placeholder="Keterangan" class="bg-zinc-50"/>
                                </div>
                            </CardContent>
                        </Card>

                        <Card class="rounded-3xl border-0 shadow-lg shadow-zinc-200/50">
                            <div class="px-6 py-4 border-b border-zinc-100 flex items-center gap-2 font-bold text-sm text-zinc-700 uppercase tracking-wider"><Zap class="w-4 h-4 text-amber-500"/> 3. Pembagian Tugas</div>
                            <CardContent class="p-6 space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-1"><Label class="text-[10px]">Ka. Rupam</Label><Input v-model="form.tugas.ka_rupam" /></div>
                                    <div class="space-y-1"><Label class="text-[10px]">Wakarupam</Label><Input v-model="form.tugas.wakarupam" /></div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-1"><Label class="text-[10px]">Kasatgas P2U</Label><Input v-model="form.tugas.kasatgas_p2u" /></div>
                                    <div class="space-y-1"><Label class="text-[10px]">Wakasatgas P2U</Label><Input v-model="form.tugas.wakasatgas_p2u" /></div>
                                </div>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 pt-4 border-t">
                                    <div v-for="label, key in { blok_a: 'Blok A', blok_b: 'Blok B', menara_1: 'M1', menara_2: 'M2', menara_3: 'M3', menara_4: 'M4', jaga_rs: 'Jaga RS', perwira_kontrol: 'P.Kontrol', perwira_piket: 'P.Piket', banjaga: 'Banjaga', piket_dapur: 'Piket Dapur', staff_kplp: 'Staf KPLP', amanah: 'Amanah', petugas_laporan: 'Petugas Laporan' }" :key="key" class="space-y-1">
                                        <Label class="text-[9px] text-zinc-500">{{label}}</Label><Input v-model="form.tugas[key]" class="h-9" />
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        <Button type="submit" class="w-full h-14 rounded-2xl bg-zinc-900 font-bold hover:bg-zinc-800 shadow-xl transition-all">Pratinjau Laporan</Button>
                    </form>
                </div>

                <aside class="lg:col-span-4 sticky top-8">
                    <div class="bg-indigo-700 rounded-3xl p-8 text-white shadow-xl shadow-indigo-200">
                        <h3 class="font-bold mb-6 flex items-center gap-2"><History class="w-5 h-5"/> Riwayat Shift</h3>
                        <div v-if="props.lastReport" class="space-y-4">
                            <div class="flex justify-between border-b border-indigo-500/50 pb-3">
                                <span class="text-indigo-200 text-sm">Narapidana</span>
                                <span class="font-bold text-lg">{{ props.lastReport.narapidana }}</span>
                            </div>
                            <div class="flex justify-between border-b border-indigo-500/50 pb-3">
                                <span class="text-indigo-200 text-sm">Total WBP</span>
                                <span class="font-bold text-lg">{{ props.lastReport.total_wbp }}</span>
                            </div>
                        </div>
                        <div v-else class="text-indigo-200 italic text-sm">Belum ada data shift sebelumnya.</div>
                    </div>
                </aside>

            </div>
        </div>

        <div v-show="isPreviewActive" class="max-w-3xl mx-auto py-10 px-4">
             <div class="bg-white rounded-3xl p-10 border border-zinc-100 shadow-xl">
                 <Button @click="submitLaporan" class="w-full mt-8 h-14 bg-emerald-600 rounded-2xl font-bold">KIRIM SEKARANG</Button>
             </div>
        </div>
    </AuthenticatedLayout>
</template>