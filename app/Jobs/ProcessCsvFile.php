<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\CsvUpload;
use App\Models\File;
use League\Csv\Reader;

class ProcessCsvFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $fileName;
    /**
     * Create a new job instance.
     * @param string $hashName
     */
    public function __construct($hashName)
    {
        $this->fileName = $hashName;
    }

    /**
     * Execute the job.
     * @return void
     */
    public function handle(): void
    {
        // Retrieve the path to the uploaded CSV file
        $filePath = storage_path('app/public/uploads/' . $this->fileName);
        $fileRecord = File::where('hash_name', $this->fileName)->first();

        // Check if the file exists
        if (Storage::disk('public')->exists('uploads/' . $this->fileName)) {
            $csv = Reader::createFromPath($filePath, 'r'); // Use your preferred CSV library
            $csv->setHeaderOffset(0); // Assume the first row contains headers

            // Iterate through each CSV row
            foreach ($csv as $row) {
                // Check if a record with the same UNIQUE_KEY already exists in the csv_upload table
                $existingRecord = CsvUpload::where('unique_key', $row['UNIQUE_KEY'])->first();
                
                if ($existingRecord) {
                    // Update the existing record with new data
                    $existingRecord->update([
                        'product_title' => mb_convert_encoding($row['PRODUCT_TITLE'], 'UTF-8', 'UTF-8'),
                        'product_description' => mb_convert_encoding($row['PRODUCT_DESCRIPTION'], 'UTF-8', 'UTF-8'),
                        'style' => mb_convert_encoding($row['STYLE#'], 'UTF-8', 'UTF-8'),
                        'sanmar_mainframe_color' => mb_convert_encoding($row['SANMAR_MAINFRAME_COLOR'], 'UTF-8', 'UTF-8'),
                        'size' => mb_convert_encoding($row['SIZE'], 'UTF-8', 'UTF-8'),
                        'color_name' => mb_convert_encoding($row['COLOR_NAME'], 'UTF-8', 'UTF-8'),
                        'piece_price' => mb_convert_encoding($row['PIECE_PRICE'], 'UTF-8', 'UTF-8'),
                        'updated_at' => now()
                    ]);
                } 
                else {
                    // Insert a new record
                    CsvUpload::create([
                        'unique_key' => mb_convert_encoding($row['UNIQUE_KEY'], 'UTF-8', 'UTF-8'),
                        'product_title' => mb_convert_encoding($row['PRODUCT_TITLE'], 'UTF-8', 'UTF-8'),
                        'product_description' => mb_convert_encoding($row['PRODUCT_DESCRIPTION'], 'UTF-8', 'UTF-8'),
                        'style' => mb_convert_encoding($row['STYLE#'], 'UTF-8', 'UTF-8'),
                        'sanmar_mainframe_color' => mb_convert_encoding($row['SANMAR_MAINFRAME_COLOR'], 'UTF-8', 'UTF-8'),
                        'size' => mb_convert_encoding($row['SIZE'], 'UTF-8', 'UTF-8'),
                        'color_name' => mb_convert_encoding($row['COLOR_NAME'], 'UTF-8', 'UTF-8'),
                        'piece_price' => mb_convert_encoding($row['PIECE_PRICE'], 'UTF-8', 'UTF-8'),
                        'created_at' => now()
                    ]);
                }
            }

            if ($fileRecord) {
                $fileRecord->update([
                    'status' => 'completed',
                    'updated_at' => now()
                ]);
            }
        }else{
            Log::error('CSV data not found: ' . $this->fileName);

            if ($fileRecord) {
                $fileRecord->update([
                    'status' => 'failed',
                    'updated_at' => now()
                ]);
            }
            return;
        }

        Log::info('CSV file processed successfully: ' . $this->fileName);
    }
}
