<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Santri extends Model
{
    use HasFactory;
  protected $table = 'santri';
protected $fillable = [
    'nama',
    'tgl_lahir',
    'kelas',
    'alamat',
    'nama_orangtua',
];

}
