<?php

namespace App\Modules\Inbound\Models;

/*
  Filename : InboundRequest.php
  Author : Ebutor
  CreateData : 20-May-2016
  Desc : Model for inboun request table
 */

use Illuminate\Database\Eloquent\Model;
use App\Modules\Inbound\Models\SellerAccount;
use Illuminate\Support\Facades\Session;
use DB;

class InboundRequest extends Model {

    protected $primaryKey = "inbound_request_id";
    
    /*
     * @param there is no parameters for this function
     * 
     * 
     * This function will joins the tables by using Eloquents(inbound_requests & inbound_product)
     * 
     * @return the joing condition
     */

    public function inboundProductDetails() {
        return $this->hasMany('App\Modules\Inbound\Models\InboundProduct', 'inbound_request_id', 'inbound_request_id');
    }

    public function createInboundRequest($requestDetails) {
        $this->client_id = Session::get('legal_entity_id');
        $this->inbound_request_type = $requestDetails['request_type'];
        $this->request_status = $requestDetails['request_status'];
        $this->created_by = Session::get('legal_entity_id');
        $this->scheduling_id = $requestDetails['time_slots'];
        $this->wh_id = $requestDetails['delivery_location'];
        $this->stn = $requestDetails['stn_number'];
        $this->save();

        return $this->inbound_request_id;
    }
    
    /*
     * @param $inbounRequestId is the inwardrequest Id
     * 
     * This function will gives the total available quantity for particular inward request
     * 
     * @return the count of the total available quantity for a particular inward Request 
     */


    public function getTotalQuantity($inbounRequestId) {
        $total = 0;
        $result = $this->with(array('inboundProductDetails' => function($query) {
                        $query->select(array('inbound_request_id', 'product_quantity'));
                    }))->where('inbound_request_id', $inbounRequestId)->get(array('inbound_request_id'))->all();
                $result_arr = json_decode($result, true);
                
                foreach ($result_arr[0]['inbound_product_details'] as $product) {
                    $i[] = $product['product_quantity'];
                    $total = array_sum($i);
                }
                return $total;
            }
            
            
    /*
     * @param $filterarray is the filter parameters(it may be empty), $orderbyarray is the order by details(it may be empty),
     * $status wont come empty 
     * 
     * This function will gives all the filtered data about the grid in the inbound index page
     * 
     * @return the filtered data 
     */

            public function getStatuses($filterarray = '', $orderbyarray = '', $page = 1, $pageSize = 1, $status, $legalentityId) {
                $sellerAccount = new SellerAccount();
                $getAllSellerIds = $sellerAccount->getAllSellerIds($legalentityId);

                $query = $this;
                if ($status == 'Pending') {
                    $query = $query->where('request_status', '!=', 'CMP');
                    $query = $query->where('request_status', '!=', 'CAN');
                } else if ($status == 'Completed') {
                    $query = $query->where('request_status', 'CMP');
                } else if ($status == 'Cancelled') {
                    $query = $query->where('request_status', 'CAN');
                }

                if (!empty($orderbyarray)) {
                    $orderClause = explode(" ", $orderbyarray);
                    $query = $query->orderby($orderClause[0], $orderClause[1]);  //order by query 
                }
                if (!empty($filterarray)) {
                    foreach ($filterarray as $filterData) {
                        $data = explode(' ', $filterData);
                        if (strpos($data[2], 'DateTime') !== false) {
                            $data[2] = str_replace(array('DateTime', "'"), "", $data[2]);
                            //echo $data[0]." || ".$data[1]." || ".$data[2];
                            $query = $query->whereDate($data[0], $data[1], $data[2]);
                        } else {
                            $query = $query->where($data[0], $data[1], $data[2]);
                        }
                    }
                }

//               if(!empty($getAllSellerIds))
//                {
//                    $sellerIDs = array();
//                    foreach ($getAllSellerIds as $key) {
//                        
//                         $sellerIDs[] = $key['seller_id'];
//                    }
//                    $query = $query->whereIn('seller_id',$sellerIDs);
//
//                }
                if($legalentityId!=0)
                {
                  $query = $query->where('client_id',$legalentityId);
                }
                $count = $query->count();
                $result = array();
                $result['count'] = $count;
                $query = $query->skip($page * $pageSize)->take($pageSize);
                //$qqq = $query->toSql();
                $result['result'] = $query->get()->all();
                //dd($qqq);
                return $result;
            }
            
            
    /*
     * @param $inwardId is the Inward request Id 
     * 
     * 
     * This function will gives the all details about the inward request along with the product details which were related to the particular inward request
     * 
     * @return the all the data about the particular inward request 
     */

            public function inwardRequestDetails($inwardId) {

                $result = $this->with('inboundProductDetails')->where('inbound_request_id', $inwardId)->get()->all();
                return $result;
            }


            public function getAllRecordsCount($LegalentityId)
            {
                $getAllSellerIds    = $sellerAccount->getAllSellerIds($LegalentityId);
                if(!empty($getAllSellerIds))
                {
                    $sellerIDs = array();
                    foreach ($getAllSellerIds as $key) {
                        
                         $sellerIDs[] = $key['seller_id'];
                    }
                    //$query = $query->whereIn('seller_id',$sellerIDs);

                }
                $query              = $query->whereIn('seller_id',$sellerIDs)->get()->count();
                return $query;
            }



            public function getAllPendingRecordsCount($LegalentityId)
            {$getAllSellerIds           = $sellerAccount->getAllSellerIds($LegalentityId);
                if(!empty($getAllSellerIds))
                {
                    $sellerIDs = array();
                    foreach ($getAllSellerIds as $key) {
                        
                         $sellerIDs[] = $key['seller_id'];
                    }
                    $query              = $query->whereIn('seller_id',$sellerIDs);

                }
                $query = $query->where('request_status', '!=', 'CMP')->where('request_status', '!=', 'CAN')->get()->count();
                return $query;
            }

            public function getAllCompletedRecordsCount($LegalentityId)
            {
                $getAllSellerIds                = $sellerAccount->getAllSellerIds($LegalentityId);
                if(!empty($getAllSellerIds))
                {
                    $sellerIDs = array();
                    foreach ($getAllSellerIds as $key) {
                        
                         $sellerIDs[] = $key['seller_id'];
                    }
                    $query = $query->whereIn('seller_id',$sellerIDs);

                }
                $query                          = $query->where('request_status', 'CMP')->get()->count();
                return $query;
            }


            public function getAllCancelledRecordsCount($LegalentityId)
            {
                $getAllSellerIds                    = $sellerAccount->getAllSellerIds($LegalentityId);
                if(!empty($getAllSellerIds))
                {
                    $sellerIDs = array();
                    foreach ($getAllSellerIds as $key) {
                        
                         $sellerIDs[] = $key['seller_id'];
                    }
                    $query = $query->whereIn('seller_id',$sellerIDs);

                }
                $query                              = $query->where('request_status', 'CAN')->get()->count();
                return $query;
            }

            public function getAllCountHere($LegalentityId)
            {
//                $sellerIDs = array();
//                $sellerAccount = new SellerAccount();
//                $getAllSellerIds = $sellerAccount->getAllSellerIds($LegalentityId);
//
//                if(!empty($getAllSellerIds))
//                {
//                    
//                    foreach ($getAllSellerIds as $key) {
//                        
//                         $sellerIDs[] = $key['seller_id'];
//                    }
//
//                }
              if($LegalentityId == 0)
              {
                $query  = $this->select(DB::raw('count(*) as count,request_status'))->groupBy('request_status')->pluck( 'count', 'request_status')->all();
              }
              else
              {
                $query  = $this->select(DB::raw('count(*) as count,request_status'))->where('client_id',$LegalentityId)->groupBy('request_status')->pluck('count', 'request_status')->all(); 
              }
                 
                 return json_decode($query,true);
            }



        }
        