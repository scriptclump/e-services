<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\Vouchers\Models\Voucher;
use DB;

class UpdateSalesVouchers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */


    /**
     * The console command description.
     *
     * @var string
     */
    protected $signature = 'UpdateSalesVouchers';


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
        /*$obj_vocuhers = new Voucher();
        $this->info("started the migration");
        $response = $obj_vocuhers->saveSalesVoucher();
        $this->info($response." :: Successfully");
        die('Successfully inserted');
        */
       
       $obj_vocuhers = new Voucher();

       $fromDate = '2017-01-03 00:00:00';
       $toDate = '2017-01-31 23:59:59';
       //$invoiceId = '14142';

        $fields = array('invgrid.invoice_code','invgrid.gds_invoice_grid_id', 'invgrid.created_at');
        $query = DB::table('gds_invoice_grid as invgrid')->select($fields);
        //$query->where('invgrid.gds_invoice_grid_id', $invoiceId);
        $query->whereBetween('invgrid.created_at', array($fromDate, $toDate));
        $invoiceArr = $query->get()->all();
        //echo '<pre>';print_r($invoiceArr);die;
        $this->info("Started the migration");
        if(count($invoiceArr)) {
            $slno = 1;
            foreach ($invoiceArr as $invoice) {
                $obj_vocuhers->saveSalesVoucher($invoice->gds_invoice_grid_id, 'Sales Entry New');
                $this->info("SNo: ".$slno." - Voucher Generated of #".$invoice->invoice_code);
                $slno++;         
            }
        }
        else {
            $this->info("No Record Found.");
        }
        $this->info("Successfully Created");
    }
}
