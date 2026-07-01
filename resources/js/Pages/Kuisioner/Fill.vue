<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    questions: Array
});

// Setup form. Menyiapkan field answers sebagai object { question_id: jawaban }
const form = useForm({
    answers: {}
});

const submit = () => {
    form.post(route('kuisioner.submit'));
};
</script>

<template>
    <Head title="Isi Kuisioner" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Formulir Kuisioner</h2>
        </template>

        <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-3xl mx-auto">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg sm:rounded-xl">
                <div class="p-6 text-gray-900 flex flex-col gap-6">
                    <p class="text-sm text-gray-600 mb-2">Silahkan isi form di bawah ini dengan benar sesuai evaluasi Anda.</p>
                    
                    <form @submit.prevent="submit" class="flex flex-col gap-5">
                        <div v-for="(question, index) in questions" :key="question.id" class="flex flex-col gap-2">
                            <InputLabel :value="(index + 1) + '. ' + question.pertanyaan" class="text-md sm:text-lg font-bold text-gray-800" />
                            <TextInput 
                                v-model="form.answers[question.id]" 
                                type="text" 
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                placeholder="Ketik jawaban Anda di sini..."
                                required
                            />
                        </div>

                        <div class="mt-4 border-t pt-4">
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="w-full sm:w-auto justify-center">
                                Kirim Jawaban
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>