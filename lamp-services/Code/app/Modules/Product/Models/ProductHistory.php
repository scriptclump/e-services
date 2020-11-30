<?php

namespace App\Modules\Product\Models;

/*
  Filename : Product.php
  Author : Vijaya Bhaskar Chenna
  CreateData : 31-May-2016
  Desc : Model for product mongo table
 */
use Session;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class ProductHistory extends Eloquent {

    protected $connection = 'mongo';
    protected $primaryKey = '_id';
    protected $table ="user_activity_logs";
    public function getUserData()
    {

      return $this->hasOne('App\Modules\Product\Models\UserModel', 'user_id', 'created_by');
    
    }
    public function productHistoryData($product_id,$pageSize,$page)
    {    
       $result = array();
       $count=0;
       $pageCntData = ($page==1)?1:$pageSize*$page;
       

        $data = $this->where('module','=','Products')->where('Uniquevalue.Product_id','=',$product_id)
        //->where("action",'=','Freebie configuration has been deleted.')
        ->orderby('_id','DESC')
        ->select(['action','Uniquevalue','userDetails','newvalues',(int)'updated_at'])
        ->get()->all();
        
        foreach($data as $val)
        {
          $records = $val;//->toArray();
           $records['name'] = $records['userDetails']['username'];
            $records['prd_data'] = $records['newvalues'];
           $result[] = $records;
        }     

        $result['count'] = count($data);
       return $result;

    }


}
