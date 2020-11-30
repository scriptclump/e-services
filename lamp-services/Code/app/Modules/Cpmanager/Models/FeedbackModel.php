<?php
  namespace App\Modules\Cpmanager\Models;
  use Illuminate\Database\Eloquent\Model;
  use DB;  
  
 
  class FeedbackModel extends Model {
  /*
    * Function name: getCustomerData
    * Description: Used to get feedback reasons based on feedbackgroupid
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 11th nov 2016
    * Modified Date & Reason:
    */
  

  public function getFeedbackReasons($feedback_groupid){

  	try
  	{  
		
		  $result = DB::table('master_lookup as ml')
		  ->select(DB::raw("ml.master_lookup_name as name ,ml.value"))
		  ->where('ml.parent_lookup_id','=',$feedback_groupid)
		  ->get()->all();
  
       return $result;

    }catch (Exception $e)
      {
       
          return Array('status' => "failed", 'message' =>$e->getMessage(), 'data' =>[]);
      } 

 } 
    

      /*
    * Function name: saveFeedbackReasons
    * Description: Used to get save feedback reasons
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 11th nov 2016
    * Modified Date & Reason:
    */
  

  public function saveFeedbackReasons($ff_id,$legal_entity_id,$feedback_groupid,$feedback_id,$comments,$feedback_pic,$feedback_audio)
  {

  	try
  	{  
		
		   $result= DB::table('customer_feedback')->insert([
                                'legal_entity_id' => $legal_entity_id,
                                'feedback_type' => $feedback_id,
                                'feedback_group_type' =>$feedback_groupid,
                                'comments' =>$comments,
                                'picture'=>$feedback_pic,
                                'audio'=>$feedback_audio,
                                'created_by'  =>$ff_id,  
                                'created_at' => date("Y-m-d H:i:s")
                                ]);

        return $result;
        
    }catch (Exception $e)
      {
       
          return Array('status' => "failed", 'message' =>$e->getMessage(), 'data' =>[]);
      } 

 } 

}
  
  ?>