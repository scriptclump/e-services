<?php
namespace App\Modules\Cpmanager\Models;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Cpmanager\Views\order;
use App\Central\Repositories\CustomerRepo;
use DB;
use Log;
use views;
use view;
use Config;
use Cache;



class ReOrderModel extends Model
{

 public function reOrderings($array) {

 try{

    $result=DB::table('gds_orders AS go ')->select(DB::raw("distinct gop.product_id"))->Join('gds_order_products as gop','go.gds_order_id','=','gop.gds_order_id')->Join('products as prod','prod.product_id','=','gop.product_id')
                ->where('go.cust_le_id', '=',$array['legal_entity_id'])->where('prod.product_id', '!=',69009)->where('prod.is_sellable', '=', 1)->where('prod.cp_enabled', '=',1)
                ->where(db::raw('GetCPInventoryByProductId(prod.product_id,'.$array['le_wh_id'].')'),'>',0)
               ->orderBy('gop.created_at', 'DESC');

      $tempCount = clone $result;
      $total = $tempCount->get()->all();
      $total = count($total);

    $result=  $result->skip($array['offset'])->take($array['offset_limit'])->get()->all();

   if(count($result)>0)
   {
         foreach ($result as $key => $value) 
          {
	      $product_id[]=$value->product_id;
           }
       $data['product_id']=implode(',',$product_id);
       $data['count']=$total;
   }else{
       $data=[];

   }
    return $data;

    } catch(Exception $e) {
       Log::info($e->getMessage());
        Log::info($e->getTraceAsString());
        }    
    
  }
 
}