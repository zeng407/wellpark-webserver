<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PredictParkInfoResource extends JsonResource
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
            'free_quantity' => $this->free_quantity,
            'free_quantity_big' => $this->free_quantity_big,
            'free_quantity_mot' => $this->free_quantity_mot,
            'free_quantity_dis' => $this->free_quantity_dis,
            'free_quantity_cw' => $this->free_quantity_cw,
            'free_quantity_ecar' => $this->free_quantity_ecar,
            'future_time' => $this->future_time,
        ];
    }
}
