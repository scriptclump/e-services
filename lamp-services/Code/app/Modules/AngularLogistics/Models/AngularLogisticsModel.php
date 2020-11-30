<?php

namespace App\Modules\AngularLogistics\Models;

use DB;
use Log;
use Illuminate\Database\Eloquent\Model;
use \Response;


class AngularLogisticsModel extends Model{

	public function __construct()
	{
    	/* 
         * The DC Type 118001, is to retrieve only Dc`s 
         */
		define("DC_TYPE",118001);
        /* 
         * The DC Type 118002, is to retrieve only HUB`s 
         */
        define("HUB_TYPE",118002);
        /* 
         * By Default, Its 1 to Denote Active Users
         */
        define("IS_ACTIVE",1);
        /* 
         * Flag to Show all Hubs Logistics Dashboard Data!
         */
		define("ANY_HUB",2);
	}

    /**
     * Action for WareHouse info by giving dc or hub id
     * @param  [int] $warehouse_id 
     * @param  [int] $warehouse_type 
     * @return  Array void
     */
    public function getWareHouseInfoById($warehouse_id,$warehouse_type)
    {
        $query = "
            SELECT
                le_wh_id AS 'Warehouse Id',
                lp_wh_name as 'Warehouse Name'
            FROM
                legalentity_warehouses
            WHERE status = 1";

        if($warehouse_type == "dc"){
            $query = $query." AND dc_type = ".DC_TYPE;
        }elseif ($warehouse_type == "hub") {
            $query = $query." AND dc_type = ".HUB_TYPE;
        }

        if(is_array($warehouse_id)){
            $query = $query." AND le_wh_id IN ($warehouse_id)";
        }

        if($warehouse_id != NULL or $warehouse_id != ''){
            $query = $query." AND le_wh_id = $warehouse_id";   
        }

        $result = DB::SELECT($query);
        return $result;
    }

    /**
     * Method to Call Logistics Procedure Code
     * @param  [int] $dc_id, [int] $hub_id, [date] $fromDate, [date] $toDate 
     * @return  Array void
     */
    public function getLogisticsData($fromDate, $toDate, $dcId = NULL, $hubId = NULL)
    {
        if($dcId == NULL)
            $dcId = "NULL";

        if($hubId == NULL)
            $hubId = "NULL";

        $query = "CALL getLogisticsDashboard_web(?,?,?,?,?)";
        $result = DB::SELECT($query,[$dcId,$hubId,$fromDate,$toDate,ANY_HUB]);
        
        if(empty($result))
            return false;
        else
            return $result;
    }

    /**
     * Method to Call all the Valid Operations Officers along with Warehouse IDs
     * Example : Delviery Boys, Pickers
     * @param  Role ID
     * @return  Array void
     */
    public function getAllOperationalOfficers($featureCode)
    {
        if(empty($featureCode))
            return [];
        $msdata = DB::table('master_lookup')
                ->select('master_lookup_id','description','value')
                ->where('value',78014)->first();
        $roletoIgnore = isset($msdata->description)?$msdata->description:'0';
        
        $query = 
            "SELECT
                user_roles.user_id AS 'UserId',
                CONCAT(users.firstname,' ',users.lastname) AS 'UserName',
                legalentity_warehouses.le_wh_id as 'WarehouseId'
            FROM
                user_roles
            JOIN users ON users.user_id = user_roles.user_id
            JOIN role_access ON role_access.role_id=user_roles.role_id
            JOIN features ON role_access.feature_id=features.feature_id            
            LEFT JOIN legalentity_warehouses ON legalentity_warehouses.bu_id = users.business_unit_id
            WHERE
                users.is_active = ? AND user_roles.role_id NOT IN ($roletoIgnore)";

        if(is_array($featureCode))
            $query.=" AND features.feature_code IN (".implode(",", $featureCode).")";
        else
            $query.=" AND features.feature_code = '$featureCode'";
        
        $query.=" group by user_roles.user_id";

        $result = DB::SELECT($query,[IS_ACTIVE]);
        
        if(empty($result))
            return [];
        else
            return $result;
    }

    public function getWorkingCapitalData($dc){

        $WorkingCapitalQuery=DB::selectFromWriteConnection(DB::raw("CALL getKPIWorkingCapital($dc)"));

        if(count($WorkingCapitalQuery)>0){
            return Response::json(array('status'=>'true','data'=>$WorkingCapitalQuery));
        }else{

             return Response::json(array('status'=>'false','data'=>'No data found!'));
        }

    }

    public function getDamageReportData($dc_id,$hub_id,$start_date,$end_date){

        $getDamageReport=DB::selectFromWriteConnection(DB::raw("CALL getKPIMissingDamageReport($dc_id,$hub_id,'$start_date','$end_date')"));
        if(count($getDamageReport)>0){
            return Response::json(array('status'=>true,'data'=>$getDamageReport));
        }else{

             return Response::json(array('status'=>false,'data'=>'No data found!'));
        }

    }

    public function getVehicleReportData($dc_id,$hub_id,$vehicle_id,$start_date,$end_date){

        $getVehicleReport=DB::selectFromWriteConnection(DB::raw("CALL getVehicleReport($dc_id,$hub_id,$vehicle_id,'$start_date','$end_date')"));
        if(count($getVehicleReport)>0){
            return Response::json(array('status'=>true,'data'=>$getVehicleReport));
        }else{
            return Response::json(array('status'=>false,'data'=>'No data found!'));
        }

    }

    public function getDnCLeaderdata($dc_id){

        $getDnCLeaderdata=DB::selectFromWriteConnection(DB::raw("CALL getKPI_DC_Leader($dc_id)"));
        if(count($getDnCLeaderdata)>0){
            return Response::json(array('status'=>true,'data'=>$getDnCLeaderdata));
        }else{

             return Response::json(array('status'=>false,'data'=>'No data found!'));
        }

    }

    public function getDeliveryLeaderdata($hub_id){

        $getDeliveryLeaderdata=DB::selectFromWriteConnection(DB::raw("CALL getKPI_DnC_Delivery_Leader($hub_id)"));
        if(count($getDeliveryLeaderdata)>0){
            return Response::json(array('status'=>true,'data'=>$getDeliveryLeaderdata));
        }else{

             return Response::json(array('status'=>false,'data'=>'No data found!'));
        }

    }

    public function getSalesLeaderdata($dc_id){

        $getDnCLeaderdata=DB::selectFromWriteConnection(DB::raw("CALL getKPI_DnC_Sales_Leader($dc_id)"));
        if(count($getDnCLeaderdata)>0){
            return Response::json(array('status'=>true,'data'=>$getDnCLeaderdata));
        }else{

             return Response::json(array('status'=>false,'data'=>'No data found!'));
        }

    }

    public function getPurchaseLeaderdata($dc_id){
        $getPurchaseLeaderdata=DB::selectFromWriteConnection(DB::raw("CALL getKPI_Purchase_Leader($dc_id)"));
        if(count($getPurchaseLeaderdata)>0){
            return Response::json(array('status'=>true,'data'=>$getPurchaseLeaderdata));
        }else{

             return Response::json(array('status'=>false,'data'=>'No data found!'));
        }
    }

    /**
     * Method to Call all the Valid Vehicle Numbers along with Warehouse IDs
     * Example : Vehicle Number
     * @return  Array void
     */
    public function getAllVehiclesData()
    {
        $query = 
            "SELECT vehicle.`vehicle_id`,vehicle.`reg_no` FROM vehicle WHERE is_active =1 AND vehicle_type = 156001
            UNION 
            SELECT vehicle.`vehicle_id`,vehicle.`reg_no` FROM vehicle WHERE is_active =1 AND vehicle_type != 156001 AND created_at BETWEEN CURDATE() AND NOW();";

        $result = DB::SELECT($query);
        
        if(empty($result))
            return [];
        else
            return $result;
    }

}