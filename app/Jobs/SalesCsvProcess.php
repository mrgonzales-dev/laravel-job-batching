<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Sales;
use Illuminate\Support\Facades\Log;

class SalesCsvProcess implements ShouldQueue
{
    use Queueable;


    public  $data;
    public  $header;
    /**
     * Create a new job instance.
     */
    public function __construct($data, $header)
    {
        $this->data = $data;
        $this->header = $header;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->data as $row) {
            try {
                // Skip header-like rows
                if (array_map('strtolower', $row) === array_map('strtolower', $this->header)) {
                    continue;
                }

                $row = array_slice($row, 0, count($this->header));

                if (count($row) !== count($this->header)) {
                    Log::warning('Malformed row in CSV', ['row' => $row]);
                    continue;
                }

                $saleData = array_combine($this->header, $row);

                Sales::create($saleData);
            } catch (\Throwable $e) {
                Log::error('Row insert failed', [
                    'row' => $row,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
