<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    users: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({});

const deleteUser = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus petugas ini? Data tidak dapat dikembalikan.')) {
        form.delete(route('admin.users.destroy', id));
    }
};
</script>

<template>
    <Head title="Manajemen Pengguna" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="font-black text-3xl text-slate-900 tracking-tight">Manajemen Personel</h2>
                    <p class="text-slate-500 mt-1">Otorisasi akses dan pengelolaan data petugas SPARTA.</p>
                </div>
                <Link :href="route('admin.users.create')">
                    <button class="bg-[#0f172a] hover:bg-[#1e293b] text-amber-500 font-bold px-6 py-3 rounded-2xl shadow-lg transition-all active:scale-95 flex items-center gap-2">
                        <span>+ Tambah Petugas</span>
                    </button>
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                    
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50/50">
                                <tr class="text-[11px] uppercase tracking-widest font-bold text-slate-400">
                                    <th class="px-8 py-5">Petugas</th>
                                    <th class="px-8 py-5">Email</th>
                                    <th class="px-8 py-5">Akses (Role)</th>
                                    <th class="px-8 py-5 text-right">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="user in users" :key="user.id" class="hover:bg-slate-50/80 transition-colors group">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-white font-bold text-sm shadow-md">
                                                {{ user.name.charAt(0).toUpperCase() }}
                                            </div>
                                            <span class="font-bold text-slate-900">{{ user.name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-slate-500 font-medium">{{ user.email }}</td>
                                    <td class="px-8 py-6">
                                        <div class="flex gap-2">
                                            <template v-if="user.roles && user.roles.length > 0">
                                                <span v-for="role in user.roles" :key="role.id" 
                                                    class="px-3 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200/60">
                                                    {{ role.name }}
                                                </span>
                                            </template>
                                            <span v-else class="text-xs text-slate-300 italic">No role</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <Link :href="route('admin.users.edit', user.id)">
                                                <button class="text-[11px] font-bold text-indigo-600 hover:text-indigo-800 bg-indigo-50 px-3 py-1.5 rounded-lg">EDIT</button>
                                            </Link>
                                            <button @click="deleteUser(user.id)" class="text-[11px] font-bold text-red-600 hover:text-red-800 bg-red-50 px-3 py-1.5 rounded-lg">HAPUS</button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="md:hidden divide-y divide-slate-100">
                        <div v-for="user in users" :key="user.id" class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-amber-500 flex items-center justify-center text-white font-bold text-sm shadow">
                                        {{ user.name.charAt(0).toUpperCase() }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900">{{ user.name }}</p>
                                        <p class="text-xs text-slate-500">{{ user.email }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <div class="flex gap-1.5">
                                    <template v-if="user.roles && user.roles.length > 0">
                                        <span v-for="role in user.roles" :key="role.id" 
                                            class="px-2.5 py-1 rounded-md text-[9px] font-bold uppercase bg-amber-50 text-amber-700 border border-amber-200">
                                            {{ role.name }}
                                        </span>
                                    </template>
                                </div>
                                <div class="flex gap-2">
                                    <Link :href="route('admin.users.edit', user.id)">
                                        <button class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-lg">EDIT</button>
                                    </Link>
                                    <button @click="deleteUser(user.id)" class="text-[10px] font-bold text-red-600 bg-red-50 px-3 py-1.5 rounded-lg">HAPUS</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>