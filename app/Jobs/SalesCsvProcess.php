<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Sales;
use Illuminate\Support\Facades\Log;

class SalesCsvProcess implements ShouldQueue
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
        $path = resource_path('temp');
        $files = glob("$path/*.csv");

        foreach ($files as $file) {
            $data = array_map('str_getcsv', file($file));
            $header = $data[0];
            unset($data[0]);

            foreach ($data as $row) {
                $row = array_slice($row, 0, count($header));
                if (count($row) !== count($header)) {
                    Log::warning('Bad row in CSV', ['row' => $row]);
                    continue;
                }

                $saleData = array_combine($header, $row);
                Sales::create($saleData);
            }

            unlink($file);
        }
    }
}
