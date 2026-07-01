<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { format } from 'date-fns';

defineProps({ responses: Array });

const downloadCsv = () => {
    // Trigger file download using native window object (melalui route StreamedResponse)
    window.location.href = route('admin.kuisioner.export');
};
</script>

<template>
    <Head title="Hasil Kuisioner" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Hasil Kuisioner</h2>
                <PrimaryButton @click="downloadCsv" class="w-full sm:w-auto flex justify-center items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    Download CSV
                </PrimaryButton>
            </div>
        </template>

        <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                <div class="overflow-x-auto p-4 sm:p-0">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b hidden sm:table-header-group">
                            <tr>
                                <th scope="col" class="px-6 py-3">Petugas</th>
                                <th scope="col" class="px-6 py-3">Waktu Isi</th>
                                <th scope="col" class="px-6 py-3">Jumlah Pertanyaan Dijawab</th>
                            </tr>
                        </thead>
                        <tbody class="block sm:table-row-group">
                            <tr v-for="res in responses" :key="res.id" class="border-b block sm:table-row mb-4 sm:mb-0 hover:bg-gray-50">
                                <td class="px-6 py-4 block sm:table-cell font-medium text-gray-900 before:content-['Petugas:_'] sm:before:content-none before:font-bold before:text-gray-500">
                                    {{ res.user.name }}
                                </td>
                                <td class="px-6 py-4 block sm:table-cell text-gray-600 before:content-['Waktu:_'] sm:before:content-none before:font-bold before:text-gray-500">
                                    {{ new Date(res.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' }) }}
                                </td>
                                <td class="px-6 py-4 block sm:table-cell text-gray-600 before:content-['Dijawab:_'] sm:before:content-none before:font-bold before:text-gray-500">
                                    {{ res.answers.length }} Pertanyaan
                                </td>
                            </tr>
                            <tr v-if="!responses.length">
                                <td colspan="3" class="px-6 py-10 text-center text-gray-500 block sm:table-cell">Belum ada yang mengisi kuisioner.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>