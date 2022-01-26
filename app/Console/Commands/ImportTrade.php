<?php

namespace App\Console\Commands;

use App\Jobs\StoreTradeRecords;
use App\Models\Trade;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ImportTrade extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trade:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('started');
        $context = stream_context_create(
            array(
                "http" => array(
                    "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36"
                )
            )
        );
        
        $time = time();
        $filename = "trade-{$time}.csv";
        $file = file_get_contents("https://www1.nseindia.com/archives/equities/mkt/MA241221.csv", false, $context);
        
        // $csv_file = fopen("storage/csv/{$filename}", "r");
        
        $importData = [];
        // $i = 0;
        // while (($filedata = fgetcsv($csv_file, 1000, ",")) !== FALSE) {
        //     $num = count($filedata);
        //     for ($c = 0; $c < $num; $c++) {
        //         $importData[$i][] = $filedata[$c];
        //     }
        //     $i++;
        // }
        $csv_array = explode(',',$file);
        $importData = [];
        $i = 0;
        foreach($csv_array as $data) {
            $importData[$i][] = $data;
            if(strstr($data, "\n")) {
                $i++;
            }
        }
        
        $start = false;
        foreach($importData as $data) {
            if(!$start && count($data) > 6) {
                $start = true;
                continue;
            }
            if($start && count($data) > 6) {
                Trade::create([
                    'index' => $data[0],
                    'previous_open' => $data[1],
                    'open' => $data[2],
                    'high' => $data[3],
                    'low' => $data[4],
                    'close' => $data[5],
                    'gain_loss' => $data[6]
                ]);
            }
        }
        // Storage::disk('public')->put("csv/{$filename}", $file);

        // StoreTradeRecords::dispatch($filename);
        
        $this->info('ended');
        // return 0;
    }
}
