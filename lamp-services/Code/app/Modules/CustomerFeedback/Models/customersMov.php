<?php

namespace App\Modules\CustomerFeedback\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
Class customersMov extends Model{

    public function ecashCreditlimitQuery($makeFinalSql, $orderBy, $page, $pageSize,$userId,$leIDS){
        if($orderBy!=''){
            $orderBy = ' ORDER BY ' . $orderBy;
        }
        $sqlWhrCls = '';
        $countLoop = 0;
        foreach ($makeFinalSql as $value) {
            if( $countLoop==0 ){
                $sqlWhrCls .= ' WHERE ' . $value;
            }elseif(count($makeFinalSql)==$countLoop ){
                $sqlWhrCls .= $value;

            }else{
                $sqlWhrCls .= ' AND ' . $value;
            }
            $countLoop++;
        }
        $act = "'<center><code>','<a data-toggle=\'modal\' onclick =\'editEcashCreditlimit(',ec.ecash_id,')\'/ > <i class=\'fa fa-pencil\'></i> </a>&nbsp;&nbsp;&nbsp;</code></center>'";
        
        $ecash="SELECT * FROM (SELECT ec.ecash_id ,ec.state_id,ec.dc_id,ec.customer_type,ec.creditlimit,ec.minimum_order_value,ec.self_order_mov,
                mp.master_lookup_id,ec.mov_ordercount, mp.mas_cat_id , mp.master_lookup_name, mp.description,le_wh_id,dc_type,lp_wh_name,state,pincode,city,
                zo.zone_id,zo.name AS state_name ,CONCAT('<center><code>','<a data-toggle=\'modal\' onclick =\'editEcashCreditlimit(',ec.ecash_id,')\'/ > <i class=\'fa fa-pencil\'></i> </a>&nbsp;&nbsp;&nbsp;</code></center>') AS `actions`
                    FROM ecash_creditlimit AS ec 
                    JOIN legalentity_warehouses AS lw ON ec.dc_id = lw.le_wh_id
                    JOIN master_lookup AS mp ON ec.customer_type = mp.value 
                    JOIN zone AS zo ON ec.state_id = zo.zone_id               
                    WHERE ec.dc_id IN ($leIDS)GROUP BY ec.ecash_id )AS innrtbl" .$sqlWhrCls.$orderBy;

        $allRecallData = DB::select(DB::raw($ecash));
        $TotalRecordsCount = count($allRecallData);
        if($page!='' && $pageSize!=''){
            $page = $page=='0' ? 0 : (int)$page * (int)$pageSize;
            $allRecallData = array_slice($allRecallData, $page, $pageSize);
        }
        return json_encode(array('results'=>$allRecallData,
        'TotalRecordsCount'=>(int)($TotalRecordsCount))); 
       // return json_encode($query);
    }
    public function editecashCreditLimit($id){
        $ecashTable = DB::table('ecash_creditlimit AS ec')->select(['ec.ecash_id','ec.state_id','ec.dc_id','ec.customer_type','ec.creditlimit','ec.minimum_order_value','ec.mov_ordercount','ec.self_order_mov','mp.master_lookup_id','mp.mas_cat_id','mp.master_lookup_name','mp.description','le_wh_id','dc_type','lp_wh_name','state','pincode','city','zo.zone_id','zo.name AS state_name' ])
            ->leftJoin('legalentity_warehouses AS lw', 'ec.dc_id','=','lw.le_wh_id')
            ->leftJoin('master_lookup AS mp','ec.customer_type','=', 'mp.value')
            ->leftJoin('zone AS zo','ec.state_id','=', 'zo.zone_id')
            ->where('ec.ecash_id','=',$id)
            ->first();
        return $ecashTable;
    }
    public function customerType(){
       $query = DB::table('master_lookup')->select(['value','master_lookup_name'])->where('mas_cat_id','=',3)->get()->all();
       return $query;           
    } 
    public function dcTypesLegalentityWarehouses(){
        $query = DB::table('legalentity_warehouses')->select(['le_wh_id','le_wh_code','lp_wh_name'])
                        ->where('dc_type','=',118001) 
                        ->where('status', '=', 1)
                        ->orderBy('lp_wh_name','ASC')
                        ->get()->all();
        return $query;   
    }
    public function addDCToCustomers($data){
        $query = DB::table('ecash_creditlimit')->select('*')->where(['customer_type'=>$data['cust_mas_id'],
                                                                 'dc_id'=>$data['dcDetail_id'],
                                                                ])->first();
        if(count($query)){
        $self_order_mov = !empty($data['self_order_mov']) ? $data['self_order_mov'] : 2000;
        $minimum_order_value = !empty($data['minimum_order_value']) ? $data['minimum_order_value'] : 1000;
        $mov_ordercount = !empty($data['mov_ordercount']) ? $data['mov_ordercount'] : 0;
        $Credit_Limit = !empty($data['Credit_Limit']) ? $data['Credit_Limit'] : 0;
        $query = DB::table('ecash_creditlimit')->where('ecash_id','=',$query->ecash_id)
                                ->update(
                                      ['state_id'=>$data['add_state_id'],                                      
                                       'creditlimit'=>$Credit_Limit,
                                       'minimum_order_value'=>$minimum_order_value,
                                       'self_order_mov'=>$self_order_mov,
                                       'mov_ordercount'=>$mov_ordercount
                                     ]);
            return 0;
        }
        else{                                                                                                                                  
        $self_order_mov = !empty($data['self_order_mov']) ? $data['self_order_mov'] : 2000;
        $minimum_order_value = !empty($data['minimum_order_value']) ? $data['minimum_order_value'] : 1000;
        $mov_ordercount = !empty($data['mov_ordercount']) ? $data['mov_ordercount'] : 0;
        $Credit_Limit = !empty($data['Credit_Limit']) ? $data['Credit_Limit'] : 0;
        $query = DB::table('ecash_creditlimit')->insert(
                                      ['state_id'=>$data['add_state_id'],
                                       'dc_id'=>$data['dcDetail_id'],
                                       'customer_type'=>$data['cust_mas_id'],
                                       'creditlimit'=>$Credit_Limit,
                                       'minimum_order_value'=>$minimum_order_value,
                                       'self_order_mov'=>$self_order_mov,
                                       'mov_ordercount'=>$mov_ordercount
                                   ]);
            return 1;
           }
        }
        public function ecashCreditLimitID($data){
            $query = DB::table('ecash_creditlimit')->select('*')->where('customer_type','=',$data['Customer_id'])
                                                           ->where('dc_id','=',$data['DCName_id'])->first();
            if (count($query)){
                $self_order_mov = isset($data['self_order_mov']) ? $data['self_order_mov'] : 0;
                $minimum_order_value = isset($data['minimum_order_value']) ? $data['minimum_order_value'] : 0;
                $mov_ordercount = isset($data['mov_ordercount']) ? $data['mov_ordercount'] : 0;
                $Credit_Limit = isset($data['Credit_Limit']) ? $data['Credit_Limit'] : 0;
                $StateName_id = isset($data['StateName_id']) ? $data['StateName_id'] : 0;
                $query = DB::table('ecash_creditlimit')->where('ecash_id','=',$query->ecash_id)
                                ->update(
                                      ['state_id'=>$StateName_id,                                      
                                       'creditlimit'=>$Credit_Limit,
                                       'minimum_order_value'=>$minimum_order_value,
                                       'self_order_mov'=>$self_order_mov,
                                       'mov_ordercount'=>$mov_ordercount
                                     ]);
                return 0;
            }
            else{                                                   
            $query = DB::table('ecash_creditlimit')
                              ->where('ecash_id','=',$data['ecash_id'])
                              ->update(['customer_type' =>$data['Customer_id'],
                                         'state_id'=>$data['StateName_id'],
                                         'dc_id'=>$data['DCName_id'],
                                         'self_order_mov'=>$data['self_order_mov'],
                                         'minimum_order_value'=>$data['minimum_order_value'],
                                         'mov_ordercount'=>$data['mov_ordercount'],
                                     ]);
                  return 1;              
               }                                                    
        }

    public function stateNames(){
        $query = DB::table('zone')->select(['zone_id','country_id','name','code'])->where('country_id','=',99)->get()->all();
        return $query;
    }
}


