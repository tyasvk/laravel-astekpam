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
        <div class="py-6 sm:py-8 bg-zinc-50/30 min-h-screen">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 space-y-6 sm:space-y-8">
                
                <div class="space-y-6">
                    <div class="bg-white rounded-2xl p-6 border border-zinc-200 shadow-sm">
                        <div class="flex items-center gap-3 mb-2">
                            <ShieldCheck class="w-8 h-8 text-blue-600 shrink-0" />
                            <h1 class="text-xl sm:text-2xl font-extrabold text-zinc-900 tracking-tight">Data Pegawai & Pejabat</h1>
                        </div>
                        <p class="text-zinc-500 text-sm mb-6">
                            Tambahkan data Pengawas Piket, Perwira Piket, dan Staff KPLP di sini. Data ini akan muncul otomatis di pilihan dropdown formulir Astekpam.
                        </p>

                        <div class="p-4 sm:p-5 bg-zinc-50/80 rounded-xl border border-zinc-100">
                            <div class="flex items-center gap-2 font-bold text-xs text-blue-600 uppercase tracking-wider mb-4">
                                <UserPlus class="w-4 h-4" /> Form Tambah Data
                            </div>
                            
                            <form @submit.prevent="submit" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-end">
                                <div class="space-y-1.5 md:col-span-1">
                                    <Label class="text-[11px] font-bold text-zinc-600 uppercase tracking-wider">Kategori</Label>
                                    <Select v-model="form.kategori">
                                        <SelectTrigger class="h-11 rounded-lg bg-white border-zinc-200 focus:ring-1 focus:ring-blue-500 font-bold text-zinc-800">
                                            <SelectValue placeholder="Pilih Kategori" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="Pengawas Piket">Pengawas Piket</SelectItem>
                                            <SelectItem value="Perwira Piket">Perwira Piket</SelectItem>
                                            <SelectItem value="Staff KPLP">Staff KPLP</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div class="space-y-1.5 md:col-span-1">
                                    <Label class="text-[11px] font-bold text-zinc-600 uppercase tracking-wider">Nama Lengkap</Label>
                                    <Input v-model="form.nama" type="text" required class="h-11 rounded-lg bg-white border-zinc-200 focus:ring-1 focus:ring-blue-500" placeholder="Contoh: Budi Santoso" />
                                </div>
                                <div class="space-y-1.5 md:col-span-1">
                                    <Label class="text-[11px] font-bold text-zinc-600 uppercase tracking-wider">Jabatan <span class="text-zinc-400 normal-case font-normal">(Opsional)</span></Label>
                                    <Input v-model="form.jabatan" type="text" class="h-11 rounded-lg bg-white border-zinc-200 focus:ring-1 focus:ring-blue-500" placeholder="Kasi Kamtib" />
                                </div>
                                <div class="md:col-span-1">
                                    <Button type="submit" :disabled="form.processing" class="w-full h-11 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-md shadow-blue-200 transition-all active:scale-[0.99]">
                                        <Plus class="w-4 h-4 mr-1.5" /> Simpan
                                    </Button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div v-for="(group, index) in groupedPejabats" :key="index" class="space-y-3">
                        
                        <div class="flex items-center justify-between px-1">
                            <div class="flex items-center gap-2.5">
                                <div :class="['w-8 h-8 rounded-lg flex items-center justify-center', group.colorClass]">
                                    <component :is="group.icon" class="w-4 h-4" />
                                </div>
                                <h2 class="text-base font-extrabold text-zinc-800 tracking-tight">{{ group.kategori }}</h2>
                            </div>
                            <span class="bg-zinc-200 text-zinc-600 text-[10px] font-bold px-2 py-1 rounded-md">
                                {{ group.data.length }} Orang
                            </span>
                        </div>

                        <Card class="rounded-xl border border-zinc-200 shadow-sm bg-white overflow-hidden">
                            <CardContent class="p-0">
                                <div v-if="group.data.length > 0" class="divide-y divide-zinc-100">
                                    <div v-for="pejabat in group.data" :key="pejabat.id" class="p-4 sm:px-6 flex items-center justify-between hover:bg-zinc-50 transition-colors gap-4">
                                        <div class="min-w-0">
                                            <h3 class="font-bold text-zinc-900 text-sm truncate">{{ pejabat.nama }}</h3>
                                            <p class="text-xs text-zinc-500 mt-0.5 truncate">{{ pejabat.jabatan || 'Tidak ada data jabatan' }}</p>
                                        </div>
                                        <Button @click="deletePejabat(pejabat.id)" variant="ghost" size="sm" class="text-rose-500 hover:bg-rose-50 hover:text-rose-600 font-bold text-xs h-8 px-3 shrink-0 rounded-lg">
                                            <Trash2 class="w-3.5 h-3.5 mr-1 sm:mr-1.5" /> <span class="hidden sm:inline">Hapus</span>
                                        </Button>
                                    </div>
                                </div>
                                
                                <div v-else class="p-8 text-center bg-zinc-50/50">
                                    <Users class="w-8 h-8 text-zinc-300 mx-auto mb-2" />
                                    <p class="text-sm font-semibold text-zinc-500">Belum ada data untuk {{ group.kategori }}.</p>
                                </div>
                            </CardContent>
                        </Card>

                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>