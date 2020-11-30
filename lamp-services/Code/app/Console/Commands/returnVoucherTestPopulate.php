<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Modules\Orders\Models\OrderModel;
use App\Modules\Orders\Models\ReturnModel;
use Cache;

class returnVoucherTestPopulate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'returnVoucherTestPopulate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update returns vouchers all';

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

        $_OrderModel = new OrderModel;
        $_ReturnModel = new ReturnModel;
        $data = DB::table('gds_return_grid')
                ->whereDate('created_at', '>=', '2017-07-14')
                ->orderBy('return_grid_id', 'desc')
                ->get()->all();
        $returns = json_decode(json_encode($data),true);
        foreach ($returns as $return) {

            $collectionData = array();
            $invoice_id = $_OrderModel->getInvoiceIdFromOrderId($return['gds_order_id']);
            $collectionData['order_id'] = $return['gds_order_id'];
            $collectionData['return_id'] = $return['return_grid_id'];
            $collectionData['reference_num'] = $return['return_order_code'];
            $collectionData['collected_on'] = $return['created_at'];
            $collectionData['invoice'] = $invoice_id[0]->gds_order_invoice_id;
            $collectionData['invoice_reference'] = $invoice_id[0]->invoice_code;
            $collectionData['gst']['cgst_total'] = $return['cgst_total'];
            $collectionData['gst']['sgst_total'] = $return['sgst_total'];
            $collectionData['gst']['igst_total'] = $return['igst_total'];
            $collectionData['gst']['utgst_total'] = $return['utgst_total'];



            $collectionData['collection_amount'] = $this->getReturnValueFromReturnId($return['return_grid_id']);
            $t_key = 0;
            var_dump($collectionData);
            $_ReturnModel->saveReturnsVoucherGST(null,$collectionData);


           
        }

        //var_dump($returns);
        

    }

    public function getReturnValueFromReturnId($return_id){

        $query = "select sum(total) as return_total from gds_returns where return_grid_id = $return_id";
        $data = DB::select($query);
        $data = json_decode(json_encode($data),true);
        return $data[0]['return_total'];

    }

}
