<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Jobs\SalesCsvProcess;

class SalesController extends Controller
{

    public function index() {

        return view('upload-file');

    }

    public function upload(Request $request)
    {
        if ($request->hasFile('csvfile')) {
            $file = $request->file('csvfile');
            $rows = array_map('str_getcsv', file($file->getRealPath()));

            $header = $rows[0];
            unset($rows[0]);

            $chunks = array_chunk($rows, 100);
            $path = resource_path('temp');
            if (!file_exists($path)) mkdir($path, 0777, true);

            foreach ($chunks as $key => $chunk) {
                // Add header back to each chunk
                array_unshift($chunk, $header);
                $lines = array_map(function ($row) {
                    return implode(',', $row);
                }, $chunk);

                $filename = "chunk_{$key}.csv";
                file_put_contents($path . DIRECTORY_SEPARATOR . $filename, implode("\n", $lines));
            }

            return 'success';
        }

        return 'wengk wengk wala man';
    }

    public function store()
    {
        SalesCsvProcess::dispatch();
        return 'stored';
    }


}
