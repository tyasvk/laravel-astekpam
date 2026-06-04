<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Card, CardContent } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { computed } from 'vue';
import { Users, Plus, Trash2, ShieldCheck, UserPlus, Shield, Briefcase, BadgeCheck } from 'lucide-vue-next';

const props = defineProps({
    pejabats: Array
});

const form = useForm({
    nama: '',
    jabatan: '',
    kategori: 'Pengawas Piket',
});

const submit = () => {
    form.post(route('pejabat.store'), {
        onSuccess: () => form.reset(),
    });
};

const deletePejabat = (id) => {
    if (confirm('Yakin ingin menghapus data ini?')) {
        form.delete(route('pejabat.destroy', id));
    }
};

// Mengelompokkan data berdasarkan urutan yang diminta
const groupedPejabats = computed(() => {
    return [
        {
            kategori: 'Pengawas Piket',
            icon: Shield,
            colorClass: 'text-amber-600 bg-amber-100',
            data: props.pejabats.filter(p => p.kategori === 'Pengawas Piket')
        },
        {
            kategori: 'Perwira Piket',
            icon: BadgeCheck,
            colorClass: 'text-indigo-600 bg-indigo-100',
            data: props.pejabats.filter(p => p.kategori === 'Perwira Piket')
        },
        {
            kategori: 'Staff KPLP',
            icon: Briefcase,
            colorClass: 'text-blue-600 bg-blue-100',
            data: props.pejabats.filter(p => p.kategori === 'Staff KPLP')
        }
    ];
});
</script>

<template>
    <Head title="Master Pejabat" />

    <AuthenticatedLayout>
        <!-- Kontainer Utama: Padding kecil untuk mobile, membesar di sm/md -->
        <div class="py-4 sm:py-8 bg-zinc-50/30 min-h-screen font-sans">
            <div class="max-w-4xl mx-auto px-3 sm:px-6 space-y-6 sm:space-y-8">
                
                <!-- Section Form Tambah Data -->
                <div class="space-y-4 sm:space-y-6">
                    <div class="bg-white rounded-2xl p-4 sm:p-6 border border-zinc-200 shadow-sm">
                        <!-- Header Card -->
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4 mb-4 sm:mb-6">
                            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                                <ShieldCheck class="w-6 h-6 text-blue-600" />
                            </div>
                            <div>
                                <h1 class="text-xl sm:text-2xl font-extrabold text-zinc-900 tracking-tight leading-tight">Data Pegawai & Pejabat</h1>
                                <p class="text-zinc-500 text-xs sm:text-sm mt-1">
                                    Atur Pengawas Piket, Perwira Piket, dan Staff KPLP untuk opsi dropdown.
                                </p>
                            </div>
                        </div>

                        <!-- Form Wrapper -->
                        <div class="p-4 sm:p-5 bg-zinc-50/80 rounded-2xl border border-zinc-100">
                            <div class="flex items-center gap-2 font-bold text-xs text-blue-600 uppercase tracking-wider mb-4 border-b border-zinc-200/60 pb-3">
                                <UserPlus class="w-4 h-4" /> Tambah Data Baru
                            </div>
                            
                            <!-- Form Grid: Kolom tunggal di mobile, baris grid di tablet -->
                            <form @submit.prevent="submit" class="flex flex-col sm:grid sm:grid-cols-2 md:grid-cols-4 gap-4 items-end">
                                
                                <div class="space-y-2 md:col-span-1 w-full">
                                    <Label class="text-[11px] font-bold text-zinc-600 uppercase tracking-wider pl-1">Kategori</Label>
                                    <Select v-model="form.kategori">
                                        <SelectTrigger class="h-12 sm:h-11 rounded-xl bg-white border-zinc-200 focus:ring-2 focus:ring-blue-500 font-bold text-zinc-800 shadow-sm w-full">
                                            <SelectValue placeholder="Pilih Kategori" />
                                        </SelectTrigger>
                                        <SelectContent class="rounded-xl">
                                            <SelectItem value="Pengawas Piket">Pengawas Piket</SelectItem>
                                            <SelectItem value="Perwira Piket">Perwira Piket</SelectItem>
                                            <SelectItem value="Staff KPLP">Staff KPLP</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                                
                                <div class="space-y-2 md:col-span-1 w-full">
                                    <Label class="text-[11px] font-bold text-zinc-600 uppercase tracking-wider pl-1">Nama Lengkap</Label>
                                    <Input v-model="form.nama" type="text" required class="h-12 sm:h-11 rounded-xl bg-white border-zinc-200 focus:ring-2 focus:ring-blue-500 shadow-sm w-full" placeholder="Cth: Budi Santoso" />
                                </div>
                                
                                <div class="space-y-2 md:col-span-1 w-full">
                                    <Label class="text-[11px] font-bold text-zinc-600 uppercase tracking-wider pl-1">Jabatan <span class="text-zinc-400 normal-case font-normal">(Opsional)</span></Label>
                                    <Input v-model="form.jabatan" type="text" class="h-12 sm:h-11 rounded-xl bg-white border-zinc-200 focus:ring-2 focus:ring-blue-500 shadow-sm w-full" placeholder="Cth: Kasi Kamtib" />
                                </div>
                                
                                <div class="md:col-span-1 w-full mt-2 sm:mt-0">
                                    <Button type="submit" :disabled="form.processing" class="w-full h-12 sm:h-11 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-md shadow-blue-200 transition-all active:scale-[0.98]">
                                        <Plus class="w-5 h-5 sm:w-4 sm:h-4 mr-1.5" /> Simpan
                                    </Button>
                                </div>
                                
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Section List Data -->
                <div class="space-y-6 sm:space-y-8">
                    <div v-for="(group, index) in groupedPejabats" :key="index" class="space-y-3 sm:space-y-4">
                        
                        <!-- List Header -->
                        <div class="flex items-center justify-between px-1">
                            <div class="flex items-center gap-3">
                                <div :class="['w-9 h-9 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center shrink-0', group.colorClass]">
                                    <component :is="group.icon" class="w-4 h-4 sm:w-5 sm:h-5" />
                                </div>
                                <h2 class="text-base sm:text-lg font-extrabold text-zinc-900 tracking-tight">{{ group.kategori }}</h2>
                            </div>
                            <span class="bg-white border border-zinc-200 text-zinc-600 text-[10px] sm:text-xs font-bold px-2.5 sm:px-3 py-1 rounded-full shadow-sm">
                                {{ group.data.length }} Orang
                            </span>
                        </div>

                        <!-- List Content -->
                        <Card class="rounded-2xl border border-zinc-200 shadow-sm bg-white overflow-hidden">
                            <CardContent class="p-0">
                                <div v-if="group.data.length > 0" class="divide-y divide-zinc-100">
                                    <div v-for="pejabat in group.data" :key="pejabat.id" class="p-4 sm:p-5 flex items-center justify-between hover:bg-zinc-50 transition-colors gap-3">
                                        
                                        <!-- Text Data -->
                                        <div class="flex-1 min-w-0 pr-2">
                                            <h3 class="font-bold text-zinc-900 text-sm sm:text-base truncate">{{ pejabat.nama }}</h3>
                                            <p class="text-[11px] sm:text-xs font-medium text-zinc-500 mt-0.5 truncate">{{ pejabat.jabatan || 'Belum ada data jabatan' }}</p>
                                        </div>
                                        
                                        <!-- Action Button: Hanya Icon di Mobile, Icon+Teks di Desktop -->
                                        <Button @click="deletePejabat(pejabat.id)" variant="ghost" class="w-10 h-10 sm:w-auto sm:h-9 sm:px-3 flex items-center justify-center p-0 text-rose-500 hover:bg-rose-50 hover:text-rose-700 font-bold text-xs shrink-0 rounded-xl sm:rounded-lg border border-transparent hover:border-rose-100 transition-colors">
                                            <Trash2 class="w-4 h-4 sm:mr-1.5" /> 
                                            <span class="hidden sm:inline">Hapus</span>
                                        </Button>

                                    </div>
                                </div>
                                
                                <!-- Empty State -->
                                <div v-else class="py-10 px-4 text-center bg-zinc-50/30">
                                    <div class="w-12 h-12 bg-zinc-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <Users class="w-6 h-6 text-zinc-400" />
                                    </div>
                                    <h3 class="text-sm font-bold text-zinc-700">Belum Ada Data</h3>
                                    <p class="text-xs font-medium text-zinc-500 mt-1 max-w-[200px] mx-auto leading-relaxed">
                                        Data {{ group.kategori }} kosong. Silakan tambah data di atas.
                                    </p>
                                </div>
                            </CardContent>
                        </Card>

                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>