<?php

namespace App\Modules\Banners\Models;
use Illuminate\Database\Eloquent\Model;
use App\Central\Repositories\ProductRepo;
use DB;
use Log;
use URL;
use Session;
use Carbon;

class Sponsor extends Model
{
    protected $table = 'sponsors';
    protected $primaryKey = 'sponsor_id';
    
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


    public function EditSponsor($id){

           $editdata=DB::table("sponsors")
                         ->select('*')
                         ->where('sponsor_id','=',$id)
                         ->get()->all();
 
           return json_decode(json_encode($editdata),true);
    }

    public function getsponsorList($makeFinalSql, $orderBy, $page, $pageSize,$editdeletepermission){

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
            '<a href=\"/sponsors/editsponsor/',sponsor_id,'\" onclick=\"updatePriceData(',sponsor_id,')\">
              <i class=\"fa fa-pencil\"></i>
              </a>&nbsp;&nbsp;&nbsp;
              <a href=\"javascript:void(0)\" onclick=\"deleteData(',sponsor_id,')\">
              <i class=\"fa fa-trash-o\"></i>
              </a>
            </code>
            </center>') 
            AS 'CustomAction', ";
       }
        
        $concatQuery.="CONCAT('<center><code>',
                 '<label class=\"switch\" style=\"float:right;\"><input class=\"switch-input block_users\" type=\"checkbox\" name=\"',sponsor_id,'\" id=\"',sponsor_id,'\" value=\"',sponsor_id,'\" ', IF(status=1,'checked',''),'><span class=\"switch-label\" data-on=\"Yes\" data-off=\"No\"></span><span class=\"switch-handle\"></span></label></code>
            </center>') 
            AS 'CustomActio',";


         $sqlQuery="select ".$concatQuery." sponsor_name,sponsor_id,frequency,IFNULL(getLeWhName(le_wh_id),'All') as lname,IFNULL(getLeWhName(hub_id),'All') as hname,click_cost,impression_cost,from_date,to_date,status from sponsors";

         if($sqlWhrCls!=''){
            
              $sqlQuery.=$sqlWhrCls;
         }
           
         $sqlQuery.=" order by status desc";        
         $pageLimit = '';
        if($page!='' && $pageSize!=''){
            $pageLimit = " LIMIT " . (int)($page*$pageSize) . ", " . $pageSize;
        }

        $result = DB::select(DB::raw($sqlQuery . $pageLimit));

                        
       return json_decode(json_encode($result),true);


    }

    public function DeleteSponsorModel($did){

     try {
            $id = 0;
            $status = false; 
            $message = 'Unable to delete data please contact admin'; 

            if(!empty($did)){

            $message="Record Deleted Successfully";
            $bannerdelete = DB::table("sponsors")
                         ->where('sponsor_id', '=', $did)->delete();
             }

            return $message;
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }


    public function GetType(){

      $gettype=DB::table("master_lookup")
                         ->select('value','master_lookup_name')
                         ->where('mas_cat_id','=','166')
                         ->get()->all();
      return json_decode(json_encode($gettype));
    } 

    public function GetBannerType(){

        $getbannertype=DB::table("master_lookup")
                         ->select('value','master_lookup_name')
                         ->where('mas_cat_id','=','167')
                         ->get()->all();
      return json_decode(json_encode($getbannertype));
    }

    public function getreportsData_forsponsors($fdate,$tdate,$listids=NULL,$warehouse=0,$hubs=0,$beats=0,$flag=0){
        /*if($listids='NULL'){
        $query = DB::selectFromWriteConnection(DB::raw("CALL getSponsorHistoryDetails('".$fdate."','".$tdate."',".$listids.",'".$warehouse."','".$hubs."','".$beats."','".$flag."')"));
        }else{

      $query = DB::selectFromWriteConnection(DB::raw("CALL getSponsorHistoryDetails('".$fdate."','".$tdate."','".$listids."','".$warehouse."','".$hubs."','".$beats."','".$flag."')")); 
  }*/

       
      if($flag==0){      
      
      $query = DB::selectFromWriteConnection(DB::raw("CALL getSponsorHistoryDetails('".$fdate."','".$tdate."',".$listids.",NULL,NULL,NULL,'".$flag."')")); 
      
      }elseif($flag==1){
        
      $query = DB::selectFromWriteConnection(DB::raw("CALL getSponsorHistoryDetails('".$fdate."','".$tdate."',".$listids.",".$warehouse.",".$hubs.",".$beats.",'".$flag."')")); 
     
      }elseif($flag==2){
        
      $query = DB::selectFromWriteConnection(DB::raw("CALL getSponsorHistoryDetails('".$fdate."','".$tdate."',".$listids.",".$warehouse.",NULL,NULL,'".$flag."')")); 
     
       }elseif($flag==3){
        
      $query = DB::selectFromWriteConnection(DB::raw("CALL getSponsorHistoryDetails('".$fdate."','".$tdate."',".$listids.",NULL,".$hubs.",NULL,'".$flag."')")); 
      
       }elseif($flag==4){
        
      $query = DB::selectFromWriteConnection(DB::raw("CALL getSponsorHistoryDetails('".$fdate."','".$tdate."',".$listids.",NULL,NULL,".$beats.",'".$flag."')")); 
      
       }
      return $query;
    }   

    public function changeSponsorSts($bid,$sts){

       try{
        //db::enableQueryLog();
        $result=DB::table('sponsors')
                    ->where('sponsor_id','=',$bid)
                    ->update(['status'=>$sts,'updated_by'=>Session::get('userId')]);
                    //dd(db::getQueryLog());die();
       return $result;
       } catch (\Exception $e) {
            Log::info($e->getMessage());
            Log::info($e->getTraceAsString());
            return "Sorry! Something Went Wrong. Please check the Logs for more details!";
        }
    }
}
