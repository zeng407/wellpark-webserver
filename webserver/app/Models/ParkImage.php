<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParkImage extends Model
{
    use HasFactory;

    protected $table = 'park_images';

    protected $fillable = [
        'park_no',
        'path',
        'url',
        'captured_at',
        'recognition_result'
    ];
}
