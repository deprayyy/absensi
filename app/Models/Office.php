<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    use HasFactory;

    // Tambahkan fillable supaya mass assignment diizinkan
    protected $fillable = [
        'name',
        'latitude',
        'longitude',
    ];
}
