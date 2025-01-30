<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Novels extends Model
{
    use HasFactory;

    protected $table = 'novels'; 
    protected $fillable = [
        'nama_novel',
        'foto_sampul',
        'deskripsi',
        'rating_novel'
    ];

}