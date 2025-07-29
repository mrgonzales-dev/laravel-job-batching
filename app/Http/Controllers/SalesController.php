<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Jobs\SalesCsvProcess;
use Illuminate\Support\Facades\File;
use PgSql\Lob;

class SalesController extends Controller
{
    public function index()
    {
        return view('upload-file');
    }

    public function upload(Request $request)
    {
        try {
            if (!$request->hasFile('csvfile')) {
                Log::warning('Upload failed: No file uploaded.');
                return response()->json(['error' => 'No file uploaded.'], 400);
            }

            $file = $request->file('csvfile');

            $rows = array_map(fn($v) => str_getcsv($v, separator: ',', escape: '\\', enclosure: '"'), file($file->getRealPath()));


            if (count($rows) < 2) {
                Log::warning('Upload failed: CSV too short or empty.', ['rows' => $rows]);
                return response()->json(['error' => 'CSV must have at least a header and one data row.'], 422);
            }

            $header = $rows[0];
            unset($rows[0]);

            $chunks = array_chunk($rows, 1000);

            foreach ($chunks as $key => $chunk) {

                try {

                    Log::info('Processing chunk', ['chunk' => $key]);

                    // Set Headers
                    array_unshift($chunk, $header);

                    if (count($chunk) < 2) {
                        Log::warning('CSV file too short. Skipping.');
                        continue;
                    }

                    SalesCsvProcess::dispatch($chunk, $header);

                } catch (\Throwable $e) {
                    Log::error('Failed to dispatch job for file', [
                        'file' => $file,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
            return response()->json(['status' => 'success']);
        } catch (\Throwable $e) {
            Log::critical('Unexpected upload error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }


    // NOTE: Not used anymore
    // public function store()
    // {
    //     try {
    //         $path = resource_path('temp');
    //         $files = glob("$path/*.csv");
    //
    //         if (empty($files)) {
    //             Log::info('No CSV files found in temp path.');
    //             return response()->json(['message' => 'No files to process.'], 200);
    //         }
    //
    //         foreach ($files as $file) {
    //             try {
    //
    //                 $data = array_map(fn($row) => str_getcsv($row, separator: ',', escape: '\\', enclosure: '"'), file($file));
    //
    //
    //                 if (count($data) < 2) {
    //                     Log::warning('CSV file too short. Skipping.', ['file' => $file]);
    //                     unlink($file);
    //                     continue;
    //                 }
    //
    //                 $header = $data[0];
    //                 unset($data[0]);
    //
    //                 SalesCsvProcess::dispatch($data, $header);
    //                 unlink($file);
    //             } catch (\Throwable $e) {
    //                 Log::error('Failed to dispatch job for file', [
    //                     'file' => $file,
    //                     'error' => $e->getMessage(),
    //                 ]);
    //                 // optionally: rename or move file to avoid re-processing
    //             }
    //         }
    //
    //         return response()->json(['status' => 'stored']);
    //     } catch (\Throwable $e) {
    //         Log::critical('Unexpected store error', [
    //             'error' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString(),
    //         ]);
    //         return response()->json(['error' => 'Internal Server Error'], 500);
    //     }
    // }
}
