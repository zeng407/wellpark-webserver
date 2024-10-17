<?php

namespace App\Http\Resources;

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
        return [
            'park_no' => $this->park_no,
            'path' => $this->path,
            'url' => $this->url,
            'captured_at' => $this->captured_at,
            'recognition_result' => (bool) $this->recognition_result
        ];
    }
}
