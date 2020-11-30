<?php

namespace App\Modules\Cpmanager\Models;

/*
  Filename : Product.php
  Author : Pratibha Yadav
  CreateData : 12-July-2016
  Desc : Model for product mongo table
 */

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Review extends Eloquent {

    protected $connection = 'mongo';
    protected $primaryKey = '_id';

     

    public function Review($product_id) {
    
        $mongo_user_id = $this->where('entity_id', $product_id)->get(array('rating'))->all();
      /*Updating in MongoDB
      $product_id= 438;
      $mongo_user_id = $this->where('entity_id', $product_id)->first();
      
      $mongo_user_id->status=(int)6;
      $mongo_user_id->save();*/
        $mongo_review_data = json_decode(json_encode($mongo_user_id, true));
        if(!empty($mongo_review_data)){
          $size = sizeof($mongo_review_data);
         foreach ($mongo_review_data as $ratings) {
          $rating[] = $ratings->rating;
         
        } 
         return  array_sum($rating)/$size;
        }else{
          return 0;
        }
    }

   public function addReviewRating($user_id,$reviewData,$firstname,$lastname){
  $count = $this->where('entity_id', (int)$reviewData['reviews']['entity_id'])->where('rating',(int)$reviewData['reviews']['rating'])->where('user_id',(int)$user_id)->get(array('user_id'))->all();
  $count = json_decode(json_encode($count, true));
  if(empty($count)){$this->user_id = (int)$user_id;
        $this->author = $firstname.' '.$lastname;

        $this->review_type = $reviewData['reviews']['review_type'];
        $this->entity_id = (int)$reviewData['reviews']['entity_id'];
        $this->segment_id = (int)$reviewData['reviews']['segment_id'];
        $this->comment = $reviewData['reviews']['comment'];
        $this->rating = (int)$reviewData['reviews']['rating'];
        $this->status = $reviewData['reviews']['status'];
        $this->date_added = date("Y-m-d H:i:s");
    
        $this->save();

        $reviewData['status'] = 1;

      }else{
        $reviewData['status'] = 0;
      }


  //print_r($reviewData);exit;
        
        

    return $reviewData;

    }

    public function gettopRated($offset,$offset_limit,$segment_id) {
    if(!empty($segment_id)){
      $topRated = $this->where('segment_id', (int)$segment_id)
               ->orderBy('rating',"desc")
               ->skip((int)$offset)
               ->take((int)$offset_limit)
               ->get()->all();
             }else{
             $topRated = $this
               ->orderBy('rating',"desc")
               ->skip((int)$offset)
               ->take((int)$offset_limit)
               ->get()->all();
             }
        
      /*Updating in MongoDB
      $product_id= 438;
      $mongo_user_id = $this->where('entity_id', $product_id)->first();
      
      $mongo_user_id->status=(int)6;
      $mongo_user_id->save();*/
        $mongo_review_data = json_decode(json_encode($topRated, true));
        return $mongo_review_data;
    }


      /*
    * Function Name: RatingRange()
    * Description: Used to get min and max values for rating
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 14 July 2016
    * Modified Date & Reason:
    */


 public function RatingRange($product_id) {


        $mongo_minrating= $this->whereIn('entity_id',[(int)$product_id])->min('rating');

        $mongo_minrat_data = json_decode(json_encode($mongo_minrating), true);

        $mongo_maxrating = $this->whereIn('entity_id',[(int)$product_id])->max('rating');
        $mongo_maxrat_data = json_decode(json_encode($mongo_minrating), true);

        $data[]= $mongo_minrat_data;
        $data[]= $mongo_maxrat_data;


      //print_r($mongo_review_data);exit;
        return $data;
    }

/*
* Function Name: ProductRating()
* Description: Used to get average rating of product
* Author: Ebutor <info@ebutor.com>
* Copyright: ebutor 2016
* Version: v1.0
* Created Date: 15th July 2016
* Modified Date & Reason:
*/    


public function ProductRating($product_id) {

      $prod = $this->where('entity_id', $product_id)->get()->all();     
      $data = json_decode(json_encode($prod, true));


      if(empty($data))  {

         $avgrating =0;
        }
        else
        {
          $totrating=0;

          $i=0;
          foreach($data as $p) {
          $totrating+=$p->rating;
          $i++;
          $avgrating = $totrating/$i;
        }
        
        
        } 
                
        return $avgrating;

    }


    public function getReviews($product_id){
      
      $status = 1;
      $prod = $this->where('entity_id',(int)$product_id)->where('status',(int)$status)->get(array('user_id','comment','rating','author'))->all();  
     
      $data = json_decode(json_encode($prod), true);  
     
      return $data;
    }


       /*
    * Function Name: FilterRatingProducts()
    * Description: Used to get products between min and max rating 
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 3 August 2016
    * Modified Date & Reason:
    */

        public function FilterRatingProducts($minvalue,$maxvalue) {

  
      $products=$this->select('entity_id')->where(function($query) use($minvalue,$maxvalue){

        $query->where("rating BETWEEN $min and $max");
       
      })->distinct()->get()->all();
   
      $mongo_minrat_data = json_decode(json_encode($products), true);

      $data =array();     
        $i=0;

      foreach ( $mongo_minrat_data as $key => $value) 
      {
     
       $mongo_avgrating= $this->where('entity_id',(int)$value[0])->avg('rating');

       $mongo_data = json_decode(json_encode($mongo_avgrating), true);

       $data[$i][$value[0]]=$mongo_data;

      $i++;

      }


      $result=array();

      foreach ($data as $key => $value) {

      foreach ($value as $key => $values) {

      if($values <= $min && $max>=$values)

      {

      $result[]=$key;

      }

      }
      }


      $last_result = json_decode(json_encode($result), true);
   
      return $last_result ;


    
  }

}