<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Buat;

class Pejabat extends Model
{
    use HasFactory;

    // Laravel otomatis pakai tabel plural "pejabats"
    protected $fillable = [
        'nama',
        'nip',
        'pangkat',
        'jabatan',
        'masa_bakti',
    ];
}
