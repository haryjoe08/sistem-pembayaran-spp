<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    use HasFactory;

    protected $table = 'tagihan';

    protected $fillable = [
        'siswa_nis',
        'jenis_tagihan_id',
        'kelas_id',
        'tahun_ajaran_id',
        'total_tagihan',
        'sudah_dibayar',
        'status',
        'jatuh_tempo',
    ];

    // Tambahkan cast untuk tanggal
    protected $casts = [
        'jatuh_tempo' => 'date',
        'total_tagihan' => 'decimal:2',
        'sudah_dibayar' => 'decimal:2',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_nis', 'nis');
    }

    public function jenisTagihan()
    {
        return $this->belongsTo(JenisPembayaran::class, 'jenis_tagihan_id');
    }
    public function tarifTagihan()
    {
        return $this->belongsTo(TarifTagihan::class);
    }
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }
    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'tagihan_id');
    }

    // Helper method untuk cek jatuh tempo
    public function isJatuhTempo()
    {
        return $this->jatuh_tempo && $this->jatuh_tempo <= now() && $this->status != 'lunas';
    }

    // Helper method untuk cek mendekati jatuh tempo (7 hari)
    public function isMendekatJatuhTempo()
    {
        return $this->jatuh_tempo
            && $this->jatuh_tempo <= now()->addDays(7)
            && $this->jatuh_tempo > now()
            && $this->status != 'lunas';
    }
    // Di Model Tagihan
    public function paymentOrders()
    {
        return $this->hasMany(PaymentOrder::class);
    }
}
