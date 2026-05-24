<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Card, CardContent } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Button } from '@/Components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { 
    Users, UserPlus, Shield, Edit2, Trash2, X, Filter
} from 'lucide-vue-next';

const props = defineProps({
    users: Array
});

const isModalOpen = ref(false);
const isEditMode = ref(false);
const currentUserId = ref(null);

// State Filter Frontend
const filterRegu = ref('all');
const filterJabatanGroup = ref('all');

const form = useForm({
    name: '',
    email: '',
    nip: '',
    password: '',
    regu: '',
    jabatan: ''
});

// Sistem Penyaringan & Pengurutan otomatis (Regu I-IV -> Karupam -> Wakarupam -> Anggota)
const filteredAndSortedUsers = computed(() => {
    let result = [...props.users];

    // 1. FILTER DATA
    if (filterRegu.value !== 'all') {
        result = result.filter(user => user.regu === filterRegu.value);
    }
    
    if (filterJabatanGroup.value !== 'all') {
        result = result.filter(user => {
            if (!user.jabatan) return false;
            const jab = user.jabatan.toLowerCase();
            return jab.includes(filterJabatanGroup.value.toLowerCase());
        });
    }

    // 2. URUT DATA BERDASARKAN REGU & HIERARKI JABATAN
    return result.sort((a, b) => {
        // Urutan Bobot Regu (I sampai IV)
        const reguOrder = { 'I': 1, 'II': 2, 'III': 3, 'IV': 4 };
        const weightA = reguOrder[a.regu] || 99;
        const weightB = reguOrder[b.regu] || 99;

        if (weightA !== weightB) {
            return weightA - weightB;
        }

        // Urutan Bobot Jabatan jika berada di dalam regu yang sama
        const getJabatanWeight = (jabatanText) => {
            if (!jabatanText) return 4;
            const j = jabatanText.toLowerCase();
            if (j.includes('wakarupam')) return 2; // Cek wakarupam dulu agar tidak terdeteksi karupam
            if (j.includes('karupam')) return 1;
            if (j.includes('anggota')) return 3;
            return 4;
        };

        return getJabatanWeight(a.jabatan) - getJabatanWeight(b.jabatan);
    });
});

const openAddModal = () => {
    form.clearErrors();
    isEditMode.value = false;
    currentUserId.value = null;
    form.reset();
    isModalOpen.value = true;
};

const openEditModal = (user) => {
    form.clearErrors();
    isEditMode.value = true;
    currentUserId.value = user.id;
    form.name = user.name;
    form.email = user.email;
    form.nip = user.nip;
    form.regu = user.regu || ''; 
    form.jabatan = user.jabatan || ''; 
    form.password = ''; 
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    form.reset();
    form.clearErrors();
};

const submitForm = () => {
    if (isEditMode.value) {
        form.put(route('admin.users.update', currentUserId.value), {
            onSuccess: () => closeModal(),
            preserveScroll: true
        });
    } else {
        form.post(route('admin.users.store'), {
            onSuccess: () => closeModal(),
            preserveScroll: true
        });
    }
};

const deleteUser = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus pengguna ini?')) {
        form.delete(route('admin.users.destroy', id), {
            preserveScroll: true
        });
    }
};
</script>

<template>
    <Head title="Manajemen Pengguna" />

    <AuthenticatedLayout>
        <div class="py-6 bg-zinc-50/30 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                
                <div class="bg-white rounded-2xl p-6 border border-zinc-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-extrabold text-zinc-900 tracking-tight flex items-center gap-2">
                            <Users class="w-6 h-6 text-blue-600" /> Manajemen Pengguna
                        </h1>
                        <p class="text-zinc-500 text-sm mt-0.5">Kelola data petugas, pembagian regu pengamanan, dan jabatan.</p>
                    </div>
                    <Button @click="openAddModal" class="rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold shadow-md shadow-blue-200 h-11 px-5 w-full sm:w-auto">
                        <UserPlus class="w-4 h-4 mr-2" /> Tambah Petugas
                    </Button>
                </div>

                <Card class="rounded-2xl border border-zinc-200 shadow-sm bg-white p-4">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                        <div class="flex items-center gap-2 text-xs font-bold text-zinc-400 uppercase tracking-wider">
                            <Filter class="w-4 h-4 text-zinc-400" />
                            <span>Filter Data:</span>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:flex gap-3 w-full sm:w-auto">
                            <Select v-model="filterRegu">
                                <SelectTrigger class="h-10 rounded-xl bg-zinc-50 border-0 focus:ring-1 focus:ring-blue-500 w-full sm:w-44 text-xs font-bold">
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

                            <Select v-model="filterJabatanGroup">
                                <SelectTrigger class="h-10 rounded-xl bg-zinc-50 border-0 focus:ring-1 focus:ring-blue-500 w-full sm:w-48 text-xs font-bold">
                                    <SelectValue placeholder="Semua Jabatan" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">Semua Jabatan</SelectItem>
                                    <SelectItem value="karupam">Karupam</SelectItem>
                                    <SelectItem value="wakarupam">Wakarupam</SelectItem>
                                    <SelectItem value="anggota">Anggota</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                    </div>
                </Card>

                <Card class="rounded-2xl border border-zinc-200 shadow-sm bg-white overflow-hidden">
                    <CardContent class="p-0 overflow-x-auto">
                        <table class="w-full text-left border-collapse min-w-[700px]">
                            <thead>
                                <tr class="bg-zinc-50 border-b border-zinc-100 text-zinc-400 font-bold text-[11px] tracking-wider uppercase">
                                    <th class="py-4 px-6">Nama / NIP</th>
                                    <th class="py-4 px-6">Email</th>
                                    <th class="py-4 px-6">Regu</th>
                                    <th class="py-4 px-6">Jabatan</th>
                                    <th class="py-4 px-6 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 text-sm font-medium text-zinc-800">
                                <tr v-for="user in filteredAndSortedUsers" :key="user.id" class="hover:bg-zinc-50/50 transition-colors">
                                    <td class="py-4 px-6">
                                        <p class="font-bold text-zinc-900">{{ user.name }}</p>
                                        <p class="text-xs text-zinc-400 font-normal mt-0.5">NIP. {{ user.nip || '-' }}</p>
                                    </td>
                                    <td class="py-4 px-6 text-zinc-600">{{ user.email }}</td>
                                    <td class="py-4 px-6">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-zinc-100 text-zinc-800 border border-zinc-200">
                                            Regu {{ user.regu || '-' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                            {{ user.jabatan || '-' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-right space-x-2">
                                        <Button @click="openEditModal(user)" variant="ghost" size="sm" class="h-9 w-9 p-0 text-zinc-600 hover:bg-zinc-100">
                                            <Edit2 class="w-4 h-4" />
                                        </Button>
                                        <Button @click="deleteUser(user.id)" variant="ghost" size="sm" class="h-9 w-9 p-0 text-rose-600 hover:bg-rose-50">
                                            <Trash2 class="w-4 h-4" />
                                        </Button>
                                    </td>
                                </tr>
                                <tr v-if="filteredAndSortedUsers.length === 0">
                                    <td colspan="5" class="text-center py-12 text-zinc-400 italic">Tidak ada data petugas yang cocok dengan filter.</td>
                                </tr>
                            </tbody>
                        </table>
                    </CardContent>
                </Card>

                <div v-if="isModalOpen" class="fixed inset-0 bg-zinc-950/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                    <div class="bg-white rounded-2xl border border-zinc-200 shadow-2xl max-w-lg w-full overflow-hidden animate-in fade-in zoom-in-95 duration-200 max-h-[90vh] overflow-y-auto">
                        <div class="px-6 py-4 border-b border-zinc-100 flex items-center justify-between bg-zinc-50 sticky top-0 bg-white z-10">
                            <h3 class="font-bold text-zinc-900 flex items-center gap-2">
                                <Shield class="w-4 h-4 text-blue-600" />
                                <span>{{ isEditMode ? 'Edit Data Petugas' : 'Tambah Petugas Baru' }}</span>
                            </h3>
                            <button type="button" @click="closeModal" class="text-zinc-400 hover:text-zinc-600 transition-colors">
                                <X class="w-4 h-4" />
                            </button>
                        </div>

                        <form @submit.prevent="submitForm" class="p-6 space-y-4">
                            <div class="space-y-1">
                                <Label class="text-xs font-bold text-zinc-500">NAMA LENGKAP</Label>
                                <Input v-model="form.name" placeholder="Masukkan nama petugas" class="h-11 rounded-xl" />
                                <p v-if="form.errors.name" class="text-xs text-rose-500 mt-1">{{ form.errors.name }}</p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <Label class="text-xs font-bold text-zinc-500">NIP</Label>
                                    <Input v-model="form.nip" placeholder="Masukkan NIP" class="h-11 rounded-xl" />
                                    <p v-if="form.errors.nip" class="text-xs text-rose-500 mt-1">{{ form.errors.nip }}</p>
                                </div>
                                <div class="space-y-1">
                                    <Label class="text-xs font-bold text-zinc-500">EMAIL</Label>
                                    <Input type="email" v-model="form.email" placeholder="alamat@email.com" class="h-11 rounded-xl" />
                                    <p v-if="form.errors.email" class="text-xs text-rose-500 mt-1">{{ form.errors.email }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <Label class="text-xs font-bold text-zinc-500">REGU PENGAMANAN</Label>
                                    <Select v-model="form.regu">
                                        <SelectTrigger class="h-11 rounded-xl bg-white border-zinc-200 shadow-sm w-full">
                                            <SelectValue placeholder="Pilih Regu" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="I">Regu I</SelectItem>
                                            <SelectItem value="II">Regu II</SelectItem>
                                            <SelectItem value="III">Regu III</SelectItem>
                                            <SelectItem value="IV">Regu IV</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <p v-if="form.errors.regu" class="text-xs text-rose-500 mt-1">{{ form.errors.regu }}</p>
                                </div>

                                <div class="space-y-1">
                                    <Label class="text-xs font-bold text-zinc-500">JABATAN</Label>
                                    <Select v-model="form.jabatan">
                                        <SelectTrigger class="h-11 rounded-xl bg-white border-zinc-200 shadow-sm w-full">
                                            <SelectValue placeholder="Pilih Jabatan" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="Karupam I">Karupam I</SelectItem>
                                            <SelectItem value="Karupam II">Karupam II</SelectItem>
                                            <SelectItem value="Karupam III">Karupam III</SelectItem>
                                            <SelectItem value="Karupam IV">Karupam IV</SelectItem>
                                            <SelectItem value="Wakarupam I">Wakarupam I</SelectItem>
                                            <SelectItem value="Wakarupam II">Wakarupam II</SelectItem>
                                            <SelectItem value="Wakarupam III">Wakarupam III</SelectItem>
                                            <SelectItem value="Wakarupam IV">Wakarupam IV</SelectItem>
                                            <SelectItem value="Anggota I">Anggota I</SelectItem>
                                            <SelectItem value="Anggota II">Anggota II</SelectItem>
                                            <SelectItem value="Anggota III">Anggota III</SelectItem>
                                            <SelectItem value="Anggota IV">Anggota IV</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <p v-if="form.errors.jabatan" class="text-xs text-rose-500 mt-1">{{ form.errors.jabatan }}</p>
                                </div>
                            </div>

                            <div class="space-y-1">
                                <Label class="text-xs font-bold text-zinc-500">
                                    PASSWORD {{ isEditMode ? '(Kosongkan jika tidak ingin diubah)' : '' }}
                                </Label>
                                <Input type="password" v-model="form.password" placeholder="••••••••" class="h-11 rounded-xl" />
                                <p v-if="form.errors.password" class="text-xs text-rose-500 mt-1">{{ form.errors.password }}</p>
                            </div>

                            <div class="flex gap-3 pt-4 border-t border-zinc-100 mt-6 sticky bottom-0 bg-white">
                                <Button type="button" variant="outline" @click="closeModal" class="flex-1 h-11 rounded-xl">Batal</Button>
                                <Button type="submit" class="flex-1 h-11 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-lg shadow-blue-200" :disabled="form.processing">
                                    {{ form.processing ? 'Menyimpan...' : (isEditMode ? 'Simpan Perubahan' : 'Daftarkan Petugas') }}
                                </Button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>