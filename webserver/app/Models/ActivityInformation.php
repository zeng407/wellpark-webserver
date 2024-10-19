<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityInformation extends Model
{
    use HasFactory;

    protected $table = 'activity_informations';

    protected $fillable = [
        'serno',
        'pubunitname',
        'subject',
        'posterdate',
        'subjectclass',
        'administationclass',
        'hostunit',
        'activitysdate',
        'activityedate',
        'activityplace',
        'activitydateremark',
        'voice',
        'detailcontent',
        'recognition_location'
    ];
}
