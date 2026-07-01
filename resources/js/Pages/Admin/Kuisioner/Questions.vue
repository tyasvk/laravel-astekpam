<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref } from 'vue';

const props = defineProps({ 
    questions: Array,
    isKuisionerActive: Boolean // Menambahkan props untuk status
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
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kelola Pertanyaan Kuisioner</h2>
                
                <!-- TOMBOL TOGGLE ON/OFF -->
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" v-model="statusForm.status" @change="toggleStatus" class="sr-only peer" :disabled="statusForm.processing">
                    <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    <span class="ml-3 text-sm font-bold" :class="statusForm.status ? 'text-indigo-700' : 'text-gray-500'">
                        {{ statusForm.status ? 'Kuisioner Aktif' : 'Kuisioner Nonaktif' }}
                    </span>
                </label>
            </div>
        </template>

        <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto space-y-6">
            
            <div class="bg-white p-4 sm:p-6 shadow sm:rounded-lg">
                <form @submit.prevent="submitNewQuestion" class="flex flex-col sm:flex-row gap-4 items-start sm:items-end">
                    <div class="w-full">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pertanyaan Baru</label>
                        <TextInput v-model="form.pertanyaan" type="text" class="block w-full" required />
                    </div>
                    <PrimaryButton :disabled="form.processing" class="w-full sm:w-auto h-fit justify-center whitespace-nowrap">
                        Tambah
                    </PrimaryButton>
                </form>
            </div>

            <div class="bg-white shadow sm:rounded-lg flex flex-col">
                <div v-for="q in questions" :key="q.id" class="p-4 border-b last:border-0 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    
                    <div class="w-full" v-if="editId === q.id">
                        <form @submit.prevent="saveEdit(q.id)" class="flex gap-2 w-full">
                            <TextInput v-model="editForm.pertanyaan" class="block w-full" />
                            <PrimaryButton type="submit">Simpan</PrimaryButton>
                            <DangerButton type="button" @click="editId = null">Batal</DangerButton>
                        </form>
                    </div>

                    <template v-else>
                        <span class="text-gray-800 text-sm sm:text-base flex-1">{{ q.pertanyaan }}</span>
                        <div class="flex items-center gap-2 mt-2 sm:mt-0 self-end sm:self-auto">
                            <button @click="startEdit(q)" class="text-indigo-600 font-bold text-sm">Edit</button>
                            <button @click="deleteQuestion(q.id)" class="text-red-600 font-bold text-sm">Hapus</button>
                        </div>
                    </template>

                </div>
                <div v-if="!questions.length" class="p-4 text-center text-gray-500 text-sm">
                    Belum ada pertanyaan.
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>