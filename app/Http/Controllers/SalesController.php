<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Jobs\SalesCsvProcess;
use Illuminate\Support\Facades\Bus;


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

            $batchJobs = [];

            foreach ($chunks as $key => $chunk) {

                try {

                    Log::info('Processing chunk', ['chunk' => $key]);

                    // Set Headers
                    array_unshift($chunk, $header);

                    if (count($chunk) < 2) {
                        Log::warning('CSV file too short. Skipping.');
                        continue;
                    }

                    $batchJobs[] = new SalesCsvProcess($chunk, $header);

                } catch (\Throwable $e) {
                    Log::error('Failed to dispatch job for file', [
                        'file' => $file,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
            Bus::batch($batchJobs)->dispatch();
            return $batchJobs;
        } catch (\Throwable $e) {
            Log::critical('Unexpected upload error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    private function isDone($batch) {

        if ($batch->progress() === 100) {
           return true;
        } elseif(!$batch->progress() < 100) {
           return false;
        }

    }

    public function batch()
    {
        $batchId = request('id');

        if (!$batchId) {

            return response()->json(['error' => 'No batch ID provided.'], 400);
        }

        //Finds the batch
        $batch = Bus::findBatch($batchId);

        // if...and
        if (!$batch || $this->isDone($batch)) {
            return 'tapus na ata or di ko mahanap';
        }


        return response()->json([
            'progress ' => $batch->progress(),
            'pendingJobs ' => $batch->pendingJobs,
            'status' => $batch->status,
            'totalJobs' => $batch->totalJobs
        ]);
    }
}
