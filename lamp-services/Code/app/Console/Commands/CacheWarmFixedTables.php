<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Cache;

class CacheWarmFixedTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CacheWarm {tablename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the cache for warm setup of fixed tables';

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
     * @return mixed
     */
    public function handle()
    {
        $arguments = $this->argument();
        //$tablename = 
        $this->$arguments['tablename']();
    }

    private function master_lookup(){

        // echo Cache::get('master_lookup_'.'108001');
        // exit;
        $query = "select * from master_lookup";
        $data = DB::select($query);
        if(count($data) > 0){

            $data = json_encode($data);
            $data = json_decode($data,true);

            foreach($data as $mas){

                Cache::put('master_lookup_'.$mas['value'], $mas['master_lookup_name'],3600);
            }

        }else{

            $this->error('wrong !!! couldnt find ');
        }
    }
}
