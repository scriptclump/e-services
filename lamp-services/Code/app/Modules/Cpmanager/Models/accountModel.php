<?php
    namespace App\Modules\Cpmanager\Models;
    use \DB;
    use Config;
	use App\Central\Repositories\RoleRepo;
	use App\Modules\Retailer\Models\Retailer;
	use DateTime;
	
    class accountModel extends \Eloquent {
		
			
/*
	* Function Name: getCustomerData()
	* Description: getCustomerData function is used to get  the customer data of the customer_token passed .
	* Author: Ebutor <info@ebutor.com>
	* Copyright: ebutor 2016
	* Version: v1.0
	* Created Date: 6 July 2016
	* Modified Date & Reason:
*/
            
	public function getCustomerData($customer_token){            

		$id = DB::table('users')
		->select('legal_entity_id','mobile_no')
		->where('password_token','=',$customer_token)
        ->useWritePdo()
		->get()->all();
		
		$legal_entity_id = $id[0]->legal_entity_id;
		$mobile_no = $id[0]->mobile_no;
		
		//21 oct added condition 
		  $master = DB::table('master_lookup as ml')
				   ->where("ml.value", "=", 78002)->get()->all();
		  
			$desc= $master[0]->description;
		
		$result = DB::table('users as u')
		->select(DB::raw("u.firstname AS firstname, u.lastname,u.profile_picture AS documents, le.business_legal_name AS company, le.legal_entity_id AS address_id, le.address1 AS address_1,  le.address2 AS address_2, IFNULL(le.locality,'') AS locality, IFNULL(le.landmark,'') AS landmark,
		 le.city,  coun.name,  le.pincode AS postcode,  u.mobile_no AS telephone, u.email_id AS email, le.business_type_id AS business_type,c.No_of_shutters, cp.city_id as area_id, IFNULL(cp.officename,'') as area,z.name as state,c.volume_class,up.preference_value as delivery_time,up.preference_value1 as pref_value1,
         c.master_manf as manufacturers, c.smartphone,c.dist_not_serv,c.is_icecream,c.facilities,
         up.sms_subscription as sms_notification,c.is_visicooler,c.is_milk,c.is_deepfreezer,c.is_swipe,c.is_fridge,c.is_vegetables,up.business_start_time,
         up.business_end_time, c.network as internet_availability, 
         le.legal_entity_type_id as buyer_type,c.beat_id,u.is_parent,le.gstin,le.arn_number,IFNULL(p.pdp,'') as pdp,IFNULL(p.pdp_slot,'') as pdp_slot,
        CASE  WHEN le.legal_entity_type_id = 3013  THEN 1
                    ELSE 0
                    END AS is_premium
         "))
        ->leftJoin('legal_entities as le','le.legal_entity_id','=','u.legal_entity_id')
        ->leftJoin('customers as c','c.le_id','=','le.legal_entity_id')
        ->leftJoin('cities_pincodes as cp','cp.city_id','=','c.area_id')
        ->leftJoin('countries as coun','coun.country_id','=','le.country')
        ->leftJoin('zone as z','z.zone_id','=','le.state_id')
        ->leftJoin('user_preferences as up','up.user_id','=','u.user_id')
        ->leftJoin('pjp_pincode_area as p','p.pjp_pincode_area_id','=','c.beat_id')
        ->where('u.mobile_no','=',$mobile_no)
        ->where('z.country_id','=',99)
            //21 oct added condition 
        ->where(function ($query) {
                  $query->where('le.legal_entity_type_id', 'LIKE', '%30%')
                        ->orWhere('le.legal_entity_type_id', 'LIKE', 1014)
                        ->orWhere('le.legal_entity_type_id', 'LIKE', 1016);
                })    
        ->get()->all();

       $is_primary = DB::table('legalentity_warehouses as lew')
        ->leftJoin('users as u', function($join){
                                    $join->on('u.mobile_no','=','lew.phone_no');
                                    $join->on('u.legal_entity_id','=','lew.legal_entity_id');
          })
        ->select(DB::raw('count(lew.le_wh_id) as count'))
        ->where('u.password_token','=',$customer_token)
        
        ->get()->all();

        $is_primary = $is_primary[0]->count;
       
        $result = json_decode(json_encode($result),true);
        $result = $result[0];
        if(count($result)>0){
            $data['fridges']=array(array("is_deepfreezer"=>$result['is_deepfreezer'],
                                    "key"=>"is_deepfreezer"),
                            array("is_fridge"=>$result['is_fridge'],"key"=>"is_fridge"),
                            array("is_visicooler"=>$result['is_visicooler'],"key"=>"is_visicooler"));

            $data['alsoselling']=[["is_icecream"=>$result['is_icecream'],"key"=>"is_icecream"],
                            ["is_milk"=>$result['is_milk'],"key"=>"is_milk"],
                            ["is_vegetables"=>$result['is_vegetables'],"key"=>"is_vegetables"]];

            $data['notification']=[["sms_notification"=>$result['sms_notification'],"key"=>"sms_notification"]];
            $data['others']=[["is_swipe"=>$result['is_swipe'],"key"=>"is_swipe"]];

            $result['retailer_details']=$data;

        }
       if($is_primary>0 || $result['is_parent']==1){

        $users = DB::table('users as u')
               ->select('u.mobile_no','u.firstname','u.user_id')
               ->where('legal_entity_id','=',$legal_entity_id)
               ->where('mobile_no','!=',$mobile_no)
               ->where('is_active','=',1)
               ->where('is_disabled','=',0)
               ->take('2')
               ->get()->all();

               $users = json_decode(json_encode($users),true);

            if(!empty($users)){
                $i=1;
               foreach ($users as $user) {
                $contact['contact_name'.$i] = $user['firstname'];
                $contact['contact_no'.$i] = $user['mobile_no'];
                $contact['user_id'.$i] = $user['user_id'];
               $i++;
               }

        $result = array_merge($result,$contact);

            }else{

                $contact['contact_no1'] = '';
                $contact['contact_name1'] = '';
                $contact['contact_no2'] = '';
                $contact['contact_name2'] = '';
                $contact['user_id1'] = '';
                $contact['user_id2'] = '';
                $result = array_merge($result,$contact);
            } 
              
    }   


        $parentData = DB::table('users as u')
        ->select(DB::raw('count(lew.phone_no) as count'))
        ->leftJoin('legalentity_warehouses as lew','lew.phone_no','=','u.mobile_no')
        ->where('u.password_token','=',$customer_token)
        ->useWritePdo()
        ->get()->all();

        // $parentData[0]->count=1;
        if(($parentData[0]->count) > 0) {
            $parent['is_parent']=1;       

        }else {
            $parent['is_parent']=$result['is_parent'];            
        }

         $result= array_merge($result,$parent);

        
        if(!empty($result)){
         
            $pos=strpos($result['email'], '@nomail');
            if($pos==false)
            {
                
            return $result;
            }else{
                   $result['email']= '';
                   return $result;

			}


		} else
		{
			return;
		}  
		

	}
	
/*
		* Function Name: checkCustomerToken()
		* Description: checkCustomerToken function is used to check if the customer_token passed when the customer is logged in is valid.
		* Author: Ebutor <info@ebutor.com>
		* Copyright: ebutor 2016
		* Version: v1.0
		* Created Date: 6 July 2016
		* Modified Date & Reason:
*/
    public function checkCustomerToken($customer_token){
            $query = DB::table("users as u")
            ->select(DB::raw("count(u.password_token) as count")) 
            ->where("u.password_token","=",$customer_token)
            ->useWritePdo()   
            ->get()->all();

            return $query[0]->count;   


    }
/*
    * Function Name: getDocument()
    * Description: checkCustomerToken function is used to check if the customer_token passed when the customer is logged in is valid.
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 6 July 2016
    * Modified Date & Reason:
*/
    public function getDocument($customer_token){
            $result = DB::table('users as u')
            ->select(DB::raw("profile_picture as documents")) 
            ->where("u.password_token","=",$customer_token)   
            ->get()->all();

            return $result;


    }
    
    
/*
    * Function Name: getFirstname()
    * Description: checkCustomerToken function is used to check if the customer_token passed when the customer is logged in is valid.
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 6 July 2016
    * Modified Date & Reason:
*/
        
    public function getFirstname($customer_token){
            $result = DB::table('users as u')
            ->select(DB::raw("firstname")) 
            ->where("u.password_token","=",$customer_token)   
            ->get()->all();

            return $result;
    }
    
    /*
    * Function Name: allTelephone()
    * Description: allTelephone function is used to check if the customer_token passed when the customer is logged in is valid.
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 6 July 2016
    * Modified Date & Reason:
*/
        
    public function allTelephone($telephone,$customer_token){

            /*$legal_entity_type_id = DB::table('users as u')
            ->select('le.legal_entity_type_id')
            ->leftJoin('legal_entities as le','le.legal_entity_id','=','u.legal_entity_id')
            ->where('u.password_token','=',$customer_token)
            ->get()->all();
            $legal_entity_type_id = $legal_entity_type_id[0]->legal_entity_type_id;
           

            $result = DB::table('users as u')
            ->select(DB::raw(" count(mobile_no) as count")) 
            ->leftJoin('legal_entities as le','le.legal_entity_id','=','u.legal_entity_id')
            ->where("u.mobile_no","=",$telephone)   
            ->where("le.legal_entity_type_id",'=',$legal_entity_type_id)
            ->get()->all();*/

            $result = 1;
            if($telephone != null){
                $result = DB::table('users as u')
                ->select(DB::raw("count(mobile_no) as count"))
                ->where("u.mobile_no","=",$telephone)
                ->get()->all();
                $result = $result[0]->count;
            }

            return $result;

    }
    
    /*
    * Function Name: getTelephone()
    * Description: getTelephone function is used to check if the customer_token passed when the customer is logged in is valid.
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 6 July 2016
    * Modified Date & Reason:
*/
        
    public function getTelephone($customer_token){
            $result = DB::table('users as u')
            ->select(DB::raw(" u.mobile_no ")) 
            ->where("u.password_token","=",$customer_token)   
            ->get()->all();

            return $result;

    }
/*
    * Function Name: generateOtp()
    * Description: generateOtp function is used to check if the customer_token passed when the customer is logged in is valid.
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 6 July 2016
    * Modified Date & Reason:
*/
        
    public function generateOtp($customer_token,$phone){

			$randnumber= rand(100000,999999);

			$ch = curl_init();
			$mno = $phone;
			$message =  "Your OTP for Ebutor is  " .$randnumber;
			if(preg_match( '/^[A-Z0-9]{10}$/', $mno) && !empty($message)) {
					$ch = curl_init();

					$user=Config::get('dmapi.DB_USER');
					$receipientno= $mno; 
					$senderID=Config::get('dmapi.DB_SENDER_ID'); 

					$msgtxt= $message; 
					curl_setopt($ch,CURLOPT_URL,  "http://api.mVaayoo.com/mvaayooapi/MessageCompose");
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, "user=$user&senderID=$senderID&receipientno=$receipientno&msgtxt=$msgtxt");
					//  print_r($ch);exit;
					$buffer = curl_exec($ch);

					if(empty ($buffer))
					{ 

							echo " buffer is empty "; 
							}else{

							DB::Table('users')  
							->where('password_token', $customer_token)
							->update(array('otp' => $randnumber,'updated_at' => date("Y-m-d H:i:s")));



							return $randnumber;
			}

					}
					curl_close($ch);
			}
/*
    * Function Name: updateProfile()
    * Description: updateProfile function is used to check if the customer_token passed when the customer is logged in is valid.
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 6 July 2016
    * Modified Date & Reason:
*/      
    public function updateProfile($customer_token, $firstname , $filepath2, $lastname){
           
           //Get user id from users table
            $user_data = DB::table('users')
            ->select('user_id','legal_entity_id')
            ->where('password_token','=',$customer_token)
            ->first();
         
            $user_id = $user_data->user_id;
            $legal_entity_id= $user_data->legal_entity_id;

            /*if($legal_entity_id!=2)
             {  */ 
            
            DB::table('users')  
            ->where('password_token', $customer_token)
            ->update(array(
            'firstname' => $firstname,
            'lastname' => $lastname,
            'profile_picture' => $filepath2,
			//'updated_by'=>$user_id,
            'updated_at' => date("Y-m-d H:i:s")));



            $legal_entity_id = DB::table('users')
            ->select('legal_entity_id')
            ->where('password_token','=',$customer_token)
            ->get()->all();
            $legal_entity_id = $legal_entity_id[0]->legal_entity_id;
            
            $roleRepo = new RoleRepo();
            $retailer = new Retailer($roleRepo);
            $retailer->updateFlatTable($legal_entity_id);

           
            $BasicData = array();
            $BasicData['firstname'] = $firstname;
            $BasicData['lastname'] = $lastname;
            $BasicData['documents'] = $filepath2;
            return $BasicData;
       /* }else{
           
            return '';

        }*/

    }   
/*
    * Function Name: getOtp()
    * Description: getOtp function is used to check if the customer_token passed when the customer is logged in is valid.
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 6 July 2016
    * Modified Date & Reason:
*/
    public function getOtp($customer_token){
           /* $result = DB::table('users as u')
            ->select(DB::raw("u.otp")) 
            ->where("u.password_token","=",$customer_token)   
            ->get()->all();

            print_r($result);exit;*/
            $salesotp = DB::selectFromWriteConnection(DB::raw("select otp from users where password_token='".$customer_token ."'"));
            //print_r($salesotp);exit;

            return $salesotp;


    }
/*
    * Function Name: updateTelephone()
    * Description: updateTelephone function is used to check if the customer_token passed when the customer is logged in is valid.
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 6 July 2016
    * Modified Date & Reason:
*/      
  public function updateTelephone($customer_token,$telephone){
        
            //Get user id from users table
            $user_data = DB::table('users')
            ->select('user_id','legal_entity_id')
            ->where('password_token','=',$customer_token)
            ->get()->all();
            $user_id = $user_data[0]->user_id;
            $legal_entity_id=$user_data[0]->legal_entity_id;
         if($legal_entity_id!=2)
         {
            DB::table('users')  
            ->where('password_token', $customer_token)
            ->update(array('mobile_no' => $telephone,
							//'updated_by' => $user_id,
            'updated_at' => date("Y-m-d H:i:s")));


           

            $data = DB::table('users')
                    ->select('email_id')
                    ->where('password_token','=',$customer_token)
                    ->get()->all();
                    $existingEmail = $data[0]->email_id;
                    $pos=strpos($existingEmail, '@nomail');
                    if($pos==true){

                        DB::table('users')  
                    ->where('password_token', $customer_token)
                    ->update(array('email_id' => $telephone.'@nomail.com',
					//'updated_by' => $user_id,
                    'updated_at' => date("Y-m-d H:i:s")));

                    }

                $roleRepo = new RoleRepo();
                $retailer = new Retailer($roleRepo);
                $retailer->updateFlatTable($legal_entity_id);
            }


    }
/*
    * Function Name: updateEmail()
    * Description: updateEmail function is used to check if the customer_token passed when the customer is logged in is valid.
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 6 July 2016
    * Modified Date & Reason:
*/      
  public function updateEmail($customer_token,$email){
            
                $user_data = DB::table('users')
                ->select('user_id','legal_entity_id')
                ->where('password_token','=',$customer_token)
                ->get()->all();
                $user_id = $user_data[0]->user_id;
                $legal_entity_id=$user_data[0]->legal_entity_id;

        if($legal_entity_id!=2)
          {  
        $pos=strpos($email, '@nomail');
                if($pos==false)
                {
             DB::table('users')  
            ->where('password_token', $customer_token)
            ->update(array('email_id' => $email,
			//'updated_by' => $user_id,
            'updated_at' => date("Y-m-d H:i:s")));
               
                }else{
                    $data = DB::table('users')
                    ->select('email_id','mobile_no')
                    ->where('password_token','=',$customer_token)
                    ->get()->all();
                    $existingEmail = $data[0]->email_id;

                    if(empty($email_id)){
                        $mobile_no = $data[0]->mobile_no;
                    DB::table('users')  
                    ->where('password_token', $customer_token)
                    ->update(array('email_id' => $mobile_no.'@nomail.com',
					//'updated_by' => $user_id,
                    'updated_at' => date("Y-m-d H:i:s")));

                    }

                       

                }
}
             return 1;

    }
/*
    * Function Name: updateAddressData()
    * Description: updateAddressData function is used to check if the customer_token passed when the customer is logged in is valid.
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 6 July 2016
    * Modified Date & Reason:
*/      

    public function updateAddressData($customer_token,$address_1,$address_2,$locality,$landmark,$city,$postcode,$state,$gstin,$arn_number){
         
          $user_data = DB::table('users')
            ->select('user_id','legal_entity_id')
            ->where('password_token','=',$customer_token)
            ->first();
          $roleRepo = new RoleRepo();
          $retailer = new Retailer($roleRepo);
          $parent_le_id=$retailer->checkPincodeLegalentity($postcode);
         
         if($user_data->legal_entity_id!=2)
           { 
 
            DB::table('legal_entities as le')  
             ->where('le.legal_entity_id','=',$user_data->legal_entity_id)
            ->update(array('le.address1' => $address_1,
                    'le.address2' => $address_2,
                    'le.locality' => $locality,
                    'le.landmark' => $landmark,
                    'le.city' => $city,
                    'le.pincode' => $postcode,
                    'le.state_id' => $state,
                    'le.gstin' => $gstin,
                    'le.arn_number' => $arn_number,
                    'le.updated_at' => date("Y-m-d H:i:s"),
                    'parent_le_id' => $parent_le_id));

                   
         $le_wh_id = DB::table('wh_serviceables as ws')
                ->select(DB::raw("group_concat(distinct ws.le_wh_id separator ',') as le_wh_id"))
                ->join('legalentity_warehouses as lew','ws.pincode','=','lew.pincode')
                ->where("ws.pincode",'=', $postcode)
                ->get()->all();
                if(empty($le_wh_id)){
                    $le_wh_id = '';
                }else{
                    $le_wh_id = $le_wh_id[0]->le_wh_id;
                }
            
            
            
            
            $retailer->updateFlatTable($user_data->legal_entity_id);
            
            $AddressData = array();
            $AddressData['address_1'] = $address_1;
            $AddressData['address_2'] = $address_2;
            $AddressData['locality'] = $locality;
            $AddressData['landmark'] = $landmark;
            $AddressData['city'] = $city;
            $AddressData['postcode'] = $postcode;
            $AddressData['customer_token'] = $customer_token;
            $AddressData['le_wh_id'] = $le_wh_id;
            return $AddressData;
        }else{
            return '';
        }

    }
/*
    * Function Name: getShippingAddress()
    * Description: getShippingAddress function is used to check if the customer_token passed when the customer is logged in is valid.
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 6 July 2016
    * Modified Date & Reason:
*/
    public function getShippingAddress($customer_token,$legal_entity_id, $customer_type){
        //Orginal code commented on 12 oct 2016
   if(!empty($legal_entity_id)){
        $result = DB::table('legal_entities as le')
            ->select('u.firstname as Firstname','le.address1 as Address','le.address2 as Address1','le.landmark','le.locality','u.mobile_no as telephone','le.city as City','le.pincode as pin','z.name as state','coun.name as country','u.email_id','le.legal_entity_id as legal_entity_id','p.pdp','p.pdp_slot')
            ->leftJoin('users as u','u.legal_entity_id','=','le.legal_entity_id')
            ->leftJoin('countries as coun','coun.country_id','=','le.country')
            ->leftJoin('zone as z','z.zone_id','=','le.state_id')
            ->leftJoin('retailer_flat as r','r.legal_entity_id','=','le.legal_entity_id')
            ->leftJoin('pjp_pincode_area as p','p.pjp_pincode_area_id','=','r.beat_id')
            ->where('u.password_token', $customer_token)
            ->useWritePdo()
            ->get()->all();
           
            
   }else{      
           $result = DB::table('legalentity_warehouses as lew')
           ->select('lew.contact_name as Firstname','lew.address1 as Address','lew.address2 as Address1','lew.phone_no as telephone','le.landmark','le.locality','lew.city as City','lew.pincode as pin','z.name as state','coun.name as country','lew.email as email_id','lew.le_wh_id as address_id','p.pdp','p.pdp_slot')
           ->leftJoin('legal_entities as le','le.legal_entity_id','=','lew.legal_entity_id')
           ->leftJoin('users as u','u.legal_entity_id','=','lew.legal_entity_id')
           ->leftJoin('countries as coun','coun.country_id','=','lew.country')
           ->leftJoin('zone as z','z.zone_id','=','lew.state')           
           ->leftJoin('retailer_flat as r','r.legal_entity_id','=','le.legal_entity_id')
           ->leftJoin('pjp_pincode_area as p','p.pjp_pincode_area_id','=','r.beat_id')
            ->where('u.password_token', $customer_token)
            ->useWritePdo()
            ->get()->all();

           

   }
   
   if(count($result)>0){
        $date = new DateTime();
        if($result[0]->pdp == 'Mon'){
            $date->modify('next monday');
        }else if($result[0]->pdp == 'Tue'){
            $date->modify('next Tuesday');
        }else if($result[0]->pdp == 'Wed'){
            $date->modify('next wednesday');
        }else if($result[0]->pdp == 'Thu'){
            $date->modify('next Thursday');
        }else if($result[0]->pdp == 'Fri'){
            $date->modify('next Friday');
        }else if($result[0]->pdp == 'Sat'){
            $date->modify('next Saturday');
        }else if($result[0]->pdp == 'Sun'){
            $date->modify('next Sunday');
        }
        $result[0]->date=$date->format('Y-m-d');
        if($result[0]->pdp == ''){
            $result[0]->date='';
        }

        //added new delivery options for all customer type.
        //paymentOption for consumer type master_lookup value   =  22020,22021
        $payOption = DB::table('master_lookup')
            ->select('master_lookup_name AS name')
            ->whereIn('VALUE', [22010 , 22021])
             ->useWritePdo()
             ->get();
        $result[0]->delivery_type1=$payOption[0]->name;
        $result[0]->delivery_type2=$payOption[1]->name;
        $result[0]->payment_header="Payment Options";

   }
    return $result;
    }
/*
    * Function Name: check_duplicate_address()
    * Description: check_duplicate_address function is used to check if the customer_token passed when the customer is logged in is valid.
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 6 July 2016
    * Modified Date & Reason:
*/
    public function check_duplicate_address($data,$customer_token)
    {
			$arraysize =  sizeof($data);
			// /echo $arraysize;die;
			//for ($i=0; $i <= $arraysize-1 ; $i++) { 
			$fname = $data['FirstName'];
			$lname = $data['LastName'];
			$address = $data['Address'];
			$address1 = $data['Address1'];
			$city = $data['City'];
			$pin = $data['pin'];
			$state = $data['state'];
			$country = $data['country'];
			$addressType = $data['addressType'];
			$telephone = $data['telephone'];
    if(empty($telephone))
    {
            $telephone = DB::table('users as u')
            ->select(DB::raw(" u.mobile_no ")) 
            ->where("u.password_token","=",$customer_token)   
            ->get()->all();
            $telephone = $telephone[0]->mobile_no;


    }

            $email = $data['email'];

            $check_address_count = DB::table('legalentity_warehouses as lew')
            ->select(DB::raw("count(le_wh_id) as countt"))
            ->leftJoin('users as u','u.legal_entity_id','=','lew.legal_entity_id')
            ->where("u.password_token",'=',$customer_token)
            ->where('lew.contact_name','=',$fname)
            ->where('lew.phone_no','=',$telephone)
            ->where('lew.address1','=',$address)
            ->where('lew.address2','=',$address1)
            ->where('lew.city','=',$city)
            ->where('lew.pincode','=',$pin)
            ->where('lew.state','=',$state)
            ->where('lew.country','=',$country)
            ->where('lew.email','=',$email)
            ->get()->all();


            //$check_address_count  = $check_address_duplicate;
            //}
            return $check_address_count[0]->countt;
    }
/*
    * Function Name: Addaddress()
    * Description: Addaddress function is used to check if the customer_token passed when the customer is logged in is valid.
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 6 July 2016
    * Modified Date & Reason:
*/
    public function Addaddress($data,$customer_token)
    {

            $arraysize =  sizeof($data);

            for ($i=0; $i <= $arraysize-1 ; $i++) { 
                    $fname = $data[$i]['FirstName'];
            $lname = $data[$i]['LastName'];
            $address = $data[$i]['Address'];
            $address1 = $data[$i]['Address1'];
            $city = $data[$i]['City'];
            $pin = $data[$i]['pin'];
            $state = $data[$i]['state'];
            $country = $data[$i]['country'];
            $addressType = $data[$i]['addressType'];
            $telephone = isset($data[$i]['telephone']) ? $data[$i]['telephone'] : '';
            if(empty($telephone))
            {
				$telephone = DB::table('users as u')
				->select(DB::raw(" u.mobile_no ")) 
				->where("u.password_token","=",$customer_token)   
				->get()->all();
				$telephone = $telephone[0]->mobile_no;


            }


        $email = isset($data[$i]['email']) ? $data[$i]['email'] : '';
        
            $legal_entity_id = DB::table("users as u")
            ->select("legal_entity_id")
            ->where("u.password_token","=",$customer_token)   
            ->get()->all();
            
            $legal_entity_id = $legal_entity_id[0]->legal_entity_id;   
            


        DB::table('legalentity_warehouses')
				 ->insert(['contact_name' => $fname,
						   'phone_no' =>  $telephone, 
						   'email' => $email,
						   'country'=>$country,
						   'city' => $city,
						   'state'=>$state,
						   'address1'=>$address,
						   'address2'=>$address1,
						   'pincode'=>$pin,
						   'legal_entity_id'=>$legal_entity_id,
							'created_at' => date("Y-m-d H:i:s")]);
			 $lastaddressId=    DB::getPdo()->lastInsertId();

				$addresses = array();
				$addresses['address_id'] = $lastaddressId;
				$addresses['FirstName'] = $fname;
			   $addresses['telephone'] =  $telephone;
			   $addresses['email'] = $email;
			   $addresses['country']=$country;
			   $addresses['city'] =  $city;
			   $addresses['state']=$state;
			   $addresses['Address']=$address;
			   $addresses['Address1']=$address1;
			   $addresses['pin']=$pin;


            
        }
        

        return $addresses;
        
    }
/*
    * Function Name: editAddress()
    * Description: editAddress function is used to check if the customer_token passed when the customer is logged in is valid.
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 6 July 2016
    * Modified Date & Reason:
*/
    public function editAddress($data,$customer_token){
					$arraysize =  sizeof($data);

                    $address_id = $data[0]['address_id'];
                    $fname = $data[0]['FirstName'];
                    $lname = $data[0]['LastName'];
                    $address = $data[0]['Address'];
                    $address1 = $data[0]['Address1'];
                    $city = $data[0]['City'];
                    $pin = $data[0]['pin'];
                    $state = $data[0]['state'];
                    $country = $data[0]['country'];
                    $addressType = $data[0]['addressType'];
                    $telephone = isset($data[0]['telephone']) ? $data[0]['telephone'] : '';
            if(empty($telephone))
            {
                    $telephone = DB::table('users as u')
                    ->select(DB::raw(" u.mobile_no ")) 
                    ->where("u.password_token","=",$customer_token)   
                    ->get()->all();
                    $telephone = $telephone[0]->mobile_no;


            }

            $email = isset($data[0]['email']) ? $data[0]['email'] : '';

            $legal_entity_id = DB::table("users as u")
                    ->select("legal_entity_id")
                    ->where("u.password_token","=",$customer_token)   
                    ->get()->all();

                    $legal_entity_id = $legal_entity_id[0]->legal_entity_id;   

            DB::table('legalentity_warehouses')
            ->where('le_wh_id', $address_id)
                         ->update(['contact_name' => $fname,
                           'phone_no' =>  $telephone, 
                           'email' => $email,
                           'country'=>$country,
                           'city' => $city,
                           'state'=>$state,
                           'address1'=>$address,
                           'address2'=>$address1,
                           'pincode'=>$pin,
                          'legal_entity_id'=>$legal_entity_id,
                           'updated_at' => date("Y-m-d H:i:s")]);


                            $addresses = array();
                            $addresses['address_id'] = $address_id;
                            $addresses['FirstName'] = $fname;
                           $addresses['telephone'] =  $telephone;
                           $addresses['email'] = $email;
                           $addresses['country']=$country;
                           $addresses['city'] =  $city;
                           $addresses['state']=$state;
                           $addresses['Address']=$address;
                           $addresses['Address1']=$address1;
                           $addresses['pin']=$pin;


            return $addresses;


    }
/*
    * Function Name: getCountries()
    * Description: getCountries function is used to check if the customer_token passed when the customer is logged in is valid.
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 6 July 2016
    * Modified Date & Reason:
*/
	public function getCountries(){
			$result = DB::table('countries')
					->select("country_id","name")
					->get()->all();

					return $result;
	}
/*
    * Function Name: getStates()
    * Description: getStates function is used to check if the customer_token passed when the customer is logged in is valid.
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 6 July 2016
    * Modified Date & Reason:
*/

	public function getStates($country_id){
			$result = DB::table('zone as c')
					->select("c.zone_id as state_id","c.name as state_name")
					->where("c.country_id","=",$country_id)
					->where("c.zone_id","!=",4035) 
					->where("status","=",1) 
					->orderBy("sort_order")    
					->get()->all();

					return $result;
	}
/*
    * Function Name: serviceablePincode()
    * Description: serviceablePincode function is used to check if the customer_token passed when the customer is logged in is valid.
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 6 July 2016
    * Modified Date & Reason:
*/
	public function serviceablePincode($pincode){
		$query =DB::table('wh_serviceables as whs')
			->select(DB::raw("count(le_wh_id) as count"))
			->where("whs.pincode",'=', $pincode)
			->get()->all();

			return $query[0]->count;
	}

/*
    * Function Name: updateCustomerContact()
    * Description: serviceablePincode function is used to check if the customer_token passed when the customer is logged in is valid.
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 6 July 2016
    * Modified Date & Reason:
*/
    public function updateCustomerContact($user_id1,$contact_no1,$contact_name1){
		//Get user id from users table
				/*$user_id = DB::table('users')
				->select('user_id')
				->where('password_token','=',$customer_token)
				->get()->all();
				$user_id = $user_id[0]->user_id;*/

        if(isset($user_id1) && !empty($user_id1)){
                    DB::Table('users')  
                    ->where('user_id', $user_id1)
                    ->update(array('mobile_no' => $contact_no1,
                                   'updated_at' => date("Y-m-d H:i:s"),
                                   'firstname' => $contact_name1,
								   //'updated_by' => $user_id,
                                  'email_id' => $contact_no1.'@nomail.com'
                                   ));


        }
      
          $result['contact_no1']= $contact_no1;
          $result['contact_name1'] = $contact_name1;
		$result['email_id'] = $contact_no1.'@nomail.com';
          $result['user_id1'] = $user_id1;
         
          return $result;

    }

/*
    * Function Name: updateBussinessType()
    * Description: serviceablePincode function is used to check if the customer_token passed when the customer is logged in is valid.
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 6 July 2016
    * Modified Date & Reason:
*/
    public function updateBussinessType($business_type,$business_legal_name, $buyer_type, $customer_token){
            $legal_entity_id = DB::table('users')
            ->select('legal_entity_id','user_id')
            ->where('password_token','=',$customer_token)
            ->get()->all();
               if($legal_entity_id[0]->legal_entity_id!=2)
              {  
            DB::table('legal_entities')
            ->where('legal_entity_id','=',$legal_entity_id[0]->legal_entity_id)
            ->update(array('business_legal_name' => $business_legal_name,
                'business_type_id'=>$business_type,
                'legal_entity_type_id'=>$buyer_type,
                'updated_by'=>$legal_entity_id[0]->user_id,
                'updated_at'=>date("Y-m-d H:i:s")));
        }
    }

/*
    * Function Name: updateCustomerTable()
    * Description: serviceablePincode function is used to check if the customer_token passed when the customer is logged in is valid.
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 6 July 2016
    * Modified Date & Reason:
*/
    public function updateCustomerTable($internet_availability,$manufacturers,$No_of_shutters,$area,$volume_class,$delivery_time,$pref_value1,$business_start_time,$business_end_time,$postcode,$city,$smartphone,$customer_token,$state,$beat,$is_icecream=0,$sms_notification=0,$is_milk=0,$is_fridge=0,$is_vegetables=0,$is_visicooler=0,$dist_not_serv='',$facilities=0,$is_deepfreezer=0,$is_swipe=0){
        $user_id = DB::table('users')
            ->select('user_id')
            ->where('password_token','=',$customer_token)
            ->get()->all();
            $roleRepo = new RoleRepo();
            $retailer = new Retailer($roleRepo);			
            $user_id = $user_id[0]->user_id;
            $legal_entity_id = DB::table('users')
            ->select('legal_entity_id')
            ->where('password_token','=',$customer_token)
            ->get()->all();
			
            $legal_entity_id = $legal_entity_id[0]->legal_entity_id;
            DB::table('user_preferences')
            ->where('user_id','=',$user_id)
            ->update(array(
            'preference_value'=>$delivery_time,
            'preference_value1'=>$pref_value1,
            'business_start_time'=>$business_start_time,
            'business_end_time'=>$business_end_time, 
			'sms_subscription' => $sms_notification,
            'updated_at'=>date("Y-m-d H:i:s"))); 


        $area_chk = DB::table('cities_pincodes as cp')
                       ->select("cp.city_id")
                       ->where("cp.pincode", "=", $postcode)
                       ->where("cp.officename","LIKE",'%'.$area.'%')
                       ->get()->all();
		if(!empty($area_chk))
            {
			DB::table('customers')
			->where('le_id','=',$legal_entity_id)
            ->update(array('network' => $internet_availability,
            'No_of_shutters'=>$No_of_shutters,
            'volume_class'=>$volume_class,
            'master_manf' => $manufacturers,
            'smartphone' => $smartphone,
            'beat_id'=> $beat,
            'area_id'=>$area_chk[0]->city_id,
            'is_icecream' =>$is_icecream,
            //'sms_notification'=>$sms_notification,
            'is_milk'=>$is_milk,
            'is_fridge'=>$is_fridge,
            'is_vegetables'=>$is_vegetables,
            'is_visicooler'=>$is_visicooler,
            'dist_not_serv'=>$dist_not_serv,
            'facilities'=>$facilities,
            'is_deepfreezer'=>$is_deepfreezer,
            'is_swipe'=>$is_swipe,
			//'updated_by'=>$user_id, 			
            'updated_at'=>date("Y-m-d H:i:s"))); 

            }else{
               

			  $state_name=DB::table('zone')
			->select("name")
			->where("zone_id", "=", $state)
			->get()->all();

                DB::table('cities_pincodes')->insert(['country_id' =>99,
                'pincode' => $postcode, 
                'city' => $city,
                'state' => $state_name[0]->name,
                'officename'=> $area
                    ]);
             
            $last_insert_city_id = DB::getPdo()->lastInsertId();
            

             DB::table('customers')
             ->where('le_id','=',$legal_entity_id)
            ->update(array('network' => $internet_availability,
            'No_of_shutters'=>$No_of_shutters,
            'volume_class'=>$volume_class,
            'master_manf' => $manufacturers,
            'smartphone' => $smartphone,
            'beat_id'=> $beat,
            'area_id'=>$last_insert_city_id,            
            'is_icecream' =>$is_icecream,
            //'sms_notification'=>$sms_notification,
            'is_milk'=>$is_milk,
            'is_fridge'=>$is_fridge,
            'is_vegetables'=>$is_vegetables,
            'is_visicooler'=>$is_visicooler,
            'dist_not_serv'=>$dist_not_serv,
            'facilities'=>$facilities,
            'is_deepfreezer'=>$is_deepfreezer,
            'is_swipe'=>$is_swipe,
			//'updated_by'=>$user_id, 				
            'updated_at'=>date("Y-m-d H:i:s"))); 

            }
        $retailer->updateFlatTable($legal_entity_id);


   
    }
/*
    * Function Name: AddContact()
    * Description: serviceablePincode function is used to check if the customer_token passed when the customer is logged in is valid.
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 6 July 2016
    * Modified Date & Reason:
*/
	public function AddContact($contact_no1, $contact_name1,$customer_token){

				$legal_entity_id = DB::table('users')
				->select('legal_entity_id','user_id')
				->where('password_token','=',$customer_token)
				->get()->all();
				
				//$legal_entity_id = $legal_entity_id[0]->legal_entity_id;
				DB::table('users')->insert([
					'mobile_no' => $contact_no1, 
					'firstname' => $contact_name1,
					'email_id' => $contact_no1.'@nomail.com',
					'legal_entity_id' => $legal_entity_id[0]->legal_entity_id,
					'is_active' => 1,
					//'updated_by' => $legal_entity_id[0]->user_id,
					'updated_at'=> date("Y-m-d H:i:s")
						]);
		$user_id = DB::getPdo()->lastInsertId();
		return $user_id;

	}
/*
    * Function Name: getMobile()
    * Description: serviceablePincode function is used to check if the customer_token passed when the customer is logged in is valid.
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 6 July 2016
    * Modified Date & Reason:
*/
	public function getMobile($user_id){
		$user_id = DB::table('users')
				->select('mobile_no')
				->where('user_id','=',$user_id)

				->get()->all();

				$mobile_no = $user_id[0]->mobile_no;
				return $mobile_no;

	}

/*
    * Function Name: DisableContactuser()
    * Description: ///Disable Contact Disable User///////
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 6 July 2016
    * Modified Date & Reason:
*/	

public function DisableContactuser($customer_token,$telephone){

    $query= DB::table('users')  
            ->where('mobile_no', $telephone)
            ->update(array('is_active' => 0,'is_disabled'=> 1,
             'updated_at' => date("Y-m-d H:i:s")));
            return $query;

}


 /*
    * Function Name: eMailcheck()
    * Description: getTelephone function is used to check if the customer_token passed when the customer is logged in is valid.
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 6 July 2016
    * Modified Date & Reason:
*/
        
    public function eMailcheck($customer_token){
      
            $result = DB::table('users as u')
            ->select(DB::raw(" u.email_id ")) 
            ->where("u.password_token","=",$customer_token)   
            ->get()->all();
            return $result;

    }
	
	 /*
    * Function Name: getLastname()
    * Description: checkCustomerToken function is used to check if the customer_token passed when the customer is logged in is valid.
    * Author: Ebutor <info@ebutor.com>
    * Copyright: ebutor 2016
    * Version: v1.0
    * Created Date: 1 Nov 2016
    * Modified Date & Reason:
*/
        
    public function getLastname($customer_token){
            $result = DB::table('users as u')
            ->select(DB::raw("lastname")) 
            ->where("u.password_token","=",$customer_token)
            ->useWritePdo()
            ->get()->all();
            return $result;
    }
    
     public function customerLegalid($customer_token){

           $data = DB::table('users')
                    ->select('legal_entity_id')
                    ->where('password_token','=',$customer_token)
                    ->useWritePdo()
                    ->first();
    
            return $data->legal_entity_id;
    }

    public function getUserIdByCustomerToken($customer_token = null){
        if($customer_token != null){
            $data = DB::table('users')
                ->select('user_id')
                ->where('password_token','=',$customer_token)
                ->useWritePdo()
                ->first();
            return $data->user_id;
        }
        return null;
    }

        
    public function getGstinNo($gstin){
               $result = 1;
          $result = DB::table('legal_entities as le')
                ->select(DB::raw("count(gstin) as count"))
                ->where("le.gstin","=",$gstin)
                ->get()->all();
                $result = $result[0]->count;
           

            return $result;

    }
    
      public function getArnNo($arn_number){
               $result = 1;
          $result = DB::table('legal_entities as le')
                ->select(DB::raw("count(arn_number) as count"))
                ->where("le.arn_number","=",$arn_number)
                ->get()->all();
                $result = $result[0]->count;
           

            return $result;

    }
    
    public function getUserGstinNo($customer_token){
        $gstin_data = DB::table('legal_entities as le')
               ->join('users as u','u.legal_entity_id','=','le.legal_entity_id')
                ->select('le.gstin')
                ->where('u.password_token','=',$customer_token)
                ->get()->all();

                if(!empty($gstin_data))
                  {

                    $gstin_data=$gstin_data[0]->gstin;
                  }else{

                    $gstin_data='';
                  }

                return $gstin_data;

    }

    public function getUserArnNo($customer_token){
        $arn_data = DB::table('legal_entities as le')
               ->join('users as u','u.legal_entity_id','=','le.legal_entity_id')
                ->select('le.arn_number')
                ->where('u.password_token','=',$customer_token)
                ->get()->all();

                if(!empty($arn_data))
                  {

                    $arn_data=$arn_data[0]->arn_number;
                  }else{

                    $arn_data='';
                  }

                return $arn_data;

    }
    public function getFFPincode($userId)
    {
        $getPincode=DB::Select(DB::raw("select wh.pincode FROM  wh_serviceables wh
                         INNER JOIN legalentity_warehouses le ON le.le_wh_id = wh.le_wh_id 
                         INNER JOIN users u ON u.legal_entity_id = le.legal_entity_id WHERE u.user_id = ".$userId)) ;
        //$getPincode=json_encode($getPincode);
        return $getPincode;

    }
    public function getTimeslotData(){
        $getTimeSlotData = DB::table('master_lookup')
                            ->select('value','master_lookup_name')
                            ->where('mas_cat_id',171)
                            ->get()->all();
        return $getTimeSlotData;
    }
    public function updateRetailerData($mobile_no)
    {
        $query =DB::table('legal_entities AS leg')
                ->Join('users as users1','leg.legal_entity_id','=','users1.legal_entity_id')
                ->Join('customers as cust','leg.legal_entity_id','=','cust.le_id')
                  ->select('leg.business_legal_name AS company','leg.latitude','leg.longitude',
                  'leg.address1 AS address_1','leg.address2','leg.legal_entity_id',
                  db::raw("CONCAT(users1.firstname,' ',users1.lastname) as firstname"),
                  'cust.beat_id',db::raw("getBeatName(cust.beat_id) as beatname"),
                  db::raw("getRetailerCheck_in( leg.legal_entity_id) as check_in"),
                  'users1.mobile_no AS telephone','users1.user_id AS customer_id',
                  'users1.password_token as customer_token',
                  'No_of_shutters','volume_class','business_type_id','master_manf',
                  'leg.legal_entity_type_id as buyer_type',db::raw("CASE
                  WHEN volume_class IS NULL OR volume_class='' OR No_of_shutters IS NULL OR No_of_shutters=''
                  OR master_manf IS NULL OR master_manf='' THEN 1
                  ELSE 0
                  END AS popup"))
                  ->where('users1.mobile_no',$mobile_no)
                  ->where('legal_entity_type_id','LIKE','%30%')->get()->all();

        $query = json_decode(json_encode($query),1);
        return $query;
    }

    public function getDataFromToken($flag,$token,$datatoget)
    {
        if($flag == 1){
            $query = DB::table('users')->select($datatoget)
                           ->where('password_token','=',$token)
                           ->useWritePdo()
                           ->first();
            
            if(!empty($query)){
                return $query;
            }else{
                $query = DB::table('users')->select($datatoget)
                            ->where('lp_token','=',$token)
                            ->useWritePdo()
                            ->first();
                return $query;
            }
        }
        if($flag == 2){
            $query = DB::table('users')->select($datatoget)
                            ->where('password_token','=',$token)
                            ->useWritePdo()
                            ->get()->all();
            if(count($query)){
                return $query;
            }else{
                $query = DB::table('users')->select($datatoget)
                            ->where('lp_token','=',$token)
                            ->useWritePdo()
                            ->get()->all(); 
                return $query;
            }
    
        }
    }

    public function getLegalEntityTypeId($customer_token){
        $le_type_id=DB::table('users as u')
                   ->join('legal_entities as le','u.legal_entity_id','=','le.legal_entity_id')
                   ->select('le.legal_entity_type_id')
                   ->where('u.password_token',$customer_token)
                   ->where('legal_entity_type_id','LIKE','3%')
                   ->first();
        $result=json_decode(json_encode($le_type_id),true);
        if(count($result)>0){
            return 1;
        }
        return 0;
    }


}   