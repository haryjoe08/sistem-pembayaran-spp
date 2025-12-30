<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    use HasFactory;
    protected $table = 'jurusan';

    protected $fillable = [
        'nama',
        'status'
    ];

    // Jika jurusan berelasi dengan kelas
public function siswa()
    {
        return $this->hasMany(Siswa::class, 'jurusan.id');
    }
}
