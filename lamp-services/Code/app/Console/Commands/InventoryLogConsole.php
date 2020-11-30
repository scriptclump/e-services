<?php
//@prasenjit
namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Cache;
use Log;

class InventoryLogConsole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventoryLog {method} {data}';

    //data is a json encoded feild and i want it to be in base 64 encoded

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Entry Into the inventory log status';
    protected $methods = array('update','insert');

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
        $data = $arguments['data'];
        $method = $arguments['method'];
        $method = strtolower($method);
        $data_json = base64_decode($data);
        //check available methods
        $available_method = array('insert','insertbulk');

        if(in_array($method, $available_method, true)){

            $method = 'InventoryLog'.$method;
            return  $this->$method($data_json);

        }else{
            $this->error("\n".'Method Not listed in the queue!! check method'."\n");
        }

    }

    /**
     * [InventoryLoginsert description]
     * @param [type] $data [description]
     */
    private function InventoryLoginsert($data){

        $enlisted_columns = array ( 'le_wh_id',
                                    'product_id',
                                    'soh',
                                    'order_qty',
                                    'ref',
                                    'ref_type'
                            );

        $data = json_decode($data,true);
        $data_key = array_keys($data);

        foreach ($enlisted_columns as $value) {
            
            if(!in_array($value, $data_key)){

//                Log::info('Fatal Error : '+$value +'not Available in the data set');
                break;
            }
        }

        foreach ($data as $key => $value) {

            if($value == ''){
                $data[$key] = null;
            }
        }
        //extract()
        //$input = print_r($data);
        //Log::info('Info : Data to be inserted'.$input);
        try{
            DB::table('inv_update_log')->insert($data);
        }catch(\Exception $e){

            Log::info($e->getMessage());

        }       
        
    }

    /**
     * [InventoryLoginsert description]
     * @param [type] $data [description]
     */
    private function InventoryLoginsertbulk($data){

        $data = json_decode($data,true);
        //$input = print_r($data);
        //Log::info('Info : Data to be inserted'.$input);
        foreach ($data as $value) {

            $value = json_encode($value);
            try{

                $this->InventoryLoginsert($value);

            }catch(\Exception $e){
                Log::info($e->getMessage());
            }
            
        }

    }

}
