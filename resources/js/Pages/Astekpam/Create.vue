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
    ChevronLeft, Plus, Trash2, FileText, Users, ShieldAlert, Zap, History
} from 'lucide-vue-next';

const props = defineProps({
    latestReport: Object,
    lastReport: Object
});

const previousReportData = computed(() => props.latestReport || props.lastReport);

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
    kapasitas: previousReportData.value?.kapasitas ?? 813,
    narapidana: previousReportData.value?.narapidana ?? 0,
    blok_a: previousReportData.value?.blok_a ?? 0,
    blok_b: previousReportData.value?.blok_b ?? 0,
    dapur: previousReportData.value?.dapur ?? 0,
    klinik: previousReportData.value?.klinik ?? 0,
    dalam_lapas: previousReportData.value?.dalam_lapas ?? 0,
    luar_lapas: previousReportData.value?.luar_lapas ?? 0,
    
    // Keterangan Luar Lapas (Dynamic Arrays)
    rawat_inap_items: [{ ket: '' }],
    berobat_items: [{ ket: '' }],
    bon_luar_items: [{ ket: '' }],
    
    total_wbp: previousReportData.value?.total_wbp ?? 0,

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

const getPreviousData = (key) => {
    if (!previousReportData.value) return null;
    let data = previousReportData.value[key];
    
    if (data === undefined || data === null) return '(-)';

    if (typeof data === 'string' && data.startsWith('[')) {
        try { data = JSON.parse(data); } catch(e) { data = []; }
    }

    if (Array.isArray(data)) {
        const validItems = data.filter(item => item && item.ket && String(item.ket).trim() !== '');
        return validItems.length === 0 ? '(-)' : `${validItems.length} Data`;
    }

    return data !== '' ? data : '(-)';
};

const addItem = (type) => form[type].push({ ket: '' });
const removeItem = (type, index) => {
    if (form[type].length > 1) form[type].splice(index, 1);
    else form[type][0].ket = '';
};

watch(() => form.tugas.ka_rupam, (val) => form.tugas.petugas_laporan = val);

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
        
        <div class="max-w-7xl mx-auto px-4 py-8 bg-zinc-50/50 min-h-screen">
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-zinc-900 tracking-tight">Laporan Astekpam</h1>
                    <p class="text-zinc-500 font-medium mt-1">Sistem Informasi Pengamanan Terpadu</p>
                </div>
                <Button v-if="isPreviewActive" @click="togglePreview" variant="outline" class="rounded-xl h-11 px-6 shadow-sm">
                    <ChevronLeft class="w-4 h-4 mr-2" /> Kembali Edit
                </Button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <div class="lg:col-span-8">
                    <form @submit.prevent="togglePreview" class="space-y-6" v-if="!isPreviewActive">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <Label class="text-xs font-bold text-zinc-500 tracking-wider">TANGGAL</Label>
                                <Input type="date" v-model="form.tanggal" class="h-12 rounded-xl bg-white border-zinc-200 shadow-sm transition-all focus:ring-2 focus:ring-blue-500" />
                            </div>
                            <div class="space-y-1.5">
                                <Label class="text-xs font-bold text-zinc-500 tracking-wider">PUKUL</Label>
                                <Select v-model="form.pukul">
                                    <SelectTrigger class="h-12 rounded-xl bg-white border-zinc-200 shadow-sm transition-all focus:ring-2 focus:ring-blue-500">
                                        <SelectValue />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="(07.30-13.00)">(07.30-13.00)</SelectItem>
                                        <SelectItem value="(13.00-19.30)">(13.00-19.30)</SelectItem>
                                        <SelectItem value="(19.30-07.30)">(19.30-07.30)</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>

                        <Card class="rounded-2xl border border-zinc-200 shadow-sm overflow-hidden bg-white">
                            <div class="px-6 py-4 border-b border-zinc-100 flex items-center gap-2 font-bold text-sm text-zinc-800 bg-zinc-50">
                                <FileText class="w-4 h-4 text-blue-600"/> SERAH TERIMA
                            </div>
                            <CardContent class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <Label class="text-xs font-semibold text-zinc-500">DARI (LAMA)</Label>
                                    <div class="flex gap-2">
                                        <Select v-model="form.dari_rupam">
                                            <SelectTrigger class="h-11 rounded-lg bg-zinc-50 border-0 focus:ring-1 focus:ring-blue-500 w-full">
                                                <SelectValue placeholder="Rupam" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="Rupam I">Rupam I</SelectItem>
                                                <SelectItem value="Rupam II">Rupam II</SelectItem>
                                                <SelectItem value="Rupam III">Rupam III</SelectItem>
                                                <SelectItem value="Rupam IV">Rupam IV</SelectItem>
                                            </SelectContent>
                                        </Select>

                                        <Select v-model="form.dari_shift">
                                            <SelectTrigger class="h-11 rounded-lg bg-zinc-50 border-0 focus:ring-1 focus:ring-blue-500 w-full">
                                                <SelectValue placeholder="Shift" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="Pagi">Pagi</SelectItem>
                                                <SelectItem value="Siang">Siang</SelectItem>
                                                <SelectItem value="Malam">Malam</SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <Label class="text-xs font-semibold text-zinc-500">KE (BARU)</Label>
                                    <div class="flex gap-2">
                                        <Select v-model="form.ke_rupam">
                                            <SelectTrigger class="h-11 rounded-lg bg-zinc-50 border-0 focus:ring-1 focus:ring-blue-500 w-full">
                                                <SelectValue placeholder="Rupam" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="Rupam I">Rupam I</SelectItem>
                                                <SelectItem value="Rupam II">Rupam II</SelectItem>
                                                <SelectItem value="Rupam III">Rupam III</SelectItem>
                                                <SelectItem value="Rupam IV">Rupam IV</SelectItem>
                                            </SelectContent>
                                        </Select>

                                        <Select v-model="form.ke_shift">
                                            <SelectTrigger class="h-11 rounded-lg bg-zinc-50 border-0 focus:ring-1 focus:ring-blue-500 w-full">
                                                <SelectValue placeholder="Shift" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="Pagi">Pagi</SelectItem>
                                                <SelectItem value="Siang">Siang</SelectItem>
                                                <SelectItem value="Malam">Malam</SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                </div>

                                <div class="md:col-span-2 space-y-2">
                                    <Label class="text-xs font-semibold text-zinc-500">DIPIMPIN</Label>
                                    <Input v-model="form.pimpinan" class="h-11 rounded-lg bg-zinc-50 border-0 focus:ring-1 focus:ring-blue-500 focus:bg-white transition-colors" />
                                </div>
                            </CardContent>
                        </Card>

                        <Card class="rounded-2xl border border-zinc-200 shadow-sm overflow-hidden bg-white">
                            <div class="px-6 py-4 border-b border-zinc-100 flex items-center gap-2 font-bold text-sm text-white tracking-wider bg-zinc-900">
                                <Users class="w-4 h-4 text-emerald-400"/> A. JUMLAH PENGHUNI
                            </div>
                            <CardContent class="p-6 space-y-6">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <Label class="text-xs font-medium text-zinc-600 flex justify-between items-center w-full">
                                            <span>Kapasitas</span>
                                            <span v-if="getPreviousData('kapasitas')" class="text-[10px] bg-zinc-100 px-2 py-0.5 rounded-md text-zinc-500 font-bold">= {{ getPreviousData('kapasitas') }}</span>
                                        </Label>
                                        <Input v-model="form.kapasitas" class="h-11 rounded-lg"/>
                                    </div>
                                    <div class="space-y-2">
                                        <Label class="text-xs font-bold text-indigo-700 flex justify-between items-center w-full">
                                            <span>Narapidana</span>
                                            <span v-if="getPreviousData('narapidana')" class="text-[11px] bg-indigo-100 px-2 py-0.5 rounded-md text-indigo-600 font-extrabold">= {{ getPreviousData('narapidana') }}</span>
                                        </Label>
                                        <Input v-model="form.narapidana" class="h-11 rounded-lg border-indigo-200 bg-indigo-50/50 font-semibold text-indigo-900 focus:ring-indigo-500" />
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <div v-for="b in ['blok_a', 'blok_b', 'dapur', 'klinik']" :key="b" class="space-y-1.5">
                                        <Label class="capitalize text-xs font-medium text-zinc-600 flex justify-between items-center w-full">
                                            <span>{{ b.replace('_', ' ') }}</span>
                                            <span v-if="getPreviousData(b)" class="text-[10px] bg-zinc-100 px-2 py-0.5 rounded-md text-zinc-500 font-bold">= {{ getPreviousData(b) }}</span>
                                        </Label>
                                        <Input v-model="form[b]" class="h-11 rounded-lg bg-zinc-50 border-0 focus:bg-white focus:ring-1 focus:ring-blue-500" />
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-zinc-100">
                                    <div class="space-y-2">
                                        <Label class="text-xs font-medium text-zinc-600 flex justify-between items-center w-full">
                                            <span>Dalam Lapas</span>
                                            <span v-if="getPreviousData('dalam_lapas')" class="text-[10px] bg-zinc-100 px-2 py-0.5 rounded-md text-zinc-500 font-bold">= {{ getPreviousData('dalam_lapas') }}</span>
                                        </Label>
                                        <Input v-model="form.dalam_lapas" class="h-11 rounded-lg" />
                                    </div>
                                    <div class="space-y-2">
                                        <Label class="text-xs font-medium text-zinc-600 flex justify-between items-center w-full">
                                            <span>Luar Lapas</span>
                                            <span v-if="getPreviousData('luar_lapas')" class="text-[10px] bg-zinc-100 px-2 py-0.5 rounded-md text-zinc-500 font-bold">= {{ getPreviousData('luar_lapas') }}</span>
                                        </Label>
                                        <Input v-model="form.luar_lapas" class="h-11 rounded-lg" />
                                    </div>
                                </div>
                                
                                <div v-for="(items, key) in { rawat_inap_items: 'Rawat Inap RS', berobat_items: 'Berobat RS', bon_luar_items: 'Lain-lain (Bon Luar)' }" :key="key" class="space-y-3 pt-2">
                                    <div class="flex justify-between items-center bg-zinc-50 px-3 py-2 rounded-lg border border-zinc-100">
                                        <Label class="text-[11px] font-bold text-zinc-700 uppercase flex items-center gap-3">
                                            <span>{{ items }}</span>
                                            <span v-if="getPreviousData(key)" class="text-[9px] bg-blue-100/50 text-blue-600 px-2 py-0.5 rounded-md font-bold normal-case border border-blue-200">
                                                Sblm: {{ getPreviousData(key) }}
                                            </span>
                                        </Label>
                                        <Button type="button" @click="addItem(key)" size="sm" variant="ghost" class="h-7 text-xs hover:bg-zinc-200">
                                            <Plus class="w-3 h-3 mr-1"/> Tambah
                                        </Button>
                                    </div>
                                    <div v-for="(item, i) in form[key]" :key="i" class="flex gap-2">
                                        <Input v-model="item.ket" class="h-11 rounded-lg" placeholder="Masukkan keterangan..."/>
                                        <Button v-if="form[key].length > 1" type="button" @click="removeItem(key, i)" variant="ghost" class="h-11 w-11 px-0 text-rose-500 hover:bg-rose-50 hover:text-rose-600">
                                            <Trash2 class="w-4 h-4"/>
                                        </Button>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        <Card class="rounded-2xl border border-zinc-200 shadow-sm overflow-hidden bg-white">
                            <div class="px-6 py-4 border-b border-zinc-100 flex items-center gap-2 font-bold text-sm text-zinc-800 bg-zinc-50">
                                <ShieldAlert class="w-4 h-4 text-rose-600"/> B. PERSONIL PENGAMANAN
                            </div>
                            <CardContent class="p-6 space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="md:col-span-3 text-xs font-bold text-zinc-400 uppercase tracking-wider mb-1">
                                        1. Rupam
                                    </div>
                                    <Input v-model="form.rupam_pilihan" placeholder="Pilihan (Contoh: Rupam I)" class="h-11 rounded-lg bg-zinc-50 border-0 focus:bg-white focus:ring-1 focus:ring-blue-500"/>
                                    <Input v-model="form.rupam_jumlah" placeholder="Jumlah Personil" class="h-11 rounded-lg bg-zinc-50 border-0 focus:bg-white focus:ring-1 focus:ring-blue-500"/>
                                    <Input v-model="form.rupam_hadir" placeholder="Jumlah Hadir" class="h-11 rounded-lg bg-zinc-50 border-0 focus:bg-white focus:ring-1 focus:ring-blue-500"/>
                                    <Input class="md:col-span-3 h-11 rounded-lg bg-zinc-50 border-0 focus:bg-white focus:ring-1 focus:ring-blue-500" v-model="form.rupam_keterangan" placeholder="Keterangan Tambahan..."/>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-6 border-t border-zinc-100">
                                    <div class="md:col-span-3 text-xs font-bold text-zinc-400 uppercase tracking-wider mb-1">2. Satgas P2U</div>
                                    <Input v-model="form.p2u_jumlah" placeholder="Jumlah" class="h-11 rounded-lg bg-zinc-50 border-0 focus:bg-white focus:ring-1 focus:ring-blue-500"/>
                                    <Input v-model="form.p2u_hadir" placeholder="Hadir" class="h-11 rounded-lg bg-zinc-50 border-0 focus:bg-white focus:ring-1 focus:ring-blue-500"/>
                                    <Input v-model="form.p2u_keterangan" placeholder="Keterangan" class="h-11 rounded-lg bg-zinc-50 border-0 focus:bg-white focus:ring-1 focus:ring-blue-500"/>
                                </div>
                            </CardContent>
                        </Card>

                        <Card class="rounded-2xl border border-zinc-200 shadow-sm overflow-hidden bg-white">
                            <div class="px-6 py-4 border-b border-zinc-100 flex items-center gap-2 font-bold text-sm text-zinc-800 bg-zinc-50">
                                <Zap class="w-4 h-4 text-amber-500"/> 3. PEMBAGIAN TUGAS
                            </div>
                            <CardContent class="p-6 space-y-5">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-1.5"><Label class="text-xs font-medium">Ka. Rupam</Label><Input v-model="form.tugas.ka_rupam" class="h-11 rounded-lg" /></div>
                                    <div class="space-y-1.5"><Label class="text-xs font-medium">Wakarupam</Label><Input v-model="form.tugas.wakarupam" class="h-11 rounded-lg" /></div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-1.5"><Label class="text-xs font-medium">Kasatgas P2U</Label><Input v-model="form.tugas.kasatgas_p2u" class="h-11 rounded-lg" /></div>
                                    <div class="space-y-1.5"><Label class="text-xs font-medium">Wakasatgas P2U</Label><Input v-model="form.tugas.wakasatgas_p2u" class="h-11 rounded-lg" /></div>
                                </div>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-6 border-t border-zinc-100">
                                    <div v-for="label, key in { blok_a: 'Blok A', blok_b: 'Blok B', menara_1: 'Menara 1', menara_2: 'Menara 2', menara_3: 'Menara 3', menara_4: 'Menara 4', jaga_rs: 'Jaga RS', perwira_kontrol: 'P.Kontrol', perwira_piket: 'P.Piket', banjaga: 'Banjaga', piket_dapur: 'Piket Dapur', staff_kplp: 'Staf KPLP', amanah: 'Amanah', petugas_laporan: 'Petugas Laporan' }" :key="key" class="space-y-1.5">
                                        <Label class="text-[10px] text-zinc-500 font-semibold tracking-wide uppercase">{{label}}</Label>
                                        <Input v-model="form.tugas[key]" class="h-10 rounded-lg text-sm bg-zinc-50 border-0 focus:bg-white focus:ring-1 focus:ring-blue-500" />
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        <div class="pt-4 sticky bottom-6 z-10">
                            <Button type="submit" class="w-full h-14 rounded-2xl bg-zinc-900 text-white font-bold text-base hover:bg-zinc-800 shadow-xl shadow-zinc-300 transition-all active:scale-[0.99]">
                                Pratinjau Laporan
                            </Button>
                        </div>
                    </form>

                    <div v-if="isPreviewActive" class="animate-in fade-in slide-in-from-bottom-4 duration-500">
                         <div class="bg-white rounded-3xl p-8 border border-zinc-200 shadow-xl shadow-zinc-200/50 text-center">
                             <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                 <FileText class="w-8 h-8" />
                             </div>
                             <h2 class="text-2xl font-bold text-zinc-900 mb-2">Konfirmasi Laporan</h2>
                             <p class="text-zinc-500 mb-8 max-w-sm mx-auto">Silakan periksa kembali pratinjau laporan Anda. Apakah data sudah sesuai dan siap dikirim?</p>
                             <Button @click="submitLaporan" class="w-full h-14 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-bold text-lg shadow-lg shadow-emerald-200 transition-all active:scale-[0.99]">
                                 KIRIM LAPORAN SEKARANG
                             </Button>
                         </div>
                    </div>
                </div>

                <aside class="lg:col-span-4">
                    <div class="sticky top-8">
                        <div class="bg-gradient-to-br from-indigo-700 to-indigo-900 rounded-3xl p-8 text-white shadow-xl shadow-indigo-200">
                            <h3 class="font-bold mb-6 flex items-center gap-2 text-lg">
                                <History class="w-5 h-5 text-indigo-300"/> Riwayat Shift
                            </h3>
                            <div v-if="previousReportData" class="space-y-4">
                                <div class="flex justify-between border-b border-indigo-500/50 pb-3 items-center">
                                    <span class="text-indigo-200 text-sm font-medium">Narapidana</span>
                                    <span class="font-extrabold text-xl">{{ previousReportData.narapidana }}</span>
                                </div>
                                <div class="flex justify-between border-b border-indigo-500/50 pb-3 items-center">
                                    <span class="text-indigo-200 text-sm font-medium">Total WBP</span>
                                    <span class="font-extrabold text-xl">{{ previousReportData.total_wbp }}</span>
                                </div>
                            </div>
                            <div v-else class="text-indigo-200/80 bg-indigo-900/30 p-4 rounded-xl italic text-sm text-center">
                                Belum ada data riwayat shift sebelumnya.
                            </div>
                        </div>
                    </div>
                </aside>

            </div>
        </div>
    </AuthenticatedLayout>
</template>