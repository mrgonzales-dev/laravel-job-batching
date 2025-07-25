<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Sales;
use PDO;

class SalesController extends Controller
{

    public function index() {

        return view('upload-file');

    }

    public function upload(Request $request) {
        if(request()->has('csvfile')) {

            // $data = array_map('str_getcsv', file(request()->csvfile));
            $data = file(request()->csvfile);
            // $header = $data[0];
            // unset($data[0]);


            // Chunking file
            $chunks = array_chunk($data, 100);

            // Convert 1000 records into a new csv file

            foreach ($chunks as $key => $chunk) {

                $name = "/tmp{$key}.csv";
                $path = resource_path('temp');

                file_put_contents($path . $name, implode("\n", $chunk));
            }

            return 'success';
        }
            return 'wengk wengk wala man';
    }


    public function store() {

        $path = resource_path('temp');
        $files = glob("$path/*.csv");

        $header = [];

        foreach ($files as $key => $file) {
        $data = array_map('str_getcsv', file($file));

            if ($key === 0 ) {
                $header = $data[0];
                unset($data[0]);

            }

            foreach ($data as $sale) {

                $saleData = array_combine($header, $sale);
                Sales::create($saleData);
            }

        }
        return $files;
    }


}
