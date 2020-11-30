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

class ProductComments extends Eloquent {

    protected $connection = 'mongo';
    protected $primaryKey = '_id';
    protected $table ="product_comments";
    public function getUserData()
    {

      return $this->hasOne('App\Modules\Product\Models\UserModel', 'user_id', 'created_by');
    
    }
    public function createProductComments($comments,$product_id)
    {
     /*return $this->all();*/
        $result = array();
        $this->product_id = (int)$product_id;
        $this->comments = $comments;
        $this->created_by =  Session::get('userId');
        $this->created_on = date("Y-m-d");
        $this->save();
         $count=0;  
          $data = $this->with(array('getUserData' => function($query)
        {
            $query->select('firstname','lastname','user_id','profile_picture');
        }))->where('_id','=',$this->_id)->where('product_id','=',$product_id)->get(array('comments', (int)'product_id', (int)'created_by', 'created_on'))->all();
        foreach($data as $val)
        {

          $records = $val;//->toArray();

           $records['name'] = $records['get_user_data']['firstname'].' '.$records['get_user_data']['lastname'];
           $records['pic'] = $records['get_user_data']['profile_picture'];
            $records['count']= $count+1;
           $result[] = $records;
        }
        
       return $result;
    }
    public function getProductComments($product_id)
    {
      try{
        $result = array();
        $data = $this->with(array('getUserData' => function($query)
        {
            $query->select('firstname','lastname','user_id','profile_picture');
        }))->where('product_id',(int)$product_id)->get(array('comments', (int)'product_id', (int)'created_by', 'created_on'))->all();
       
       //print_r($data);die();
        $count=0;
        foreach($data as $val)
        {

          $records = $val;//->toArray();

           $records['name'] = $records['get_user_data']['firstname'].' '.$records['get_user_data']['lastname'];
           $records['pic'] = $records['get_user_data']['profile_picture'];
             $records['count']= $count+1;
           $result[] = $records;
         
        }
        return $result;
      }catch(ErrorException $ex) {
        Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        $result=array();
         return $result;
      }
      
       //return $result;

    }


}
