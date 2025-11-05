<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class LoginSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('login')->insert([
            [
                'username' => 'admin1',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ],
            [
                'username' => 'siswa1',
                'password' => Hash::make('siswa123'),
                'role' => 'siswa',
            ],
            [
                'username' => 'siswa2',
                'password' => Hash::make('siswa123'),
                'role' => 'siswa',
            ],
        ]);
    }
}
