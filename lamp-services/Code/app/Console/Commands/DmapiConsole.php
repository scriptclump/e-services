<?php


/**
 *
 * Created By : Prasenjit CHowdhury
 * date : 5th August
 * Description : Single base controll to move to queue can be used for every other movement to queue
 *                For any carification contact prasenji.chowdhury@ebutor.com / jisionpc@gmail.com 
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Central\Repositories\RoleRepo;
use App\Central\Repositories\OrderRepo;
use App\Central\Repositories\CustomerRepo;
use App\Central\Repositories\MasterApiRepo;
use App\models\Mongo\MongoDmapiModel;

use DB;
use App\Http\Controllers\DmapiController;
use App\Modules\DmapiV2\Controllers\Dmapiv2Controller;
use App\Lib\Queue;

class DmapiConsole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dmapi {api_name} {data} {token}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will be the console front of the dmapi controller';

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
        $api_name = $arguments['api_name'];
        $data = $arguments['data'];
        $token = $arguments['token'];
        $roleAccess = new RoleRepo();
        $custRepo = new CustomerRepo();
        $ApiAccess = new MasterApiRepo();
        $orderRepo = new OrderRepo();
        $data = base64_decode($data);
        $this->info("Received api_name ".$api_name);
        // $this->info("Recieved Data" . $data);
        // $this->info("recieved Token" .$token);
        $this->info("Converting to data array");
        $data = json_decode($data,true);
        $DmapiController = new DmapiController($roleAccess,$custRepo,$ApiAccess,$orderRepo);
        $response = $DmapiController->dmapiConsoleRequest($api_name,$data,$token);
        $MongoDmapiModel = new MongoDmapiModel();
        $MongoDmapiModel->updateResponse($token,$response);
        $this->info('Order Updated Have to handle it later echo only'); 
        return true;
        
        
    }

}
