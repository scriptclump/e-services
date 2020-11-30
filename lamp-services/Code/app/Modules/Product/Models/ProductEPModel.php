<?php

namespace App\Modules\Product\Models;
use Illuminate\Database\Eloquent\Model;
use Session;
use Utility;
use DB;
use UserActivity;
use App\Modules\Product\Models\ProductTOT;
use Notifications;
use Mail;
use App\Modules\Notifications\Models\NotificationsModel;
use \App\Modules\Users\Models\Users;
use App\Modules\Roles\Models\Role;
use App\Modules\ApprovalEngine\Models\CommonApprovalFlowFunctionModel;
use Log;
class ProductEPModel extends Model
{

  public function productWorkFlowModel($product_id)
  {
    $approval_flow_func = new CommonApprovalFlowFunctionModel();
    $totalhistory=$approval_flow_func->getApprovalHistoryFromCommentsTable($product_id,'Product PIM');         
        if(count($totalhistory)>0){
                $history=json_decode($totalhistory[0]->comments,1);
            }else{
                  $history= DB::table('appr_workflow_history as hs')
                        ->join('products as pd','pd.product_id','=','hs.awf_for_id')
                        ->join('users as us','us.user_id','=','hs.user_id')
                        ->join('user_roles as ur','ur.user_id','=','hs.user_id')
                        ->join('roles as rl','rl.role_id','=','ur.role_id')
                        ->join('master_lookup as ml','ml.value','=','hs.status_to_id')
                        ->select('us.profile_picture','us.firstname','us.lastname',DB::raw('group_concat(rl.name) as name'),'hs.created_at','hs.status_to_id','hs.status_from_id','hs.awf_comment','ml.master_lookup_name')
                        ->where('hs.awf_for_id',$product_id)
                        ->where('awf_for_type','Product PIM')
                        //->groupBy('ur.user_id')
                        ->groupBy('ml.value')         
                        ->orderBy('hs.created_at')    
              ->get()->all();
            }
        return json_decode(json_encode($history),true);
  }  
  public function productSlabModel($product_id,$userId,$dcid=null)
  {
    if($dcid==null)
    {
      $whId = Session::get('warehouseId'); 
    }else{
      $whId = $dcid;
    }
    $pricing= DB::select('call getProductSlabs(?,?,?)', array($product_id, $whId, $userId));
    if(!empty($pricing))
    {
      $pricing = $pricing[0]->pack_size;
    } else
    {
      $pricing = '';
    }
    return $pricing;
  } 
  public function productTaxModel($product_id)
  {
    $flag= 1;
    $tax= DB::table('tax_class_product_map')->select('tax_class_id','status')
                                ->where('product_id', $product_id)->get()->all();
    if(!empty($tax))
    {
        foreach($tax as $onetax)
        {
            if($onetax->status != 1)
            {
                $flag =2;
            }
        }
    }
    else
    {
      $flag = '';  
    }
    return $flag;
  }
  public function getMasterLookUpPackageData($id,$name)
    {
      $returnData = DB::table('master_lookup_categories')
            ->join('master_lookup','master_lookup.mas_cat_id','=','master_lookup_categories.mas_cat_id')
           ->select('master_lookup.master_lookup_name as name','master_lookup.value as value')
            ->where('master_lookup_categories.mas_cat_id','=',$id)
            ->where('master_lookup.is_active','1')
            ->where('master_lookup_categories.mas_cat_name','=',$name)
            ->orderBy('master_lookup.sort_order', 'asc')
            ->get()->all();
      return $returnData;
    }
    public function getMasterLookUpData($id,$name)
    {
      $returnData = DB::table('master_lookup_categories')
            ->join('master_lookup','master_lookup.mas_cat_id','=','master_lookup_categories.mas_cat_id')
           ->select('master_lookup.master_lookup_name as name','master_lookup.value as value','sort_order')
            ->where('master_lookup_categories.mas_cat_id','=',$id)
            ->where('master_lookup.is_active','1')
            ->where('master_lookup_categories.mas_cat_name','=',$name)
             ->get()->all();
      return $returnData;
    }
    public function getMasterLookUpWeightUom($id,$name)
    {
      $returnData = DB::table('master_lookup_categories')
            ->join('master_lookup','master_lookup.mas_cat_id','=','master_lookup_categories.mas_cat_id')
           ->select('master_lookup.master_lookup_name as name','master_lookup.value as value')
            ->where('master_lookup_categories.mas_cat_id','=',$id)
            ->where('master_lookup.is_active','1')
            ->where('master_lookup_categories.mas_cat_name','=',$name)
            ->orderBy('master_lookup.value', 'asc')
            ->get()->all();
      return $returnData;
    }
    public function getWarehousesList()
    {
      $rs=DB::table('warehouse_config')
          ->where('wh_location_types','=','120001')
          ->select('le_wh_id','wh_location as lp_wh_name')
          ->get()->all();
          $rs = json_decode(json_encode($rs),true);
      return $rs;
    }
    public function getProductGroupInfo($pid){
      $getPid= DB::table('products as p')
              ->join('product_groups as p_grp','p.product_group_id','=','p_grp.product_grp_ref_id')
              ->select('p.product_title AS product_name',
              'p_grp.product_grp_ref_id AS product_grp_id')
              ->where('p.product_id','=',$pid)
              ->select('p_grp.product_grp_name AS product_name',
              'p_grp.product_grp_ref_id AS product_grp_id')
              ->first();
          $getPid=json_decode(json_encode($getPid),true);
        return $getPid;
    }
    public  function GetBinDimensionLevels($value='')
    {
        $getBinDim= DB::table('bin_type_dimensions')
                    ->join('master_lookup','master_lookup.value','=','bin_type_dimensions.bin_type')
                    ->select('bin_type_dim_id',DB::raw("CONCAT(master_lookup_name,'(',LENGTH,',',breadth,',',heigth,')') AS bin_dim_name"))
                    ->get()->all();
        $getBinDim=json_decode(json_encode($getBinDim),true);
        return $getBinDim;
    }
    public function getActiveSuppliers()
    {
        $loggedInLeId = Session::get('legal_entity_id'); 
        $legalEntityIds = DB::table('legal_entities')
                ->join('suppliers','suppliers.legal_entity_id','=','legal_entities.legal_entity_id')
                ->where(['legal_entity_type_id' => 1002, 'parent_le_id' => $loggedInLeId])->pluck('business_legal_name','legal_entities.legal_entity_id')->all();
        return $legalEntityIds;
    }

    
    public function quickProductSupp($supplier_id,$DcId,$ProductID)
    {
        $user_id = Session::get('userId');
        $rolesModel = new Role();
        $warehouseDetails = $rolesModel->getWarehouseData($user_id, 6);
        if($warehouseDetails != '' && ($DcId=='' || $DcId==0))
        {
            $warehouseInfo = (array) json_decode($warehouseDetails, true);
            $DcId = isset($warehouseInfo['118001']) ? $warehouseInfo['118001'] : 0;
            $DcId=explode(",", $DcId);
            if(count($DcId)==0){
              return false;
            }
            $DcId=count($DcId)>0 ? $DcId[0]:0;

        } 
        
        $wh_prd_parent = ProductTOT::where('supplier_id', $supplier_id)
                ->where('product_id', $ProductID)
                ->where('le_wh_id', $DcId)
                ->first();
        if (empty($wh_prd_parent) && count($wh_prd_parent) == 0)
        {
            $WhInfo = DB::table('legalentity_warehouses')->where('le_wh_id', $DcId)->pluck('state')->all();
            $stateidSeller = ($WhInfo[0]) ? $WhInfo[0] : 0;
            $SupInfo = DB::table('suppliers')->where('legal_entity_id', $supplier_id)->pluck('sup_state')->all();
            $stateidBeller = ($SupInfo[0]) ? $SupInfo[0] : 0;
            $getTax = $this->getTaxByState($ProductID, $stateidSeller, $stateidBeller);
            $ProductModel = new ProductModel();
            $ProductInfo = $ProductModel::where('product_id', $ProductID)->pluck('product_title')->all();
            $Product_Title = isset($ProductInfo[0])?$ProductInfo[0]:'';
            $Tot_Array = array(
                'le_wh_id' => $DcId,
                'product_id' => $ProductID,
                'product_name' => $Product_Title,
                'supplier_id' => $supplier_id,
                'subscribe' => 1,
                'created_by' => $user_id,
                'supplier_dc_relationship' => 100001,
                'grn_freshness_percentage' => 90,
                'moq' => 1,
                'moq_uom' => 16004,
                'delivery_terms' => 1,
                'delivery_tat_uom' => 71002,
                'grn_days' => 'MONDAY,TUESDAY,WEDNESDAY,THURSDAY,FRIDAY,SATURDAY,SUNDAY',
                'inventory_mode' => 45001,
                'atp' => 0,
                'atp_period' => 80002,
                'kvi' => 69002,
                'effective_date' => date('Y-m-d')
            );
            if ($getTax['Status'] == '200')
            {
                $taxData = $getTax['ResponseBody'];
                $Tot_Array['tax'] = $taxData[0]['Tax Percentage'];
                $taxInfo = DB::table('master_lookup')->where(array('mas_cat_id' => 9, 'master_lookup_name' => $taxData[0]['Tax Type']))->pluck('value')->all();
                $taxType = ($taxInfo[0]) ? $taxInfo[0] : 9003;
                $Tot_Array['tax_type'] = $taxType;
            }
            $totIncrId = ProductTOT::insertGetId($Tot_Array);
            /*$insert_inventory_dc=DB::table('inventory')
                                ->insert(['le_wh_id'=>$DcId,'product_id'=>$ProductID,'updated_by'=> Session::get('userId'),'updated_at'=>date('Y-m-d H:i:s')]);
             if($insert_inventory_dc){ */                  
               DB::table('product_cpenabled_dcfcwise')->insert(['product_id'=> $ProductID,'le_wh_id'=> $DcId,'cp_enabled'=> 0, 'is_sellable'=>0,'esu'=> '','created_by'=>$user_id,'created_at'=>date("Y-m-d H:i:s")]);
             //}
            return $totIncrId;
        }
    }
    
        private function getTaxByState($product_id, $stateidSeller, $stateidBeller){

        $url = env('APP_TAXAPI');
        $callType = "POST";
        $postData = array(
                    'product_id' => $product_id, 
                    'seller_state_id' => $stateidSeller,
                    'buyer_state_id' => $stateidBeller
                );

        $postData = json_encode($postData);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'api_key: testkey',
            'api_secret: testsecret',
            'Content-Type: application/json'
        ));
        if ($callType == "POST") {
            curl_setopt($ch, CURLOPT_POST, count($postData));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        }
        $output = curl_exec($ch);
        curl_close($ch);

        $outputs = json_decode($output, true);

        return $outputs;

    }
	
    public function sendNotificationAlert($sku, $id, $title, $mrp, $url)
    {
        $notificationObj = new NotificationsModel();
        $usersObj = new Users();
        $userData = $notificationObj->getUsersByCode('PRDN001');
        $userDecodData = json_decode(json_encode($userData),1);
        $Subject = $notificationObj->getMessageByCode('PRDN001');
        $emailList = $usersObj->wherein('user_id', $userDecodData)->pluck('email_id')->all();
        $emails = json_decode(json_encode($emailList));

        Notifications::addNotification(['note_code' => 'PRDN001',
            'note_priority' => 1, 'note_type' => 1, 'note_params' => ['SKU' => $sku], 'note_link' => '/editproduct/' . $id]);

        //$copyTo = ['toName' => 'Srikanth', 'toEmail' => $emails, 'fromName' => 'Ebutor', 'fromEmail' => 'tracker@ebutor.com'];
        $subject = 'New Product Created - SKU : ' . $sku;
        $body= array('template'=>'emails.newproduct', 'attachment'=>'', 'title' => $title, 'sku' => $sku,'mrp' => $mrp, 'url' => $url, 'name' => 'Team');
        Log::info('newproduct');
        Log:info($emails);
        Utility::sendEmail($emails,$subject,$body);
        // Mail::send('emails.newproduct', ['title' => $title, 'sku' => $sku,
        //     'mrp' => $mrp, 'url' => $url, 'name' => 'Team'], function ($message) use ($copyTo, $sku) {
        //     $message->from($copyTo['fromEmail'], $copyTo['fromName']);
        //     $message->to($copyTo['toEmail'], $copyTo['toName']);
        //     $message->subject('New Product Created - SKU : ' . $sku);
        // });
    }


    public function productPacksGrid($makeFinalSql, $orderBy, $page, $pageSize,$product_id){

      if($orderBy!=''){
        $orderBy = ' ORDER BY ' . $orderBy;
      }

      $sqlWhrCls = '';
      $countLoop = 0;
      
      foreach ($makeFinalSql as $value) {
        if( $countLoop==0 ){
          $sqlWhrCls .= ' WHERE ' . $value;
        }elseif( count($makeFinalSql)==$countLoop ){
          $sqlWhrCls .= $value;
        }else{
          $sqlWhrCls .= ' AND ' .$value;
        }
        $countLoop++;
      }
      if($sqlWhrCls == ""){
        $sqlWhrCls .= " Where product_id = $product_id";
      }else{
        $sqlWhrCls .=  " and product_id = $product_id";
      }
      $sqlQuery = "SELECT * FROM (SELECT product_id,getMastLookupValue(customer_type) as customer_type,elp,esp,margin,getMastLookupValue(pack_id) AS pack,getMastLookupValue(color_code) AS color,getDcNameById(le_wh_id) AS dcname FROM product_pack_color_wh) AS innertbl".$sqlWhrCls;
      $pageLimit = '';
      if($page!='' && $pageSize!=''){
        $pageLimit = " LIMIT " . (int)($page*$pageSize) . ", " . $pageSize;
      }
      $allRecallData = DB::select(DB::raw($sqlQuery . $pageLimit));
      $TotalRecordsCount = count($allRecallData);

      return json_encode(array('results'=>$allRecallData, 'TotalRecordsCount'=>(int)($TotalRecordsCount))); 

    }


    public function getPricingForProduct($pid){

      try{
        $pricing=DB::table('product_prices')
                      ->select('price')
                      ->where('product_id',$pid)
                      ->get()->all();
        $pricing=json_decode(json_encode($pricing),true);

        if(!empty($pricing)){
           $pricing=$pricing[0]['price'];
        }else{
          $pricing='';
        }
        return  $pricing;             
      }  catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }

    }

  public function productPricing($product_id,$userId,$dcid=null)
  {
    
      $whId = $dcid;
    //$pricing= DB::select('call getProductSlabs(?,?,?)', array($product_id, $whId, $userId));
      $pricing=DB::table('product_prices')
                    ->select('price')
                    ->where('product_id',$product_id)
                    ->where('dc_id',$whId)
                    //->where('customer_type',3014)
                    ->get()->all();
    if(!empty($pricing))
    {
      $pricing = $pricing[0]->price;
    } else
    {
      $pricing = '';
    }
    return $pricing;
  }

  public function checkFreeBieConfiguredIsConsumerpackOutside($product_id){
    $freebieprd=DB::table('freebee_conf')->where('main_prd_id', $product_id)->get()->all();
    if(count($freebieprd)>0){
      return 1;
    }else{
      return 0;
    } 
  }
}
