<?php

namespace App\Jobs;

use App\Models\ActivityInformation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class CreateActivityInformations implements ShouldQueue, ShouldBeUnique
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
        $files = Storage::files('activityinfo');

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
                    ActivityInformation::updateOrCreate([
                        'serno' => $info['serno']
                    ],[
                        'serno' => $info['serno'],
                        'pubunitname' => $info['pubunitname'],
                        'subject' => $info['subject'],
                        'posterdate' => $info['posterdate'],
                        'subjectclass' => $info['subjectclass'],
                        'administationclass' => $info['administationclass'],
                        'hostunit' => $info['hostunit'],
                        'activitysdate' => $info['activitysdate'],
                        'activityedate' => $info['activityedate'],
                        'activityplace' => $info['activityplace'],
                        'activitydateremark' => $info['activitydateremark'],
                        'voice' => $info['voice'],
                        'detailcontent' => $info['detailcontent'],
                    ]);
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
