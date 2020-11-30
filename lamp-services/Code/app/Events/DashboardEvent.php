<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Redis;
use App\Central\Repositories\ReportsRepo;
use DB;
use Cache;

class DashboardEvent extends Event implements ShouldBroadcast
{
    use SerializesModels;
    public $data;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user_id)
    {   
        $reports = new ReportsRepo();
        $fromDate = date('Y-m-d');
        $datetime = new \DateTime('tomorrow');
        $toDate = $datetime->format('Y-m-d');
        $date = date('Y-m-d h:i:s a');

        $response = DB::select("CALL getOrdersDashboardNew(0, 1,'$fromDate','$toDate')");
        $response = json_decode(json_encode($response[0]),true);
        $result = array();
        foreach ($response as $key => $value) {
            //$result[strtolower(preg_replace('', '_', $key))]= $value;
            $result[strtolower(str_replace(' ', '_', $key))]= $value;
        }
        
        Cache::put('dasboard_report'.'0'.'_1_'.$fromDate.'_'.$toDate,$result,2);
        Cache::put('dasboard_report'.'0'.'_1_'.$fromDate.'_'.$toDate.'last_updated',$date,2);      
        $last_updated = $date;

        if($user_id != 0){

           $data =  DB::select("CALL getOrdersDashboardNew($user_id, 1,'$fromDate','$toDate')");
           $responseD = json_decode(json_encode($data[0]),true);
           $resultD = array();
           foreach ($responseD as $key => $value) {
                //$result[strtolower(preg_replace('', '_', $key))]= $value;
                $resultD[strtolower(str_replace(' ', '_', $key))]= $value;
            }
            Cache::put('dasboard_report'.$user_id.'_1_'.$fromDate.'_'.$toDate,$resultD,2);
            Cache::put('dasboard_report'.$user_id.'_1_'.$fromDate.'_'.$toDate.'last_updated',$date,2);

        }
        if(!empty($response))
        {
            $this->data = array('order_details' => $result,'last_updated' => $last_updated);
        }else{
            $this->data = '';
        }          
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['dashboard-channel'];
    }
}
