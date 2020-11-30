<?php

namespace App\Central\Repositories;     //Name space define 

/*
 * This is class is used for access role permision based on user and feature
 */

use Token;
use User;
use DB;  //Include laravel db class
use Session;
use App\Modules\Product\Models\ProductModel;
use App\Modules\Product\Models\ProductRelations;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use File;
use App\Modules\Reports\Models\FfReportModel;
use Illuminate\Support\Facades\Log;
use App\Lib\Queue;
use App\Modules\Attendance\Models\AttendanceMongoModel;
class ProductRepo {    // Define class name is RoleRepo

    public function __construct() {
        
    }

    function generateSKUcode() {
        /**
          Bit Note on part of developer by 2038 the system for microtime will break
          as the time used now is dependent on 32 bit time generation as time is calculated
          from epoch (0:00:00 January 1,1970 GMT)
          So the code should be efficient enough to accomodate the 64 bit systems
         * */
        date_default_timezone_set('UTC');

        //generate a microtime with extended decimal micro second
        $microTime = microTime(true);
        $parts = explode('.', $microTime);


        /*
          What basically we are doing is shifting the Base 10 -decimal to base 32 encode [numeric]
          we will explode it up to accomaodate a bigger space of alphanumberic numbers
         */
        //string base_convert ( string $number , int $frombase , int $tobase );
        $string1 = base_convert($parts[0], 10, 36);

        $string2 = base_convert($parts[1], 10, 36);

        $string = strtoupper($string1 . $string2);

        //adding up the second part of the micro second to string
        return $string;
    }
    
    /**
     * get image of entity 
     * @param type $image
     * @param type $path
     * @return string
     */
    public function getImageUrl($image,$path)
    {
        if ($image != '')
        {
                if(strstr($image, 'http')){
                        $imageUrl = $image;
                }
                else
                {
                        $imageUrl = $path.$image;
                }	
        }
        else
        {
                $imageUrl = '/uploads/brand_logos/notfound.png';
        }
        return $imageUrl;
    }
    
    public function updateParentRelations($prevParentId, $currentParentId)
    {   
        //echo $prevParentId.'--'.$currentParentId; die;
        try{
        $relations = $this->getRelations($prevParentId);
        ProductRelations::where('parent_id',$prevParentId)->delete();
        $user = Session::get('userId');
        foreach ($relations as $val)
        {
            if($val == $currentParentId)
            {
                continue;
            }
            $pro = new ProductRelations();
            $pro->product_id = $val;
            $pro->parent_id = $currentParentId;
            $pro->created_by = $user;
            $pro->save();
        }                
        ProductModel::where('product_id',$prevParentId)->update(['is_parent'=>0]);
        ProductModel::where('product_id',$currentParentId)->update(['is_parent'=>1]);
        return 1;
            
        } catch (Exception $ex) {
        return 0; 
        }
    }
    
    public function getRelations($parent_id)
    {
        $childs = ProductRelations::where('parent_id',$parent_id)->pluck('product_id');
        $childArray = $childs->toArray();
        $childArray[] = $parent_id;
    
        return $childArray;
    }

    public function getApprovalHistory($table,$field,$id) {
        $history=DB::table('appr_workflow_history as hs')
                        ->join($table.' as supp', 'supp.'.$field,'=','hs.awf_for_id')
                        ->join('users as us','us.user_id','=','hs.user_id')
                        ->join('user_roles as ur','ur.user_id','=','hs.user_id')
                        ->join('roles as rl','rl.role_id','=','ur.role_id')
                        ->join('master_lookup as ml','ml.value','=','hs.status_to_id')
                        ->select('us.profile_picture','us.firstname','us.lastname',DB::raw('group_concat(rl.name) as name'),'hs.created_at','hs.status_to_id','hs.status_from_id','hs.awf_comment','ml.master_lookup_name')
                        ->where('hs.awf_for_id',$id)
                        //->groupBy('ur.user_id')   
                        ->groupBy('hs.created_at')    
                        ->get()->all();
        return json_decode(json_encode($history),true);
    }
	
	public function uploadToS3($imgObj,$EntityType,$type,$mimeType=null)
	{
		switch($type)
		{
			case 1:			
			$imageFileName = time() . '.' . $imgObj->getClientOriginalExtension();	
			break;	
			   
			case 2:
			$image = new \Symfony\Component\HttpFoundation\File\File($imgObj);
			$extension = File::extension($image);
			$imageFileName = time() . '.' . $extension;	
			break;
		}
		$folder = Config::get('filesystems.disks.s3.'.$EntityType);
		$bucket = Config::get('filesystems.disks.s3.bucket');
		$s3 = \Storage::disk('s3');
		$filePath = $folder.'/' . $imageFileName;
		$s3->put($filePath, file_get_contents($imgObj),'public');
		//$url = 'https://s3.ap-south-1.amazonaws.com/supplier1204/'.$folder.'/'.$imageFileName;
		$url = 'https://s3.ap-south-1.amazonaws.com/'.$bucket.'/'.$folder.'/'.$imageFileName;
		
		return $url;
	}
	
	public function deleteFromS3($url) {
        if (!$url) {
            return 0;
        }
        $objectArray = explode('/', $url);
        $count = count($objectArray);
        if (isset($objectArray[$count - 1])) {
            $key = explode('.', $objectArray[$count - 1]);
        }
        try {
            $folderpath = array();
            foreach ($objectArray as $key => $value) {
                if ($key > 3 && $key != $count - 1)
                    $folderpath[] = $value;
            }
            $folder = implode('/', $folderpath);
            $bucket = Config::get('filesystems.disks.s3.bucket');
            $s3 = \AWS::createClient('s3');

            $result = $s3->deleteObject(array(
                'Bucket' => $bucket,
                'Key' => $folder . '/' . $objectArray[$count - 1]
            ));
            return 1;
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
        }
    }
	
	public function setFfReportData()
	{
		//$toDate = date('Y-m-d h:i:s');
		//$fromDate = date('Y-m-d h:i:s', strtotime('-4 months'));
		$toDate = date('Y-m-d');
		$fromDate = date('Y-m-d');
		//$order_data = DB::select("CALL getFF_Report_tranx(0,'".$fromDate."','".$toDate."')");
        $order_data =DB::selectFromWriteConnection(DB::raw("CALL getFF_Report_tranx(0,'".$fromDate."','".$toDate."')"));
		foreach($order_data as $order)
		{
			$reportModel = new FfReportModel();
			if($order->order_cnt==0)			 
			{
				$reportModel->order_date = $fromDate;				
			}
			else
			{
				$reportModel->order_date = $order->order_date;
			}
			$ff_rp_id = FfReportModel::where(['user_id'=>$order->user_id,'order_date'=>$reportModel->order_date])->pluck('ff_rp_id')->first();				
			if($ff_rp_id)
			{
				$reportModel = $reportModel->find($ff_rp_id);
			}
			else
			{
				$reportModel->user_id = $order->user_id;
			}
			$reportModel->tbv = $order->tbv;
			$reportModel->name = $order->name;
		     $reportModel->beat = $order->beat;
			 $reportModel->order_cnt = $order->order_cnt;
			 $reportModel->calls_cnt = $order->calls_cnt;
			 $reportModel->uob = $order->uob;
			 $reportModel->abv = $order->abv;
			 $reportModel->tlc = $order->tlc;
			 $reportModel->ulc = $order->ulc;
			 $reportModel->alc = $order->alc;
			 $reportModel->contrib = $order->contribution;
			 $reportModel->margin = $order->margin;
			 $reportModel->first_call = $order->first_call;
			 $reportModel->first_order = $order->first_order;
			 $reportModel->delivered_margin = $order->delivered_margin;
             $reportModel->hub_name = $order->hub_name;
             $reportModel->role = $order->role;
			 $reportModel->commission = $order->commission;
             $reportModel->actual_calls = $order->actual_calls;
             $reportModel->green_per = $order->green_per;
             $reportModel->total_time_spent = $order->total_time_spent;
             $reportModel->last_call = $order->last_call;
             $reportModel->state = $order->state;
             $reportModel->city = $order->city;
             $reportModel->delivered_cnt = $order->delivered_cnt;
             $reportModel->new_outltes = $order->new_outltes;
             $reportModel->dc_name = $order->dc_name;
             $reportModel->invoice_cnt =$order->invoice_cnt;
             $reportModel->invoice_value =$order->invoice_val;
             $reportModel->return_ord_cnt =$order->return_cnt;
             $reportModel->return_ord_val =$order->return_val;
             $reportModel->cancel_ord_cnt =$order->cancel_cnt;
             $reportModel->cancel_ord_val =$order->cancel_val;
             $reportModel->productive_hrs =$order->productive_hrs;
			$reportModel->save();
		}
		return 'ff report data updated';
	}
	
	public function setFfReportcompleteData()
	{
		//$toDate = date('Y-m-d h:i:s');
		//$fromDate = date('Y-m-d h:i:s', strtotime('-4 months'));
		$toDate = date('Y-m-d');
		$fromDate = date('Y-m-d');
		$order_data = DB::select("CALL getFF_Report('".$fromDate."','".$toDate."')");
		foreach($order_data as $order)
		{
			$reportModel = new FfReportModel();

			$ff_rp_id = FfReportModel::where('user_id',$order->user_id)
                                                   ->whereBetween('order_date',array($order->order_date,$order->order_date.' 23:59:59'))
                                                   ->pluck('ff_rp_id')->first();
												   
			if($ff_rp_id)
			{
				$reportModel = $reportModel->find($ff_rp_id);
				switch($order->flag)
				{
				case '1':  $reportModel->cancel_ord_val = $order->val;	break;
				case '2': $reportModel->cancel_ord_cnt = $order->val;	break;
				case '3': $reportModel->return_ord_val = $order->val;	break;
				case '4': $reportModel->return_ord_cnt = $order->val;    break;
				}
				//Log::info('flag:'.$order->flag);
				//Log::info($order->val);
				$reportModel->save();
			}
		}
		return 'ff report complete data updated';
	}	
	
	public function getParentGroupId($productId) 
	{ 
		$products = ProductModel::find($productId); 	

		if ($products->product_group_id == 0) 
		{ 
			return $productId; 
		} 
		else 
		{ 
			return $this->getParentGroupId($products->product_group_id); 
		} 
	}
        
    public function pushNotifications($message, $tokenDetails,$type ='default',$sentBy = 'Ebutor',$link ='',$pushMessageId='',$pushMessageCreatedBy='',$data='' , $start  = 0 , $end = 0)
    {    
        $queue = new Queue();
        $tokenDetails = json_encode($tokenDetails);
        $tokenDetails = base64_encode($tokenDetails);
        $args = array(  'ConsoleClass' => 'notification', 
                        'arguments' => array($message,$tokenDetails,$type,$sentBy,$link,$pushMessageId,$pushMessageCreatedBy,$data));
        $token_job = $queue->enqueue('default', 'ResqueJobRiver', $args);
    }  
	

	
	public function updateFfAttendance($fromDate,$toDate)
    {
       return $attend_data = DB::select("call getFFAttendance('".$fromDate."','".$toDate."')");
    }
    
    
    public function updatePickerAttendance($fromDate,$toDate)
    {
        return $attend_data = DB::select("call getDelAttendance('".$fromDate."','".$toDate."')");

    }
    
    public function updateDelAttendance($fromDate,$toDate)
    {
        return $attend_data = DB::select("call getPickAttendance('".$fromDate."','".$toDate."')");
    }
    public function updateAttendance()
    {
        try
        {
//            Log::info('in updateAttendance');
            $mongo = new AttendanceMongoModel();
            //$fromDate = date('Y-m-d', strtotime(' -6 day'));
            //$toDate = date('Y-m-d');
            $toDate = date('Y-m-d', strtotime(' +1 day'));
            $fromDate = date('Y-m-d');
            $ff = $this->updateFfAttendance($fromDate,$toDate);
            $pick = $this->updatePickerAttendance($fromDate,$toDate);
            $delivery = $this->updateDelAttendance($fromDate,$toDate);

            $attend_data = array_merge($ff,$pick,$delivery);
           // Log::info($attend_data);
            foreach ($attend_data as $data)
            {
                $ffAttendance = json_decode(json_encode($data), 1);
                $message = $mongo->updateAttendance($ffAttendance);
            }
        }
        catch (Exception $ex)
        {
            Log::info($ex->getMessage());
        }
		return 'Attendance updated successfully';
    }
}
