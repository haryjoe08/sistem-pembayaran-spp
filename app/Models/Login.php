<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Login extends Authenticatable
{
    use Notifiable;

    protected $table = 'login';   // pakai tabel login
    protected $fillable = ['username', 'password', 'role'];

    public function admin()
    {
        return $this->hasOne(Admin::class, 'login_id');
    }

    public function siswa()
    {
        return $this->hasOne(Siswa::class, 'login_id');
    }
}
