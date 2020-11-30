<?php
  namespace App\Modules\Cpmanager\Controllers;
  
  use Illuminate\Support\Facades\Input;
  use Session;
  use Response;
  use Log;
  use URL;
  use DB;
  use PDF;
  use Lang;
  use Config;
  use Illuminate\Http\Request;
  use App\Http\Controllers\BaseController;
  use App\Modules\Cpmanager\Models\CategoryModel;
  use App\Modules\Cpmanager\Models\FeedbackModel;
  use App\Modules\Cpmanager\Models\RegistrationModel;
  use App\Central\Repositories\ProductRepo;

  
  
 class FeedbackController extends BaseController {
    
    public function __construct() 
    {  
      
      $this->categoryModel = new CategoryModel();    
      $this->_feedback = new FeedbackModel();
      $this->_register = new RegistrationModel(); 
      $this->repo = new ProductRepo();         
     }


    /*
    * Function name: getFeedbackReasons
    * Description: Used to get feedback reasons based on feedbackgroupid
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 11th nov 2016
    * Modified Date & Reason:
    */
  
  public function getFeedbackReasons() {
      
    try{

       if (isset($_POST['data'])) 
       { 
        $data = $_POST['data'];                   
        $array = json_decode($data); 

       if(isset($array->sales_token) && $array->sales_token!='') {
       if(isset($array->feedback_groupid) && $array->feedback_groupid!='') { 

          $valToken = $this->categoryModel->checkCustomerToken($array->sales_token);

          if($valToken>0) { 

             $feedback_reasons=$this->_feedback->getFeedbackReasons($array->feedback_groupid);

             if(!empty($feedback_reasons))
             {

            return json_encode(Array('status'=>'success','message'=>"getFeedbackReasons",'data'=>$feedback_reasons));
            }else{
             
            return json_encode(Array('status'=>'success','message'=>"No data",'data'=>[]));

            } 

            }else{
             return json_encode(Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => []));    
          }
           
           }else{
             return json_encode(Array('status' => 'failed', 'message' =>'feedback_groupid is not sent', 'data' => []));    
          }

        } else {
             return json_encode(Array('status' => 'failed', 'message' =>'Sales token is not sent', 'data' => []));
       }
        }
       else
       {
         return json_encode(Array(
        'status' => "failed",
        'message' => "No data",
        'data' => []
      ));
      
      }

    }catch (Exception $e)
      {
       
          return Array('status' => "failed", 'message' =>"Internal server error", 'data' =>[]);
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
  
  
public function saveFeedbackReasons() {
      
    try{
      
            
       if (isset($_POST['data'])) 
       { 
        $data = $_POST['data'];                   
        $array = json_decode($data); 

      //  $bytes = strlen(file_get_contents('php://input'));
       $flag= (isset($array->flag) && $array->flag!='')? $array->flag:'';

        if(isset($_FILES['feedback_pic'])){

            if(!empty($_FILES['feedback_pic']['name']))
            {

              $allowed = array("image/jpeg", "image/png","image/gif","image/jpg");
              if(!in_array(strtolower($_FILES['feedback_pic']['type']), $allowed)) {
                 $res['message']="Please upload image jpeg/png/gif";
                 $data['status']="failed";
                 $data['data']="";
                 $final=json_encode($data);
                 print_r($final);die;

            }

            }
    
          //$doc=$_FILES['feedback_pic'];

          $feedback_pic_move = $array->legal_entity_id."_".date("Y-m-d-H-i-s")."_". $_FILES['feedback_pic']['name'];
          $feedback_pic="uploads/feedback/picture/".$feedback_pic_move;

          move_uploaded_file($_FILES['feedback_pic']['tmp_name'], $feedback_pic);

          $feedback_pic=$this->repo->uploadToS3($feedback_pic,'feedback',2);

          }
          else
          {
           
            $feedback_pic ='';

          }

           if(isset($_FILES['feedback_audio'])){

          //$doc=$_FILES['feedback_pic'];

          $feedback_audio_move = $array->legal_entity_id."_".date("Y-m-d-H-i-s")."_". $_FILES['feedback_audio']['name'];
          $feedback_audio="uploads/feedback/audio/".$feedback_audio_move;

          move_uploaded_file($_FILES['feedback_audio']['tmp_name'], $feedback_audio);
          $feedback_audio=$this->repo->uploadToS3($feedback_audio,'feedback',2);
          
          }
          else
          {
           
            $feedback_audio ='';

          }

      $comments= (isset($array->comments) && $array->comments!='')? $array->comments:'';
          

       if($flag==1)
       {
            $feedback_groupid= (isset($array->feedback_groupid) && $array->feedback_groupid!='')? $array->feedback_groupid:'';
            $feedback_id= (isset($array->feedback_id) && $array->feedback_id!='')? $array->feedback_id:'';
            $legal_entity_id= (isset($array->legal_entity_id) && $array->legal_entity_id!='')? $array->legal_entity_id:'';
            $sales_token= (isset($array->sales_token) && $array->sales_token!='')? $array->sales_token:'';
            $latitude= (isset($array->latitude) && $array->latitude!='')? $array->latitude:'';
            $longitude= (isset($array->longitude) && $array->longitude!='')? $array->longitude:'';
            $user_id= (isset($array->user_id) && $array->user_id!='')? $array->user_id:'';
            $activity= (isset($array->activity) && $array->activity!='')? $array->activity:'';
            
            $valToken = $this->categoryModel->checkCustomerToken($array->sales_token);

           if($valToken>0) { 
            
             $ff_id= $this->categoryModel->getUserId($array->sales_token); 
                

             if(!empty($feedback_groupid))   
             {      
             $feedback_reasons=$this->_feedback->saveFeedbackReasons($ff_id[0]->user_id
              ,$legal_entity_id,$feedback_groupid,$feedback_id,
              $comments,$feedback_pic,$feedback_audio);
             }

            $data=$this->_register->InsertNewFfComments( $sales_token,$user_id,$activity,$latitude,$longitude);

             if(!empty($data))
             {

            return json_encode(Array('status'=>'success','message'=>"Saved Succesfully",'data'=>$data));
            }else{
             
            return json_encode(Array('status'=>'success','message'=>"Not Saved",'data'=>[]));

            } 

            }else{
             return json_encode(Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => []));    
          }




       }else{

       if(isset($array->sales_token) && $array->sales_token!='') {
       if(isset($array->feedback_groupid) && $array->feedback_groupid!='') { 
       if(isset($array->feedback_id) && $array->feedback_id!='') { 
       if(isset($array->legal_entity_id) && $array->legal_entity_id!='') { 

          $valToken = $this->categoryModel->checkCustomerToken($array->sales_token);

          if($valToken>0) { 
            
             $ff_id= $this->categoryModel->getUserId($array->sales_token); 
                      
             $feedback_reasons=$this->_feedback->saveFeedbackReasons($ff_id[0]->user_id
              ,$array->legal_entity_id,$array->feedback_groupid,$array->feedback_id,
              $comments,$feedback_pic,$feedback_audio);

             if(!empty($feedback_reasons))
             {

            return json_encode(Array('status'=>'success','message'=>"Saved Succesfully",'data'=>$feedback_reasons));
            }else{
             
            return json_encode(Array('status'=>'success','message'=>"Not Saved",'data'=>[]));

            } 

            }else{
             return json_encode(Array('status' => 'session', 'message' =>'Your Session Has Expired. Please Login Again.', 'data' => []));    
          }
           }else{
             return json_encode(Array('status' => 'failed', 'message' =>'legal_entity_id is not sent', 'data' => []));    
           }
            }else{
             return json_encode(Array('status' => 'failed', 'message' =>'feedback_id is not sent', 'data' => []));    
           }
           }else{
             return json_encode(Array('status' => 'failed', 'message' =>'feedback_groupid is not sent', 'data' => []));    
          }

        } else {
             return json_encode(Array('status' => 'failed', 'message' =>'Sales token is not sent', 'data' => []));
       }

     }


        }
       else
       {
         return json_encode(Array(
        'status' => "failed",
        'message' => "No data",
        'data' => []
      ));
      
      }

    }catch (Exception $e)
      {
       
          return Array('status' => "failed", 'message' =>"Internal server error", 'data' =>[]);
      } 

  }
  

}
