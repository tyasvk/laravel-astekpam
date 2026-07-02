<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Card, CardContent } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Button } from '@/Components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { User, Lock, CheckCircle } from 'lucide-vue-next';

// Menerima data user yang dikirim langsung dari ProfileController
const props = defineProps({
    user: Object,
    status: String
});

// Form Informasi Profil (Bisa diedit oleh semua user)
const profileForm = useForm({
    name: props.user.name || '',
    email: props.user.email || '',
    no_hp: props.user.no_hp || '',
    nip: props.user.nip || '',
    regu: props.user.regu || '',
    jabatan: props.user.jabatan || '',
});

// Form Ubah Password
const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const showProfileSuccess = ref(false);
const showPasswordSuccess = ref(false);

const updateProfile = () => {
    profileForm.patch(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            showProfileSuccess.value = true;
            // Menghilangkan notifikasi sukses setelah 3 detik
            setTimeout(() => showProfileSuccess.value = false, 3000);
        },
    });
};

const updatePassword = () => {
    passwordForm.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => {
            passwordForm.reset();
            showPasswordSuccess.value = true;
            setTimeout(() => showPasswordSuccess.value = false, 3000);
        },
        onError: () => {
            if (passwordForm.errors.password) {
                passwordForm.reset('password', 'password_confirmation');
            }
            if (passwordForm.errors.current_password) {
                passwordForm.reset('current_password');
            }
        },
    });
};
</script>

<template>
    <Head title="Pengaturan Profil" />

    <AuthenticatedLayout>
        <div class="py-6 sm:py-8 bg-zinc-50/30 min-h-screen">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 space-y-6">
                
                <!-- Title Header -->
                <div class="bg-white rounded-2xl p-5 border border-zinc-200 shadow-sm">
                    <h1 class="text-xl sm:text-2xl font-extrabold text-zinc-900 tracking-tight flex items-center gap-2">
                        <User class="w-5 h-5 text-blue-600" /> Pengaturan Profil
                    </h1>
                    <p class="text-zinc-500 text-xs sm:text-sm mt-1">Perbarui informasi personal, data kedinasan, dan kata sandi akun Anda.</p>
                </div>

                <!-- KARTU 1: DATA INFORMASI PROFIL LENGKAP -->
                <Card class="rounded-2xl border border-zinc-200 shadow-sm bg-white overflow-hidden">
                    <div class="bg-zinc-50 px-5 py-4 border-b border-zinc-100 flex items-center gap-2">
                        <User class="w-4 h-4 text-zinc-500" />
                        <span class="font-bold text-sm text-zinc-700">Informasi Akun & Kedinasan</span>
                    </div>
                    <CardContent class="p-5 sm:p-6">
                        <form @submit.prevent="updateProfile" class="space-y-4">
                            
                            <!-- Alert Sukses Profil -->
                            <transition enter-active-class="transition ease-out duration-300" enter-from-class="opacity-0 -translate-y-2" leave-active-class="transition ease-in duration-200" leave-to-class="opacity-0 translate-y-2">
                                <div v-if="showProfileSuccess" class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl flex items-center gap-2 text-sm font-medium">
                                    <CheckCircle class="w-5 h-5 text-emerald-500" /> 
                                    Profil dan Data Kedinasan berhasil diperbarui!
                                </div>
                            </transition>

                            <!-- Baris 1: Nama & Email -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <Label class="text-[11px] font-bold text-zinc-500 tracking-wide">NAMA LENGKAP</Label>
                                    <Input v-model="profileForm.name" class="h-11 rounded-xl text-sm" placeholder="Masukkan nama" />
                                    <p v-if="profileForm.errors.name" class="text-xs text-rose-500 mt-1">{{ profileForm.errors.name }}</p>
                                </div>

                                <div class="space-y-1.5">
                                    <Label class="text-[11px] font-bold text-zinc-500 tracking-wide">EMAIL</Label>
                                    <Input type="email" v-model="profileForm.email" class="h-11 rounded-xl text-sm" placeholder="alamat@email.com" />
                                    <p v-if="profileForm.errors.email" class="text-xs text-rose-500 mt-1">{{ profileForm.errors.email }}</p>
                                </div>
                            </div>

                            <!-- Baris 2: NIP & No HP/WA -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <Label class="text-[11px] font-bold text-zinc-500 tracking-wide">NIP</Label>
                                    <Input v-model="profileForm.nip" class="h-11 rounded-xl text-sm" placeholder="Masukkan NIP" />
                                    <p v-if="profileForm.errors.nip" class="text-xs text-rose-500 mt-1">{{ profileForm.errors.nip }}</p>
                                </div>

                                <div class="space-y-1.5">
                                    <Label class="text-[11px] font-bold text-zinc-500 tracking-wide">NOMOR WHATSAPP / HP</Label>
                                    <Input v-model="profileForm.no_hp" class="h-11 rounded-xl text-sm" placeholder="Contoh: 0812345678" />
                                    <p v-if="profileForm.errors.no_hp" class="text-xs text-rose-500 mt-1">{{ profileForm.errors.no_hp }}</p>
                                </div>
                            </div>

                            <!-- Baris 3: Regu & Jabatan -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <Label class="text-[11px] font-bold text-zinc-500 tracking-wide">REGU PENGAMANAN</Label>
                                    <Select v-model="profileForm.regu">
                                        <SelectTrigger class="h-11 text-sm rounded-xl bg-white border-zinc-200 shadow-sm w-full">
                                            <SelectValue placeholder="Pilih Regu" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="I">Regu I</SelectItem>
                                            <SelectItem value="II">Regu II</SelectItem>
                                            <SelectItem value="III">Regu III</SelectItem>
                                            <SelectItem value="IV">Regu IV</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <p v-if="profileForm.errors.regu" class="text-xs text-rose-500 mt-1">{{ profileForm.errors.regu }}</p>
                                </div>

                                <div class="space-y-1.5">
                                    <Label class="text-[11px] font-bold text-zinc-500 tracking-wide">JABATAN</Label>
                                    <Select v-model="profileForm.jabatan">
                                        <SelectTrigger class="h-11 text-sm rounded-xl bg-white border-zinc-200 shadow-sm w-full">
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
                                    <p v-if="profileForm.errors.jabatan" class="text-xs text-rose-500 mt-1">{{ profileForm.errors.jabatan }}</p>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-4 pt-2">
                                <!-- Teks indikator "Tersimpan" di sebelah tombol -->
                                <transition enter-active-class="transition ease-out duration-300" enter-from-class="opacity-0 scale-95" leave-active-class="transition ease-in duration-200" leave-to-class="opacity-0">
                                    <span v-if="showProfileSuccess" class="text-sm text-emerald-600 font-bold flex items-center gap-1.5">
                                        <CheckCircle class="w-4 h-4" /> Tersimpan.
                                    </span>
                                </transition>
                                
                                <Button type="submit" class="rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 h-10 shadow-md shadow-blue-100 text-xs sm:text-sm" :disabled="profileForm.processing">
                                    {{ profileForm.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>

                <!-- KARTU 2: KATA SANDI / SECURITY -->
                <Card class="rounded-2xl border border-zinc-200 shadow-sm bg-white overflow-hidden">
                    <div class="bg-zinc-50 px-5 py-4 border-b border-zinc-100 flex items-center gap-2">
                        <Lock class="w-4 h-4 text-zinc-500" />
                        <span class="font-bold text-sm text-zinc-700">Perbarui Kata Sandi</span>
                    </div>
                    <CardContent class="p-5 sm:p-6">
                        <form @submit.prevent="updatePassword" class="space-y-4">
                            
                            <!-- Alert Sukses Password -->
                            <transition enter-active-class="transition ease-out duration-300" enter-from-class="opacity-0 -translate-y-2" leave-active-class="transition ease-in duration-200" leave-to-class="opacity-0 translate-y-2">
                                <div v-if="showPasswordSuccess" class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl flex items-center gap-2 text-sm font-medium">
                                    <CheckCircle class="w-5 h-5 text-emerald-500" /> 
                                    Kata sandi Anda berhasil diperbarui.
                                </div>
                            </transition>

                            <div class="space-y-1.5">
                                <Label class="text-[11px] font-bold text-zinc-500 tracking-wide">KATA SANDI SAAT INI</Label>
                                <Input type="password" v-model="passwordForm.current_password" class="h-11 rounded-xl text-sm" placeholder="••••••••" />
                                <p v-if="passwordForm.errors.current_password" class="text-xs text-rose-500 mt-1">{{ passwordForm.errors.current_password }}</p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <Label class="text-[11px] font-bold text-zinc-500 tracking-wide">KATA SANDI BARU</Label>
                                    <Input type="password" v-model="passwordForm.password" class="h-11 rounded-xl text-sm" placeholder="••••••••" />
                                    <p v-if="passwordForm.errors.password" class="text-xs text-rose-500 mt-1">{{ passwordForm.errors.password }}</p>
                                </div>

                                <div class="space-y-1.5">
                                    <Label class="text-[11px] font-bold text-zinc-500 tracking-wide">KONFIRMASI KATA SANDI BARU</Label>
                                    <Input type="password" v-model="passwordForm.password_confirmation" class="h-11 rounded-xl text-sm" placeholder="••••••••" />
                                    <p v-if="passwordForm.errors.password_confirmation" class="text-xs text-rose-500 mt-1">{{ passwordForm.errors.password_confirmation }}</p>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-4 pt-2">
                                <!-- Teks indikator "Tersimpan" di sebelah tombol Sandi -->
                                <transition enter-active-class="transition ease-out duration-300" enter-from-class="opacity-0 scale-95" leave-active-class="transition ease-in duration-200" leave-to-class="opacity-0">
                                    <span v-if="showPasswordSuccess" class="text-sm text-emerald-600 font-bold flex items-center gap-1.5">
                                        <CheckCircle class="w-4 h-4" /> Sandi Tersimpan.
                                    </span>
                                </transition>
                                
                                <Button type="submit" class="rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 h-10 shadow-md shadow-blue-100 text-xs sm:text-sm" :disabled="passwordForm.processing">
                                    {{ passwordForm.processing ? 'Memperbarui...' : 'Perbarui Sandi' }}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>

            </div>
        </div>
    </AuthenticatedLayout>
</template>