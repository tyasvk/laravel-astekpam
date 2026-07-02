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
    Image as ImageIcon, UploadCloud, ClipboardList, Send, FileSearch
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

const previewUrl = ref(null);
const showSuccessModal = ref(false); 

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
        ka_rupam: '', wakarupam: '', 
        kasatgas_p2u: '', wakasatgas_p2u: '',
        
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
            e.target.value = ''; 
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

        const cariJabatan = (kataKunci, hindariKata = null) => {
            const user = anggotaRegu.find(u => {
                const jab = bersihkanTeks(u.jabatan);
                if (hindariKata && jab.includes(hindariKata)) return false;
                return jab.includes(kataKunci);
            });
            return user ? user.name : '';
        };

        form.tugas.ka_rupam = cariJabatan('karupam', 'wakarupam');
        form.tugas.wakarupam = cariJabatan('wakarupam');
        form.tugas.kasatgas_p2u = cariJabatan('kasatgas', 'wakasatgas'); 
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
// FUNGSI SUBMIT
// ==========================================
const submitLaporan = () => {
    form.post(route('astekpam.store'), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            showSuccessModal.value = true;
            setTimeout(() => {
                showSuccessModal.value = false;
            }, 1500);
        },
        onError: (errors) => {
            console.error(errors);
            alert("GAGAL MENYIMPAN! Terdapat field wajib yang belum diisi. Silakan cek kembali form berwarna merah.");
            
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
        
        <div class="px-4 py-6 sm:py-10 bg-slate-50 min-h-screen relative">
            
            <div class="absolute top-0 inset-x-0 h-64 bg-gradient-to-b from-indigo-100/50 to-transparent pointer-events-none"></div>

            <div class="max-w-4xl mx-auto w-full relative z-10">
                
                <div class="mb-5 sm:mb-8 flex items-start">
                    <Link :href="route('dashboard')">
                        <Button variant="outline" size="sm" class="rounded-xl bg-white/80 backdrop-blur-sm border-slate-200 text-slate-600 hover:text-indigo-600 hover:border-indigo-200 shadow-sm h-10 px-4 font-semibold tracking-wide w-full sm:w-auto transition-all">
                            <ChevronLeft class="w-4 h-4 mr-1.5" /> Kembali ke Dashboard
                        </Button>
                    </Link>
                </div>

                <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4 font-sans">
                    <div class="text-center sm:text-left">
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-indigo-100 text-indigo-700 text-xs font-bold mb-3 uppercase tracking-widest">
                            <FileText class="w-3.5 h-3.5" /> Form Input Laporan
                        </div>
                        <h1 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Buat Laporan Astekpam</h1>
                        <p class="text-slate-500 text-sm font-medium mt-2">Lengkapi data sistem informasi pengamanan terpadu hari ini.</p>
                    </div>
                    <Button v-if="isPreviewActive" type="button" @click="togglePreview" variant="outline" class="rounded-xl h-11 px-6 shadow-sm w-full sm:w-auto border-slate-200 text-slate-700 hover:bg-slate-100 font-bold">
                        <ChevronLeft class="w-4 h-4 mr-2" /> Kembali Edit Form
                    </Button>
                </div>

                <div v-if="Object.keys(form.errors).length > 0 && !isPreviewActive" class="mb-8 p-5 bg-rose-50 border border-rose-200 rounded-2xl text-rose-700 shadow-sm animate-in fade-in slide-in-from-top-4">
                    <div class="flex items-center gap-2 mb-3 font-bold text-sm">
                        <ShieldAlert class="w-5 h-5 text-rose-500" /> Gagal Menyimpan. Ada field yang belum diisi:
                    </div>
                    <ul class="list-disc pl-7 text-sm space-y-1 font-medium">
                        <li v-for="(error, key) in form.errors" :key="key">{{ key }}: {{ error }}</li>
                    </ul>
                </div>

                <form @submit.prevent class="space-y-6 sm:space-y-8" v-if="!isPreviewActive">
                    
                    <Card class="rounded-3xl border-0 shadow-md shadow-slate-200/50 bg-white overflow-hidden transition-all hover:shadow-lg hover:shadow-slate-200">
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 flex items-center gap-3 border-b border-indigo-100/50">
                            <div class="p-2 bg-white rounded-lg shadow-sm text-indigo-600"><ImageIcon class="w-5 h-5"/></div>
                            <span class="font-bold text-sm text-indigo-900 tracking-wide">BUKTI FOTO LAPORAN</span>
                        </div>
                        <CardContent class="p-6 sm:p-8">
                            <div class="flex flex-col md:flex-row items-start gap-8">
                                <div class="flex-1 w-full">
                                    <Label class="text-xs font-bold text-slate-400 tracking-wider mb-3 block">UPLOAD FOTO (MAKS. 10MB)</Label>
                                    
                                    <div class="relative group mt-2">
                                        <div class="absolute inset-0 w-full h-full border-2 border-dashed border-slate-200 rounded-2xl bg-slate-50 group-hover:bg-indigo-50/50 group-hover:border-indigo-300 transition-colors pointer-events-none flex flex-col items-center justify-center gap-2">
                                            <UploadCloud class="w-8 h-8 text-slate-400 group-hover:text-indigo-500 transition-colors" />
                                            <span v-if="!form.foto_laporan" class="text-sm text-slate-500 font-medium group-hover:text-indigo-600">Klik atau seret gambar ke sini</span>
                                            <span v-else class="text-sm text-indigo-600 font-bold max-w-[200px] truncate">{{ form.foto_laporan.name }}</span>
                                        </div>
                                        <input 
                                            type="file" 
                                            @change="handleFileUpload" 
                                            accept="image/png, image/jpeg, image/jpg"
                                            class="block w-full h-32 opacity-0 cursor-pointer"
                                        />
                                    </div>
                                </div>
                                <div class="shrink-0 w-full md:w-48 flex flex-col">
                                    <Label class="text-xs font-bold text-slate-400 tracking-wider mb-3 block text-center md:text-left">PREVIEW</Label>
                                    <div class="w-full h-48 md:w-48 md:h-48 rounded-2xl border-2 border-slate-100 flex items-center justify-center bg-slate-50 overflow-hidden shadow-inner group">
                                        <img v-if="previewUrl" :src="previewUrl" alt="Preview" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                                        <ImageIcon v-else class="w-10 h-10 text-slate-300" />
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <Card class="rounded-3xl border-0 shadow-md shadow-slate-200/50 bg-white p-2">
                            <div class="p-4 bg-slate-50 rounded-2xl h-full space-y-2">
                                <Label class="text-xs font-bold text-slate-400 tracking-wider">TANGGAL LAPORAN</Label>
                                <Input type="date" v-model="form.tanggal" class="h-12 text-sm font-semibold rounded-xl bg-white border-slate-200 shadow-sm focus:ring-2 focus:ring-indigo-500" />
                            </div>
                        </Card>
                        <Card class="rounded-3xl border-0 shadow-md shadow-slate-200/50 bg-white p-2">
                            <div class="p-4 bg-slate-50 rounded-2xl h-full space-y-2">
                                <Label class="text-xs font-bold text-slate-400 tracking-wider">PUKUL (SHIFT)</Label>
                                <Select v-model="form.pukul">
                                    <SelectTrigger class="h-12 text-sm font-semibold rounded-xl bg-white border-slate-200 shadow-sm focus:ring-2 focus:ring-indigo-500">
                                        <SelectValue />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="(07.30-13.00)">(07.30-13.00) - Pagi</SelectItem>
                                        <SelectItem value="(13.00-19.30)">(13.00-19.30) - Siang</SelectItem>
                                        <SelectItem value="(19.30-07.30)">(19.30-07.30) - Malam</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                        </Card>
                    </div>

                    <Card class="rounded-3xl border-0 shadow-md shadow-slate-200/50 bg-white overflow-hidden transition-all hover:shadow-lg hover:shadow-slate-200">
                        <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-4 flex items-center gap-3 border-b border-orange-100/50">
                            <div class="p-2 bg-white rounded-lg shadow-sm text-orange-500"><ClipboardList class="w-5 h-5"/></div>
                            <span class="font-bold text-sm text-orange-900 tracking-wide">SERAH TERIMA SHIFT</span>
                        </div>
                        <CardContent class="p-6 sm:p-8 grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8">
                            <div class="space-y-3 bg-slate-50 p-5 rounded-2xl border border-slate-100">
                                <Label class="text-xs font-bold text-slate-500 tracking-wide">DARI REGU (LAMA) <span class="text-rose-500">*</span></Label>
                                <div class="flex flex-col sm:flex-row gap-3">
                                    <Select v-model="form.dari_rupam">
                                        <SelectTrigger class="h-12 text-sm rounded-xl bg-white border-slate-200 focus:ring-2 focus:ring-orange-500 font-semibold w-full"><SelectValue placeholder="Pilih Rupam" /></SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="Rupam I">Rupam I</SelectItem><SelectItem value="Rupam II">Rupam II</SelectItem>
                                            <SelectItem value="Rupam III">Rupam III</SelectItem><SelectItem value="Rupam IV">Rupam IV</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <Select v-model="form.dari_shift">
                                        <SelectTrigger class="h-12 text-sm rounded-xl bg-white border-slate-200 focus:ring-2 focus:ring-orange-500 font-semibold w-full"><SelectValue placeholder="Pilih Shift" /></SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="Pagi">Pagi</SelectItem><SelectItem value="Siang">Siang</SelectItem><SelectItem value="Malam">Malam</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                            </div>
                            
                            <div class="space-y-3 bg-slate-50 p-5 rounded-2xl border border-slate-100 relative">
                                <div class="hidden md:flex absolute top-1/2 -left-6 transform -translate-y-1/2 w-8 h-8 bg-white border border-slate-200 rounded-full items-center justify-center shadow-sm z-10 text-orange-400">
                                    <ChevronLeft class="w-4 h-4 rotate-180" />
                                </div>
                                <Label class="text-xs font-bold text-slate-500 tracking-wide">KE REGU (BARU)</Label>
                                <div class="flex flex-col sm:flex-row gap-3">
                                    <Select v-model="form.ke_rupam">
                                        <SelectTrigger class="h-12 text-sm rounded-xl bg-white border-slate-200 focus:ring-2 focus:ring-orange-500 font-semibold w-full"><SelectValue placeholder="Pilih Rupam" /></SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="Rupam I">Rupam I</SelectItem><SelectItem value="Rupam II">Rupam II</SelectItem>
                                            <SelectItem value="Rupam III">Rupam III</SelectItem><SelectItem value="Rupam IV">Rupam IV</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <Select v-model="form.ke_shift">
                                        <SelectTrigger class="h-12 text-sm rounded-xl bg-white border-slate-200 focus:ring-2 focus:ring-orange-500 font-semibold w-full"><SelectValue placeholder="Pilih Shift" /></SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="Pagi">Pagi</SelectItem><SelectItem value="Siang">Siang</SelectItem><SelectItem value="Malam">Malam</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                            </div>

                            <div class="md:col-span-2 space-y-3">
                                <Label class="text-xs font-bold text-slate-500 tracking-wide">DIPIMPIN OLEH (PIMPINAN APEL)</Label>
                                <Input v-model="form.pimpinan" class="h-12 text-sm rounded-xl bg-white border-slate-200 focus:ring-2 focus:ring-orange-500 font-semibold shadow-sm" placeholder="Contoh: STAF KPLP" />
                            </div>
                        </CardContent>
                    </Card>

                    <Card class="rounded-3xl border-0 shadow-md shadow-slate-200/50 bg-white overflow-hidden transition-all hover:shadow-lg hover:shadow-slate-200">
                        <div class="bg-gradient-to-r from-emerald-50 to-teal-50 px-6 py-4 flex items-center gap-3 border-b border-emerald-100/50">
                            <div class="p-2 bg-white rounded-lg shadow-sm text-emerald-600"><Users class="w-5 h-5"/></div>
                            <span class="font-bold text-sm text-emerald-900 tracking-wide">A. JUMLAH PENGHUNI</span>
                        </div>
                        <CardContent class="p-6 sm:p-8 space-y-8">
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 sm:gap-6">
                                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 space-y-3">
                                    <Label class="text-xs font-bold text-slate-500 flex justify-between items-center uppercase tracking-wider">
                                        <span>Kapasitas Lapas</span>
                                        <span v-if="getPreviousData('kapasitas')" class="text-[10px] bg-slate-200/70 px-2.5 py-1 rounded-lg text-slate-600 font-bold">Shift Lalu: {{ getPreviousData('kapasitas') }}</span>
                                    </Label>
                                    <Input type="number" v-model="form.kapasitas" class="h-12 text-sm font-bold rounded-xl bg-white border-slate-200 focus:ring-2 focus:ring-emerald-500"/>
                                </div>
                                <div class="bg-emerald-50 p-5 rounded-2xl border border-emerald-100 space-y-3">
                                    <Label class="text-xs font-bold text-emerald-700 flex justify-between items-center uppercase tracking-wider">
                                        <span>Total Narapidana</span>
                                        <span v-if="getPreviousData('narapidana')" class="text-[10px] bg-emerald-200/50 px-2.5 py-1 rounded-lg text-emerald-800 font-extrabold">Shift Lalu: {{ getPreviousData('narapidana') }}</span>
                                    </Label>
                                    <Input type="number" v-model="form.narapidana" class="h-12 text-lg font-black rounded-xl border-emerald-200 bg-white text-emerald-900 shadow-sm focus:ring-2 focus:ring-emerald-600" />
                                </div>
                            </div>
                            
                            <div class="space-y-4">
                                <Label class="text-xs font-bold text-slate-400 tracking-wider">DISTRIBUSI BLOK & AREA</Label>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                    <div v-for="b in ['blok_a', 'blok_b', 'dapur', 'klinik']" :key="b" class="space-y-2 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                        <Label class="capitalize text-xs font-bold text-slate-600 flex justify-between items-center">
                                            <span>{{ b.replace('_', ' ') }}</span>
                                            <span v-if="getPreviousData(b)" class="hidden sm:inline text-[10px] text-slate-400 font-bold">({{ getPreviousData(b) }})</span>
                                        </Label>
                                        <Input type="number" v-model="form[b]" class="h-11 text-sm font-bold rounded-xl bg-white border-slate-200 focus:ring-2 focus:ring-emerald-500" />
                                    </div>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-6 border-t border-slate-100">
                                <div class="space-y-2 p-4 bg-blue-50/50 rounded-2xl border border-blue-100">
                                    <Label class="text-xs font-bold text-blue-700 flex justify-between items-center uppercase tracking-wider">
                                        <span>Posisi Dalam Lapas</span>
                                        <span v-if="getPreviousData('dalam_lapas')" class="text-[10px] bg-blue-100 px-2 py-1 rounded-lg text-blue-700 font-bold">Lalu: {{ getPreviousData('dalam_lapas') }}</span>
                                    </Label>
                                    <Input type="number" v-model="form.dalam_lapas" class="h-11 text-sm font-bold rounded-xl bg-white border-blue-200 focus:ring-2 focus:ring-blue-500" />
                                </div>
                                <div class="space-y-2 p-4 bg-rose-50/50 rounded-2xl border border-rose-100">
                                    <Label class="text-xs font-bold text-rose-700 flex justify-between items-center uppercase tracking-wider">
                                        <span>Posisi Luar Lapas</span>
                                        <span v-if="getPreviousData('luar_lapas')" class="text-[10px] bg-rose-100 px-2 py-1 rounded-lg text-rose-700 font-bold">Lalu: {{ getPreviousData('luar_lapas') }}</span>
                                    </Label>
                                    <Input type="number" v-model="form.luar_lapas" class="h-11 text-sm font-bold rounded-xl bg-white border-rose-200 focus:ring-2 focus:ring-rose-500" />
                                </div>
                            </div>
                            
                            <div class="space-y-6 pt-4">
                                <div v-for="(items, key) in { rawat_inap_items: 'Rawat Inap RS', berobat_items: 'Berobat RS', bon_luar_items: 'Lain-lain (Bon Luar)' }" :key="key" class="bg-slate-50 p-4 sm:p-5 rounded-2xl border border-slate-100 space-y-4">
                                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                                        <Label class="text-xs font-bold text-slate-700 uppercase flex items-center gap-3 tracking-wider">
                                            <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                            {{ items }}
                                            <span v-if="getPreviousData(key) && getPreviousData(key) !== '(-)'" class="text-[10px] bg-slate-200 text-slate-600 px-2 py-1 rounded-md font-bold normal-case">
                                                Shift Lalu: {{ getPreviousData(key) }}
                                            </span>
                                        </Label>
                                        <Button type="button" @click="addItem(key)" size="sm" variant="outline" class="h-9 text-xs rounded-lg font-bold border-slate-200 text-emerald-600 hover:bg-emerald-50 hover:text-emerald-700">
                                            <Plus class="w-3.5 h-3.5 mr-1.5"/> Tambah Baris
                                        </Button>
                                    </div>
                                    <div class="space-y-2">
                                        <div v-for="(item, i) in form[key]" :key="i" class="flex gap-2 group">
                                            <Input v-model="item.ket" class="h-11 text-sm rounded-xl bg-white border-slate-200 focus:ring-2 focus:ring-emerald-500" placeholder="Ketik keterangan detail..."/>
                                            <Button v-if="form[key].length > 1" type="button" @click="removeItem(key, i)" variant="ghost" class="h-11 w-11 p-0 text-rose-400 hover:bg-rose-100 hover:text-rose-600 shrink-0 rounded-xl transition-colors">
                                                <Trash2 class="w-4 h-4"/>
                                            </Button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <Card class="rounded-3xl border-0 shadow-md shadow-slate-200/50 bg-white overflow-hidden transition-all hover:shadow-lg hover:shadow-slate-200 mb-20">
                        <div class="bg-gradient-to-r from-purple-50 to-fuchsia-50 px-6 py-4 flex items-center gap-3 border-b border-purple-100/50">
                            <div class="p-2 bg-white rounded-lg shadow-sm text-purple-600"><ShieldAlert class="w-5 h-5"/></div>
                            <span class="font-bold text-sm text-purple-900 tracking-wide">B. PERSONIL & PEMBAGIAN TUGAS</span>
                        </div>
                        <CardContent class="p-6 sm:p-8 space-y-8">
                            
                            <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100">
                                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4 flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-full bg-slate-200 flex items-center justify-center text-slate-700">1</span>
                                    Regu Pengamanan (Rupam) & Kehadiran
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <Select v-model="form.rupam_pilihan">
                                        <SelectTrigger class="h-12 text-sm rounded-xl bg-white border-slate-200 focus:ring-2 focus:ring-purple-500 font-bold"><SelectValue placeholder="Pilih Rupam Terlibat" /></SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="Rupam I">Rupam I</SelectItem><SelectItem value="Rupam II">Rupam II</SelectItem>
                                            <SelectItem value="Rupam III">Rupam III</SelectItem><SelectItem value="Rupam IV">Rupam IV</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <div class="grid grid-cols-2 gap-4 md:col-span-2">
                                        <div class="space-y-1">
                                            <Label class="text-[10px] text-slate-400 font-bold px-1">TOTAL REGU</Label>
                                            <Input v-model="form.rupam_jumlah" type="number" class="h-12 text-sm font-bold rounded-xl bg-white border-slate-200 focus:ring-2 focus:ring-purple-500"/>
                                        </div>
                                        <div class="space-y-1">
                                            <Label class="text-[10px] text-slate-400 font-bold px-1">JUMLAH HADIR</Label>
                                            <Input v-model="form.rupam_hadir" type="number" class="h-12 text-sm font-bold rounded-xl bg-white border-slate-200 focus:ring-2 focus:ring-purple-500"/>
                                        </div>
                                    </div>
                                    <div class="md:col-span-3">
                                        <Input class="h-12 text-sm rounded-xl bg-white border-slate-200 focus:ring-2 focus:ring-purple-500" v-model="form.rupam_keterangan" placeholder="Keterangan Rupam (Opsional)..."/>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100">
                                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4 flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-full bg-slate-200 flex items-center justify-center text-slate-700">2</span>
                                    Satgas P2U
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="grid grid-cols-2 gap-4 md:col-span-2">
                                        <div class="space-y-1">
                                            <Label class="text-[10px] text-slate-400 font-bold px-1">TOTAL P2U</Label>
                                            <Input v-model="form.p2u_jumlah" type="number" class="h-12 text-sm font-bold rounded-xl bg-white border-slate-200 focus:ring-2 focus:ring-purple-500"/>
                                        </div>
                                        <div class="space-y-1">
                                            <Label class="text-[10px] text-slate-400 font-bold px-1">HADIR P2U</Label>
                                            <Input v-model="form.p2u_hadir" type="number" class="h-12 text-sm font-bold rounded-xl bg-white border-slate-200 focus:ring-2 focus:ring-purple-500"/>
                                        </div>
                                    </div>
                                    <div class="space-y-1">
                                        <Label class="text-[10px] text-slate-400 font-bold px-1">KETERANGAN</Label>
                                        <Input v-model="form.p2u_keterangan" placeholder="Opsional..." class="h-12 text-sm rounded-xl bg-white border-slate-200 focus:ring-2 focus:ring-purple-500"/>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-2 space-y-6">
                                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-full bg-slate-200 flex items-center justify-center text-slate-700">3</span>
                                    Pembagian Tugas Spesifik
                                </h3>
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-5 bg-white border border-slate-200 rounded-2xl shadow-sm">
                                    <div class="space-y-2"><Label class="text-xs font-bold text-slate-600">Ka. Rupam</Label><Input v-model="form.tugas.ka_rupam" class="h-12 text-sm rounded-xl font-semibold bg-slate-50 border-slate-200 focus:bg-white focus:ring-2 focus:ring-purple-500" /></div>
                                    <div class="space-y-2"><Label class="text-xs font-bold text-slate-600">Wakarupam</Label><Input v-model="form.tugas.wakarupam" class="h-12 text-sm rounded-xl font-semibold bg-slate-50 border-slate-200 focus:bg-white focus:ring-2 focus:ring-purple-500" /></div>
                                    <div class="space-y-2"><Label class="text-xs font-bold text-slate-600">Kasatgas</Label><Input v-model="form.tugas.kasatgas_p2u" class="h-12 text-sm rounded-xl font-semibold bg-slate-50 border-slate-200 focus:bg-white focus:ring-2 focus:ring-purple-500" /></div>
                                    <div class="space-y-2"><Label class="text-xs font-bold text-slate-600">Wakasatgas</Label><Input v-model="form.tugas.wakasatgas_p2u" class="h-12 text-sm rounded-xl font-semibold bg-slate-50 border-slate-200 focus:bg-white focus:ring-2 focus:ring-purple-500" /></div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <div v-for="(label, key) in dropdownTugas" :key="key" class="p-5 bg-white border border-slate-200 rounded-2xl shadow-sm space-y-4 hover:border-purple-200 transition-colors">
                                        <Label class="text-xs text-slate-800 font-black tracking-widest uppercase border-b border-slate-100 pb-2 block">{{label}}</Label>
                                        <div class="grid gap-3 grid-cols-1">
                                            <div v-for="jam in jumlahJam" :key="jam" class="space-y-1.5 flex flex-col">
                                                <Label class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Jam Ke-{{ jam }}</Label>
                                                <Select v-model="form.tugas[key]['jam_' + jam]">
                                                    <SelectTrigger class="h-11 rounded-xl text-sm font-semibold bg-slate-50 border-slate-200 focus:bg-white focus:ring-2 focus:ring-purple-500 w-full"><SelectValue placeholder="Pilih Anggota..." /></SelectTrigger>
                                                    <SelectContent>
                                                        <SelectItem value="-">(Kosong)</SelectItem>
                                                        <SelectItem v-for="anggota in anggotaReguOptions" :key="anggota.id" :value="anggota.name">{{ anggota.name }}</SelectItem>
                                                    </SelectContent>
                                                </Select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-5 bg-white border border-slate-200 rounded-2xl shadow-sm">
                                    <div class="space-y-2">
                                        <Label class="text-xs font-bold text-slate-600">Perwira Piket</Label>
                                        <Select v-model="form.tugas.perwira_kontrol">
                                            <SelectTrigger class="h-12 rounded-xl text-sm font-semibold bg-slate-50 border-slate-200 focus:bg-white focus:ring-2 focus:ring-purple-500 w-full"><SelectValue placeholder="Pilih..." /></SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="-">(Kosong)</SelectItem>
                                                <SelectItem v-for="p in perwiraPiketOptions" :key="p.id" :value="p.nama">{{ p.nama }} {{ p.jabatan ? `(${p.jabatan})` : '' }}</SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                    <div class="space-y-2">
                                        <Label class="text-xs font-bold text-slate-600">Pengawas Piket</Label>
                                        <Select v-model="form.tugas.perwira_piket">
                                            <SelectTrigger class="h-12 rounded-xl text-sm font-semibold bg-slate-50 border-slate-200 focus:bg-white focus:ring-2 focus:ring-purple-500 w-full"><SelectValue placeholder="Pilih..." /></SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="-">(Kosong)</SelectItem>
                                                <SelectItem v-for="p in pengawasPiketOptions" :key="p.id" :value="p.nama">{{ p.nama }} {{ p.jabatan ? `(${p.jabatan})` : '' }}</SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                    <div class="space-y-2">
                                        <Label class="text-xs font-bold text-slate-600">Staff KPLP</Label>
                                        <Select v-model="form.tugas.staff_kplp">
                                            <SelectTrigger class="h-12 rounded-xl text-sm font-semibold bg-slate-50 border-slate-200 focus:bg-white focus:ring-2 focus:ring-purple-500 w-full"><SelectValue placeholder="Pilih..." /></SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="-">(Kosong)</SelectItem>
                                                <SelectItem v-for="p in staffKplpOptions" :key="p.id" :value="p.nama">{{ p.nama }} {{ p.jabatan ? `(${p.jabatan})` : '' }}</SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                                    <div v-for="(label, key) in inputTugas" :key="key" class="space-y-2">
                                        <Label class="text-[11px] text-slate-500 font-bold uppercase tracking-wider">{{label}}</Label>
                                        <Input v-model="form.tugas[key]" class="h-11 rounded-xl text-sm font-semibold bg-white border-slate-200 focus:ring-2 focus:ring-purple-500 shadow-sm" />
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <div class="fixed bottom-6 left-0 right-0 px-4 z-40 pointer-events-none flex justify-center">
                        <Button type="button" @click="togglePreview" class="pointer-events-auto h-14 rounded-full bg-slate-900 text-white px-8 font-bold text-sm hover:bg-slate-800 shadow-2xl shadow-slate-900/40 transition-all hover:scale-105 active:scale-95 flex items-center gap-2">
                            <FileSearch class="w-5 h-5" /> LIHAT PRATINJAU DOKUMEN
                        </Button>
                    </div>
                </form>

                <div v-if="isPreviewActive" class="animate-in fade-in slide-in-from-bottom-8 duration-500 font-sans w-full pb-32">
                     
                    <Card class="border-none shadow-xl bg-white p-6 sm:p-8 md:p-12 rounded-2xl sm:rounded-xl text-slate-900 font-sans text-[13px] sm:text-[14px] md:text-[15px] leading-relaxed mb-6 overflow-x-auto custom-scrollbar max-w-4xl mx-auto">
                        
                        <div class="font-bold text-center mb-6 sm:mb-8 text-sm sm:text-base uppercase font-sans">
                            ASTEKPAM LAPAS KELAS I PALEMBANG
                        </div>

                        <div v-if="previewUrl" class="mb-8 flex flex-col items-center bg-slate-50 p-4 rounded-xl border border-slate-200">
                            <span class="text-xs font-bold text-slate-500 mb-3 w-full text-center sm:text-left uppercase tracking-wider flex items-center justify-center sm:justify-start gap-2">
                                <ImageIcon class="w-4 h-4" /> Lampiran Foto Bukti
                            </span>
                            <img :src="previewUrl" alt="Foto Laporan" class="w-full max-w-sm rounded-lg shadow-sm border border-slate-200 object-contain" />
                        </div>
                        
                        <p>Assalamu’alaikum Warahmatullahi Wabarakatuh</p>
                        <p>Selamat {{ salamWaktu }}....</p>
                        <br>
                        
                        <div class="grid grid-cols-[100px_10px_1fr] sm:grid-cols-[130px_15px_1fr] md:grid-cols-[180px_15px_1fr] gap-y-1 font-sans">
                            <div>Hari/Tgl</div><div>:</div><div>{{ displayHari }}</div>
                            <div>Pukul</div><div>:</div><div>{{ form.pukul }} WIB</div>
                        </div>
                        <br>
                        
                        <p>Berikut dilaporkan pelaksanaan ASTEKPAM dari <span class="font-bold">{{ form.dari_rupam || '-' }}</span> (Shift {{ form.dari_shift || '-' }}) kepada <span class="font-bold">{{ form.ke_rupam || '-' }}</span> (Shift {{ form.ke_shift || '-' }}). Kegiatan dipimpin oleh <span class="font-bold">{{ form.pimpinan || '-' }}</span> berjalan aman dan tertib.</p>
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
                            <div class="font-bold border-t border-slate-200 pt-2 mt-2 align-top">l. Pelapor</div><div class="font-bold pt-2 mt-2 align-top">:</div><div class="font-bold pt-2 mt-2 uppercase align-top break-words">{{ form.tugas.petugas_laporan || '-' }}</div>
                        </div>
                        <br>

                        <p>Demikian Laporan ini, kami sampaikan dan diucapkan terima kasih.</p>
                        <br>
                        <p>Wassalamu'alaikum Warahmatullaahi wabarakaatuh</p>
                        <p>Salam Sejahtera</p>
                        <p>Salam Sehat Selalu…🙏</p>

                    </Card>

                    <div class="fixed bottom-6 left-0 right-0 px-4 z-40 flex justify-center w-full">
                        <div class="flex items-center gap-3 bg-white/90 backdrop-blur-md p-3 rounded-full border border-slate-200 shadow-2xl max-w-2xl w-full mx-auto">
                            <Button @click="togglePreview" type="button" variant="ghost" class="h-12 rounded-full font-bold text-sm text-slate-600 hover:bg-slate-100 px-6">
                                <ChevronLeft class="w-4 h-4 mr-1"/> Edit Form
                            </Button>
                            <Button @click="submitLaporan" type="button" class="flex-1 h-12 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white rounded-full font-bold text-sm shadow-md transition-all hover:scale-[1.02] active:scale-[0.98] border-0" :disabled="form.processing">
                                <span v-if="form.processing" class="flex items-center gap-2 justify-center">
                                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Menyimpan...
                                </span>
                                <span v-else class="flex items-center justify-center">
                                    <Send class="w-4 h-4 mr-2"/> KIRIM LAPORAN SEKARANG
                                </span>
                            </Button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div v-if="showSuccessModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-gray-900/70 backdrop-blur-sm transition-opacity">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden transform transition-all p-8 text-center animate-bounce-short">
                <div class="w-20 h-20 rounded-full bg-green-100 text-green-500 flex items-center justify-center mx-auto mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-gray-900 mb-2">Berhasil!</h3>
                <p class="text-gray-500 font-medium">
                    Laporan sudah terkirim.
                </p>
                <div class="mt-6 flex justify-center">
                    <div class="w-8 h-1 bg-gray-200 rounded-full overflow-hidden">
                        <div class="w-full h-full bg-green-500 animate-pulse"></div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}
::-webkit-scrollbar-track {
    background: transparent;
}
::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}
::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

input[type=number]::-webkit-inner-spin-button, 
input[type=number]::-webkit-outer-spin-button { 
  -webkit-appearance: none; 
  margin: 0; 
}
input[type=number] {
    -moz-appearance: textfield; 
}

.animate-bounce-short {
    animation: bounce-short 0.4s ease-out forwards;
}
@keyframes bounce-short {
    0% { transform: scale(0.9); opacity: 0; }
    50% { transform: scale(1.05); opacity: 1; }
    100% { transform: scale(1); opacity: 1; }
}
</style>