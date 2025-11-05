<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswa';
    protected $primaryKey = 'nis'; // Primary Key pakai nis
    public $incrementing = false;  // karena nis bukan auto-increment
    protected $keyType = 'int';

    protected $fillable = [
        'nis',
        'login_id',
        'nama',
        'tgl_lahir',
        'jenis_kelamin',
        'jurusan_id',
        'kelas_id',
        'tahun_ajaran_id',
        'alamat',
        'wali',
        'kontak',
    ];

    public function login()
    {
        return $this->belongsTo(Login::class, 'login_id');
    }


    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }
    public function tagihan()
{
    return $this->hasMany(Tagihan::class, 'siswa_nis', 'nis');
}
}
