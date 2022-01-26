<?php

namespace App\Jobs;

use App\Models\Trade;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class StoreTradeRecords implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $filename;
    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {        
        $start = false;
        do {
            if(File::exists("storage/csv/{$this->filename}")) {
                $start = true;
            }
        } while($start);
        // dd(File::exists("storage/csv/{$this->filename}"));
        $csv_file = fopen("storage/csv/{$this->filename}", "r");
        
        $importData = [];
        $i = 0;
        while (($filedata = fgetcsv($csv_file, 1000, ",")) !== FALSE) {
            $num = count($filedata);
            for ($c = 0; $c < $num; $c++) {
                $importData[$i][] = $filedata[$c];
            }
            $i++;
        }
        
        $start = false;
        foreach($importData as $data) {
            if(!$start && count($data) > 7) {
                $start = true;
                continue;
            }
            if($start && count($data) > 7) {
                Trade::create([
                    'index' => $data[1],
                    'previous_open' => $data[2],
                    'open' => $data[3],
                    'high' => $data[4],
                    'low' => $data[5],
                    'close' => $data[6],
                    'gain_loss' => $data[7]
                ]);
            }
        }
    }
}
