<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParkInfoResource extends JsonResource
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
            'parking_name' => $this->parking_name,
            'address' => $this->address,
            'business_hours' => $this->business_hours,
            'weekdays' => $this->weekdays,
            'holiday' => $this->holiday,
            'free_quantity_big' => $this->free_quantity_big,
            'total_quantity_big' => $this->total_quantity_big,
            'free_quantity' => $this->free_quantity,
            'total_quantity' => $this->total_quantity,
            'free_quantity_mot' => $this->free_quantity_mot,
            'total_quantity_mot' => $this->total_quantity_mot,
            'free_quantity_dis' => $this->free_quantity_dis,
            'total_quantity_dis' => $this->total_quantity_dis,
            'free_quantity_cw' => $this->free_quantity_cw,
            'total_quantity_cw' => $this->total_quantity_cw,
            'free_quantity_ecar' => $this->free_quantity_ecar,
            'total_quantity_ecar' => $this->total_quantity_ecar,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'update_time' => $this->update_time,
        ];
    }
}
