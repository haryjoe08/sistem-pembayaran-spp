<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;

    protected $table = 'admin';

    protected $fillable = [
        'login_id',
        'nama',
        'email',
    ];

    public function login()
    {
        return $this->belongsTo(Login::class, 'login_id');
    }
}
