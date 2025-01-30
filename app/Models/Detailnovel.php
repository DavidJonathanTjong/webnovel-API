<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detailnovel extends Model
{
    use HasFactory;

    protected $table = 'detailnovel';
    protected $fillable = [
        'id_novel',
        'chapter_novel',
        'text_novel'
    ];

}