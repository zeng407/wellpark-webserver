<?php

namespace App\Jobs;

use App\Events\ParkInfoCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ParkInformation;
use App\Models\LatestParkInformation;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldBeUnique;


class CreateParkInfomations implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Get all files in the directory
        $files = Storage::files('parkinfo');

        foreach ($files as $file) {
            try {
                // Log the file being processed
                Log::info("Processing file: $file");

                // Read the file content
                $content = Storage::get($file);

                // Decode JSON content
                $data = json_decode($content, true);

                foreach($data as $info){
                    // Insert info into the infobase
                    $now = now();
                    $parkInfo = ParkInformation::create([
                        'park_no' => $info['PARKNO'],
                        'parking_name' => $info['PARKINGNAME'],
                        'address' => $info['ADDRESS'],
                        'business_hours' => $info['BUSINESSHOURS'],
                        'weekdays' => $info['WEEKDAYS'],
                        'holiday' => $info['HOLIDAY'],
                        'free_quantity_big' => $info['FREEQUANTITYBIG'],
                        'total_quantity_big' => $info['TOTALQUANTITYBIG'],
                        'free_quantity' => $info['FREEQUANTITY'],
                        'total_quantity' => $info['TOTALQUANTITY'],
                        'free_quantity_mot' => $info['FREEQUANTITYMOT'],
                        'total_quantity_mot' => $info['TOTALQUANTITYMOT'],
                        'free_quantity_dis' => $info['FREEQUANTITYDIS'],
                        'total_quantity_dis' => $info['TOTALQUANTITYDIS'],
                        'free_quantity_cw' => $info['FREEQUANTITYCW'],
                        'total_quantity_cw' => $info['TOTALQUANTITYCW'],
                        'free_quantity_ecar' => $info['FREEQUANTITYECAR'],
                        'total_quantity_ecar' => $info['TOTALQUANTITYECAR'],
                        'longitude' => $info['LONGITUDE'],
                        'latitude' => $info['LATITUDE'],
                        'update_time' => $info['UPDATETIME'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    ParkInfoCreated::dispatch($parkInfo);
                }

                // Log successful insertion
                Log::info("Successfully inserted info from file: $file");

                // remove the file
                Storage::delete($file);

            } catch (\Exception $e) {
                // Log any errors
                Log::error("Error processing file: $file. Error: " . $e->getMessage());
            }
        }
    }
}
