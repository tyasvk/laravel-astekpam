<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Reset cache Spatie
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Buat Permission (Hak Akses) menggunakan firstOrCreate agar tidak error jika sudah ada
        Permission::firstOrCreate(['name' => 'manage users']);
        Permission::firstOrCreate(['name' => 'create reports']);
        Permission::firstOrCreate(['name' => 'view reports']);
        Permission::firstOrCreate(['name' => 'verify reports']);

        // 3. Buat Role menggunakan firstOrCreate dan gunakan syncPermissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all());

        $userRole = Role::firstOrCreate(['name' => 'user']);
        $userRole->syncPermissions(['create reports', 'view reports']);

        // Role Pejabat (Hanya bisa view reports)
        $pejabatRole = Role::firstOrCreate(['name' => 'pejabat']);
        $pejabatRole->syncPermissions(['view reports']);

        // 4. Akun Admin Contoh
        $admin = User::updateOrCreate(
            ['email' => 'admin@astekpam.com'],
            ['name' => 'Admin Utama', 'password' => Hash::make('password123')]
        );
        $admin->assignRole($adminRole);

        // 5. Akun Petugas Contoh
        $petugas = User::updateOrCreate(
            ['email' => 'petugas@astekpam.com'],
            ['name' => 'Petugas Jaga', 'password' => Hash::make('password123')]
        );
        $petugas->assignRole($userRole);

        // 6. Akun Pejabat Contoh
        $pejabat = User::updateOrCreate(
            ['email' => 'pejabat@astekpam.com'],
            ['name' => 'Pejabat Lapas', 'password' => Hash::make('password123')]
        );
        $pejabat->assignRole($pejabatRole);

        $this->command->info('Role dan Permission berhasil disuntikkan ke database!');
    }
}