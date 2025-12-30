<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisPembayaran extends Model
{
    use HasFactory;
    protected $table = 'jenis_tagihan';
    protected $fillable = [
        'nama',
        'nominal',
        'tipe',
        'status',
        'deskripsi',
    ];


    // Relasi ke tarif per tahun ajaran
    public function tarifPerTahun()
    {
        return $this->hasMany(TarifTagihan::class);
    }

    // Get tarif untuk tahun ajaran tertentu
    public function getTarifByTahunAjaran($tahunAjaranId)
    {
        return $this->tarifPerTahun()
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->first();
    }

    // Get tarif aktif (tahun ajaran yang sedang aktif)
    public function getTarifAktif()
    {
        $tahunAktif = TahunAjaran::where('status', 'aktif')->first();
        if (!$tahunAktif) return null;

        return $this->getTarifByTahunAjaran($tahunAktif->id);
    }
}
