<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityInformationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'serno' => $this->serno,
            'pubunitname' => $this->pubunitname,
            'subject' => $this->subject,
            'posterdate' => $this->posterdate,
            'subjectclass' => $this->subjectclass,
            'administationclass' => $this->administationclass,
            'hostunit' => $this->hostunit,
            'activitysdate' => $this->activitysdate,
            'activityedate' => $this->activityedate,
            'activityplace' => $this->activityplace,
            'activitydateremark' => $this->activitydateremark,
            'voice' => $this->voice,
            'detailcontent' => $this->detailcontent,
            'recognition_location' => $this->recognition_location,
        ];
    }
}
