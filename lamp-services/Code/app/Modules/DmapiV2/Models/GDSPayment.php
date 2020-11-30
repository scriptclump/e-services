<?php

namespace App\Modules\DmapiV2\Models;
use App\Modules\DmapiV2\Models\Dmapiv2Model;
use App\Modules\Orders\Models\OrderModel;
use DB;
use Illuminate\Database\Eloquent\Model;
use Cache;

class GDSPayment extends Model {

    public function gdsOrderPayment($data, $gds_orders) {
        try {
                date_default_timezone_set('Asia/Kolkata');
                $dmapiv2Model = new Dmapiv2Model;
                $data = json_decode($data['orderdata']);
                
                $paymentInfo = $data->payment_info[0];

                DB::table('gds_orders_payment')->insert([
                    'gds_order_id' => $gds_orders->getGdsOrderId(),
                    'payment_method_id' => $dmapiv2Model->getPaymentIdFromPaymentMethod($paymentInfo->paymentmethod),
                    'payment_status_id' => $dmapiv2Model->getPaymentStatusIdFromPaymentStatus($paymentInfo->paymentstatus),
                    'currency_id' => $dmapiv2Model->getCurrencyIdFromCurrency($paymentInfo->paymentcurrency),
                    'amount' => $paymentInfo->amount,
                    'transaction_id' => $paymentInfo->transactionId,
                    'payment_date' => $paymentInfo->paymentDate,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
        } catch (ErrorException $e) {
            return $e;
        }
    }
}