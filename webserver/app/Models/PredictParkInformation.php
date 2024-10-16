<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PredictParkInformation extends Model
{
    use HasFactory;

    protected $table = 'predict_park_informations';

    protected $fillable = [
        'park_no',
        'free_quantity',
        'free_quantity_big',
        'free_quantity_mot',
        'free_quantity_dis',
        'free_quantity_cw',
        'free_quantity_ecar',
        'future_time'
    ];

}
