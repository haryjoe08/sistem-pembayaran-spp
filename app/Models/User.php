<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'login'; // pakai tabel login
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'username',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
    ];

        // Relasi ke Admin
    public function admin()
    {
        return $this->hasOne(Admin::class, 'login_id');
    }

    // Relasi ke Siswa
    public function siswa()
    {
        return $this->hasOne(Siswa::class, 'login_id');
    }
}
