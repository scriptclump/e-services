<?php
namespace App\Http\Controllers;
use App\Central\Repositories\RoleRepo;
use App\Central\Repositories\OrderRepo;
use App\Central\Repositories\CustomerRepo;
use App\Central\Repositories\MasterApiRepo;
use App\Central\Repositories\MarketPlaceRepo;
use App\Central\Repositories\ApiRepo;
use DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;

class MarketPlaceController extends BaseController {

    protected $roleAccess;
    protected $custRepo;
    protected $apiAccess;
    protected $apiObj;
    protected $marketRepo;
    protected $masterRepo;

    public function __construct(RoleRepo $roleAccess, CustomerRepo $custRepo, MasterApiRepo $apiAccess, ApiRepo $apiObj, MarketPlaceRepo $marketRepo,MasterApiRepo $masterRepo) {
        $this->roleAccess = $roleAccess;
        $this->custRepo = $custRepo;
        $this->apiAccess = $apiAccess;
        $this->apiObj = $apiObj;
        $this->marketRepo = $marketRepo;
        $this->masterRepo = $masterRepo;
    }

    public function commonRoute($api_name) {
        try {
            $status = 0;
            $data = Input::get();

            $result = $this->$api_name($data);
            return $result;
        } catch (Exception $e) {
            $message = $e->getMessage();
        }
        return json_encode(['Status' => 0, 'Message' => 'Server:' . $message]);
    }

    public function getGdsProducts() {
        return $this->marketRepo->getGdsProducts(Input::get());
    }

    public function getUpdateItem() {
        return $this->marketRepo->getUpdateItem(Input::get());
    }

    public function getChannelUrl() {
        return $this->marketRepo->getChannelUrl(Input::get());
    }

    public function updateChannelProduct() {
        return $this->marketRepo->updateChannelProduct(Input::get());
    }

    public function addChannelProduct() {
        return $this->marketRepo->addChannelProduct(Input::get());
    }

    public function sendMail() {
        try {

            $present_date = date('Y-m-d 17-00-00');
            $previous_date = date('Y-m-d 17-00-00', strtotime(' -1 day'));
            $sub = 'Factail Orders';
            $arr = array();

            $result = DB::table('gds_orders as orders')
                    ->join('gds_order_products as prod', 'prod.gds_order_id', '=', 'orders.gds_order_id')
                    ->join('eseal_customer as cust', 'cust.customer_id', '=', 'orders.manufacturer_id')
                    ->groupBy('prod.pid')
                    ->whereIn('orders.order_status_id', [17002])
                    ->whereNotNull('prod.pname')
                    ->where('orders.order_date', '>', $previous_date)
                    ->where('orders.order_date', '<', $present_date)
                    ->get([DB::raw('sum(prod.qty) as quantity'), 'prod.pname', 'cust.brand_name', 'cust.email'])->all();
            if (empty($result))
            {
//                Log::info('There are no new orders to send mail');
                return 'No latest orders to send mail.';
            }
            foreach ($result as $orderDetails) {
                $productInformation = array();
                $brand_name = $orderDetails->brand_name;
                $email = $orderDetails->email;

                foreach ($result as $res) 
                {
                    if ($res->brand_name == $brand_name) 
                    {
                        $productInformation[] = ['product_name' => $res->pname, 'order_count' => $res->quantity];
                    }
                }

                $status = Mail::send('order_mail', ['mailArray' => $productInformation, 
                    'from_date' => $previous_date, 
                    'to_date' => $present_date, 
                    'brand_name' => $brand_name], 
                        function($message) use ($sub, $email) {
                            $message->to($email)->subject($sub);
                        });

                if (!$status1) {
                    $data[] = $brand_name;
                }

                $status = 1;
                $message = 'A Order Update mail has been successfully sent to the manufacturers.';
            }
        } catch (Exception $e) {
            $status = 0;
            $message = $e->getMessage();
        }
        //\Log::info(['Status' => $status, 'Message' => $message, 'Data' => $arr]);
        return json_encode(['Status' => $status, 'Message' => $message, 'Data' => $arr]);
    }

    public function getUpdatedImage() {
        return $this->marketRepo->getUpdatedImage(Input::get());
    }
    
    public function cancelOrder(){
        return $this->marketRepo->cancelOrder(Input::get());
    }

    public function getUpdatedQty(){
        return $this->marketRepo->getUpdatedQty(Input::get());   
    }
    
    public function getUpdatedOrder(){
        return $this->marketRepo->getUpdatedOrder(Input::get());
    }

    public function pullReturnOrders(){
        return $this->marketRepo->pullReturnOrders(Input::get());   
    }

    public function UpdateReturnStatus(){
        return $this->marketRepo->UpdateReturnStatus(Input::get());      
    }

    public function UpdateReturnOrder(){
        return $this->marketRepo->UpdateReturnOrder(Input::get());         
    }

    public function pushReturnStatus(){
        return $this->marketRepo->pushReturnStatus(Input::get());            
    }

    public function pullRefundOrders(){
        return $this->marketRepo->pullRefundOrders(Input::get());               
    }

    public function UpdateRefundStatus(){
       return $this->marketRepo->UpdateRefundStatus(Input::get());                  
    }

    public function UpdateRefundOrder(){
       return $this->marketRepo->UpdateRefundOrder(Input::get());                     
    }

    public function pullCancellations(){
       return $this->marketRepo->pullCancellations(Input::get());                     
    }

    public function UpdateCancelOrder(){
       return $this->marketRepo->UpdateCancelOrder(Input::get());                        
    }

    public function getReturnData(){
       return $this->marketRepo->getReturnData(Input::get());                        
    }   

    public function saveShippingData(){
       return $this->marketRepo->saveShippingData(Input::get()); 
    }
}
