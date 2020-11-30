<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Modules\RoutingAdmin\Controllers\UserGeoTrackerController;

class RoutingDataCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'RoutingDataCron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'For routing cron services';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    
    private $_UserGeoTrackerController;
    public function __construct()
    {
        parent::__construct();
        $this->_UserGeoTrackerController = new UserGeoTrackerController();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $currDate = date('Y-m-d');
        $query = "  SELECT user_id
                        FROM geo_track gt
                        WHERE gt.created_at BETWEEN '$currDate 00:00:00' AND '$currDate 23:59:59'
                        GROUP BY gt.user_id";
        
        $datas = DB::select($query);
        $tempArr = [];
        foreach ($datas as $key => $data) {
            $tempArr['user_id'] = $data->user_id;
            $tempArr['date'] = $currDate;
            $response[$key][] = $this->_UserGeoTrackerController->storeTrackHistoryCron($tempArr['user_id'], $tempArr['date']);
        }

        /**
         * For stoping the the command for execution
         */
        print_r($response); die;
    }
}
