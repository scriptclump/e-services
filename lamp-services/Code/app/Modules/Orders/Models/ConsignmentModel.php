<?php

namespace App\Modules\Orders\Models;

use Illuminate\Database\Eloquent\Model;

use DB;

class ConsignmentModel extends Model
{

  public function getdocketDetails($data) {
      try {
            
            $selectArray = array('orders.order_code',
                                'orders.gds_order_id',
                                'pcm.container_num as container_number',
                                'pcm.container_barcode as container_id',
                                DB::raw('SUBSTRING_INDEX(pcm.container_barcode, "-",-1) AS barcode'),
                                DB::raw('((SUM(pcm.weight)+IFNULL(cm.weight,0))/1000) AS weight')
                                );  

            $query = DB::table('gds_order_track as track')->select($selectArray);
            $query->join('gds_orders as orders','orders.gds_order_id','=','track.gds_order_id');
            
            $query->leftJoin('picker_container_mapping as pcm','pcm.order_id','=','orders.gds_order_id');
 
            $query->leftJoin('container_master as cm','pcm.container_barcode','=','cm.crate_code');
 
            
            if($data['transfer_type']=='hub') {
                    $query->whereIn('orders.order_status_id', array('17024'));
                    $query->where('track.st_docket_no', $data['docket_no']);

                  } 
            if($data['transfer_type']=='dc') {
                    $query->whereIn('orders.order_status_id', array('17022','17023'));
                    $query->whereIn('orders.order_transit_status', array('17027'));
                    $query->where('track.rt_docket_no', $data['docket_no']);
                  } 

            $query->groupBy('pcm.container_barcode');      
            $query->groupBy('orders.order_code');      
            $query->orderBy('orders.gds_order_id');      
            

            $result = $query->get()->all();
      
          $rowcount = 1;

          $resultArray = array();

          $cfcArray = array();

          foreach($result as $k=>$row) {

              if(!in_array($row->gds_order_id,$cfcArray)) { // insert crate info at the end of every order
                
                $cfc_cnt=$this->getBagsCratesCount($row->gds_order_id,1);
                $bags_cnt=$this->getBagsCratesCount($row->gds_order_id,2);

                if(!empty($cfc_cnt)) {

                    $cfc_cnt[0]->chk = '<input type=checkbox name=container[] order_code="'.$cfc_cnt[0]->order_code.'" container_id="'.$cfc_cnt[0]->container_id.'" weight="'.$cfc_cnt[0]->weight.'" row-count="'.$rowcount.'" class="alldock" value='.$cfc_cnt[0]->gds_order_id.' />';
                    $rowcount++;
                    
                    $resultArray[] = $cfc_cnt[0];
                }

                if(!empty($bags_cnt)) {
                  
                    $bags_cnt[0]->chk = '<input type=checkbox name=container[] order_code="'.$bags_cnt[0]->order_code.'" container_id="'.$bags_cnt[0]->container_id.'" weight="'.$bags_cnt[0]->weight.'" row-count="'.$rowcount.'" class="alldock" value='.$bags_cnt[0]->gds_order_id.' />';
                    $rowcount++;

                    $resultArray[] = $bags_cnt[0];
                }

                $cfcArray[] = $row->gds_order_id;                  
              }  

              $result[$k]->chk = '<input type=checkbox name=container[] order_code="'.$row->order_code.'" container_id="'.$row->container_id.'" weight="'.$row->weight.'" row-count="'.$rowcount.'" class="alldock" value='.$row->gds_order_id.' />';

              $resultArray[] = $result[$k];
              $rowcount++;
          }

          return $resultArray;  
      }
      catch(Exception $e) {
              Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
      }
  }

  public function getOrdersByDockets($Hubs,$Flag) {
      try {
          
        $Hubs = explode(',',$Hubs);  

        $result = DB::table('gds_orders as go')
                ->Join('gds_order_track as got','go.gds_order_id','=','got.gds_order_id')
               ->whereIn('go.hub_id',$Hubs);
        if($Flag==1)
         {
            $result->whereIn("go.order_status_id",[17022,17023])
                  ->select("got.rt_docket_no as st_docket_no")
                  ->where("go.order_transit_status","=",17027)
                  ->GROUPBY('got.rt_docket_no');


         }else{ 

            $result->select("got.st_docket_no")->where("go.order_status_id",'=','17024')               ->GROUPBY('got.st_docket_no');
         }

        return $result->get()->all(); 


      }
      catch(Exception $e) {
              Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
      }
  }

     public function getBagsCratesCount($orderId,$flag){
        try{

         if($flag==1)
         {  
         $result_cnt=DB::table('gds_order_track as got')
         ->select(DB::raw("distinct got.gds_order_code as order_code,got.gds_order_id,
          '' as container_number,CONCAT('BAG(',got.bags_cnt,')') as container_id,
          0 as weight,0 as weight_uom")) 
         ->where('got.gds_order_id',$orderId)
         ->where('got.bags_cnt','>',0)
         ->get()->all();
      
           }else{
             $result_cnt=DB::table('gds_order_track as got')
             ->select(DB::raw("distinct got.gds_order_code as order_code,got.gds_order_id,
          '' as container_number,CONCAT('CFC(',got.cfc_cnt,')') as container_id,
          0 as weight,0 as weight_uom")) 
         ->where('got.gds_order_id',$orderId)
         ->where('got.cfc_cnt','>',0)->get()->all();
           }

               return $result_cnt ;


        } catch (Exception $ex) {
            Log::info($ex->getMessage() . ' => ' . $ex->getTraceAsString());
            return $ex->getMessage();
        }
    }


}