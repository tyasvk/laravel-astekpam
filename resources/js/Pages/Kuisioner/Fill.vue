<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ref, onMounted } from 'vue';

const props = defineProps({
    questions: {
        type: Array,
        required: true
    }
});

// Setup form Inertia khusus untuk menampung jawaban berdasarkan ID pertanyaan
const form = useForm({
    answers: {}
});

// Inisialisasi struktur form saat komponen dimuat
onMounted(() => {
    props.questions.forEach(q => {
        form.answers[q.id] = '';
    });
});

// State untuk mengatur kemunculan Pop-up (Modal)
const showConfirmModal = ref(false);
const showSuccessModal = ref(false);

// Fungsi untuk memvalidasi dan memunculkan pop-up konfirmasi
const openConfirm = () => {
    // Cek apakah ada jawaban yang masih kosong
    let allFilled = true;
    props.questions.forEach(q => {
        if (!form.answers[q.id] || form.answers[q.id].trim() === '') {
            allFilled = false;
        }
    });

    if (!allFilled) {
        alert('Mohon isi semua pertanyaan terlebih dahulu sebelum mengirim.');
        return;
    }

    showConfirmModal.value = true;
};

// Fungsi yang dijalankan saat tombol "Ya, Kirim" di pop-up di-klik
const submitForm = () => {
    showConfirmModal.value = false;
    
    form.post(route('kuisioner.submit'), {
        preserveScroll: true,
        onSuccess: () => {
            // Tampilkan pop up sukses saat berhasil dikirim
            showSuccessModal.value = true;
            
            // Pop up akan tampil selama 1.5 detik sebelum Inertia me-redirect ke Dashboard
            setTimeout(() => {
                showSuccessModal.value = false;
            }, 1500);
        }
    });
};
</script>

<template>
    <Head title="Isi Kuisioner" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-bold text-xl text-gray-800 leading-tight">Pengisian Kuisioner</h2>
        </template>

        <div class="py-6 px-4 max-w-4xl mx-auto sm:py-8 sm:px-6 lg:px-8">
            
            <div class="bg-indigo-50 border-l-4 border-indigo-600 p-4 rounded-r-xl mb-6 shadow-sm flex items-start gap-4">
                <div class="bg-indigo-100 p-2 rounded-full shrink-0 mt-0.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-indigo-900">Petunjuk Pengisian</h3>
                    <p class="text-sm text-indigo-700 mt-1 leading-relaxed">
                        Silakan jawab seluruh pertanyaan di bawah ini sesuai dengan kondisi riil di lapangan. Data ini akan digunakan untuk kebutuhan evaluasi laporan Astekpam.
                    </p>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden sm:p-2">
                
                <div v-if="!questions.length" class="p-12 text-center text-gray-400">
                    <p>Tidak ada daftar pertanyaan saat ini.</p>
                </div>

                <form v-else @submit.prevent="openConfirm" class="p-5 sm:p-6 space-y-8">
                    
                    <div v-for="(q, index) in questions" :key="q.id" class="space-y-3 group">
                        <label :for="'question_'+q.id" class="flex items-start gap-3 text-sm sm:text-base font-semibold text-gray-800">
                            <span class="bg-gray-100 group-hover:bg-indigo-100 group-hover:text-indigo-700 transition-colors text-gray-500 h-7 w-7 flex items-center justify-center rounded-full shrink-0 text-xs mt-0.5">
                                {{ index + 1 }}
                            </span>
                            <span class="pt-1 leading-relaxed">{{ q.pertanyaan }}</span>
                        </label>
                        
                        <div class="pl-10">
                            <textarea 
                                :id="'question_'+q.id"
                                v-model="form.answers[q.id]"
                                rows="3"
                                class="w-full border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-xl text-sm shadow-sm transition-all resize-y placeholder-gray-300"
                                placeholder="Tulis deskripsi jawaban Anda di sini..."
                                required
                            ></textarea>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-100 flex justify-end pl-10">
                        <PrimaryButton type="submit" :disabled="form.processing" class="h-11 px-6 rounded-xl text-sm font-bold shadow-sm hover:shadow-md transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            Kirim Jawaban
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>

        <div v-if="showConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm transition-opacity">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all border border-gray-100">
                <div class="p-6">
                    <div class="w-14 h-14 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center mb-5 mx-auto border-4 border-indigo-100/50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 transform rotate-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-extrabold text-center text-gray-900 mb-2">Kirim Kuisioner?</h3>
                    <p class="text-sm text-center text-gray-500 leading-relaxed px-4">
                        Pastikan semua jawaban telah diisi dengan benar. Data yang sudah dikirim akan langsung tersimpan di sistem admin.
                    </p>
                </div>
                <div class="px-6 py-4 bg-gray-50 flex gap-3 justify-end border-t border-gray-100">
                    <button @click="showConfirmModal = false" type="button" class="px-5 py-2.5 text-sm font-bold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-100 focus:outline-none transition-colors">
                        Periksa Lagi
                    </button>
                    <button @click="submitForm" :disabled="form.processing" type="button" class="px-5 py-2.5 text-sm font-bold text-white bg-indigo-600 border border-transparent rounded-xl hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-100 flex items-center transition-all shadow-sm">
                        <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Ya, Kirim Sekarang
                    </button>
                </div>
            </div>
        </div>

        <div v-if="showSuccessModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-gray-900/70 backdrop-blur-sm transition-opacity">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden transform transition-all p-8 text-center animate-bounce-short">
                <div class="w-20 h-20 rounded-full bg-green-100 text-green-500 flex items-center justify-center mx-auto mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-gray-900 mb-2">Berhasil!</h3>
                <p class="text-gray-500 font-medium">
                    Anda sudah mengisi kuisioner.
                </p>
                <div class="mt-6 flex justify-center">
                    <div class="w-8 h-1 bg-gray-200 rounded-full overflow-hidden">
                        <div class="w-full h-full bg-green-500 animate-pulse"></div>
                    </div>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>

<style scoped>
/* Animasi ringan untuk Pop Up Sukses */
.animate-bounce-short {
    animation: bounce-short 0.4s ease-out forwards;
}
@keyframes bounce-short {
    0% { transform: scale(0.9); opacity: 0; }
    50% { transform: scale(1.05); opacity: 1; }
    100% { transform: scale(1); opacity: 1; }
}
</style>