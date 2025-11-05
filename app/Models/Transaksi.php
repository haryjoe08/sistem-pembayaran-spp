<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $fillable = [
        'tagihan_id',
        'tanggal',
        'siswa_nis',
        'jumlah_bayar',
        'metode',
        'keterangan',
    ];

    // Tambahkan cast untuk tanggal
    protected $casts = [
        'tanggal' => 'datetime',
        'jumlah_bayar' => 'decimal:2',
    ];

    /**
     * Relasi ke Tagihan
     */
    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class, 'tagihan_id');
    }

    /**
     * Relasi ke Siswa
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_nis', 'nis');
    }
}