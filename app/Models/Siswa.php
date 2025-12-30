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
        'tahun_masuk',
        'tgl_lahir',
        'jenis_kelamin',
        'jurusan_id',
        'kelas_id',
        'alamat',
        'wali',
        'kontak',
        'status',
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

    public function tagihan()
    {
        return $this->hasMany(Tagihan::class, 'siswa_nis', 'nis');
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeTidakAktif($query)
    {
        return $query->where('status', '!=', 'aktif');
    }

    // Helper Methods
    public function isAktif()
    {
        return $this->status === 'aktif';
    }

    public function statusBadgeClass()
    {
        return match ($this->status) {
            'aktif' => 'bg-success',
            'tidak_aktif' => 'bg-secondary',
            'lulus' => 'bg-primary',
            'pindah' => 'bg-warning',
            'keluar' => 'bg-danger',
            default => 'bg-secondary'
        };
    }

    public function statusLabel()
    {
        return ucfirst(str_replace('_', ' ', $this->status));
    }
}
