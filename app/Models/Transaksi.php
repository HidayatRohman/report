<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaksi extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'tanggal',
        'brand',
        'nominal',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'float',
    ];
}
