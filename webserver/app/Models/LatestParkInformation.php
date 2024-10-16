<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LatestParkInformation extends Model
{
    use HasFactory;

    protected $table = 'latest_park_informations';

    protected $fillable = [
        'park_no',
        'update_time',
        'park_information_id',
    ];

    public function park_information()
    {
        return $this->belongsTo(ParkInformation::class);
    }
}
