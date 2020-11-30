<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Modules\Orders\Models\OrderModel;
use App\Modules\Orders\Models\ReturnModel;

class fixReturnVouchers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixReturnVouchers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'fix Return Vouchers narations';

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
        $_OrderModel = new OrderModel;
        $_ReturnModel = new ReturnModel;
        $vouchers = DB::table('vouchers')
                ->select('voucher_id','voucher_code','naration', 'reference_no', 'ledger_group', 'voucher_date')
                ->where('voucher_type', '=', 'Credit Note')
                ->whereBetween('voucher_date', ['2017-07-01','2017-07-09'] )
                ->get()->all();
        foreach ($vouchers as $key => $voucher) {
            $order_data = $this->getOrderCodeByReturnCode($voucher->voucher_code);
            $text = $voucher->naration;
            $order_code = $order_data['order_code'];
            $order_date =  $order_data['order_date'];
            $invoice_code = $order_data['invoice_code'];
            // TSSO17060020304 dated 2017-07-10 14:25:30 with return no TSSR17070005260 dated 2017-07-13 18:12:42
            if ($voucher->ledger_group == 'Sundry Debtors') {
                $new_str = $order_code.' dated '.$order_date.' with return no '.$voucher->voucher_code.' dated '.$order_data['created_at'];
                $new_line = preg_replace("/TSSO.+$/m", $new_str, $text);            
                echo "[$key]. replacing  >>> $text \n with >>> $new_line".PHP_EOL;
                $this->updateNaration($voucher->voucher_id, $new_line);
                echo "replacing reference_no: $voucher->reference_no with $invoice_code";
                $this->updateReferenceNo($voucher->voucher_id, $invoice_code);
                echo "UPDATED >>>".PHP_EOL;
            }else{
                echo "replacing reference_no: $voucher->reference_no with $invoice_code";
                $this->updateReferenceNo($voucher->voucher_id, $invoice_code);
                echo "UPDATED >>>".PHP_EOL;
            }
            
        }
    }
    public function getOrderCodeByReturnCode($return_code){
        $return_data = [];
        $query = "SELECT 
                     go.order_code,
                     go.order_date,
                     getInvoiceCode(go.gds_order_id) as invoice_code,
                     gr.created_at
                     FROM gds_return_grid gr
                     Left JOIN gds_orders go ON go.gds_order_id = gr.gds_order_id
                     WHERE gr.return_order_code = '$return_code'";
        $data = DB::select($query);
        $return_data = json_decode(json_encode($data[0]), true);
        return $return_data;
    }
    public function updateNaration($voucher_id, $new_naration)
    {
        $query = "UPDATE vouchers SET vouchers.naration = '$new_naration'
                    WHERE vouchers.voucher_id = $voucher_id";
        $data = DB::statement($query);
        return true;
    }
    public function updateReferenceNo($voucher_id, $new_reference)
    {
        $query = "UPDATE vouchers SET vouchers.reference_no = '$new_reference'
                    WHERE vouchers.voucher_id = $voucher_id";
        $data = DB::statement($query);
        return true;   
    }
}
