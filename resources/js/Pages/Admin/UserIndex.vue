<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
// Import komponen Shadcn yang sudah di-add tadi
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';
import { Button } from '@/Components/ui/button';
import { Badge } from '@/Components/ui/badge'; // npx shadcn-vue@latest add badge

// Data 'users' ini dikirim dari Laravel Controller lewat Inertia
defineProps({
    users: Array
});
</script>

<template>
    <Head title="Manage Users" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Petugas</h2>
                <Button>Tambah Petugas</Button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white p-6 shadow-sm sm:rounded-lg border">
                    
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Nama Petugas</TableHead>
                                <TableHead>Email</TableHead>
                                <TableHead>Role</TableHead>
                                <TableHead class="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="user in users" :key="user.id">
                                <TableCell class="font-medium">{{ user.name }}</TableCell>
                                <TableCell>{{ user.email }}</TableCell>
                                <TableCell>
                                    <Badge variant="outline" class="capitalize">
                                        {{ user.roles[0]?.name || 'No Role' }}
                                    </Badge>
                                </TableCell>
                                <TableCell class="text-right">
                                    <Button variant="ghost" size="sm" class="mr-2">Edit</Button>
                                    <Button variant="destructive" size="sm">Hapus</Button>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>