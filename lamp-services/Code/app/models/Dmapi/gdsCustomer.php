<?php

namespace App\models\Dmapi;

use Illuminate\Database\Eloquent\Model;
use DB;
use Log;

class gdsCustomer extends Model
{
    /**
     * [$primaryKey - primary key for the table]
     * [$timestamps - unknown]
     * [$table - 'table name']
     * 
     * @var string
     */
    protected $primaryKey = 'gds_cust_id';
    public $timestamps = false;
    protected $table = 'gds_customer';

    /**
     * [gdsCustomer description]
     * @param  [json] $data "customer_info": {
									        "suffix": "*****",
									        "first_name": "*******",
									        "middle_name": "*********",
									        "last_name": "*********",
									        "channel_user_id": "*******",
									        "email_address": "*********",
									        "mobile_no": "********",
									        "dob": "*********",
									        "channel_id": "int",
									        "gender": "male/female",
									        "registered_date": "y-m-d h:i:s"
									    }
     * @return [json]  [customer id from the gds_customer table]
     */
    public function gdsCustomer($data){
        try{
			if(is_array($data)){
				$data = json_decode(json_encode($data), FALSE);
            }
			
            $status     = 0;
            $message    = '';
            $userId     = 0;
			if(!property_exists($data, 'channel_user_id')){
                $userId = DB::table('gds_customer')
                    ->where(['email_address' => $data->email_address, 'mp_id' => $data->channel_id])
                    ->pluck('gds_cust_id');
                if(count($userId) > 0)
                    $userId =   $userId[0];
                else
                    $userId =   0;
            }elseif(property_exists($data, 'channel_user_id') && $data->channel_user_id != ''){                
                $userId =   DB::table('gds_customer')
                            ->where(['mp_user_id' => $data->channel_user_id, 'mp_id' => $data->channel_id])
                            ->pluck('gds_cust_id'); 
				if(count($userId) > 0)
                    $userId =   $userId[0];
                else
                    $userId =   0;
            }elseif(property_exists($data, 'channel_user_id') && $data->channel_user_id == ''){
                $userId = DB::table('gds_customer')
                    ->where(['email_address' => $data->email_address, 'mp_id' => $data->channel_id])
                    ->pluck('gds_cust_id');
                if(count($userId) > 0)
                    $userId =   $userId[0];
                else
                    $userId =   0;
            }
            $customerArray = ['suffix' => isset($data->suffix) ? $data->suffix : '',
                'firstname' => $data->first_name,
                'lastname' => $data->last_name,
                'middlename' => $data->middle_name,
                'mp_user_id' => property_exists($data, 'channel_user_id') ? $data->channel_user_id : $data->email_address,
                'email_address' => $data->email_address,
                'mobile_no' => $data->mobile_no,
                'dob' => isset($data->dob) ? $data->dob : '',
                'mp_id' => $data->channel_id,
                'gender' => isset($data->gender) ? $data->gender : '',
                'registered_date' => isset($data->registered_date) ? $data->registered_date : date('Y-m-d H:i:s')
            ];
            
           
            try
            {
                if (empty($userId)){
                    $userId = DB::table('gds_customer')->insertGetId($customerArray);
                    $status = 1;
                    $message = 'Successfully inserted.';
                } else{
                    if (isset($customerArray['mp_user_id'])){
                        unset($customerArray['mp_user_id']);
                    }
                    if (isset($customerArray['mp_id'])){
                        unset($customerArray['mp_id']);
                    }
                    if (isset($customerArray['registered_date'])){
                        unset($customerArray['registered_date']);
                    }
                    DB::table('gds_customer')
                            ->where('gds_cust_id', $userId)
                            ->update($customerArray);
                  $status = 1;
                  $message = 'Successfully Updated.';
                }
                //Log::info('userId');
                //Log::info($userId);
                if($userId > 0){
                    $address1 = property_exists($data, 'address1') ? $data->address1 : '';
                  //  Log::info('address1');
                    //Log::info($address1);
                    if($address1 != ''){
                        $otherInfo['customer_id'] 	= 	$userId;
                        $otherInfo['order_id'] 		= 	0;
                        $otherInfo['channel_id'] 	= 	$data->channel_id;
                      //  Log::info('otherInfo');
                       // Log::info($otherInfo);
                        $data->email 	= 	$data->email_address;
                        $data->phone 	= 	'';
                        $response 		= 	$this->customerAddress($data, $otherInfo);
                        //Log::info('response');
                        //Log::info($response);
                    }
                }
			} catch (ErrorException $e){
                $message = $e->getMessage();
            }
        } catch (Exception $e){
            $message = $e->getMessage(). $e->getTraceAsString();
        }
        return json_encode(Array('Status' => $status, 'Message' => $message, 'channel_cust_id' => $userId));
    }

}
