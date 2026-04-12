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
        // 1. Buat beberapa Permission (Hak Akses)
        // Sesuaikan dengan kebutuhan Astekpam nanti
        Permission::create(['name' => 'view reports']);
        Permission::create(['name' => 'create reports']);
        Permission::create(['name' => 'edit reports']);
        Permission::create(['name' => 'delete reports']);
        Permission::create(['name' => 'manage users']);

        // 2. Buat Role dan hubungkan dengan Permission
        $adminRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'user']);

        // Admin dapat semua akses
        $adminRole->givePermissionTo(Permission::all());
        
        // User biasa mungkin hanya bisa lihat dan buat laporan
        $userRole->givePermissionTo(['view reports', 'create reports']);

        // 3. Buat User Admin Pertama (untuk Login)
        $admin = User::create([
            'name' => 'Admin Astekpam',
            'email' => 'admin@astekpam.com',
            'password' => Hash::make('password123'), // Jangan lupa ganti nanti
        ]);

        // Berikan role admin ke user ini
        $admin->assignRole($adminRole);

        $this->command->info('Role dan Permission berhasil dibuat!');
    }
}