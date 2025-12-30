<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class KepsekSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        // kepsek login
        $kepsekId = DB::table('login')->insertGetId([
            'username' => 'kepsek',
            'password' => Hash::make('password123'),
            'role' => 'kepsek',
            'created_at' => now(),    
            'updated_at' => now(),
        ]);

        // kepsek data diri
        DB::table('admin')->insert([
            'login_id' => $kepsekId,
            'nama' => 'Kepala Sekolah',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
