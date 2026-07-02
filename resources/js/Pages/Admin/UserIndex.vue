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

const filterRegu = ref('all');
const filterJabatanGroup = ref('all');

const form = useForm({
    name: '',
    email: '',
    nip: '',
    password: '',
    regu: '',
    jabatan: '',
    role: 'user'
});

const filteredAndSortedUsers = computed(() => {
    let result = [...props.users];

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

    return result.sort((a, b) => {
        const reguOrder = { 'I': 1, 'II': 2, 'III': 3, 'IV': 4 };
        const weightA = reguOrder[a.regu] || 99;
        const weightB = reguOrder[b.regu] || 99;

        if (weightA !== weightB) {
            return weightA - weightB;
        }

        const getJabatanWeight = (jabatanText) => {
            if (!jabatanText) return 6;
            const j = jabatanText.toLowerCase();
            if (j.includes('wakarupam')) return 2; 
            if (j.includes('karupam')) return 1;
            if (j.includes('wakasatgas')) return 4;
            if (j.includes('kasatgas')) return 3;
            if (j.includes('anggota')) return 5;
            return 6;
        };

        return getJabatanWeight(a.jabatan) - getJabatanWeight(b.jabatan);
    });
});

const openAddModal = () => {
    form.clearErrors();
    isEditMode.value = false;
    currentUserId.value = null;
    form.reset();
    form.role = 'user'; 
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
    
    if (user.roles && user.roles.some(r => r.name === 'admin')) {
        form.role = 'admin';
    } else if (user.roles && user.roles.some(r => r.name === 'pejabat')) {
        form.role = 'pejabat';
    } else {
        form.role = 'user';
    }
    
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
        <div class="py-6 sm:py-8 bg-zinc-50/30 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4 sm:space-y-6">
                
                <div class="bg-white rounded-2xl p-5 sm:p-6 border border-zinc-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="text-center sm:text-left">
                        <h1 class="text-xl sm:text-2xl font-extrabold text-zinc-900 tracking-tight flex items-center justify-center sm:justify-start gap-2">
                            <Users class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" /> Manajemen Pengguna
                        </h1>
                        <p class="text-zinc-500 text-xs sm:text-sm mt-1 sm:mt-0.5">Kelola data petugas, pembagian regu pengamanan, dan jabatan.</p>
                    </div>
                    <Button @click="openAddModal" class="rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold shadow-md shadow-blue-200 h-12 sm:h-11 px-5 w-full sm:w-auto">
                        <UserPlus class="w-4 h-4 mr-2" /> <span class="text-sm">Tambah Petugas</span>
                    </Button>
                </div>

                <Card class="rounded-2xl border border-zinc-200 shadow-sm bg-white p-4">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 sm:gap-4">
                        <div class="flex items-center gap-2 text-[11px] sm:text-xs font-bold text-zinc-400 uppercase tracking-wider">
                            <Filter class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-zinc-400" />
                            <span>Filter Data:</span>
                        </div>
                        
                        <div class="grid grid-cols-2 sm:flex gap-3 w-full sm:w-auto">
                            <Select v-model="filterRegu">
                                <SelectTrigger class="h-11 sm:h-10 rounded-xl bg-zinc-50 border-zinc-200 focus:ring-1 focus:ring-blue-500 w-full sm:w-44 text-sm sm:text-xs font-bold">
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
                                <SelectTrigger class="h-11 sm:h-10 rounded-xl bg-zinc-50 border-zinc-200 focus:ring-1 focus:ring-blue-500 w-full sm:w-48 text-sm sm:text-xs font-bold">
                                    <SelectValue placeholder="Semua Jabatan" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">Semua Jabatan</SelectItem>
                                    <SelectItem value="karupam">Karupam</SelectItem>
                                    <SelectItem value="wakarupam">Wakarupam</SelectItem>
                                    <SelectItem value="kasatgas">Kasatgas P2U</SelectItem>
                                    <SelectItem value="wakasatgas">Wakasatgas P2U</SelectItem>
                                    <SelectItem value="anggota">Anggota</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                    </div>
                </Card>

                <Card class="rounded-2xl border border-zinc-200 shadow-sm bg-white overflow-hidden">
                    <CardContent class="p-0 overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left border-collapse min-w-[800px]">
                            <thead>
                                <tr class="bg-zinc-50 border-b border-zinc-100 text-zinc-400 font-bold text-[10px] sm:text-[11px] tracking-wider uppercase">
                                    <th class="py-3.5 sm:py-4 px-4 sm:px-6">Nama / NIP</th>
                                    <th class="py-3.5 sm:py-4 px-4 sm:px-6">Email</th>
                                    <th class="py-3.5 sm:py-4 px-4 sm:px-6">Hak Akses</th>
                                    <th class="py-3.5 sm:py-4 px-4 sm:px-6">Regu</th>
                                    <th class="py-3.5 sm:py-4 px-4 sm:px-6">Jabatan</th>
                                    <th class="py-3.5 sm:py-4 px-4 sm:px-6 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 text-xs sm:text-sm font-medium text-zinc-800">
                                <tr v-for="user in filteredAndSortedUsers" :key="user.id" class="hover:bg-zinc-50/50 transition-colors">
                                    <td class="py-3 sm:py-4 px-4 sm:px-6">
                                        <p class="font-bold text-zinc-900">{{ user.name }}</p>
                                        <p class="text-[10px] sm:text-xs text-zinc-400 font-normal mt-0.5">NIP. {{ user.nip || '-' }}</p>
                                    </td>
                                    <td class="py-3 sm:py-4 px-4 sm:px-6 text-zinc-600">{{ user.email }}</td>
                                    
                                    <td class="py-3 sm:py-4 px-4 sm:px-6">
                                        <span v-if="user.roles && user.roles.some(r => r.name === 'admin')" class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] sm:text-xs font-bold bg-purple-100 text-purple-700 border border-purple-200">
                                            Admin
                                        </span>
                                        <span v-else-if="user.roles && user.roles.some(r => r.name === 'pejabat')" class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] sm:text-xs font-bold bg-amber-100 text-amber-700 border border-amber-200">
                                            Pejabat
                                        </span>
                                        <span v-else class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] sm:text-xs font-bold bg-zinc-100 text-zinc-700 border border-zinc-200">
                                            Petugas
                                        </span>
                                    </td>

                                    <td class="py-3 sm:py-4 px-4 sm:px-6">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] sm:text-xs font-bold bg-zinc-100 text-zinc-800 border border-zinc-200">
                                            Regu {{ user.regu || '-' }}
                                        </span>
                                    </td>
                                    <td class="py-3 sm:py-4 px-4 sm:px-6">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] sm:text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                            {{ user.jabatan || '-' }}
                                        </span>
                                    </td>
                                    <td class="py-3 sm:py-4 px-4 sm:px-6 text-right space-x-1 sm:space-x-2 whitespace-nowrap">
                                        <Button @click="openEditModal(user)" variant="ghost" size="sm" class="h-10 w-10 sm:h-9 sm:w-9 p-0 text-zinc-600 hover:bg-zinc-100">
                                            <Edit2 class="w-4 h-4" />
                                        </Button>
                                        <Button @click="deleteUser(user.id)" variant="ghost" size="sm" class="h-10 w-10 sm:h-9 sm:w-9 p-0 text-rose-600 hover:bg-rose-50">
                                            <Trash2 class="w-4 h-4" />
                                        </Button>
                                    </td>
                                </tr>
                                <tr v-if="filteredAndSortedUsers.length === 0">
                                    <td colspan="6" class="text-center py-12 text-xs sm:text-sm text-zinc-400 italic">Tidak ada data petugas yang cocok dengan filter.</td>
                                </tr>
                            </tbody>
                        </table>
                    </CardContent>
                </Card>

                <div v-if="isModalOpen" class="fixed inset-0 bg-zinc-950/40 backdrop-blur-sm z-50 flex items-end sm:items-center justify-center sm:p-4">
                    <div class="bg-white sm:rounded-2xl rounded-t-3xl border border-zinc-200 shadow-2xl w-full max-w-lg overflow-hidden animate-in slide-in-from-bottom-8 sm:slide-in-from-bottom-0 sm:zoom-in-95 duration-200 max-h-[90vh] flex flex-col">
                        
                        <div class="px-5 sm:px-6 py-4 border-b border-zinc-100 flex items-center justify-between bg-zinc-50/80 sticky top-0 z-10 shrink-0">
                            <h3 class="font-bold text-zinc-900 flex items-center gap-2 text-sm sm:text-base">
                                <Shield class="w-4 h-4 text-blue-600" />
                                <span>{{ isEditMode ? 'Edit Data Petugas' : 'Tambah Petugas Baru' }}</span>
                            </h3>
                            <button type="button" @click="closeModal" class="p-2 -mr-2 text-zinc-400 hover:text-zinc-600 transition-colors">
                                <X class="w-5 h-5 sm:w-4 sm:h-4" />
                            </button>
                        </div>

                        <form @submit.prevent="submitForm" class="p-5 sm:p-6 space-y-4 sm:space-y-5 overflow-y-auto custom-scrollbar">
                            
                            <div class="space-y-1.5">
                                <Label class="text-[11px] sm:text-xs font-bold text-zinc-500 tracking-wide">NAMA LENGKAP</Label>
                                <Input v-model="form.name" placeholder="Masukkan nama petugas" class="h-12 sm:h-11 text-base sm:text-sm rounded-xl" />
                                <p v-if="form.errors.name" class="text-xs text-rose-500 mt-1">{{ form.errors.name }}</p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                                <div class="space-y-1.5">
                                    <Label class="text-[11px] sm:text-xs font-bold text-zinc-500 tracking-wide">NIP</Label>
                                    <Input v-model="form.nip" placeholder="Masukkan NIP" class="h-12 sm:h-11 text-base sm:text-sm rounded-xl" />
                                    <p v-if="form.errors.nip" class="text-xs text-rose-500 mt-1">{{ form.errors.nip }}</p>
                                </div>
                                <div class="space-y-1.5">
                                    <Label class="text-[11px] sm:text-xs font-bold text-zinc-500 tracking-wide">EMAIL</Label>
                                    <Input type="email" v-model="form.email" placeholder="alamat@email.com" class="h-12 sm:h-11 text-base sm:text-sm rounded-xl" />
                                    <p v-if="form.errors.email" class="text-xs text-rose-500 mt-1">{{ form.errors.email }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                                <div class="space-y-1.5">
                                    <Label class="text-[11px] sm:text-xs font-bold text-zinc-500 tracking-wide">REGU PENGAMANAN</Label>
                                    <Select v-model="form.regu">
                                        <SelectTrigger class="h-12 sm:h-11 text-base sm:text-sm rounded-xl bg-white border-zinc-200 shadow-sm w-full">
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

                                <div class="space-y-1.5">
                                    <Label class="text-[11px] sm:text-xs font-bold text-zinc-500 tracking-wide">JABATAN</Label>
                                    <Select v-model="form.jabatan">
                                        <SelectTrigger class="h-12 sm:h-11 text-base sm:text-sm rounded-xl bg-white border-zinc-200 shadow-sm w-full">
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
                                            <SelectItem value="Kasatgas I">Kasatgas I</SelectItem>
                                            <SelectItem value="Kasatgas II">Kasatgas II</SelectItem>
                                            <SelectItem value="Kasatgas III">Kasatgas III</SelectItem>
                                            <SelectItem value="Kasatgas IV">Kasatgas IV</SelectItem>
                                            <SelectItem value="Wakasatgas I">Wakasatgas I</SelectItem>
                                            <SelectItem value="Wakasatgas II">Wakasatgas II</SelectItem>
                                            <SelectItem value="Wakasatgas III">Wakasatgas III</SelectItem>
                                            <SelectItem value="Wakasatgas IV">Wakasatgas IV</SelectItem>
                                            <SelectItem value="Anggota I">Anggota I</SelectItem>
                                            <SelectItem value="Anggota II">Anggota II</SelectItem>
                                            <SelectItem value="Anggota III">Anggota III</SelectItem>
                                            <SelectItem value="Anggota IV">Anggota IV</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <p v-if="form.errors.jabatan" class="text-xs text-rose-500 mt-1">{{ form.errors.jabatan }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                                <div class="space-y-1.5">
                                    <Label class="text-[11px] sm:text-xs font-bold text-zinc-500 tracking-wide">HAK AKSES (ROLE)</Label>
                                    <Select v-model="form.role">
                                        <SelectTrigger class="h-12 sm:h-11 text-base sm:text-sm rounded-xl bg-white border-zinc-200 shadow-sm w-full">
                                            <SelectValue placeholder="Pilih Role" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="user">Petugas Biasa</SelectItem>
                                            <SelectItem value="admin">Administrator</SelectItem>
                                            <SelectItem value="pejabat">Pejabat Lapas</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <p v-if="form.errors.role" class="text-xs text-rose-500 mt-1">{{ form.errors.role }}</p>
                                </div>

                                <div class="space-y-1.5">
                                    <Label class="text-[11px] sm:text-xs font-bold text-zinc-500 tracking-wide">
                                        PASSWORD {{ isEditMode ? '(Isi jika diubah)' : '' }}
                                    </Label>
                                    <Input type="password" v-model="form.password" placeholder="••••••••" class="h-12 sm:h-11 text-base sm:text-sm rounded-xl" />
                                    <p v-if="form.errors.password" class="text-xs text-rose-500 mt-1">{{ form.errors.password }}</p>
                                </div>
                            </div>

                            <div class="flex gap-3 pt-5 sm:pt-4 border-t border-zinc-100 mt-6 shrink-0">
                                <Button type="button" variant="outline" @click="closeModal" class="flex-1 h-12 sm:h-11 text-sm sm:text-sm rounded-xl">Batal</Button>
                                <Button type="submit" class="flex-1 h-12 sm:h-11 text-sm sm:text-sm rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-lg shadow-blue-200" :disabled="form.processing">
                                    {{ form.processing ? 'Menyimpan...' : (isEditMode ? 'Simpan' : 'Daftarkan') }}
                                </Button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { height: 6px; width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>