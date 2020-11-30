<?php
namespace App\Modules\MFCcompany\Models;
use Illuminate\Database\Eloquent\Model;
use App\Modules\MFCcompany\Models\MFCModel;
use DB;
use Session;
use UserActivity;
use Utility;
class MFCModel extends Model
{
   public function GetAllCompanydata($makeFinalSql, $orderBy, $page, $pageSize,$legalid){
        try{
         if($orderBy!=''){
            $orderBy = ' ORDER BY ' . $orderBy;
        }
        $sqlWhrCls = '';
        $countLoop = 0;
        foreach ($makeFinalSql as $value) {
            if (strpos($value, 'fullname like') !== false) {
              $value = '(u.firstname OR u.lastname LIKE ' . substr($value,13) . ')';
            }
            if( $countLoop==0 ){
                $sqlWhrCls .= 'AND ' . $value;
            }elseif(count($makeFinalSql)==$countLoop ){
                $sqlWhrCls .= $value;
            }else{
                $sqlWhrCls .= 'AND' .$value;
            }

            $countLoop++;
        }

   //    $legaEntityQuery = "";
   // if(Session::get('legal_entity_id')!=2){
   //    $legaEntityQuery=" where legal_entity_id='". Session::get('legal_entity_id') ."'";
   //    }
      $act = "'<center><code>','<a href=mfccompany/details/',le.`legal_entity_id`,'> <i class=\'fa fa-pencil\'></i> </a>&nbsp;&nbsp;&nbsp;</code></center>'";
      $legalentityDetails = 'SELECT le.`address1`,le.`city`,le.`state_id`,le.`pincode`,le.`gstin`,le.`business_legal_name`,le.`legal_entity_id`,u.`user_id`,
                            CONCAT( u.`firstname`,u.`lastname`) AS fullname, `getStateNameById`(`le`.`state_id`)  AS `StateName`, u.`email_id` ,u.`mobile_no`, CONCAT('.$act.') AS `CustomAction`
                            FROM  legal_entities le
                            LEFT JOIN users AS u  ON u.`legal_entity_id` = le.`legal_entity_id` WHERE le.`legal_entity_type_id`=1015';

      $legalentityDetails.= ' '.$sqlWhrCls;

      $allData = DB::select(DB::raw($legalentityDetails));

      return $allData;
      }catch(Exception $e){
        return 'Message: ' .$e->getMessage();
      }
    }
  public function editGridId($id){
       try{
       $data = "SELECT  le.`address1`,le.`city`,le.`state_id`,le.`pincode`,le.`gstin`,le.`business_legal_name` , CONCAT( u.`firstname`,' ',u.`lastname`)
       AS fullname, u.`email_id` ,u.`mobile_no`,u.`user_id`, u.`is_active`,le.`state_id`,le.`legal_entity_id`
       FROM  users u
       LEFT JOIN legal_entities le ON le.`legal_entity_id`=u.`legal_entity_id`  WHERE u.`legal_entity_id` =$id";

          $gridData = DB::select(DB::raw($data));
          // print_r($gridData);die();
        return $gridData;
     }catch(Exception $e) {
      return 'Message: ' .$e->getMessage();
   }

    }
  public function getStates(){
        try{
          $data = "select * from zone where country_id= 99";
          $stateData = DB::select(DB::raw($data));
        return $stateData;
    }catch(Exception $e) {
      return'Message: ' .$e->getMessage();
    }

  }

  public function getCountries(){
    $country ="select * from countries";
    $country = DB::select(DB::raw($country));
    return $country;
  }
  public function upDateUsersInfo($data){
        // DB::beginTransaction();
        //   try{
        $checkbox_active = isset($data['le_check_active']) ? 1 : 0;
        // $this->warehouseMapping($data,$userdataid);
       /* $update = DB::table('legal_entities')
                    ->where('legal_entity_id', '=', $data['le_hidden1_id'] )
                    ->update([                     
                            // 'gstin'                 =>  $data['gstin_name'], 
                            // 'pincode'               =>  $data['pincode'],
                            // 'business_legal_name'   =>  $data['bu_le_name'],
                            // 'city'                  =>$data['city_name'],
                            // 'sate_id'                  =>$data['satename'],
                            ]);*/
        $users = DB::table('users')
                        ->where('legal_entity_id', '=',$data['le_hidden1_id'])
                        ->update([
                            'mobile_no'     =>$data['mobile_no'],
                            'email_id'     =>$data['email_id'],
                            'firstname'      =>  $data['f_name'],
                            'lastname'      =>  $data['l_name'],
                            'is_active'      =>  $checkbox_active
                           ]);
                     // DB::commit();
                        // print_r($users);die();
        return $users;
   //  }catch(Exception $e) {
   //        DB::rollback();
   //       return 'Message: ' .$e->getMessage();
   // }
  }
 public function GetAllUsersData($makeFinalSql, $orderBy, $page, $pageSize,$getUser_id){
    if($orderBy!=''){
            $orderBy = ' ORDER BY ' . $orderBy;
        }
        $sqlWhrCls = '';
        $countLoop = 0;
        foreach ($makeFinalSql as $value) {
            if( $countLoop==0 ){
                $sqlWhrCls .= 'AND ' . $value;
            }elseif(count($makeFinalSql)==$countLoop ){
                $sqlWhrCls .= $value;
            }else{
                $sqlWhrCls .= ' WHERE ' .$value;
            }
            $countLoop++;
        }

    $query ="select *,concat(firstname,' ',lastname) as fullname,CONCAT('<center><code>','<a href=\"javascript:void(0)\" onclick=\"update_users_data(',user_id,')\">
                            <i class=\"fa fa-pencil\"></i></a>&nbsp;&nbsp;&nbsp;\n </code>\n </center>') AS `CustomAction` from users where user_id = " .$getUser_id  .$sqlWhrCls.$orderBy;

    $user = DB::select(DB::raw($query));

      return $user;
  }


 public function registeredData($data){
    // DB::beginTransaction();
          // try{
        $data['legal_entity_id'] = $data['le_hidden_id'];
        $update = DB::table('legal_entities')
                    ->where('legal_entity_id', '=',         $data['legal_entity_id'] )
                    ->update([                     
                            'gstin'                     =>  $data['gstin'], 
                            'pincode'                   =>  $data['org_pincode'],
                            'business_legal_name'       =>  $data['business_name'],
                            'address1'                  =>  $data['org_address1'],
                            'city'                      =>  $data['org_city'],
                            'state_id'                  =>  $data['satename'],
                            ]);
    
                     // DB::commit();

        return $update;
    // }catch(Exception $e) {
    //       DB::rollback();
    //      return 'Message: ' .$e->getMessage();
    //  }
   }
    
    public function saveUsersData($data){
      //     DB::beginTransaction();
      try{

          $mLUpV = DB::table('master_lookup')->select('*')->where('value', '=',1015)->first();

          $result = json_decode(json_encode($mLUpV), true);

          $lookUpId = $result['master_lookup_id'];

          $code = Utility::getReferenceCode('MF','TS');

          $leId =  DB::table('legal_entities')->insertGetId([
                                  'business_legal_name' =>$data['business_legal_name'],
                                  'legal_entity_type_id'=>$result['value'],
                                  'business_type_id'    =>48001,
                                  'address1'            =>$data['address'],
                                  'city'                =>$data['city'],
                                  'state_id'            =>$data['state_id'],
                                  'pincode'             =>$data['pincode'],
                                  'gstin'               =>$data['gstin_number'],
                                  'le_code'             =>$code

                    ]);      
          
          $userData = DB::table('users')->insert([
                      'firstname'           =>$data['first_name'],
                      'legal_entity_id'     =>$leId,
                      'lastname'            =>$data['last_name'],
                      'email_id'            =>$data['email'],
                      'mobile_no'           =>$data['phone_number']
                     ]);

               
         return 1;

             // DB::commit();

    }catch(\ErrorException $ex) {
            \Log::error($ex->getMessage());
            return False;
        } 
   }

   public function getUsersDataGrid($id){
    $userDetails = DB::table('users')->select('*')->where('user_id','=',$id)->get(); 
    return $userDetails;
   }
           
}