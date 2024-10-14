<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Jobs\CreateParkInfomations;

class CreateParkInfomationsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the CreateParkInfomations job.
     *
     * @return void
     */
    public function testCreateParkInfomations()
    {
        // Mock the Storage facade
        Storage::fake();

        // Create a fake JSON file in the storage
        $jsonData = json_encode([
            'PARKNO' => '004',
            'PARKINGNAME' => '府後地下停車場',
            'ADDRESS' => '新竹市北區府後街42號',
            'BUSINESSHOURS' => '24H',
            'WEEKDAYS' => '汽車：20元/H',
            'HOLIDAY' => '汽車：20元/H\r\n  充電設備資訊：\r\n  AC交流慢充(Type1(J1772))',
            'FREEQUANTITYBIG' => 0,
            'TOTALQUANTITYBIG' => 0,
            'FREEQUANTITY' => 148,
            'TOTALQUANTITY' => 292,
            'FREEQUANTITYMOT' => 0,
            'TOTALQUANTITYMOT' => 0,
            'FREEQUANTITYDIS' => 0,
            'TOTALQUANTITYDIS' => 6,
            'FREEQUANTITYCW' => 0,
            'TOTALQUANTITYCW' => 0,
            'FREEQUANTITYECAR' => 0,
            'TOTALQUANTITYECAR' => 2,
            'LONGITUDE' => '120.969783',
            'LATITUDE' => '24.80726',
            'UPDATETIME' => '2024-10-13T10:02:11.083'
        ]);

        Storage::put(storage_path('parkinfo/test.json'), $jsonData);

        // Dispatch the job
        $job = new CreateParkInfomations();
        $job->handle();

        // Assert the data was inserted into the database
        $this->assertDatabaseHas('park_informations', [
            'park_no' => '004',
            'parking_name' => '府後地下停車場',
            'address' => '新竹市北區府後街42號',
            'business_hours' => '24H',
            'weekdays' => '汽車：20元/H',
            'holiday' => '汽車：20元/H\r\n  充電設備資訊：\r\n  AC交流慢充(Type1(J1772))',
            'free_quantity_big' => 0,
            'total_quantity_big' => 0,
            'free_quantity' => 148,
            'total_quantity' => 292,
            'free_quantity_mot' => 0,
            'total_quantity_mot' => 0,
            'free_quantity_dis' => 0,
            'total_quantity_dis' => 6,
            'free_quantity_cw' => 0,
            'total_quantity_cw' => 0,
            'free_quantity_ecar' => 0,
            'total_quantity_ecar' => 2,
            'longitude' => '120.969783',
            'latitude' => '24.80726',
            'update_time' => '2024-10-13T10:02:11.083'
        ]);
    }
}
