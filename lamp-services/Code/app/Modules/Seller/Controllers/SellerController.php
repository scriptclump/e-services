<?php

namespace App\Modules\Seller\Controllers;
use Session;
use App\Http\Controllers\BaseController;
use View;
use Validator;
use Illuminate\Support\Facades\Input;
use URL;
use Log;
use Redirect;
use \App\models\channels\channels;
use \App\Modules\Seller\Models\Sellers;
use \App\models\Users\Users;
use App\Central\Repositories\CustomerRepo;

class SellerController extends BaseController {

    public function __construct(CustomerRepo $custRepoObj) {
        //$breadCrumbs = array('Dashboard'=>url('/'),'Logistic partners'=>'#');                
        $this->middleware(function ($request, $next) {                
            if (!Session::has('userId')) {
                return Redirect::to('/');
            }
            return $next($request);
        }); 
        $this->custRepoObj = $custRepoObj;
    }

    public function indexAction() {
        try {
            parent::Title('Sellers');
            $breadCrumbs = array('Dashboard' => url('/'), 'Sellers' => '#');
            parent::Breadcrumbs($breadCrumbs);
            
            $channels = new channels();
            $channelInfo = $channels->getChannels();
            return View::make('Seller::index')->with(json_encode($channelInfo));
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function addSellerAction($legalEntityId, $sellerId = null) {
        try {
            parent::Title('Add Seller');
            $breadCrumbs = array('Dashboard' => url('/'), 'Sellers' => url('/seller/index'), 'Add Seller' => '#');
            parent::Breadcrumbs($breadCrumbs);
            $channels = new channels();
            $channelInfo = $channels->getChannels();
            $warehouseList = $channels->getChannelWarehouses($legalEntityId);
            $sellerInfo = [];
            if (!$legalEntityId) {
                $users = new Users();
                $legalEntityId = $users->getLegalEntityId(Session::has('userId'));
            } else {
                if ($sellerId > 0) {
                    $sellersObj = new Sellers();
                    $sellerInfo = $sellersObj->getSellerDetails($sellerId);
                }
            }
            return View::make('Seller::seller')->with(['legal_entity_id' => $legalEntityId, 'channel_info' => $channelInfo, 'warehouse_list' => $warehouseList, 'seller_id' => $sellerId, 'seller_info' => $sellerInfo]);
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }

    public function editSellerChildAction($legalEntityId, $sellerId = null) {
        try {
            parent::Title('Edit Seller');
            $breadCrumbs = array('Dashboard' => url('/'), 'Sellers' => url('/seller/index'), 'Edit Seller' => '#');
            parent::Breadcrumbs($breadCrumbs);

            $channels = new channels();
            $channelInfo = $channels->getChannels();
            $warehouseList = $channels->getChannelWarehouses($legalEntityId);
            $sellerInfo = [];
            if (!$legalEntityId) {
                $users = new Users();
                $legalEntityId = $users->getLegalEntityId(Session::has('userId'));
            } else {
                if ($sellerId > 0) {
                    $sellersObj = new Sellers();
                    $sellerInfo = $sellersObj->getSellerDetails($sellerId);
                }
            }
            return View::make('Seller::edit')->with(['legal_entity_id' => $legalEntityId, 'channel_info' => $channelInfo, 'warehouse_list' => $warehouseList, 'seller_id' => $sellerId, 'seller_info' => $sellerInfo]);
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }

    public function sellerConfig($channelId, $sellerId = null) {
        try {
            $sellerConfigInfo = '';
            if ($channelId > 0) {
                $channel = new sellers();
                $sellerConfigInfo = $channel->sellerConfig($channelId, $sellerId);
            }
            return json_encode($sellerConfigInfo);
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }

    public function saveSellerData() {
        try {
            $input = input::all();
            $validator = Validator::make(
                            array(
                        'channelId' => Input::get('channelId'),
                        'market_place_username' => Input::get('marketplaceusername'),
                        'marketplace_password' => Input::get('password'),
                        'channel_referance_name' => Input::get('channelreferancename'),
                        'description' => Input::get('description')
                            ), array(
                        'channelId' => 'required',
                        'channel_referance_name' => 'required',
                        'description' => 'required',
                        'market_place_username' => 'required',
                        'marketplace_password' => 'required'));
            if ($validator->fails()) {
                $errorMessages = $validator->messages();
                //return json_encode($errorMessages);
                return json_encode('1');
            } else {
                $Saveselelr_data = new sellers();
                $restult = $Saveselelr_data->saveSellerData($input);
                return json_encode($restult);
            }

            // Session::set('chferancename', $input['channelreferancename']);
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }

    public function updateSellerData() {
        try {
            $input = input::all();
            $validator = Validator::make(
                            array(
                        'channelId' => Input::get('channelId'),
                        'market_place_username' => Input::get('marketplaceusername'),
                        'marketplace_password' => Input::get('password'),
                        'channel_referance_name' => Input::get('channelreferancename'),
                        'description' => Input::get('description')
                            ), array(
                        'channelId' => 'required',
                        'channel_referance_name' => 'required',
                        'description' => 'required',
                        'market_place_username' => 'required',
                        'marketplace_password' => 'required'));
            if ($validator->fails()) {
                $errorMessages = $validator->messages();
                //$messageArr = json_decode($messages);
                return json_encode($errorMessages);
            } else {
                if (!empty($input['seller_id']) && !empty($input['legal_entity_id'])) {
                    $updateSelelr = new sellers();
                    $result = $updateSelelr->updateSellerDetials($input);
                    return json_encode($result);
                }
            }
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
    }

    public function channelImage() {
        try {
            $chanelDes = [];
            $channelId = input::all()['channelId'];
            $channelImg = new channels();
            $chanelDes = $channelImg->getChannelImages($channelId);
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
        return (json_encode($chanelDes));
    }

    public function getWarehouses() {
        try {
            $wharehouseList = [];
            $warehouses = new channels();
            $wharehouseList = $warehouses->getChannelWarehouses();
        } catch (\Whoops\Exception\ErrorException $ex) {
            Log::info($ex->getMessage());
            Log::info($ex->getTraceAsString());
        }
        return View::make('Seller::seller')->with(['wharehouse_list' => $wharehouseList]);
    }

    public function showSellerList() {
        try {
            $sellerList = new Sellers();
            $sellerDetails = $sellerList->showSellerList();
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        return json_encode(array('Records' => $sellerDetails));
    }

    public function showChildSellerList() {
        try {
            $data = Input::all();
            $sellerList = new Sellers();
            $sellerDetails = $sellerList->showChildSellerList($data);

            if ($sellerDetails) {
                return json_encode(array('Records' => $sellerDetails));
            } else {
                echo '{"Records":[],"TotalRecordsCount":0}';
                exit;
            }
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
        //return json_encode(array('Records' => $sellerDetails));
    }

    public function editSellerAction($id) {
        try {
            $seller = new Sellers();
            $data = $seller->getSellerData($id);
            $states = $this->custRepoObj->getStates(99);
            return view('Seller::editSeller')->with(['data' => $data, 'states' => $states]);
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function updateSeller($id) {
        try {
            $data = Input::all();
            $seller = new Sellers();
            $result = $seller->updateSeller($data, $id);
            return $result;
        } catch (ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function authenticationKeys() {
        try {
            $input = Input::all();            
            $data = json_encode($input);           
           //$data1 =json_encode(array("channel" => "AZ", "AWS_ACCESS_KEY_ID" => "AKIAJXCNP42IFEV3MCRA", "AWS_SECRET_ACCESS_KEY" => "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", "merchantId" => "ARLKW9KF6FUG5"));            
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, "http://fbempdev.ebutor.com/mpmanage/checkAuthentication");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, "channelData=$data");            
            $result = curl_exec($curl);            
            curl_close($curl);
            
            return ($result);
            
        } catch (ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function legalEntityDelete()
    {  try{
        $input = Input::get();
        $seller = new Sellers();
        $legalEntity = $seller->deleteLegalEnitity($input);
        return $legalEntity;
    } catch (ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
            
    }
}
