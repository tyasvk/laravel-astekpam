<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Reset cache Spatie (penting agar perubahan terbaca)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Buat Permission (Hak Akses)
        Permission::create(['name' => 'manage users']);
        Permission::create(['name' => 'create reports']);
        Permission::create(['name' => 'view reports']);
        Permission::create(['name' => 'verify reports']);

        // 3. Buat Role dan Berikan Permission
        $adminRole = Role::create(['name' => 'admin']);
        // Admin mendapatkan semua hak akses
        $adminRole->givePermissionTo(Permission::all());

        $userRole = Role::create(['name' => 'user']);
        // User (Petugas) hanya bisa membuat dan melihat laporan
        $userRole->givePermissionTo(['create reports', 'view reports']);

        // 4. Buat Akun Admin Contoh
        $admin = User::updateOrCreate(
            ['email' => 'admin@astekpam.com'],
            [
                'name' => 'Admin Utama',
                'password' => Hash::make('password123'),
            ]
        );
        $admin->assignRole($adminRole);

        // 5. Buat Akun Petugas Contoh
        $petugas = User::updateOrCreate(
            ['email' => 'petugas@astekpam.com'],
            [
                'name' => 'Petugas Jaga',
                'password' => Hash::make('password123'),
            ]
        );
        $petugas->assignRole($userRole);

        $this->command->info('Role dan Permission berhasil disuntikkan ke database!');
    }
}