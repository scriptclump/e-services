<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Modules\Orders\Models\OrderModel;
use App\Modules\Orders\Models\ReturnModel;

class returnsVoucherPopulate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'returnsVoucherPopulate';

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
        $data = DB::table('gds_returns')
                ->whereDate('created_at', '>=', '2016-08-17')
                ->orderBy('return_grid_id', 'desc')
                //->limit(10)
                ->get()->all();
        $returns = array();
        foreach ($data as $return) {
            
            $returns[$return->return_grid_id][] = json_decode(json_encode($return),true); 
           
        }

        foreach ($returns as $key => $return) {
            $collectionData = array();
            $invoice_id = $_OrderModel->getInvoiceIdFromOrderId($return[0]['gds_order_id']);
            $collectionData['order_id'] = $return[0]['gds_order_id'];
            $collectionData['return_id'] = $return[0]['return_grid_id'];
            $collectionData['reference_num'] = $returns[$key][0]['reference_no'];
            $collectionData['collected_on'] = $returns[$key][0]['created_at'];
            $collectionData['invoice'] = $invoice_id[0]->gds_order_invoice_id;
            $collectionData['invoice_reference'] = $invoice_id[0]->invoice_code;   

            $collectionData['collection_amount'] = 0;

            $t_key = 0;
            $returndata = array();
            foreach ($return as $return_items) {
                    
                if($return_items['qty'] > 0 ){
                    $price = $_OrderModel->getUnitPricesTaxAndWithoutTax($collectionData['order_id'],$return_items['product_id']);
                    $collectionData['collection_amount']+= $price['singleUnitPriceWithtax'] * $return_items['qty'];
                    $returndata[$t_key]['product_id'] = $return_items['product_id'];
                    $returndata[$t_key]['qty'] = $return_items['qty'];
                    $returndata[$t_key]['gds_order_id'] = $collectionData['order_id'];
                    $returndata[$t_key]['tax_details'] = $price;
                    $t_key++;
                }
                

            }
            $returnVouchers = $_ReturnModel->saveReturnsVoucher($returndata,$collectionData);
            //var_dump($returnVouchers);
                  
        }
    }

}
