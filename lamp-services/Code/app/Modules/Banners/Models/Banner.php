<?php

namespace App\Modules\Banners\Models;
use Illuminate\Database\Eloquent\Model;
use App\Central\Repositories\ProductRepo;
use DB;
use Log;
use URL;
use Session;

class Banner extends Model
{
    protected $table = 'banner';
    protected $primaryKey = 'banner_id';
    
    public function __construct() {

      $this->_productRepo = new ProductRepo();
    }

    public function GetWareHouses(){

    $warehouse_res=DB::table("legalentity_warehouses")
                         ->select('le_wh_id','lp_wh_name')
                         ->where('dc_type','=','118001')
                         ->get()->all();

                        
      return json_decode(json_encode($warehouse_res),true);

    }

    public function GetHubs()
     {
      /*$hub_qry=DB::table("legalentity_warehouses")
                         ->select('le_wh_id','lp_wh_name')
                         ->where('dc_type','=','118002')
                         ->get();*/
     $hub_qry="SELECT * FROM legalentity_warehouses WHERE dc_type='118002'";


                 $hubs_res=DB::select(DB::raw($hub_qry));

         
        return json_decode(json_encode($hubs_res),true); 
     }

     public function GetBeats()
    {

     $beat_res=DB::table("pjp_pincode_area")
                         ->select('pjp_pincode_area_id','pjp_name')->get()->all();
           
      return json_decode(json_encode($beat_res),true);

    }
    
    public function SaveBanners($data,$url=""){

         try{
            $id = 0;
            $status = false; 
            $message = 'Unable to save data please contact admin'; 
            if(!empty($data))
            {
                $status = true; 
                if($data['type']==16601 || $data['type']==16602){
                $message = 'Banner saved successfully'; 
                }else{
                    $message = 'Sponsor saved successfully'; 
                }         
                $le_wh_id = isset($data['warehouse_id']) ? $data['warehouse_id'] : '';
                $hub_id = isset($data['hub_id']) ? $data['hub_id'] : 0;
                $beat_id = isset($data['beat_id']) ? $data['beat_id'] : 0;


                if(isset($data['type']) && ($data['type']==16601 || $data['type']==16602)){
                $banner_name = isset($data['bannername']) ? trim($data['bannername']) : '';
                }else{
                    $sponser_name = isset($data['bannername']) ? trim($data['bannername']) : '';
                }
                $navigator_objects = isset($data['banner_type']) ? $data['banner_type'] : '';
                $navigator_object_id = isset($data['banner_list']) ? $data['banner_list'] : '';
                $impression_cost = isset($data['impression_cost']) ? $data['impression_cost'] : '';
                $click_cost = isset($data['click_cost']) ? $data['click_cost'] : '';
                $frequency = isset($data['banner_frequency']) ? $data['banner_frequency'] : '';
                $display_type = isset($data['type']) ? $data['type'] : '';
                $from_date = isset($data['from_date']) ? $data['from_date'] : '';
                $to_date = isset($data['to_date']) ? $data['to_date'] : '';
                $status = isset($data['status']) ? $data['status'] : '';
                $sort_order = isset($data['sort_order']) ? $data['sort_order'] : '';
                $issponsor = isset($data['is_sponsor']) ? $data['is_sponsor'] : '';
                if(isset($data['type']) && ($data['type']==16601 || $data['type']==16602)){
                $banner_url = isset($url) ? $url : '';
                }
                $created_by=Session::get('userId');
                
                if($data['type']==16601 || $data['type']==16602){

                 //multiple dcs,hubs and beats insert query using for loop

                 if(count($le_wh_id)==1 && $le_wh_id[0]==0){


                  $bnrsave=DB::table('banner')->insert(['banner_name'=> $banner_name,'banner_url'=> $banner_url,'is_active'=> $status, 'sort_order'=>$sort_order,'navigate_object_id'=> $navigator_object_id,'navigator_objects'=>$navigator_objects,'frequency'=>$frequency,'le_wh_id'=>$le_wh_id[0],'hub_id'=>$hub_id[0],'beat_id'=>$beat_id[0],'display_type'=>$display_type,'impression_cost'=>$impression_cost,'click_cost'=>$click_cost,'created_by'=>$created_by,'from_date'=>$from_date,'to_date'=>$to_date,'status'=>$issponsor]);

            }else{   
         
                 for($i=0;$i<count($le_wh_id);$i++){
                 $hbids=array_filter($hub_id);
                  
                     $dc_hubmap=DB::table('legalentity_warehouses')
                               ->join('dc_hub_mapping','dc_hub_mapping.hub_id','=','legalentity_warehouses.le_wh_id')
                               ->select('hub_id')
                               ->whereIn('dc_hub_mapping.hub_id',$hbids)
                               ->where('dc_hub_mapping.dc_id',$le_wh_id[$i])
                               ->get()->all();
                    $hubsarray=json_decode(json_encode($dc_hubmap),1);
                 for($h=0;$h<count($hubsarray);$h++){
                   
                    $beat_hubmap=DB::table("pjp_pincode_area")
                                ->Join('legalentity_warehouses','pjp_pincode_area.le_wh_id','=','legalentity_warehouses.le_wh_id')
                                ->select('pjp_pincode_area_id')
                                ->whereIn('pjp_pincode_area.pjp_pincode_area_id',$beat_id)
                                ->where('pjp_pincode_area.le_wh_id',$hubsarray[$h]['hub_id'])
                                ->get()->all();    
                    $beatsarray=json_decode(json_encode($beat_hubmap),1);   
                    
                    if(count($beatsarray)!=0){
                    for($b=0;$b<count($beatsarray);$b++){       
                                
                                $bnrsave=DB::table('banner')->insert(['banner_name'=> $banner_name,'banner_url'=> $banner_url,'is_active'=> $status, 'sort_order'=>$sort_order,'navigate_object_id'=> $navigator_object_id,'navigator_objects'=>$navigator_objects,'frequency'=>$frequency,'le_wh_id'=>$le_wh_id[$i],'hub_id'=>$hubsarray[$h]['hub_id'],'beat_id'=>$beatsarray[$b]['pjp_pincode_area_id'],'display_type'=>$display_type,'impression_cost'=>$impression_cost,'click_cost'=>$click_cost,'created_by'=>$created_by,'from_date'=>$from_date,'to_date'=>$to_date,'status'=>$issponsor]);
                            
                          }
                      }else{
                          $beatsall=0;
                         $bnrsave=DB::table('banner')->insert(['banner_name'=> $banner_name,'banner_url'=> $banner_url,'is_active'=> $status, 'sort_order'=>$sort_order,'navigate_object_id'=> $navigator_object_id,'navigator_objects'=>$navigator_objects,'frequency'=>$frequency,'le_wh_id'=>$le_wh_id[$i],'hub_id'=>$hubsarray[$h]['hub_id'],'beat_id'=>$beatsall,'display_type'=>$display_type,'impression_cost'=>$impression_cost,'click_cost'=>$click_cost,'created_by'=>$created_by,'from_date'=>$from_date,'to_date'=>$to_date,'status'=>$issponsor]);
                          }
                      }
                
                }
            }
                  /*$bnrsave=DB::table('banner')->insert(['banner_name'=> $banner_name,'banner_url'=> $banner_url,'status'=> $status, 'sort_order'=>$sort_order,'navigate_object_id'=> $navigator_object_id,'navigator_objects'=>$navigator_object_type,'frequency'=>$frequency,'le_wh_id'=>$le_wh_id,'hub_id'=>$hub_id,'beat_id'=>$beat_id,'display_type'=>$display_type,'impression_cost'=>$impression_cost,'click_cost'=>$click_cost,'created_by'=>$created_by,'from_date'=>$from_date,'to_date'=>$to_date]);*/

                }elseif($data['type']==16603){

            
            if(count($le_wh_id)==1 && $le_wh_id[0]==0){

                  $bnrsave=DB::table('sponsors')->insert(['sponsor_name'=> $sponser_name,'status'=> $status, 'sort_order'=>$sort_order,'navigate_object_id'=> $navigator_object_id,'navigator_objects'=>$navigator_objects,'frequency'=>$frequency,'le_wh_id'=>$le_wh_id[0],'hub_id'=>$hub_id[0],'beat_id'=>$beat_id[0],'display_type'=>$display_type,'impression_cost'=>$impression_cost,'click_cost'=>$click_cost,'created_by'=>$created_by,'from_date'=>$from_date,'to_date'=>$to_date]);

            }else{
            for($i=0;$i<count($le_wh_id);$i++){
                $hbids=array_filter($hub_id);

                     $dc_hubmap=DB::table('legalentity_warehouses')
                               ->join('dc_hub_mapping','dc_hub_mapping.hub_id','=','legalentity_warehouses.le_wh_id')
                               ->select('hub_id')
                               ->whereIn('dc_hub_mapping.hub_id',$hbids)
                               ->where('dc_hub_mapping.dc_id',$le_wh_id[$i])
                               ->get()->all();
                    $hubsarray=json_decode(json_encode($dc_hubmap),1);
            for($h=0;$h<count($hubsarray);$h++){
                   
               // DB::enableQueryLog();
                    $beat_hubmap=DB::table("pjp_pincode_area")
                                ->Join('legalentity_warehouses','pjp_pincode_area.le_wh_id','=','legalentity_warehouses.le_wh_id')
                                ->select('pjp_pincode_area_id')
                                ->whereIn('pjp_pincode_area.pjp_pincode_area_id',$beat_id)
                                ->where('pjp_pincode_area.le_wh_id',$hubsarray[$h]['hub_id'])
                                ->get()->all();
                      //dd(DB::getQueryLog());          
                    $beatsarray=json_decode(json_encode($beat_hubmap),1);   
                    
                     if(count($beatsarray)!=0){
                    for($b=0;$b<count($beatsarray);$b++){ 
                                $bnrsave=DB::table('sponsors')->insert(['sponsor_name'=> $sponser_name,'status'=> $status, 'sort_order'=>$sort_order,'navigate_object_id'=> $navigator_object_id,'navigator_objects'=>$navigator_objects,'frequency'=>$frequency,'le_wh_id'=>$le_wh_id[$i],'hub_id'=>$hubsarray[$h]['hub_id'],'beat_id'=>$beatsarray[$b]['pjp_pincode_area_id'],'display_type'=>$display_type,'impression_cost'=>$impression_cost,'click_cost'=>$click_cost,'created_by'=>$created_by,'from_date'=>$from_date,'to_date'=>$to_date]);
                          }
                      }else{
                        $beatall=0;
                        $bnrsave=DB::table('sponsors')->insert(['sponsor_name'=> $sponser_name,'status'=> $status, 'sort_order'=>$sort_order,'navigate_object_id'=> $navigator_object_id,'navigator_objects'=>$navigator_objects,'frequency'=>$frequency,'le_wh_id'=>$le_wh_id[$i],'hub_id'=>$hubsarray[$h]['hub_id'],'beat_id'=>$beatall,'display_type'=>$display_type,'impression_cost'=>$impression_cost,'click_cost'=>$click_cost,'created_by'=>$created_by,'from_date'=>$from_date,'to_date'=>$to_date]);
                          }
                      }
                   
                    /*$bnrsave=DB::table('sponsors')->insert(['sponsor_name'=> $sponser_name,'status'=> $status, 'sort_order'=>$sort_order,'navigate_object_id'=> $navigator_object_id,'navigator_objects'=>$navigator_object_type,'frequency'=>$frequency,'le_wh_id'=>$le_wh_id,'hub_id'=>$hub_id,'beat_id'=>$beat_id,'display_type'=>$display_type,'impression_cost'=>$impression_cost,'click_cost'=>$click_cost,'created_by'=>$created_by,'from_date'=>$from_date,'to_date'=>$to_date]);*/
                }   
                }
            }
              }
              return $message;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }

    }


    public function EditBanners($id){

           $editdata=DB::table("banner")
                         ->select('*')
                         ->where('banner_id','=',$id)
                         ->get()->all();
 
           return json_decode(json_encode($editdata),true);
    }

    public function UpdateBanner($data,$url){

       try {
            $id = 0;
            $status = false; 
            $message = 'Unable to save data please contact admin'; 
            if(!empty($data))
            {
                $status = true; 
                if($data['type']==16601 || $data['type']==16602){
                $message = 'Banner updated successfully'; 
                }else{
                    $message = 'Sponsor updated successfully'; 
                }   

                if(isset($data['type']) && ($data['type']==16601 || $data['type']==16602)){
                    if(isset($url) && $url!='' && $url!=null){
                    $banner_url = $url ;
                    }else{

                     $banner_url = $data['bannerurl_edited'];
                 }
                }
                $issponsor = isset($data['is_sponsor']) ? $data['is_sponsor'] : '';
                if($data['type']==16601 || $data['type']==16602){
                DB::table('banner')
                    ->where('banner_id','=',$data['banner_id']) 
                    ->update(['le_wh_id'=>$data['warehouse_id'][0],'hub_id'=>$data['hub_id'][0],'banner_name'=>trim($data['bannername']),'navigator_objects'=>$data['banner_type'],'navigate_object_id'=>$data['banner_list'],'impression_cost'=>$data['impression_cost'],'click_cost'=>$data['click_cost'],'frequency'=>$data['banner_frequency'],'display_type'=>$data['type'],'from_date'=>$data['from_date'],'to_date'=>$data['to_date'],'beat_id'=>$data['beat_id'][0],'is_active'=>$data['status'],'sort_order'=>$data['sort_order'],'banner_url'=>$banner_url,'status'=>$issponsor,'updated_by'=>Session::get('userId')]);  
                }elseif($data['type']==16603){

                    $bnrsave=DB::table('sponsors')
                            ->where('sponsor_id','=',$data['banner_id']) 
                            ->update(['le_wh_id'=>$data['warehouse_id'][0],'hub_id'=>$data['hub_id'][0],'sponsor_name'=>trim($data['bannername']),'navigator_objects'=>$data['banner_type'],'navigate_object_id'=>$data['banner_list'],'impression_cost'=>$data['impression_cost'],'click_cost'=>$data['click_cost'],'frequency'=>$data['banner_frequency'],'display_type'=>$data['type'],'from_date'=>$data['from_date'],'to_date'=>$data['to_date'],'status'=>$data['status'],'beat_id'=>$data['beat_id'][0],'sort_order'=>$data['sort_order'],'updated_by'=>Session::get('userId')]);

                }         
                
              }
              return $message;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }

    }

    public function getbannerList($makeFinalSql, $orderBy, $page, $pageSize,$editdeletepermission){

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

        $concatQuery='';
      
        if($editdeletepermission==1){
        $concatQuery = "CONCAT('<center><code>',
            '<a href=\"/banners/editbanner/',banner_id,'\" onclick=\"updatePriceData(',banner_id,')\">
              <i class=\"fa fa-pencil\"></i>
              </a>&nbsp;&nbsp;&nbsp;
              <a href=\"javascript:void(0)\" onclick=\"deleteData(',banner_id,')\">
              <i class=\"fa fa-trash-o\"></i>
              </a>
            </code>
            </center>') 
            AS 'CustomAction', ";
       }
        $concatQuery.="CONCAT('<center><code>',
                 '<label class=\"switch\" style=\"float:right;\"><input class=\"switch-input block_users\" type=\"checkbox\" name=\"',banner_id,'\" id=\"',banner_id,'\" value=\"',banner_id,'\" ', IF(is_active=1,'checked',''),'><span class=\"switch-label\" data-on=\"Yes\" data-off=\"No\"></span><span class=\"switch-handle\"></span></label></code>
            </center>') 
            AS 'CustomActio',";

         
           


         $sqlQuery="select ".$concatQuery." banner_name,banner_id,frequency,IFNULL(getLeWhName(le_wh_id),'All') as lname,IFNULL(getLeWhName(hub_id),'All') as hname,click_cost,impression_cost,from_date,to_date,CASE when display_type=16601 then 'Banner' else 'Popup' END AS display_type from banner ";

         if($sqlWhrCls!='')
         {
            
              $sqlQuery.=$sqlWhrCls;
         }

         $sqlQuery.=" order by is_active desc";
                 
         $pageLimit = '';
        if($page!='' && $pageSize!='')
        {
            $pageLimit = " LIMIT " . (int)($page*$pageSize) . ", " . $pageSize;
        }

        $result = DB::select(DB::raw($sqlQuery . $pageLimit));

                        
       return json_decode(json_encode($result),true);


    }

    public function DeleteModelBanner($did)
    {

     try {
            $id = 0;
            $status = false; 
            $message = 'Unable to delete data please contact admin'; 

            if(!empty($did))
            {

                $message="Record Deleted Successfully";
                $bannerdelete = DB::table("banner")
                         ->where('banner_id', '=', $did)->delete();
             }

            return $message;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function GetType()
    {

      $gettype=DB::table("master_lookup")
                         ->select('value','master_lookup_name')
                         ->where('mas_cat_id','=','166')
                         ->where('is_active','=','1')
                         ->get()->all();
      return json_decode(json_encode($gettype));
    }


    public function GetBannerType()
    {

        $getbannertype=DB::table("master_lookup")
                         ->select('value','master_lookup_name')
                         ->where('mas_cat_id','=','167')
                         ->where('is_active','=','1')
                         ->get()->all();
        return json_decode(json_encode($getbannertype));
    }


     public function getAjaxHubsList($warehouse)
     {

            $dcs=explode(',', $warehouse);
            $result = DB::table("legalentity_warehouses")
                        ->Join('dc_hub_mapping','dc_hub_mapping.hub_id','=','legalentity_warehouses.le_wh_id')
                        ->select('*')
                        ->where('dc_hub_mapping.dc_id','=',$warehouse)
                        ->get()->all();            
       
       return json_decode(json_encode($result),true);



     }

     public function getAjaxBeatsList($hubid)
     {
           //DB::enableQueryLog();
            $hubs=explode(',',$hubid);
            $result = DB::table("pjp_pincode_area")
                        ->Join('legalentity_warehouses','pjp_pincode_area.le_wh_id','=','legalentity_warehouses.le_wh_id')
                        ->select('*')
                        ->whereIn('pjp_pincode_area.le_wh_id',$hubs)
                        ->get()->all();            
       //dd(DB::getQueryLog());
       return json_decode(json_encode($result),true);



     }

     public function BannerAjaxList($data){


        try{

            if($data['bannertype']==16703)
            {

                 $result = DB::table("products")
                        ->select('product_id','product_title')
                        ->where('is_active','=',1)
                        ->get()->all();      

            }elseif($data['bannertype']==16704)
            {

                 $result = DB::table("categories")
                        ->select('category_id','cat_name')
                        ->where('is_active','=',1)
                        ->get()->all();
            }elseif($data['bannertype']==16701)
            {

                $result = DB::table("legal_entities")
                        ->Join('products','products.manufacturer_id','=','legal_entities.legal_entity_id')
                        ->select('legal_entities.legal_entity_id','legal_entities.business_legal_name')
                        ->groupby('legal_entities.legal_entity_id')
                        ->get()->all(); 
            }elseif($data['bannertype']==16702)
            {

                 $result = DB::table('brands')
                            // ->where(['brands.legal_entity_id' => $legalEntityId]) removing legal entity check
                            ->select('brand_name', 'brand_id')      
                            ->groupBy('brand_id')
                            ->get()->all();
            }

            return json_decode(json_encode($result),true);
        }
        catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
     }


     public function GetDcHubMappings($dcid,$hubid)
     {
   
             $dcid=explode(',', $dcid);
             $hubid=trim($hubid,',');
             $hubid=explode(',', $hubid);
             $flag = 1;
             for($i=0;$i<count($dcid);$i++)
             {
                 //DB::enableQueryLog();
                  $dc_hubmap=DB::table('legalentity_warehouses')
                              ->join('dc_hub_mapping','dc_hub_mapping.hub_id','=','legalentity_warehouses.le_wh_id')
                              ->select('hub_id')
                              ->whereIn('dc_hub_mapping.hub_id',$hubid)
                              ->where('dc_hub_mapping.dc_id',$dcid[$i])
                              ->get()->all();
                  //dd(DB::getQueryLog());
                   if(empty($dc_hubmap))
                   {
                     $flag=0;
                   }
             }
               if($flag==1)
               {
                 return true;
               }else{
                return false;
               }

     }


     public function GetHubBeatMappings($hubs,$beats)
     {

             $hubs=trim($hubs,',');
             $hubs=explode(',', $hubs);
             $beats=trim($beats,',');
             $beats=explode(',', $beats);
             $flag = 1;
             for($i=0;$i<count($hubs);$i++)
             {
                 //DB::enableQueryLog();
                  $beat_hubmap=DB::table("pjp_pincode_area")
                            ->Join('legalentity_warehouses','pjp_pincode_area.le_wh_id','=','legalentity_warehouses.le_wh_id')
                            ->select('pjp_pincode_area_id')
                            ->whereIn('pjp_pincode_area.pjp_pincode_area_id',$beats)
                            ->where('pjp_pincode_area.le_wh_id',$hubs[$i])
                            ->get()->all();
                  //dd(DB::getQueryLog());
                   if(empty($beat_hubmap)){
                      $flag=0;
                   }
             }

             if($flag==1)
             {
                 return true;
               }else{
                return false;
               }

     }

     public function getCategoryName($id){

        $getname=DB::table('categories')
                    ->where('category_id',$id)
                    ->get()->all();

             return $getname;       
     }

     public function bannerImgUpdate($url,$bid){
       

     $updateurl=DB::table('banner')
                    ->where('banner_id','=',$bid) 
                    ->update(['banner_url'=>$url,'updated_by'=>Session::get('userId')]); 

       if($updateurl){
        return true;
       }             
     }

     public function getreportsData_forpopups($fdate,$tdate,$listids=NULL,$warehouse=0,$hubs=0,$beats=0,$flag=0){

        /*if($listids=='NULL'){
         $query = DB::selectFromWriteConnection(DB::raw("CALL getPopupHistoryDetails('".$fdate."','".$tdate."',".$listids.",'".$warehouse."','".$hubs."','".$beats."','".$flag."')"));
        }else{
      $query = DB::selectFromWriteConnection(DB::raw("CALL getPopupHistoryDetails('".$fdate."','".$tdate."','".$listids."','".$warehouse."','".$hubs."','".$beats."','".$flag."')"));
      } */

      if($flag==0){      
      
      $query = DB::selectFromWriteConnection(DB::raw("CALL getPopupHistoryDetails('".$fdate."','".$tdate."',".$listids.",NULL,NULL,NULL,'".$flag."')")); 
      
      }elseif($flag==1){
        
      $query = DB::selectFromWriteConnection(DB::raw("CALL getPopupHistoryDetails('".$fdate."','".$tdate."',".$listids.",".$warehouse.",".$hubs.",".$beats.",'".$flag."')")); 
     
      }elseif($flag==2){
        
      $query = DB::selectFromWriteConnection(DB::raw("CALL getPopupHistoryDetails('".$fdate."','".$tdate."',".$listids.",".$warehouse.",NULL,NULL,'".$flag."')")); 
     
       }elseif($flag==3){
        
      $query = DB::selectFromWriteConnection(DB::raw("CALL getPopupHistoryDetails('".$fdate."','".$tdate."',".$listids.",NULL,".$hubs.",NULL,'".$flag."')")); 
      
       }elseif($flag==4){
        
      $query = DB::selectFromWriteConnection(DB::raw("CALL getPopupHistoryDetails('".$fdate."','".$tdate."',".$listids.",NULL,NULL,".$beats.",'".$flag."')")); 
      
       }
      return $query;
    }

    public function getreportsData_forbanners($fdate,$tdate,$listids=NULL,$warehouse=NULL,$hubs=NULL,$beats=NULL,$flag=0){

      //echo "CALL getBannerHistoryDetails('".$fdate."','".$tdate."','".$listids."','".$warehouse."','".$hubs."','".$beats."','".$flag."')";exit;
        try{
      if($flag==0){      
      
      $query = DB::selectFromWriteConnection(DB::raw("CALL getBannerHistoryDetails('".$fdate."','".$tdate."',".$listids.",NULL,NULL,NULL,'".$flag."')")); 
      
      }elseif($flag==1){
        
      $query = DB::selectFromWriteConnection(DB::raw("CALL getBannerHistoryDetails('".$fdate."','".$tdate."',".$listids.",".$warehouse.",".$hubs.",".$beats.",'".$flag."')")); 
     
      }elseif($flag==2){
        
      $query = DB::selectFromWriteConnection(DB::raw("CALL getBannerHistoryDetails('".$fdate."','".$tdate."',".$listids.",".$warehouse.",NULL,NULL,'".$flag."')")); 
     
       }elseif($flag==3){
        
      $query = DB::selectFromWriteConnection(DB::raw("CALL getBannerHistoryDetails('".$fdate."','".$tdate."',".$listids.",NULL,".$hubs.",NULL,'".$flag."')")); 
      
       }elseif($flag==4){
        
      $query = DB::selectFromWriteConnection(DB::raw("CALL getBannerHistoryDetails('".$fdate."','".$tdate."',".$listids.",NULL,NULL,".$beats.",'".$flag."')")); 
      
       }
      return $query;
  
    }catch (\Exception $e) {
            Log::info($e->getMessage());
            Log::info($e->getTraceAsString());
            return "Sorry! Something Went Wrong. Please check the Logs for more details!";
        }
}

    public function changeBannerSts($bid,$sts){

       try{
        /*echo $bid;echo $sts;
        exit;*/
        //db::enableQueryLog();

        //getting is_active status of the current popup
        $checkpopups=DB::table('banner')
                        ->select('*')
                        ->where('banner_id','=',$bid)
                        ->get()->all(); 
            
          $checkpopups=json_decode(json_encode($checkpopups),true);

          //if current banner id is popup and is_active is true then setting popup to false
          if($checkpopups[0]['display_type']==16602 && $checkpopups[0]['is_active']==0){

                //if popup is_active is false and changing is_Active to true,check whethere there are any popups on same dc or le_wh_id, based on count
            //db::enableQueryLog();
            $le_wh_id=$checkpopups[0]['le_wh_id'];
            $checkactivepopups=DB::table('banner')
                               ->select(DB::raw('count(banner_id) as count'))
                               ->where('display_type','=',$checkpopups[0]['display_type'])
                               ->where(function($query) use($le_wh_id){
                                    $query->where('le_wh_id','=',0);
                                    if($le_wh_id!=0){
                                          $query = $query->orWhere('le_wh_id','=',$le_wh_id);
                                      }
                                      if($le_wh_id==0){
                                          $query = $query->orWhere('le_wh_id','!=',0);
                                      }
                                  });
                              $checkactivepopups=$checkactivepopups->where('is_active','=',1)
                               ->get()->all();
                               //dd(db::getQueryLog());
                $checkactivepopups=json_decode(json_encode($checkactivepopups),true);

                //if count is greater than or equal to 1 then is_active is not updated since there is already one active popup for dc
            if($checkactivepopups[0]['count']>=1){
                $result="Active Banners";
            }else{
                //if count is less than 1 then is_active status is changed
            $result=DB::table('banner')
                    ->where('banner_id','=',$bid)
                    ->update(['is_active'=>$sts,'updated_by'=>Session::get('userId')]);
 
            }

          }else{        
             $result=DB::table('banner')
                    ->where('banner_id','=',$bid)
                    ->update(['is_active'=>$sts,'updated_by'=>Session::get('userId')]);

                }
       return $result;
       } catch (\Exception $e) {
            Log::info($e->getMessage());
            Log::info($e->getTraceAsString());
            return "Sorry! Something Went Wrong. Please check the Logs for more details!";
        }
    }

    public function GetHubsByaccesslevel($dclist){
    $result=DB::table('legalentity_warehouses')
                ->select('le_wh_id','lp_wh_name')
                ->where(['legalentity_warehouses.dc_type'=>'118002'])
                ->where(['legalentity_warehouses.status'=>'1'])
                ->whereIn('legalentity_warehouses.le_wh_id',explode(',', $dclist['118002']))
                ->get()->all();
                return $result;
   } 

   public function checkActivePopupsByDC($type,$dcs,$sts){

    try{

      $flag="";
        for($i=0;$i<count($dcs);$i++){
            //db::enableQueryLog();
            $dcs=$dcs[$i];
           $result=DB::table('banner')
                   ->select(DB::raw('count(banner_id) as count'))
                   ->where('display_type','=',$type)
                   ->where(function($query) use($dcs){
                                    $query->where('le_wh_id','=',0);
                                    if($dcs!=0){
                                          $query = $query->orWhere('le_wh_id','=',$dcs);
                                      }
                                      if($dcs==0){
                                          $query = $query->orWhere('le_wh_id','!=',0);
                                      }
                                  });

                  $result=$result->where('is_Active','=',1)
                                 ->get()->all();
                  $result=json_decode(json_encode($result),true);

                  if($result[0]['count']>0){
                    $flag=1;
                  }
         //dd(db::getQueryLog());
               //if($result)    
        }
        return $flag;

    }catch (\Exception $e) {
            Log::info($e->getMessage());
            Log::info($e->getTraceAsString());
            return "Sorry! Something Went Wrong. Please check the Logs for more details!";
        }
   }

   
}
