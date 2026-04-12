<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { 
    Card, CardContent, CardHeader, CardTitle, CardDescription 
} from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { 
    FilePlus, 
    History, 
    Users, 
    ShieldCheck, 
    ClipboardList,
    ArrowRight
} from 'lucide-vue-next';

const page = usePage();
const user = page.props.auth.user;

// Helper untuk cek role & permission
const hasRole = (role) => user.roles.includes(role);
const hasPermission = (perm) => user.permissions.includes(perm);
</script>

<template>
    <Head title="Dashboard Astekpam" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                        Dashboard
                    </h2>
                    <p class="text-sm text-gray-500">Sistem Digitalisasi Laporan Astekpam - Lapas Kelas I Palembang</p>
                </div>
                <div class="flex items-center gap-2 bg-blue-50 px-4 py-2 rounded-full border border-blue-100">
                    <ShieldCheck class="w-4 h-4 text-blue-600" />
                    <span class="text-xs font-semibold text-blue-700 uppercase tracking-wider">
                        Mode: {{ user.roles[0] }}
                    </span>
                </div>
            </div>
        </template>

        <div class="py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
                
                <section class="relative overflow-hidden bg-white border rounded-2xl p-8 shadow-sm">
                    <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                        <div class="text-center md:text-left">
                            <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900">
                                Selamat Bertugas, {{ user.name }}!
                            </h1>
                            <p class="mt-2 text-gray-600 max-w-xl">
                                Pastikan setiap detail laporan serah terima regu pengamanan dicatat dengan teliti demi keamanan dan ketertiban Lapas.
                            </p>
                        </div>
                        <div v-if="hasPermission('create reports')">
                            <Link :href="route('astekpam.create')">
                                <Button class="h-12 px-6 shadow-lg shadow-blue-200 gap-2">
                                    <FilePlus class="w-5 h-5" />
                                    Buat Laporan Baru
                                </Button>
                            </Link>
                        </div>
                    </div>
                    <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-blue-50 rounded-full opacity-50"></div>
                </section>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    
                    <Card v-if="hasPermission('create reports')" class="group hover:border-blue-500 transition-all duration-300 shadow-none hover:shadow-md border-2 border-dashed border-gray-200">
                        <CardHeader>
                            <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center mb-2 group-hover:bg-blue-600 transition-colors">
                                <ClipboardList class="w-6 h-6 text-blue-600 group-hover:text-white" />
                            </div>
                            <CardTitle>Input Laporan</CardTitle>
                            <CardDescription>Catat mutasi penghuni dan pembagian tugas RUPAM.</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <Link :href="route('astekpam.create')" class="flex items-center text-sm font-semibold text-blue-600 hover:gap-2 transition-all">
                                Mulai Input <ArrowRight class="ml-1 w-4 h-4" />
                            </Link>
                        </CardContent>
                    </Card>

                    <Card class="hover:border-gray-400 transition-all duration-300 shadow-none border">
                        <CardHeader>
                            <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center mb-2">
                                <History class="w-6 h-6 text-gray-600" />
                            </div>
                            <CardTitle>Riwayat Laporan</CardTitle>
                            <CardDescription>Cek data serah terima dari shift-shift sebelumnya.</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <Link :href="route('astekpam.index')" class="flex items-center text-sm font-semibold text-gray-700 hover:gap-2 transition-all">
                                Lihat Riwayat <ArrowRight class="ml-1 w-4 h-4" />
                            </Link>
                        </CardContent>
                    </Card>

                    <Card v-if="hasRole('admin')" class="hover:border-indigo-500 transition-all duration-300 shadow-none border">
                        <CardHeader>
                            <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center mb-2">
                                <Users class="w-6 h-6 text-indigo-600" />
                            </div>
                            <CardTitle>Manajemen User</CardTitle>
                            <CardDescription>Kelola akun petugas dan konfigurasi hak akses.</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <Link :href="route('admin.users.index')" class="flex items-center text-sm font-semibold text-indigo-600 hover:gap-2 transition-all">
                                Kelola Petugas <ArrowRight class="ml-1 w-4 h-4" />
                            </Link>
                        </CardContent>
                    </Card>
                </div>

                <div class="flex items-center justify-center pt-6 border-t border-gray-100">
                    <div class="flex items-center gap-2">
                        <span class="flex h-2 w-2 rounded-full bg-green-500"></span>
                        <span class="text-xs text-gray-500 font-medium">Sistem Terhubung ke Database Lapas Palembang</span>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>