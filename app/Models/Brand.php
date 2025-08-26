<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_brand',
        'pemilik',
        'deskripsi',
    ];

    /**
     * Get the transaksi for the brand.
     */
    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'brand', 'nama_brand');
    }
}
