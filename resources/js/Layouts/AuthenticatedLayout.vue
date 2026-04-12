<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { usePermission } from '@/Composables/usePermission';
import { Button } from '@/Components/ui/button';
import { ScrollArea } from '@/Components/ui/scroll-area';
import { Separator } from '@/Components/ui/separator';
import { Sheet, SheetContent, SheetTrigger } from '@/Components/ui/sheet';
import { LayoutDashboard, Users, FilePlus, History, LogOut, Menu, ShieldCheck } from 'lucide-vue-next';

const { hasRole, hasPermission } = usePermission();
const user = usePage().props.auth.user;
const isMobileOpen = ref(false);

const navigation = [
    { name: 'Dashboard', href: route('dashboard'), icon: LayoutDashboard, show: true },
    { name: 'Manajemen User', href: route('admin.users.index'), icon: Users, show: hasRole('admin') },
    { name: 'Input Astekpam', href: route('astekpam.create'), icon: FilePlus, show: hasPermission('create reports') },
    { name: 'Riwayat Laporan', href: route('astekpam.index'), icon: History, show: true },
];
</script>

<template>
    <div class="flex min-h-screen bg-zinc-50/50">
        <aside class="hidden md:flex w-64 flex-col border-r bg-white sticky top-0 h-screen">
            <div class="p-6 flex items-center gap-2">
                <div class="bg-primary p-1.5 rounded-lg text-white">
                    <ShieldCheck :size="20" />
                </div>
                <div class="flex flex-col">
                    <span class="font-bold text-sm tracking-tight">ASTEKPAM</span>
                    <span class="text-[10px] text-zinc-500 font-medium">Lapas I Palembang</span>
                </div>
            </div>

            <ScrollArea class="flex-1 px-4">
                <nav class="space-y-1">
                    <template v-for="item in navigation" :key="item.name">
                        <Link v-if="item.show" :href="item.href"
                            :class="[route().current(item.href.split('/').pop() + '*') ? 'bg-zinc-100 text-primary font-semibold' : 'text-zinc-600 hover:bg-zinc-50', 'group flex items-center px-3 py-2 text-sm rounded-md transition-all']">
                            <component :is="item.icon" class="mr-3 h-4 w-4 shrink-0" />
                            {{ item.name }}
                        </Link>
                    </template>
                </nav>
            </ScrollArea>

            <div class="p-4 border-t">
                <div class="flex items-center gap-3 px-2 py-3 bg-zinc-50 rounded-lg mb-2">
                    <div class="h-8 w-8 rounded-full bg-zinc-200 flex items-center justify-center text-xs font-bold text-zinc-600">
                        {{ user.name.charAt(0) }}
                    </div>
                    <div class="flex flex-col overflow-hidden">
                        <span class="text-xs font-bold truncate">{{ user.name }}</span>
                        <span class="text-[10px] text-zinc-500 uppercase">{{ user.roles[0] }}</span>
                    </div>
                </div>
                <Link :href="route('logout')" method="post" as="button" class="w-full">
                    <Button variant="ghost" class="w-full justify-start text-red-500 hover:text-red-600 hover:bg-red-50 h-9">
                        <LogOut class="mr-2 h-4 w-4" /> Log Out
                    </Button>
                </Link>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <header class="md:hidden flex items-center justify-between h-16 px-4 border-b bg-white">
                <div class="flex items-center gap-2">
                    <ShieldCheck class="text-primary h-6 w-6" />
                    <span class="font-bold">ASTEKPAM</span>
                </div>
                <Sheet>
                    <SheetTrigger as-child>
                        <Button variant="ghost" size="icon"><Menu /></Button>
                    </SheetTrigger>
                    <SheetContent side="left" class="w-64 p-0">
                        <div class="p-6 font-bold text-lg border-b">Menu Navigasi</div>
                        <nav class="p-4 space-y-2">
                            <template v-for="item in navigation" :key="item.name">
                                <Link v-if="item.show" :href="item.href" class="flex items-center p-2 text-sm rounded-md">
                                    <component :is="item.icon" class="mr-3 h-5 w-5" /> {{ item.name }}
                                </Link>
                            </template>
                        </nav>
                    </SheetContent>
                </Sheet>
            </header>

            <header v-if="$slots.header" class="bg-white/80 backdrop-blur-md border-b sticky top-0 z-10 hidden md:block">
                <div class="max-w-7xl mx-auto py-4 px-6">
                    <slot name="header" />
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-4 md:p-8">
                <slot />
            </main>
        </div>
    </div>
</template>