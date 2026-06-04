<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head, Link } from '@inertiajs/vue3';
import { User, ChevronLeft } from 'lucide-vue-next';
import { Button } from '@/Components/ui/button';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profil Saya" />

    <AuthenticatedLayout>
        <div class="py-6 sm:py-8 bg-zinc-50/30 min-h-screen font-sans">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 sm:space-y-8">
                
                <!-- Header Laman -->
                <div>
                    <div class="mb-5 sm:mb-6 flex items-start">
                        <Link :href="route('dashboard')">
                            <Button variant="outline" size="sm" class="rounded-full bg-white border-zinc-200 text-zinc-600 hover:text-zinc-900 shadow-sm h-10 sm:h-9 px-4 font-bold tracking-wide w-full sm:w-auto">
                                <ChevronLeft class="w-4 h-4 mr-1.5" /> Kembali ke Dashboard
                            </Button>
                        </Link>
                    </div>

                    <div class="flex items-center gap-3.5">
                        <div class="bg-blue-600 p-3 rounded-2xl text-white shadow-lg shadow-blue-200">
                            <User class="w-6 h-6" />
                        </div>
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-extrabold text-zinc-900 tracking-tight">Profil Saya</h1>
                            <p class="text-zinc-500 text-sm font-medium mt-1">Kelola informasi data diri, kepegawaian, dan keamanan akun Anda.</p>
                        </div>
                    </div>
                </div>

                <!-- Kumpulan Form -->
                <div class="space-y-6">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                    />

                    <UpdatePasswordForm />

                    <DeleteUserForm />
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>