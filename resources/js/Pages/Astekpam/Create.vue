<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Button } from '@/Components/ui/button';
import { Separator } from '@/Components/ui/separator';
import { Textarea } from '@/Components/ui/textarea';
import { Save, Send } from 'lucide-vue-next';

const form = useForm({
    tanggal: new Date().toISOString().split('T')[0],
    waktu: '13.00-19.30 WIB',
    dari_rupam: 'RUPAM IV (Shift Pagi)',
    ke_rupam: 'RUPAM II (Shift Siang)',
    pimpinan: 'STAF KPLP',
    napi_total: 1650,
    blok_a: 993,
    blok_b: 636,
    dapur: 11,
    klinik: 10,
    personil: { hadir: 10, tidak_hadir: 1, keterangan: 'Ricki Aditia Pratama (cuti)' },
    tugas: { ka_rupam: 'Danil Ferdiansyah', staff_kplp: 'Anton Sutrisno', menara_3: 'Tyas/Dhimas' }
});

const submit = () => form.post(route('astekpam.store'));
</script>

<template>
    <Head title="Input Astekpam" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-bold text-2xl text-zinc-800">Buat Laporan Serah Terima</h2>
        </template>

        <div class="max-w-4xl mx-auto pb-20">
            <form @submit.prevent="submit" class="space-y-6">
                
                <Card class="border-none shadow-sm bg-white">
                    <CardHeader>
                        <CardTitle class="text-lg">Informasi Umum</CardTitle>
                        <CardDescription>Detail waktu dan pimpinan shift.</CardDescription>
                    </CardHeader>
                    <CardContent class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label>Tanggal</Label>
                            <Input type="date" v-model="form.tanggal" class="h-11" />
                        </div>
                        <div class="space-y-2">
                            <Label>Pukul / Shift</Label>
                            <Input v-model="form.waktu" placeholder="Contoh: 07.00-13.00 WIB" class="h-11" />
                        </div>
                        <div class="space-y-2">
                            <Label>Dari Rupam</Label>
                            <Input v-model="form.dari_rupam" class="h-11" />
                        </div>
                        <div class="space-y-2">
                            <Label>Ke Rupam</Label>
                            <Input v-model="form.ke_rupam" class="h-11" />
                        </div>
                    </CardContent>
                </Card>

                <Card class="border-none shadow-sm bg-white">
                    <CardHeader>
                        <CardTitle class="text-lg">A. Jumlah Penghuni</CardTitle>
                        <CardDescription>Rincian WBP di setiap blok hunian.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="space-y-2">
                                <Label>Total WBP</Label>
                                <Input type="number" v-model="form.napi_total" class="h-11 font-bold text-primary" />
                            </div>
                            <div class="space-y-2">
                                <Label>Blok A</Label>
                                <Input type="number" v-model="form.blok_a" class="h-11" />
                            </div>
                            <div class="space-y-2">
                                <Label>Blok B</Label>
                                <Input type="number" v-model="form.blok_b" class="h-11" />
                            </div>
                            <div class="space-y-2">
                                <Label>Klinik</Label>
                                <Input type="number" v-model="form.klinik" class="h-11" />
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card class="border-none shadow-sm bg-white">
                    <CardHeader>
                        <CardTitle class="text-lg">B. Pembagian Tugas</CardTitle>
                        <CardDescription>Personil yang bertanggung jawab di pos.</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label>Ka. Rupam</Label>
                                <Input v-model="form.tugas.ka_rupam" class="h-11" />
                            </div>
                            <div class="space-y-2">
                                <Label>Staff KPLP</Label>
                                <Input v-model="form.tugas.staff_kplp" class="h-11" />
                            </div>
                        </div>
                        <div class="space-y-2">
                            <Label>Petugas Pos Atas (Menara 3)</Label>
                            <Input v-model="form.tugas.menara_3" class="h-11" />
                        </div>
                    </CardContent>
                </Card>

                <div class="fixed bottom-0 left-0 right-0 p-4 bg-white/80 backdrop-blur-lg border-t md:relative md:bg-transparent md:border-none md:p-0 flex gap-3">
                    <Button type="submit" class="w-full h-12 text-lg shadow-lg shadow-primary/20" :disabled="form.processing">
                        <Send class="mr-2 h-5 w-5" /> Kirim Laporan
                    </Button>
                </div>

            </form>
        </div>
    </AuthenticatedLayout>
</template>