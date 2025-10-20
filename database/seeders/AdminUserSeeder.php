<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Cek apakah akun admin sudah ada berdasarkan email/nama yang unik
        $adminEmail = 'admin@wifi.id';
        
        if (DB::table('users')->where('email', $adminEmail)->exists()) {
            $this->command->info('Akun Admin sudah ada, lewati seeder.');
            return;
        }

        DB::table('users')->insert([
            'name' => 'Admin Utama',
            'email' => $adminEmail,
            'email_verified_at' => now(),
            // Password: 'password'
            'password' => Hash::make('password'), 
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
            // Kolom kustom untuk membedakan admin (optional, tapi disarankan)
            // 'is_admin' => true, 
        ]);

        $this->command->info('Akun Admin berhasil ditambahkan: Email: admin@wifi.id, Password: password');
    }
}