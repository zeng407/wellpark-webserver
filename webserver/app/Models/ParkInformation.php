<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParkInformation extends Model
{
    use HasFactory;

    protected $table = 'park_informations';

    protected $fillable = [
        'park_no',
        'parking_name',
        'address',
        'business_hours',
        'weekdays',
        'holiday',
        'free_quantity_big',
        'total_quantity_big',
        'free_quantity',
        'total_quantity',
        'free_quantity_mot',
        'total_quantity_mot',
        'free_quantity_dis',
        'total_quantity_dis',
        'free_quantity_cw',
        'total_quantity_cw',
        'free_quantity_ecar',
        'total_quantity_ecar',
        'longitude',
        'latitude',
        'update_time',
    ];
}
