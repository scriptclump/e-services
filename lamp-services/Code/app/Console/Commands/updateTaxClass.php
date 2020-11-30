<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class updateTaxClass extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updateTaxClass';

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
     * @return mixed
     */
    public function handle()
    {
        $query = DB::table('tax_classes')->get()->all();

        foreach ($query as $value) {

            $tax_class_code = $value->tax_class_code;
            $tax_array = explode('_',$tax_class_code);
            $tally = array();
            $tally["IO_CODE"] = $tax_array[2].' '.$value->tax_class_type.' @'.$value->tax_percentage.'%';
            $tally["SALES_CODE"] = '401101 : Sales @'.$value->tax_percentage.'%';
            $tally["PURCHASE_CODE"] = '501100 : Purchase @'.$value->tax_percentage.'%';
            $tally["RETURN_CODE"] = '401101 : Sales Return @'.$value->tax_percentage.'%';
            $tally = json_encode($tally);

            $tlm_name = $tax_array[2].' '.$value->tax_class_type.' @'.$value->tax_percentage.'%';
            
            DB::table('tax_classes')
            ->where('tax_class_id',$value->tax_class_id)
            ->update(array('tlm_name' => $tlm_name,'tally_reference' => $tally));
        }
    }
}
