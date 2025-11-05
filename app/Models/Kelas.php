<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';

    protected $fillable = [
        'kelas',
        'tahun_ajaran_id',
    ];

    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'kelas_id');
    }
}
