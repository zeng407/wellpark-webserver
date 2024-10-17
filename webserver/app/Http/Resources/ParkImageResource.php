<?php

namespace App\Http\Resources;

use App\Models\ParkInformation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParkImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $parkInfo = ParkInformation::where('park_no', $this->park_no)->orderByDesc('update_time')->first();
        return [
            'park_no' => $this->park_no,
            'path' => $this->path,
            'url' => $this->url,
            'captured_at' => $this->captured_at,
            'recognition_result' => (bool) $this->recognition_result,
            'park_information' => $parkInfo ? new ParkInfoResource($parkInfo) : null,
        ];
    }
}
