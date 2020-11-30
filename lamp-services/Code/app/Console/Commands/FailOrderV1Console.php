<?php


/**
 *
 * Created By : Pavan Kumar Anandam
 * date : 4th July 2019
 * Description : Queue to insert failed orders into failed_order table
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
use App\Modules\DmapiV2\Models\Dmapiv2Model;
use App\Lib\Queue;

class FailOrderV1Console extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'FailOrderV1Console {api_name} {data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will be the console front of the Fail Order';

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
    public function handle(){

        $this->info('Fail Order received');
        $arguments = $this->argument();
        $api_name = $arguments['api_name'];
        $data = $arguments['data'];
        $data = base64_decode($data);
        $this->info("Received api_name ".$api_name);
        $this->info("Recieved Data" . $data);
        $this->info("Converting to data array");
        $data = json_decode($data,true);

        $Dmapiv2Model = new Dmapiv2Model();
        $Dmapiv2Model->insertFailedOrder($data);

        return true;      
        
    }

}
