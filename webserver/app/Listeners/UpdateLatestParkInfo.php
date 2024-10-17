<?php

namespace App\Listeners;

use App\Events\ParkInfoCreated;
use App\Models\LatestParkInformation;
use App\Models\ParkInformation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Carbon;

class UpdateLatestParkInfo
{
    /**
     * Handle the event.
     */
    public function handle(ParkInfoCreated $event): void
    {
        logger('UpdateLatestParkInfo: ' . $event->parkInformation->park_no);
        $latestInfo = LatestParkInformation::where('park_no', $event->parkInformation->park_no)->first();

        if (!$latestInfo) {
            LatestParkInformation::create([
                'park_no' => $event->parkInformation->park_no,
                'park_information_id' => $event->parkInformation->id,
                'update_time' => $event->parkInformation->update_time,
            ]);

        } elseif ((new Carbon($latestInfo->update_time))->lt($event->parkInformation->update_time)) {
            $latestInfo->update([
                'park_information_id' => $event->parkInformation->id,
                'update_time' => $event->parkInformation->update_time,
            ]);
        }
    }
}
