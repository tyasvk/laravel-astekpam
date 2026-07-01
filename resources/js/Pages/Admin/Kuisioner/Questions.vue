<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref } from 'vue';

const props = defineProps({ 
    questions: Array,
    isKuisionerActive: Boolean
});

// Setup form untuk pertanyaan baru
const form = useForm({
    pertanyaan: ''
});

// Setup form untuk toggle status ON/OFF
const statusForm = useForm({
    status: props.isKuisionerActive
});

const toggleStatus = () => {
    statusForm.post(route('admin.kuisioner.toggle'), {
        preserveScroll: true
    });
};

const submitNewQuestion = () => {
    form.post(route('admin.kuisioner.store'), {
        onSuccess: () => form.reset()
    });
};

const editId = ref(null);
const editForm = useForm({ pertanyaan: '' });

const startEdit = (question) => {
    editId.value = question.id;
    editForm.pertanyaan = question.pertanyaan;
};

const saveEdit = (id) => {
    editForm.put(route('admin.kuisioner.update', id), {
        onSuccess: () => { editId.value = null; }
    });
};

const deleteQuestion = (id) => {
    if(confirm('Hapus pertanyaan ini?')) {
        router.delete(route('admin.kuisioner.destroy', id));
    }
};
</script>

<template>
    <Head title="Kelola Pertanyaan" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-bold text-xl text-gray-800 leading-tight">Kelola Kuisioner</h2>
        </template>

        <div class="py-4 px-4 max-w-4xl mx-auto space-y-4 sm:py-6 sm:px-6 lg:px-8 sm:space-y-6">
            
            <div class="bg-white p-4 shadow rounded-xl border border-gray-100 flex items-center justify-between transition-all duration-300"
                 :class="statusForm.status ? 'border-l-4 border-l-indigo-600' : 'border-l-4 border-l-gray-400'">
                <div class="pr-2">
                    <h3 class="font-bold text-gray-900 text-sm sm:text-base">Akses Pengisian Kuisioner</h3>
                    <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">
                        {{ statusForm.status ? 'Petugas lapangan diizinkan untuk mengisi lembar kuisioner.' : 'Pendaftaran lembar kuisioner sedang ditutup sementara.' }}
                    </p>
                </div>
                
                <label class="relative inline-flex items-center cursor-pointer select-none shrink-0">
                    <input type="checkbox" v-model="statusForm.status" @change="toggleStatus" class="sr-only peer" :disabled="statusForm.processing">
                    <div class="w-12 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                </label>
            </div>

            <div class="bg-white p-4 shadow rounded-xl border border-gray-100 sm:p-6">
                <form @submit.prevent="submitNewQuestion" class="space-y-3 sm:space-y-0 sm:flex sm:gap-4 sm:items-end">
                    <div class="flex-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5">Pertanyaan Baru</label>
                        <TextInput 
                            v-model="form.pertanyaan" 
                            type="text" 
                            class="block w-full border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg text-sm" 
                            placeholder="Tulis draf pertanyaan di sini..."
                            required 
                        />
                    </div>
                    <PrimaryButton :disabled="form.processing" class="w-full sm:w-auto h-10 justify-center rounded-lg shadow-sm text-sm font-medium whitespace-nowrap">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah
                    </PrimaryButton>
                </form>
            </div>

            <div class="bg-white shadow rounded-xl border border-gray-100 overflow-hidden">
                <div class="bg-gray-50/70 px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Daftar Pertanyaan</span>
                    <span class="bg-indigo-50 text-indigo-700 text-xs px-2.5 py-0.5 rounded-full font-bold">
                        {{ questions.length }} Data
                    </span>
                </div>

                <div class="divide-y divide-gray-100">
                    <div v-for="(q, index) in questions" :key="q.id" class="p-4 hover:bg-gray-50/50 transition-colors flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
                        
                        <div class="w-full" v-if="editId === q.id">
                            <form @submit.prevent="saveEdit(q.id)" class="space-y-3 sm:space-y-0 sm:flex sm:gap-2 w-full">
                                <TextInput v-model="editForm.pertanyaan" class="block w-full text-sm rounded-lg" />
                                <div class="flex gap-2 justify-end">
                                    <PrimaryButton type="submit" class="px-3 py-1.5 text-xs rounded-lg">Simpan</PrimaryButton>
                                    <DangerButton type="button" @click="editId = null" class="px-3 py-1.5 text-xs rounded-lg">Batal</DangerButton>
                                </div>
                            </form>
                        </div>

                        <template v-else>
                            <div class="flex items-start gap-3 flex-1">
                                <span class="text-xs font-bold text-gray-400 mt-0.5 bg-gray-100 h-5 w-5 rounded-full flex items-center justify-center shrink-0">
                                    {{ index + 1 }}
                                </span>
                                <span class="text-gray-800 text-sm sm:text-base leading-relaxed">{{ q.pertanyaan }}</span>
                            </div>
                            
                            <div class="flex items-center gap-2 pt-2 border-t border-dashed border-gray-100 sm:border-0 sm:pt-0 justify-end shrink-0">
                                <button 
                                    @click="startEdit(q)" 
                                    class="inline-flex items-center justify-center bg-indigo-50 hover:bg-indigo-100 active:bg-indigo-200 text-indigo-700 font-bold text-xs px-3 py-2 rounded-lg transition-colors min-w-[65px]"
                                >
                                    Edit
                                </button>
                                <button 
                                    @click="deleteQuestion(q.id)" 
                                    class="inline-flex items-center justify-center bg-red-50 hover:bg-red-100 active:bg-red-200 text-red-600 font-bold text-xs px-3 py-2 rounded-lg transition-colors min-w-[65px]"
                                >
                                    Hapus
                                </button>
                            </div>
                        </template>

                    </div>
                </div>

                <div v-if="!questions.length" class="p-8 text-center text-gray-400 text-sm flex flex-col items-center justify-center space-y-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span>Belum ada daftar pertanyaan kuisioner.</span>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>