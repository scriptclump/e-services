<?php

namespace App\Modules\Cpmanager\Models;

/*
  Filename : Product.php
  Author : Pratibha Yadav
  CreateData : 12-July-2016
  Desc : Model for product mongo table
 */

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Orderhistory extends Eloquent {

    protected $connection = 'mongo';
    protected $table = 'orderhistory';
    protected $primaryKey = '_id';

     

 public function orderhistory($order_id,$data,$order_status,$order_status_id) {
   
  if($order_status_id == '17001') {

      $data_array= sizeof($data);

            for($j=0;$j<$data_array;$j++)
            {

               $insertedarray[] = array(
                    'order_id' => (int) $order_id,
                    'product_id' =>(int) $data[$j]['product_id'],
                    'order_status_id' => (int)$order_status_id,
                    'quanity' =>(int) $data[$j]['quantity'],
                    'total' =>(int) $data[$j]['prodtotal'],
                    'order_status' => $order_status,
                    'date_added' =>date('Y-m-d H:i:s')
                  );


            }

            $result = $this::insert($insertedarray);
            
   }

       else if($order_status_id == '17009') {

        $data_array= sizeof($data);

            for($j=0;$j<$data_array;$j++)
            {

               $Canceldata[] = array(
                    'order_id' => (int)$order_id,
                    'product_id' => (int)$data[$j]['product_id'],
                    'order_status_id' => (int)$order_status_id,
                    'quanity' => (int)$data[$j]['quantity'],
                    'cancel_reason_id' => (int)$data[$j]['cancel_reason_id'],
                    'order_status' => $order_status,
                    'comments'=>$data[$j]['comments'],
                    'date_added' =>date('Y-m-d H:i:s')
                  );


            }

            $result = $this::insert($Canceldata);  
       
     }
     else if($order_status_id == '17010') {

      $data_array= sizeof($data);

            for($j=0;$j<$data_array;$j++)
            {

               $returndata[] = array(
                    'order_id' => (int)$order_id,
                    'product_id' => (int)$data[$j]['product_id'],
                    'order_status_id' => (int)$order_status_id,
                    'quanity' => (int)$data[$j]['quantity'],
                    'return_reason_id' => (int)$data[$j]['returnreasonid'],
                    'order_status' => $order_status,
                    'comments'=>$data[$j]['comments'],
                    'date_added' =>date('Y-m-d H:i:s')
                  );


            }

            $result = $this::insert($returndata);
       
  }



 }

 


}  
