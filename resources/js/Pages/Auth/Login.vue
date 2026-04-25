<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import Checkbox from '@/Components/Checkbox.vue'; // Diperbaiki agar sesuai dengan file di proyek Anda
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/Components/ui/card';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import InputError from '@/Components/InputError.vue';

defineProps({
    canResetPassword: { type: Boolean },
    status: { type: String },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Masuk - ASTEKPAM" />

    <div class="min-h-screen flex flex-col items-center justify-center bg-slate-50 px-6 py-12 font-sans selection:bg-blue-600 selection:text-white">
        <div class="mb-8 text-center flex flex-col items-center">
            <Link href="/" class="flex flex-col items-center gap-3 group transition-transform hover:scale-105">
                <div class="p-3 bg-blue-800 rounded-2xl shadow-xl shadow-blue-800/20">
                    <ApplicationLogo class="h-10 w-10 fill-white" />
                </div>
                <div class="flex flex-col">
                    <span class="text-2xl font-black tracking-tighter text-slate-900 uppercase leading-none">Astekpam</span>
                    <span class="text-[10px] font-bold text-blue-700 uppercase tracking-[0.2em] mt-1">Lapas Kelas I Palembang</span>
                </div>
            </Link>
        </div>

        <Card class="w-full max-w-md border-none shadow-2xl shadow-slate-200/60 bg-white rounded-[2.5rem] overflow-hidden">
            <CardHeader class="space-y-1 pb-8 pt-10 text-center">
                <CardTitle class="text-2xl font-black tracking-tight text-slate-900 italic">Akses Portal Petugas</CardTitle>
                <CardDescription class="text-slate-500 font-medium text-sm">
                    Silakan masuk untuk mengelola laporan operasional.
                </CardDescription>
            </CardHeader>

            <div v-if="status" class="px-8 mb-4 text-sm font-medium text-green-600 text-center">
                {{ status }}
            </div>

            <CardContent>
                <form @submit.prevent="submit" class="space-y-6 px-2">
                    <div class="space-y-2">
                        <Label for="email" class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Alamat Email</Label>
                        <Input
                            id="email"
                            type="email"
                            v-model="form.email"
                            placeholder="nama@kemenkumham.go.id"
                            required
                            autofocus
                            autocomplete="username"
                            class="h-12 rounded-2xl border-slate-200 focus:ring-blue-600 focus:border-blue-600 transition-all px-4"
                        />
                        <InputError class="mt-1" :message="form.errors.email" />
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center justify-between ml-1">
                            <Label for="password" class="text-[10px] font-black uppercase tracking-widest text-slate-400">Kata Sandi</Label>
                            <Link
                                v-if="canResetPassword"
                                :href="route('password.request')"
                                class="text-xs font-bold text-blue-600 hover:text-blue-800 transition-colors"
                            >
                                Lupa sandi?
                            </Link>
                        </div>
                        <Input
                            id="password"
                            type="password"
                            v-model="form.password"
                            placeholder="••••••••"
                            required
                            autocomplete="current-password"
                            class="h-12 rounded-2xl border-slate-200 focus:ring-blue-600 focus:border-blue-600 transition-all px-4"
                        />
                        <InputError class="mt-1" :message="form.errors.password" />
                    </div>

                    <div class="flex items-center space-x-3 ml-1">
                        <Checkbox 
                            id="remember" 
                            name="remember" 
                            v-model:checked="form.remember" 
                            class="rounded border-slate-300 text-blue-800 focus:ring-blue-800"
                        />
                        <label for="remember" class="text-sm font-bold text-slate-600 cursor-pointer select-none">Ingat saya di perangkat ini</label>
                    </div>

                    <Button
                        type="submit"
                        class="w-full h-14 rounded-2xl bg-blue-800 hover:bg-blue-900 text-white font-black text-md shadow-xl shadow-blue-800/20 transition-all active:scale-95"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        Masuk Sekarang
                    </Button>
                </form>
            </CardContent>

            <CardFooter class="pb-10 pt-4 flex flex-col gap-4">
                <div class="relative w-full px-2">
                    <div class="absolute inset-0 flex items-center">
                        <span class="w-full border-t border-slate-100"></span>
                    </div>
                    <div class="relative flex justify-center text-[10px] uppercase tracking-[0.3em] font-black">
                        <span class="bg-white px-4 text-slate-300">Atau</span>
                    </div>
                </div>
                
                <p class="text-center text-sm text-slate-500 font-medium">
                    Belum memiliki akses? 
                    <Link :href="route('register')" class="text-blue-700 font-bold hover:underline underline-offset-8 transition-all">
                        Hubungi Admin
                    </Link>
                </p>
            </CardFooter>
        </Card>

        <div class="mt-12 text-center">
            <p class="text-[11px] font-black uppercase tracking-[0.5em] text-slate-400 opacity-40">
                Waspada Jangan-Jangan!
            </p>
        </div>
    </div>
</template>