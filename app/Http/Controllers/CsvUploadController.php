<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CsvUpload;
use App\Models\File;
use App\Jobs\ProcessCsvFile; 
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use Illuminate\Support\Facades\Redis;

class CsvUploadController extends Controller
{
    // Method to display the file upload form
    public function showUploadForm()
    {
        return view('upload'); // Load the 'upload.blade.php' view
    }

    public function upload(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');
        $file_name = $file->getClientOriginalName();
        $hash_name = $file->hashName();

        // Store the uploaded file
        $path = $file->store('public/uploads'); // You can specify your storage path

        // Check if the file with the same unique key already exists
        $existingUpload = File::where('hash_name', $hash_name)->first();

        if (!$existingUpload) {
            
            $upload = new File();
            $upload->name = $file_name;
            $upload->hash_name = $hash_name;
            $upload->status = 'pending'; 
            $upload->created_at = now(); 
            $upload->updated_at = now(); 

            // Save the record to the database
            $upload->save();
        }

        // Queue a job to process the file (background processing)
        ProcessCsvFile::dispatch($hash_name)->onQueue('csv_processing');

        return redirect()->back()->with('success', 'File uploaded successfully. Processing started.');
    }

    // Method to display the list of recent uploads
    public function listUploads()
    {
        $files = File::all(); // Replace with your query to retrieve files from the "files" table
        return response()->json($files);
    }

    public function testRedisConnection()
    {
        try {
            Redis::ping();
            return 'Redis is working.';
        } catch (\Exception $e) {
            return 'Redis is not working. Error: ' . $e->getMessage();
        }
    }

}
