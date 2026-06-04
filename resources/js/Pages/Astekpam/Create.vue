<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { computed, watch, ref } from 'vue';
import { Card, CardContent } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Button } from '@/Components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { 
    ChevronLeft, Plus, Trash2, FileText, Users, ShieldAlert, Zap, Check, Calendar, MapPin,
    Image as ImageIcon, UploadCloud
} from 'lucide-vue-next';

const props = defineProps({
    latestReport: Object,
    lastReport: Object,
    users: {
        type: Array,
        default: () => []
    },
    pejabats: { 
        type: Array,
        default: () => []
    }
});

const previousReportData = computed(() => props.latestReport || props.lastReport);
const isPreviewActive = ref(false);

// State untuk Preview Foto
const previewUrl = ref(null);

const anggotaReguOptions = ref([]);

// Filter Data Pejabat Berdasarkan Kategori
const perwiraPiketOptions = computed(() => {
    return props.pejabats.filter(p => p.kategori === 'Perwira Piket');
});
const pengawasPiketOptions = computed(() => {
    return props.pejabats.filter(p => p.kategori === 'Pengawas Piket');
});
const staffKplpOptions = computed(() => {
    return props.pejabats.filter(p => p.kategori === 'Staff KPLP');
});

const dropdownTugas = {
    blok_a: 'Blok A', blok_b: 'Blok B', 
    menara_1: 'Menara 1', menara_2: 'Menara 2', 
    menara_3: 'Menara 3', menara_4: 'Menara 4'
};

const inputTugas = {
    jaga_rs: 'Petugas Jaga RS', 
    banjaga: 'Piket Banjaga', 
    piket_dapur: 'Piket Dapur', 
    amanah: 'Amanah', 
    petugas_laporan: 'Petugas Laporan'
};

// ==========================================
// FUNGSI TARIK DATA LAPORAN SEBELUMNYA
// ==========================================
const getInitialItems = (key) => {
    if (!previousReportData.value) return [{ ket: '' }];
    
    let data = previousReportData.value[key];
    
    if (typeof data === 'string' && data.startsWith('[')) {
        try { data = JSON.parse(data); } catch(e) { data = []; }
    }

    if (Array.isArray(data) && data.length > 0) {
        const validItems = data.filter(item => item && item.ket && String(item.ket).trim() !== '');
        if (validItems.length > 0) {
            return validItems.map(item => ({ ket: item.ket }));
        }
    }

    return [{ ket: '' }];
};
// ==========================================

const form = useForm({
    tanggal: new Date().toISOString().split('T')[0],
    pukul: '(13.00-19.30)',
    dari_rupam: '',
    dari_shift: '',
    ke_rupam: '',
    ke_shift: '',
    pimpinan: 'STAF KPLP',
    
    // Field Foto Laporan
    foto_laporan: null,
    
    kapasitas: previousReportData.value?.kapasitas ?? 813,
    narapidana: previousReportData.value?.narapidana ?? 0,
    blok_a: previousReportData.value?.blok_a ?? 0,
    blok_b: previousReportData.value?.blok_b ?? 0,
    dapur: previousReportData.value?.dapur ?? 0,
    klinik: previousReportData.value?.klinik ?? 0,
    dalam_lapas: previousReportData.value?.dalam_lapas ?? 0,
    luar_lapas: previousReportData.value?.luar_lapas ?? 0,
    
    rawat_inap_items: getInitialItems('rawat_inap_items'),
    berobat_items: getInitialItems('berobat_items'),
    bon_luar_items: getInitialItems('bon_luar_items'),
    
    total_wbp: previousReportData.value?.total_wbp ?? 0,

    rupam_pilihan: '',
    rupam_jumlah: 9,
    rupam_hadir: 9,
    rupam_keterangan: '',
    p2u_jumlah: 2,
    p2u_hadir: 2,
    p2u_keterangan: '',

    tugas: {
        ka_rupam: '', wakarupam: '', kasatgas_p2u: '', wakasatgas_p2u: '',
        
        blok_a: { jam_1: '', jam_2: '', jam_3: '' },
        blok_b: { jam_1: '', jam_2: '', jam_3: '' },
        menara_1: { jam_1: '', jam_2: '', jam_3: '' },
        menara_2: { jam_1: '', jam_2: '', jam_3: '' },
        menara_3: { jam_1: '', jam_2: '', jam_3: '' },
        menara_4: { jam_1: '', jam_2: '', jam_3: '' },

        perwira_kontrol: '', perwira_piket: '', staff_kplp: '',
        jaga_rs: '', banjaga: '', 
        piket_dapur: '', amanah: '', petugas_laporan: ''
    }
});

// ==========================================
// HANDLER UPLOAD FOTO
// ==========================================
const handleFileUpload = (e) => {
    const file = e.target.files[0];
    
    if (file) {
        if (file.size > 10 * 1024 * 1024) {
            alert('Ukuran file tidak boleh lebih dari 10MB!');
            e.target.value = ''; // Kosongkan input
            form.foto_laporan = null;
            previewUrl.value = null;
            return;
        }

        form.foto_laporan = file;
        previewUrl.value = URL.createObjectURL(file);
    } else {
        form.foto_laporan = null;
        previewUrl.value = null;
    }
};

const jumlahJam = computed(() => form.ke_shift === 'Malam' ? 3 : 2);

const getPreviousData = (key) => {
    if (!previousReportData.value) return null;
    let data = previousReportData.value[key];
    if (data === undefined || data === null) return '(-)';
    if (typeof data === 'string' && data.startsWith('[')) {
        try { data = JSON.parse(data); } catch(e) { data = []; }
    }
    if (Array.isArray(data)) {
        const validItems = data.filter(item => item && item.ket && String(item.ket).trim() !== '');
        return validItems.length === 0 ? '(-)' : `${validItems.length} Orang`;
    }
    return data !== '' ? data : '(-)';
};

const addItem = (type) => form[type].push({ ket: '' });
const removeItem = (type, index) => {
    if (form[type].length > 1) form[type].splice(index, 1);
    else form[type][0].ket = '';
};

const displayHari = computed(() => {
    if (!form.tanggal) return '';
    return new Intl.DateTimeFormat('id-ID', { 
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' 
    }).format(new Date(form.tanggal));
});

const salamWaktu = computed(() => {
    const pukul = form.pukul || '';
    if (pukul.includes('07.30-13.00')) return 'Pagi';
    if (pukul.includes('13.00-19.30')) return 'Siang';
    if (pukul.includes('19.30-07.30')) return 'Malam';
    return 'Siang';
});

const formatOrg = (val) => (val !== null && val !== undefined && val !== '') ? `${val} Org` : '-';

const tidakHadirRupam = computed(() => {
    const diff = Number(form.rupam_jumlah) - Number(form.rupam_hadir);
    return diff > 0 ? `${diff} Org` : '-';
});

const formatJam = (posData) => {
    if (!posData) return '-';
    const jams = [posData.jam_1, posData.jam_2, posData.jam_3].filter(j => j && j !== '-');
    return jams.length > 0 ? jams.join('/') : '-';
};

const formatKetLuar = (items) => {
    if (!items) return '-';
    const valid = items.filter(i => i.ket && String(i.ket).trim() !== '');
    if (valid.length === 0) return '-';
    return valid.map(i => i.ket).join(', ');
};

watch(() => form.rupam_pilihan, (newRupam) => {
    const bersihkanTeks = (teks) => String(teks || '').toLowerCase().replace(/[\s.]/g, '');
    
    Object.keys(dropdownTugas).forEach(key => {
        form.tugas[key] = { jam_1: '', jam_2: '', jam_3: '' };
    });

    if (newRupam && props.users && props.users.length > 0) {
        const targetRegu = bersihkanTeks(newRupam);

        const anggotaRegu = props.users.filter(u => {
            const dbReguPengamanan = bersihkanTeks(u.regu_pengamanan);
            const dbReguBiasa = bersihkanTeks(u.regu);
            return dbReguPengamanan === targetRegu || dbReguBiasa === targetRegu.replace('rupam', '');
        });

        anggotaReguOptions.value = anggotaRegu;

        const cariJabatan = (kataKunci) => {
            const user = anggotaRegu.find(u => bersihkanTeks(u.jabatan).includes(kataKunci));
            return user ? user.name : '';
        };

        form.tugas.ka_rupam = cariJabatan('karupam');
        form.tugas.wakarupam = cariJabatan('wakarupam');
        form.tugas.kasatgas_p2u = cariJabatan('kasatgas'); 
        form.tugas.wakasatgas_p2u = cariJabatan('wakasatgas');

    } else {
        anggotaReguOptions.value = [];
        form.tugas.ka_rupam = ''; form.tugas.wakarupam = '';
        form.tugas.kasatgas_p2u = ''; form.tugas.wakasatgas_p2u = '';
    }
});

watch(() => form.tugas.ka_rupam, (val) => form.tugas.petugas_laporan = val);
watch(() => [form.dalam_lapas, form.luar_lapas], ([dalam, luar]) => form.total_wbp = Number(dalam) + Number(luar));

const togglePreview = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
    isPreviewActive.value = !isPreviewActive.value;
};

// ==========================================
// FUNGSI SUBMIT DIPERBAIKI 
// ==========================================
const submitLaporan = () => {
    form.post(route('astekpam.store'), {
        forceFormData: true, // SANGAT PENTING untuk mengirim file foto
        preserveScroll: true,
        onError: (errors) => {
            console.error(errors);
            alert("GAGAL MENYIMPAN! Terdapat field wajib yang belum diisi. Silakan cek kembali form berwarna merah.");
            
            // Otomatis kembalikan layar ke Form agar user bisa melihat errornya
            if (isPreviewActive.value) {
                isPreviewActive.value = false;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }
    });
};
</script>

<template>
    <Head title="Buat Laporan Astekpam" />
    <AuthenticatedLayout>
        
        <div class="px-4 py-6 sm:py-8 bg-zinc-50/50 min-h-screen">
            <div class="max-w-4xl mx-auto w-full">
                
                <div class="mb-5 sm:mb-8 flex items-start">
                    <Link :href="route('dashboard')">
                        <Button variant="outline" size="sm" class="rounded-full bg-white border-zinc-200 text-zinc-600 hover:text-zinc-900 shadow-sm h-10 sm:h-9 px-4 sm:px-4 font-bold tracking-wide w-full sm:w-auto">
                            <ChevronLeft class="w-4 h-4 mr-1.5" /> Kembali ke Dashboard
                        </Button>
                    </Link>
                </div>

                <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4 font-sans">
                    <div class="text-center sm:text-left">
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-zinc-900 tracking-tight">Laporan Astekpam</h1>
                        <p class="text-zinc-500 text-sm font-medium mt-1">Sistem Informasi Pengamanan Terpadu</p>
                    </div>
                    <Button v-if="isPreviewActive" type="button" @click="togglePreview" variant="outline" class="rounded-xl h-12 sm:h-11 px-6 shadow-sm w-full sm:w-auto">
                        <ChevronLeft class="w-4 h-4 mr-2" /> Kembali Edit Form
                    </Button>
                </div>

                <div v-if="Object.keys(form.errors).length > 0 && !isPreviewActive" class="mb-6 p-4 bg-red-50/80 border border-red-200 rounded-xl text-red-600 text-sm font-semibold">
                    <div class="flex items-center gap-2 mb-2">
                        <ShieldAlert class="w-5 h-5" /> Gagal Menyimpan. Ada field yang belum diisi:
                    </div>
                    <ul class="list-disc pl-5 text-xs text-red-500">
                        <li v-for="(error, key) in form.errors" :key="key">{{ key }}: {{ error }}</li>
                    </ul>
                </div>

                <form @submit.prevent class="space-y-5 sm:space-y-6" v-if="!isPreviewActive">
                    
                    <Card class="rounded-2xl border border-zinc-200 shadow-sm overflow-hidden bg-white">
                        <div class="px-5 sm:px-6 py-4 border-b border-zinc-100 flex items-center gap-2 font-bold text-sm text-zinc-800 bg-zinc-50">
                            <ImageIcon class="w-4 h-4 text-blue-600"/> BUKTI FOTO LAPORAN
                        </div>
                        <CardContent class="p-5 sm:p-6">
                            <div class="flex flex-col md:flex-row items-start gap-6">
                                <div class="flex-1 w-full">
                                    <Label class="text-[11px] sm:text-xs font-bold text-zinc-500 tracking-wider mb-2 block">UPLOAD FOTO (MAKS. 10MB)</Label>
                                    
                                    <div class="relative group mt-2">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                            <UploadCloud class="w-5 h-5 text-zinc-400 group-hover:text-blue-500 transition-colors" />
                                        </div>
                                        <input 
                                            type="file" 
                                            @change="handleFileUpload" 
                                            accept="image/png, image/jpeg, image/jpg"
                                            class="block w-full text-sm text-zinc-500 file:hidden pl-10 pr-4 py-3 sm:py-2.5 border border-zinc-200 rounded-xl sm:rounded-lg cursor-pointer bg-zinc-50 hover:bg-zinc-100 focus:outline-none transition-colors"
                                        />
                                        <div class="absolute inset-y-0 left-10 flex items-center pointer-events-none">
                                            <span v-if="!form.foto_laporan" class="text-sm text-zinc-400 font-medium">Pilih file gambar...</span>
                                            <span v-else class="text-sm text-zinc-800 font-medium truncate pr-4">{{ form.foto_laporan.name }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="shrink-0 w-full md:w-40 flex flex-col">
                                    <Label class="text-[11px] sm:text-xs font-bold text-zinc-500 tracking-wider mb-2 block">PREVIEW GAMBAR</Label>
                                    <div class="w-full h-40 md:w-40 md:h-40 rounded-xl border-2 border-dashed border-zinc-200 flex items-center justify-center bg-zinc-50 overflow-hidden relative">
                                        <img v-if="previewUrl" :src="previewUrl" alt="Preview" class="w-full h-full object-cover" />
                                        <ImageIcon v-else class="w-8 h-8 text-zinc-300" />
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <Label class="text-[11px] sm:text-xs font-bold text-zinc-500 tracking-wider">TANGGAL LAPORAN</Label>
                            <Input type="date" v-model="form.tanggal" class="h-12 sm:h-11 text-base sm:text-sm rounded-xl bg-white border-zinc-200 shadow-sm transition-all focus:ring-2 focus:ring-blue-500" />
                        </div>
                        <div class="space-y-1.5">
                            <Label class="text-[11px] sm:text-xs font-bold text-zinc-500 tracking-wider">PUKUL (SHIFT)</Label>
                            <Select v-model="form.pukul">
                                <SelectTrigger class="h-12 sm:h-11 text-base sm:text-sm rounded-xl bg-white border-zinc-200 shadow-sm transition-all focus:ring-2 focus:ring-blue-500">
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
                        <div class="px-5 sm:px-6 py-4 border-b border-zinc-100 flex items-center gap-2 font-bold text-sm text-zinc-800 bg-zinc-50">
                            <FileText class="w-4 h-4 text-blue-600"/> SERAH TERIMA
                        </div>
                        <CardContent class="p-5 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-6">
                            <div class="space-y-2">
                                <Label class="text-[11px] sm:text-xs font-semibold text-zinc-500">DARI REGU (LAMA) <span class="text-red-500">*</span></Label>
                                <div class="flex gap-2">
                                    <Select v-model="form.dari_rupam">
                                        <SelectTrigger class="h-12 sm:h-11 text-base sm:text-sm rounded-xl sm:rounded-lg bg-zinc-50 border-zinc-200 focus:ring-1 focus:ring-blue-500 w-full"><SelectValue placeholder="Rupam" /></SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="Rupam I">Rupam I</SelectItem><SelectItem value="Rupam II">Rupam II</SelectItem>
                                            <SelectItem value="Rupam III">Rupam III</SelectItem><SelectItem value="Rupam IV">Rupam IV</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <Select v-model="form.dari_shift">
                                        <SelectTrigger class="h-12 sm:h-11 text-base sm:text-sm rounded-xl sm:rounded-lg bg-zinc-50 border-zinc-200 focus:ring-1 focus:ring-blue-500 w-full"><SelectValue placeholder="Shift" /></SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="Pagi">Pagi</SelectItem><SelectItem value="Siang">Siang</SelectItem><SelectItem value="Malam">Malam</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <Label class="text-[11px] sm:text-xs font-semibold text-zinc-500">KE REGU (BARU)</Label>
                                <div class="flex gap-2">
                                    <Select v-model="form.ke_rupam">
                                        <SelectTrigger class="h-12 sm:h-11 text-base sm:text-sm rounded-xl sm:rounded-lg bg-zinc-50 border-zinc-200 focus:ring-1 focus:ring-blue-500 w-full"><SelectValue placeholder="Rupam" /></SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="Rupam I">Rupam I</SelectItem><SelectItem value="Rupam II">Rupam II</SelectItem>
                                            <SelectItem value="Rupam III">Rupam III</SelectItem><SelectItem value="Rupam IV">Rupam IV</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <Select v-model="form.ke_shift">
                                        <SelectTrigger class="h-12 sm:h-11 text-base sm:text-sm rounded-xl sm:rounded-lg bg-zinc-50 border-zinc-200 focus:ring-1 focus:ring-blue-500 w-full"><SelectValue placeholder="Shift" /></SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="Pagi">Pagi</SelectItem><SelectItem value="Siang">Siang</SelectItem><SelectItem value="Malam">Malam</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                            </div>
                            <div class="md:col-span-2 space-y-2">
                                <Label class="text-[11px] sm:text-xs font-semibold text-zinc-500">DIPIMPIN OLEH (PIMPINAN APEL)</Label>
                                <Input v-model="form.pimpinan" class="h-12 sm:h-11 text-base sm:text-sm rounded-xl sm:rounded-lg bg-zinc-50 border-zinc-200 focus:ring-1 focus:ring-blue-500 focus:bg-white" />
                            </div>
                        </CardContent>
                    </Card>

                    <Card class="rounded-2xl border border-zinc-200 shadow-sm overflow-hidden bg-white">
                        <div class="px-5 sm:px-6 py-4 border-b border-zinc-100 flex items-center gap-2 font-bold text-sm text-white tracking-wider bg-zinc-900">
                            <Users class="w-4 h-4 text-emerald-400"/> A. JUMLAH PENGHUNI
                        </div>
                        <CardContent class="p-5 sm:p-6 space-y-5 sm:space-y-6">
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <Label class="text-[11px] sm:text-xs font-medium text-zinc-600 flex justify-between items-center w-full">
                                        <span>Kapasitas Lapas</span>
                                        <span v-if="getPreviousData('kapasitas')" class="text-[10px] bg-zinc-100 px-2 py-0.5 rounded-md text-zinc-500 font-bold">= {{ getPreviousData('kapasitas') }}</span>
                                    </Label>
                                    <Input type="number" v-model="form.kapasitas" class="h-12 sm:h-11 text-base sm:text-sm rounded-xl sm:rounded-lg"/>
                                </div>
                                <div class="space-y-2">
                                    <Label class="text-[11px] sm:text-xs font-bold text-indigo-700 flex justify-between items-center w-full">
                                        <span>Total Narapidana</span>
                                        <span v-if="getPreviousData('narapidana')" class="text-[10px] bg-indigo-100 px-2 py-0.5 rounded-md text-indigo-600 font-extrabold">= {{ getPreviousData('narapidana') }}</span>
                                    </Label>
                                    <Input type="number" v-model="form.narapidana" class="h-12 sm:h-11 text-base sm:text-sm rounded-xl sm:rounded-lg border-indigo-200 bg-indigo-50/50 font-semibold text-indigo-900 focus:ring-indigo-500" />
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
                                <div v-for="b in ['blok_a', 'blok_b', 'dapur', 'klinik']" :key="b" class="space-y-1.5">
                                    <Label class="capitalize text-[10px] sm:text-xs font-medium text-zinc-600 flex justify-between items-center w-full">
                                        <span>{{ b.replace('_', ' ') }}</span>
                                        <span v-if="getPreviousData(b)" class="hidden sm:inline text-[9px] bg-zinc-100 px-1.5 py-0.5 rounded text-zinc-500 font-bold">={{ getPreviousData(b) }}</span>
                                    </Label>
                                    <Input type="number" v-model="form[b]" class="h-12 sm:h-11 text-base sm:text-sm rounded-xl sm:rounded-lg bg-zinc-50 border-zinc-200 focus:bg-white focus:ring-1 focus:ring-blue-500" />
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-zinc-100">
                                <div class="space-y-2">
                                    <Label class="text-[11px] sm:text-xs font-medium text-zinc-600 flex justify-between items-center w-full">
                                        <span>Dalam Lapas</span>
                                        <span v-if="getPreviousData('dalam_lapas')" class="text-[10px] bg-zinc-100 px-2 py-0.5 rounded-md text-zinc-500 font-bold">= {{ getPreviousData('dalam_lapas') }}</span>
                                    </Label>
                                    <Input type="number" v-model="form.dalam_lapas" class="h-12 sm:h-11 text-base sm:text-sm rounded-xl sm:rounded-lg" />
                                </div>
                                <div class="space-y-2">
                                    <Label class="text-[11px] sm:text-xs font-medium text-zinc-600 flex justify-between items-center w-full">
                                        <span>Luar Lapas</span>
                                        <span v-if="getPreviousData('luar_lapas')" class="text-[10px] bg-zinc-100 px-2 py-0.5 rounded-md text-zinc-500 font-bold">= {{ getPreviousData('luar_lapas') }}</span>
                                    </Label>
                                    <Input type="number" v-model="form.luar_lapas" class="h-12 sm:h-11 text-base sm:text-sm rounded-xl sm:rounded-lg" />
                                </div>
                            </div>
                            
                            <div v-for="(items, key) in { rawat_inap_items: 'Rawat Inap RS', berobat_items: 'Berobat RS', bon_luar_items: 'Lain-lain (Bon Luar)' }" :key="key" class="space-y-3 pt-2">
                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center bg-zinc-50 px-3 py-2.5 sm:py-2 rounded-xl sm:rounded-lg border border-zinc-100 gap-2">
                                    <Label class="text-[11px] font-bold text-zinc-700 uppercase flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-3">
                                        <span>{{ items }}</span>
                                        <span v-if="getPreviousData(key) && getPreviousData(key) !== '(-)'" class="text-[9px] bg-blue-100/50 text-blue-600 px-2 py-0.5 rounded-md font-bold normal-case border border-blue-200 self-start sm:self-auto">
                                            Sblmnya: {{ getPreviousData(key) }}
                                        </span>
                                    </Label>
                                    <Button type="button" @click="addItem(key)" size="sm" variant="ghost" class="h-9 sm:h-7 text-xs bg-white sm:bg-transparent border border-zinc-200 sm:border-0 hover:bg-zinc-200 w-full sm:w-auto shadow-sm sm:shadow-none">
                                        <Plus class="w-3.5 h-3.5 sm:w-3 sm:h-3 mr-1"/> Tambah
                                    </Button>
                                </div>
                                <div v-for="(item, i) in form[key]" :key="i" class="flex gap-2">
                                    <Input v-model="item.ket" class="h-12 sm:h-11 text-base sm:text-sm rounded-xl sm:rounded-lg" placeholder="Masukkan detail keterangan..."/>
                                    <Button v-if="form[key].length > 1" type="button" @click="removeItem(key, i)" variant="ghost" class="h-12 w-12 sm:h-11 sm:w-11 px-0 text-rose-500 hover:bg-rose-50 hover:text-rose-600 shrink-0 border border-transparent hover:border-rose-100 rounded-xl sm:rounded-lg">
                                        <Trash2 class="w-4 h-4 sm:w-4 sm:h-4"/>
                                    </Button>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <Card class="rounded-2xl border border-zinc-200 shadow-sm overflow-hidden bg-white">
                        <div class="px-5 sm:px-6 py-4 border-b border-zinc-100 flex items-center gap-2 font-bold text-sm text-zinc-800 bg-zinc-50">
                            <ShieldAlert class="w-4 h-4 text-rose-600"/> B. PERSONIL PENGAMANAN & TUGAS
                        </div>
                        <CardContent class="p-5 sm:p-6 space-y-6 sm:space-y-6">
                            
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
                                <div class="sm:col-span-3 text-[11px] sm:text-xs font-bold text-zinc-400 uppercase tracking-wider mb-0.5 sm:mb-1">1. Rupam & Kehadiran</div>
                                <Select v-model="form.rupam_pilihan">
                                    <SelectTrigger class="h-12 sm:h-11 text-base sm:text-sm rounded-xl sm:rounded-lg bg-zinc-50 border-zinc-200 focus:ring-1 focus:ring-blue-500 w-full"><SelectValue placeholder="Pilih Rupam Terlibat" /></SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="Rupam I">Rupam I</SelectItem><SelectItem value="Rupam II">Rupam II</SelectItem>
                                        <SelectItem value="Rupam III">Rupam III</SelectItem><SelectItem value="Rupam IV">Rupam IV</SelectItem>
                                    </SelectContent>
                                </Select>
                                <div class="grid grid-cols-2 gap-3 sm:col-span-2">
                                    <Input v-model="form.rupam_jumlah" type="number" placeholder="Total Regu" class="h-12 sm:h-11 text-base sm:text-sm rounded-xl sm:rounded-lg bg-zinc-50 border-zinc-200 focus:bg-white focus:ring-1 focus:ring-blue-500"/>
                                    <Input v-model="form.rupam_hadir" type="number" placeholder="Jml Hadir" class="h-12 sm:h-11 text-base sm:text-sm rounded-xl sm:rounded-lg bg-zinc-50 border-zinc-200 focus:bg-white focus:ring-1 focus:ring-blue-500"/>
                                </div>
                                <Input class="sm:col-span-3 h-12 sm:h-11 text-base sm:text-sm rounded-xl sm:rounded-lg bg-zinc-50 border-zinc-200 focus:bg-white focus:ring-1 focus:ring-blue-500" v-model="form.rupam_keterangan" placeholder="Keterangan Rupam (Opsional)..."/>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 pt-5 sm:pt-6 border-t border-zinc-100">
                                <div class="sm:col-span-3 text-[11px] sm:text-xs font-bold text-zinc-400 uppercase tracking-wider mb-0.5 sm:mb-1">2. Satgas P2U</div>
                                <div class="grid grid-cols-2 gap-3 sm:col-span-2">
                                    <Input v-model="form.p2u_jumlah" type="number" placeholder="Total P2U" class="h-12 sm:h-11 text-base sm:text-sm rounded-xl sm:rounded-lg bg-zinc-50 border-zinc-200 focus:bg-white focus:ring-1 focus:ring-blue-500"/>
                                    <Input v-model="form.p2u_hadir" type="number" placeholder="Hadir P2U" class="h-12 sm:h-11 text-base sm:text-sm rounded-xl sm:rounded-lg bg-zinc-50 border-zinc-200 focus:bg-white focus:ring-1 focus:ring-blue-500"/>
                                </div>
                                <Input v-model="form.p2u_keterangan" placeholder="Keterangan P2U (Opsional)" class="h-12 sm:h-11 text-base sm:text-sm rounded-xl sm:rounded-lg bg-zinc-50 border-zinc-200 focus:bg-white focus:ring-1 focus:ring-blue-500 sm:col-span-1"/>
                            </div>

                            <div class="pt-5 sm:pt-6 border-t border-zinc-100 space-y-4">
                                <div class="text-[11px] sm:text-xs font-bold text-zinc-400 uppercase tracking-wider mb-2">3. Pembagian Tugas Spesifik</div>
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                                    <div class="space-y-1.5"><Label class="text-[11px] sm:text-xs font-medium">Ka. Rupam</Label><Input v-model="form.tugas.ka_rupam" class="h-12 sm:h-11 text-base sm:text-sm rounded-xl sm:rounded-lg" /></div>
                                    <div class="space-y-1.5"><Label class="text-[11px] sm:text-xs font-medium">Wakarupam</Label><Input v-model="form.tugas.wakarupam" class="h-12 sm:h-11 text-base sm:text-sm rounded-xl sm:rounded-lg" /></div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                                    <div class="space-y-1.5"><Label class="text-[11px] sm:text-xs font-medium">Kasatgas P2U</Label><Input v-model="form.tugas.kasatgas_p2u" class="h-12 sm:h-11 text-base sm:text-sm rounded-xl sm:rounded-lg" /></div>
                                    <div class="space-y-1.5"><Label class="text-[11px] sm:text-xs font-medium">Wakasatgas P2U</Label><Input v-model="form.tugas.wakasatgas_p2u" class="h-12 sm:h-11 text-base sm:text-sm rounded-xl sm:rounded-lg" /></div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 pt-4">
                                    <div v-for="(label, key) in dropdownTugas" :key="key" class="p-3 sm:p-4 bg-zinc-50/50 border border-zinc-200 rounded-2xl sm:rounded-xl space-y-3">
                                        <Label class="text-[11px] sm:text-xs text-zinc-800 font-bold tracking-wide uppercase">{{label}}</Label>
                                        <div class="grid gap-2" :class="jumlahJam === 3 ? 'grid-cols-1 sm:grid-cols-3' : 'grid-cols-1 sm:grid-cols-2'">
                                            <div v-for="jam in jumlahJam" :key="jam" class="space-y-1.5">
                                                <Label class="text-[10px] sm:text-[9px] text-zinc-500 font-semibold tracking-wide uppercase">Jam Ke-{{ jam }}</Label>
                                                <Select v-model="form.tugas[key]['jam_' + jam]">
                                                    <SelectTrigger class="h-12 sm:h-9 rounded-xl sm:rounded-lg text-base sm:text-xs bg-white border-zinc-200 focus:bg-white focus:ring-1 focus:ring-blue-500 w-full"><SelectValue placeholder="Pilih..." /></SelectTrigger>
                                                    <SelectContent>
                                                        <SelectItem value="-">(Kosong)</SelectItem>
                                                        <SelectItem v-for="anggota in anggotaReguOptions" :key="anggota.id" :value="anggota.name">{{ anggota.name }}</SelectItem>
                                                    </SelectContent>
                                                </Select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 pt-4">
                                    <div class="space-y-1.5">
                                        <Label class="text-[10px] sm:text-[10px] text-zinc-500 font-semibold tracking-wide uppercase">Perwira Piket</Label>
                                        <Select v-model="form.tugas.perwira_kontrol">
                                            <SelectTrigger class="h-12 sm:h-10 rounded-xl sm:rounded-lg text-base sm:text-sm bg-zinc-50 border-zinc-200 focus:bg-white focus:ring-1 focus:ring-blue-500 w-full"><SelectValue placeholder="Pilih..." /></SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="-">(Kosong)</SelectItem>
                                                <SelectItem v-for="p in perwiraPiketOptions" :key="p.id" :value="p.nama">{{ p.nama }} {{ p.jabatan ? `(${p.jabatan})` : '' }}</SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                    <div class="space-y-1.5">
                                        <Label class="text-[10px] sm:text-[10px] text-zinc-500 font-semibold tracking-wide uppercase">Pengawas Piket</Label>
                                        <Select v-model="form.tugas.perwira_piket">
                                            <SelectTrigger class="h-12 sm:h-10 rounded-xl sm:rounded-lg text-base sm:text-sm bg-zinc-50 border-zinc-200 focus:bg-white focus:ring-1 focus:ring-blue-500 w-full"><SelectValue placeholder="Pilih..." /></SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="-">(Kosong)</SelectItem>
                                                <SelectItem v-for="p in pengawasPiketOptions" :key="p.id" :value="p.nama">{{ p.nama }} {{ p.jabatan ? `(${p.jabatan})` : '' }}</SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                    <div class="space-y-1.5">
                                        <Label class="text-[10px] sm:text-[10px] text-zinc-500 font-semibold tracking-wide uppercase">Staff KPLP</Label>
                                        <Select v-model="form.tugas.staff_kplp">
                                            <SelectTrigger class="h-12 sm:h-10 rounded-xl sm:rounded-lg text-base sm:text-sm bg-zinc-50 border-zinc-200 focus:bg-white focus:ring-1 focus:ring-blue-500 w-full"><SelectValue placeholder="Pilih..." /></SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="-">(Kosong)</SelectItem>
                                                <SelectItem v-for="p in staffKplpOptions" :key="p.id" :value="p.nama">{{ p.nama }} {{ p.jabatan ? `(${p.jabatan})` : '' }}</SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 pt-4">
                                    <div v-for="(label, key) in inputTugas" :key="key" class="space-y-1.5">
                                        <Label class="text-[10px] text-zinc-500 font-semibold tracking-wide uppercase">{{label}}</Label>
                                        <Input v-model="form.tugas[key]" class="h-12 sm:h-10 rounded-xl sm:rounded-lg text-base sm:text-sm bg-zinc-50 border-zinc-200 focus:bg-white focus:ring-1 focus:ring-blue-500" />
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <div class="pt-4 sticky bottom-4 sm:bottom-6 z-10">
                        <Button type="button" @click="togglePreview" class="w-full h-14 sm:h-14 rounded-2xl bg-zinc-900 text-white font-bold text-sm sm:text-base hover:bg-zinc-800 shadow-xl shadow-zinc-300 transition-all active:scale-[0.99]">
                            <FileText class="w-5 h-5 mr-2" /> Lihat Pratinjau Teks Laporan
                        </Button>
                    </div>
                </form>

                <div v-if="isPreviewActive" class="animate-in fade-in zoom-in-95 duration-300 font-sans w-full">
                     
                    <Card class="border-none shadow-xl bg-white p-6 sm:p-8 md:p-12 rounded-2xl sm:rounded-xl text-zinc-900 font-sans text-[13px] sm:text-[14px] md:text-[15px] leading-relaxed mb-6 overflow-x-auto custom-scrollbar">
                        
                        <div class="font-bold text-center mb-6 sm:mb-8 text-sm sm:text-base uppercase font-sans">
                            ASTEKPAM LAPAS KELAS I PALEMBANG
                        </div>

                        <div v-if="previewUrl" class="mb-8 flex flex-col items-center bg-zinc-50/80 p-4 rounded-xl border border-zinc-200">
                            <span class="text-xs font-bold text-zinc-500 mb-3 w-full text-left uppercase tracking-wider flex items-center gap-2">
                                <ImageIcon class="w-4 h-4" /> Lampiran Foto Bukti
                            </span>
                            <img :src="previewUrl" alt="Foto Laporan" class="w-full max-w-sm rounded-lg shadow-sm border border-zinc-200 object-contain" />
                        </div>
                        
                        <p>Assalamu’alaikum Warahmatullahi Wabarakatuh</p>
                        <p>Selamat {{ salamWaktu }}....</p>
                        <br>
                        
                        <div class="grid grid-cols-[100px_10px_1fr] sm:grid-cols-[130px_15px_1fr] md:grid-cols-[180px_15px_1fr] gap-y-1 font-sans">
                            <div>Hari/Tgl</div><div>:</div><div>{{ displayHari }}</div>
                            <div>Pukul</div><div>:</div><div>{{ form.pukul }} WIB</div>
                        </div>
                        <br>
                        
                        <p>Berikut, ASTEKPAM dari <span class="font-bold">{{ form.dari_rupam || '-' }}</span> (Shift {{ form.dari_shift || '-' }}) ke <span class="font-bold">{{ form.ke_rupam || '-' }}</span> (Shift {{ form.ke_shift || '-' }}) Dipimpin oleh <span class="font-bold">{{ form.pimpinan || '-' }}</span> berjalan aman dan tertib.</p>
                        <br>
                        
                        <p>Dengan rincian sebagai berikut :</p>
                        
                        <p class="font-bold mt-4 sm:mt-3">A. JUMLAH PENGHUNI</p>
                        <div class="grid grid-cols-[115px_10px_1fr] sm:grid-cols-[140px_15px_1fr] md:grid-cols-[180px_15px_1fr] gap-y-1 font-sans">
                            <div>1. Kapasitas</div><div>:</div><div>{{ formatOrg(form.kapasitas) }}</div>
                            <div>2. Narapidana</div><div>:</div><div>{{ formatOrg(form.narapidana) }}</div>
                            <div class="col-span-3 mt-1 font-semibold">3. Isi Blok Hunian :</div>
                            
                            <div class="pl-4 sm:pl-5">* Blok A</div><div>:</div><div>{{ formatOrg(form.blok_a) }}</div>
                            <div class="pl-4 sm:pl-5">* Blok B</div><div>:</div><div>{{ formatOrg(form.blok_b) }}</div>
                            <div class="pl-4 sm:pl-5">* Dapur</div><div>:</div><div>{{ formatOrg(form.dapur) }}</div>
                            <div class="pl-4 sm:pl-5">* Klinik</div><div>:</div><div>{{ formatOrg(form.klinik) }}</div>
                            
                            <div class="col-span-3 mt-1 font-semibold">Posisi Penghuni :</div>
                            <div class="pl-4 sm:pl-5">* Didalam Lapas</div><div>:</div><div>{{ formatOrg(form.dalam_lapas) }}</div>
                            <div class="pl-4 sm:pl-5">* Diluar Lapas</div><div>:</div><div>{{ formatOrg(form.luar_lapas) }}</div>
                            
                            <div class="col-span-3 mt-2 font-semibold">4. Keterangan di luar Lapas :</div>
                            <div class="pl-4 sm:pl-5 align-top">* Rawat Inap RS</div><div class="align-top">:</div><div class="align-top break-words">{{ formatKetLuar(form.rawat_inap_items) }}</div>
                            <div class="pl-4 sm:pl-5 align-top">* Berobat RS</div><div class="align-top">:</div><div class="align-top break-words">{{ formatKetLuar(form.berobat_items) }}</div>
                            <div class="pl-4 sm:pl-5 align-top">* Bon luar</div><div class="align-top">:</div><div class="align-top break-words">{{ formatKetLuar(form.bon_luar_items) }}</div>
                            
                            <div class="font-bold mt-3">5. Total WBP</div><div class="font-bold mt-3">:</div><div class="font-bold mt-3">{{ formatOrg(form.total_wbp) }}</div>
                        </div>
                        <br>

                        <p class="font-bold">B. PERSONIL PENGAMANAN</p>
                        <div class="grid grid-cols-[100px_10px_1fr] sm:grid-cols-[130px_15px_1fr] md:grid-cols-[180px_15px_1fr] gap-y-1 font-sans">
                            <div class="col-span-3 font-semibold mt-1">1. {{ form.rupam_pilihan || 'Rupam -' }}</div>
                            <div class="pl-4 sm:pl-5">Jumlah</div><div>:</div><div>{{ form.rupam_jumlah ? form.rupam_jumlah + ' Org' : '-' }}</div>
                            <div class="pl-4 sm:pl-5">Hadir</div><div>:</div><div>{{ form.rupam_hadir ? form.rupam_hadir + ' Org' : '-' }}</div>
                            <div class="pl-4 sm:pl-5">Tidak Hadir</div><div>:</div><div>{{ tidakHadirRupam }}</div>
                            <div class="pl-4 sm:pl-5 align-top">Keterangan</div><div class="align-top">:</div><div class="align-top break-words">{{ form.rupam_keterangan || '-' }}</div>
                            
                            <div class="col-span-3 font-semibold mt-2">2. SATGAS P2U (-)</div>
                            <div class="pl-4 sm:pl-5">Jumlah</div><div>:</div><div>{{ form.p2u_jumlah ? form.p2u_jumlah + ' Org' : '-' }}</div>
                            <div class="pl-4 sm:pl-5">Hadir</div><div>:</div><div>{{ form.p2u_hadir ? form.p2u_hadir + ' Org' : '-' }}</div>
                            <div class="pl-4 sm:pl-5 align-top">Keterangan</div><div class="align-top">:</div><div class="align-top break-words">{{ form.p2u_keterangan || '-' }}</div>
                        </div>
                        <br>

                        <p class="font-bold">3. Pembagian Tugas :</p>
                        <div class="grid grid-cols-[100px_10px_1fr] sm:grid-cols-[130px_15px_1fr] md:grid-cols-[180px_15px_1fr] gap-y-1 font-sans">
                            <div class="font-semibold align-top">a. Ka. Rupam</div><div class="align-top">:</div><div class="align-top break-words">{{ form.tugas.ka_rupam || '-' }}</div>
                            <div class="pl-4 sm:pl-5 align-top">Wakarupam</div><div class="align-top">:</div><div class="align-top break-words">{{ form.tugas.wakarupam || '-' }}</div>
                            
                            <div class="col-span-3 font-semibold mt-2">b. Petugas P2U :</div>
                            <div class="pl-4 sm:pl-5 align-top">Kasatgas</div><div class="align-top">:</div><div class="align-top break-words">{{ form.tugas.kasatgas_p2u || '-' }}</div>
                            <div class="pl-4 sm:pl-5 align-top">Wakasatgas</div><div class="align-top">:</div><div class="align-top break-words">{{ form.tugas.wakasatgas_p2u || '-' }}</div>
                            
                            <div class="col-span-3 font-semibold mt-2">c. Petugas Blok :</div>
                            <div class="pl-4 sm:pl-5 align-top">Blok A</div><div class="align-top">:</div><div class="align-top break-words">{{ formatJam(form.tugas.blok_a) }}</div>
                            <div class="pl-4 sm:pl-5 align-top">Blok B</div><div class="align-top">:</div><div class="align-top break-words">{{ formatJam(form.tugas.blok_b) }}</div>
                            
                            <div class="col-span-3 font-semibold mt-2">d. Petugas Pos Atas :</div>
                            <div class="pl-4 sm:pl-5 align-top">* Menara 1</div><div class="align-top">:</div><div class="align-top break-words">{{ formatJam(form.tugas.menara_1) }}</div>
                            <div class="pl-4 sm:pl-5 align-top">* Menara 2</div><div class="align-top">:</div><div class="align-top break-words">{{ formatJam(form.tugas.menara_2) }}</div>
                            <div class="pl-4 sm:pl-5 align-top">* Menara 3</div><div class="align-top">:</div><div class="align-top break-words">{{ formatJam(form.tugas.menara_3) }}</div>
                            <div class="pl-4 sm:pl-5 align-top">* Menara 4</div><div class="align-top">:</div><div class="align-top break-words">{{ formatJam(form.tugas.menara_4) }}</div>
                            
                            <div class="mt-2 align-top break-words">e. Jaga RS</div><div class="mt-2 align-top">:</div><div class="mt-2 align-top break-words">{{ form.tugas.jaga_rs || '-' }}</div>
                            <div class="align-top break-words">f. Piket Dapur</div><div class="align-top">:</div><div class="align-top break-words">{{ form.tugas.piket_dapur || '-' }}</div>
                            <div class="align-top break-words">g. Pengawas</div><div class="align-top">:</div><div class="align-top break-words">{{ form.tugas.perwira_piket || '-' }}</div>
                            <div class="align-top break-words">h. Perwira</div><div class="align-top">:</div><div class="align-top break-words">{{ form.tugas.perwira_kontrol || '-' }}</div>
                            <div class="align-top break-words">i. Banja</div><div class="align-top">:</div><div class="align-top break-words">{{ form.tugas.banjaga || '-' }}</div>
                            <div class="align-top break-words">j. Staff KPLP</div><div class="align-top">:</div><div class="align-top break-words">{{ form.tugas.staff_kplp || '-' }}</div>
                            <div class="align-top break-words">k. Amanah</div><div class="align-top">:</div><div class="align-top break-words">{{ form.tugas.amanah || '-' }}</div>
                            <div class="font-bold border-t pt-2 mt-2 align-top">l. Pelapor</div><div class="font-bold pt-2 mt-2 align-top">:</div><div class="font-bold pt-2 mt-2 uppercase align-top break-words">{{ form.tugas.petugas_laporan || '-' }}</div>
                        </div>
                        <br>

                        <p>Demikian Laporan ini, kami sampaikan dan diucapkan terima kasih.</p>
                        <br>
                        <p>Wassalamu'alaikum Warahmatullaahi wabarakaatuh</p>
                        <p>Salam Sejahtera</p>
                        <p>Salam Sehat Selalu…🙏</p>

                    </Card>
                     
                     <div class="sticky bottom-4 sm:bottom-6 flex flex-col sm:flex-row gap-3 bg-zinc-900/90 backdrop-blur-md p-4 sm:p-4 rounded-3xl border border-zinc-800 shadow-xl max-w-4xl mx-auto z-20">
                         <Button @click="togglePreview" type="button" variant="outline" class="w-full sm:w-1/3 h-14 sm:h-14 rounded-2xl font-bold text-sm sm:text-base text-zinc-900 bg-white hover:bg-zinc-100 border-0">
                             <ChevronLeft class="w-5 h-5 mr-1"/> Kembali Edit Form
                         </Button>
                         <Button @click="submitLaporan" type="button" class="w-full sm:w-2/3 h-14 sm:h-14 bg-emerald-500 hover:bg-emerald-600 text-white rounded-2xl font-bold text-sm sm:text-base shadow-lg transition-all active:scale-[0.99] border-0" :disabled="form.processing">
                             <span v-if="form.processing" class="flex items-center gap-2">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Sedang Mengirim...
                            </span>
                            <span v-else class="flex items-center">
                                <Check class="w-5 h-5 mr-2"/> KIRIM LAPORAN SEKARANG
                            </span>
                         </Button>
                     </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    height: 6px;
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f5f9; 
    border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1; 
    border-radius: 4px;
}
</style>