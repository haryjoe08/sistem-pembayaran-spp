<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TarifTagihan extends Model
{
    use HasFactory;

    protected $table = 'tarif_tagihan';

    protected $fillable = [
        'jenis_tagihan_id',
        'tahun_ajaran_id',
        'nominal',
        'status',
        'keterangan'
    ];

    protected $casts = [
        'nominal' => 'decimal:2'
    ];

    // Relasi ke jenis pembayaran
    public function jenisTagihan()
    {
        return $this->belongsTo(JenisPembayaran::class);
    }

    // Relasi ke tahun ajaran
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    // Format nominal ke rupiah
    public function getNominalFormatAttribute()
    {
        return 'Rp ' . number_format($this->nominal, 0, ',', '.');
    }

    // Get tarif by jenis & tahun ajaran (static method)
    public static function getTarif($jenisPembayaranId, $tahunAjaranId)
    {
        return self::where('jenis_tagihan_id', $jenisPembayaranId)
                   ->where('tahun_ajaran_id', $tahunAjaranId)
                   ->first();
    }
}