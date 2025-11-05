<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // 1. buat akun login
        $loginId = DB::table('login')->insertGetId([
            'username' => 'admin',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. buat data diri admin
        DB::table('admin')->insert([
            'login_id' => $loginId,
            'nama' => 'Super Admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
