<?php

namespace App\Modules\Lp\Controllers;


use App\Http\Controllers\BaseController;
use Session;
use View;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use URL;
use Log;
use DB;
use Hash;
use Carbon\Carbon;

use Redirect;


use App\Modules\Lp\Models\LogisticsPartner;
use App\Modules\Lp\Models\lpWarehouses;

use App\Modules\Lp\Models\countries;
use App\Modules\Lp\Models\ZoneModel;

use Excel;
use Illuminate\Support\Facades\Config;
//use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;
class LogisticPartnersController extends BaseController {

    public function __construct() {   
        try
        {
            $this->middleware(function ($request, $next) {
                if (!Session::has('userId'))
                {
                    return Redirect::to('/');
                }
                return $next($request);
            });           
            parent::Title('LogisticPartners');
			
			$breadCrumbs = array('Home'=>url('/'),'Configuration'=>url('/'),'Logistic partners'=>'#');
			parent::Breadcrumbs($breadCrumbs);		

                //Please fill the grid filed name along with db table field name example 'gridid' => 'table.fieldname'
	
		$this->grid_field_db_match = array(	'LpID'      =>  'logistics_partners.lp_id',
							
							'LpName'    =>	'logistics_partners.lp_name',

							'LpServices'=>	'logistics_partners.services',

                                                        'WarehouseId'      =>  'lp_warehouses.lp_wh_id',
                                                        'WarehouseName'      =>  'lp_warehouses.lp_wh_name',
                                                        'WarehouseArea'      =>  'lp_warehouses.lp_wh_name',                                                 'WarehouseCity'      =>  'lp_warehouses.city',
                                                        'WarehouseEmail'      =>  'lp_warehouses.email',
                                                        'WarehousePhone'      =>  'lp_warehouses.phone_no',
														'LpFullService' =>  'logistics_partners.full_service',
                                                        'LpCODService'=>'logistics_partners.cod_service',
                                                        'LpForService'=>'logistics_partners.for_service'
						);
			
			
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }        
    }
	
	
    public function indexAction()
    {
        try
        {

        $states_data=$this->getStates();
        $countries_data=$this->getCountries();
        
        $countries = countries::all();
        $countries = json_decode(json_encode($countries),true);
        
        $states = ZoneModel::all();
        $states = json_decode(json_encode($states),true);

			$lp = new LogisticsPartner();
            $lplist = $lp::all();			
            return View::make('Lp::logisticslist',['lplist'=>$lplist,'states_data'=>$states_data,'countries_data'=>$countries_data,'states'=>$states,'countries'=>$countries]);			           
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    #retirieve all the logistics partners registered with fbe
    public function getLogisticPartners(){
        try {
            $breadCrumbs = array('Dashboard' => url('/'), 'Warehouses' => '#');
            parent::Breadcrumbs($breadCrumbs);
            $logisticpartners = DB::table('logistics_partners')->select('lp_id','lp_name','files as lp_logo','description')->get()->all();
            return $logisticpartners;

        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    # get the warehouses of a particular logistic partner based on lp_id
     public function getLpWarehouses($lp_id,$legal_id){
        try {
            $existing_warehouses = DB::table('legalentity_warehouses')->where('legal_entity_id',$legal_id)->where('lp_id',$lp_id)->pluck('lp_wh_id')->all();

            if(empty($existing_warehouses)){
                 $lp_warehouses = DB::table('lp_warehouses as lpw')
                                ->leftjoin('zone','zone.zone_id','=','lpw.state')
                                ->where('lpw.lp_id',$lp_id)
                                ->select('lpw.lp_id','lpw.lp_wh_id','lpw.city','lpw.lp_wh_name','lpw.address1','lpw.address2','lpw.pincode','lpw.state','zone.name as state')
                                ->get()->all();
            }
            else{
             $lp_warehouses = DB::table('lp_warehouses as lpw')
                                ->leftjoin('zone','zone.zone_id','=','lpw.state')
                                ->leftjoin('legalentity_warehouses as lew','lew.lp_wh_id','=','lpw.lp_wh_id')
                                ->where('lpw.lp_id',$lp_id)
                                ->where('lew.legal_entity_id',$legal_id)
                                ->select('lpw.lp_id','lpw.lp_wh_id','lpw.city','lpw.lp_wh_name','lpw.address1','lpw.address2','lpw.pincode','lpw.state','zone.name as state','lew.lp_wh_id as existing_id')
                                ->get()->all();
            }
             return $lp_warehouses;
            }
         catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
    
    public function getStates()
    {

        $states_data = DB::table('master_lookup_categories')
        ->join('master_lookup','master_lookup.mas_cat_id','=','master_lookup_categories.mas_cat_id')
        ->select('master_lookup.master_lookup_name as state_name','master_lookup.master_lookup_id as id')
        ->where('master_lookup_categories.mas_cat_id','=','40')
        ->where('master_lookup_categories.mas_cat_name','=','states')
        ->get()->all();
        return $states_data;
    }
    public function getCountries()
    {
        $countries_data = DB::table('master_lookup_categories')
        ->join('master_lookup','master_lookup.mas_cat_id','=','master_lookup_categories.mas_cat_id')
        ->select('master_lookup.master_lookup_name as country_name','master_lookup.master_lookup_id as id')
        ->where('master_lookup_categories.mas_cat_id','=','41')
        ->where('master_lookup_categories.mas_cat_name','=','countries')
        ->get()->all();
        return $countries_data;
    }


    /**
     * desc : Adds Logistic partner
     * @return void
     */


    public function addAction()
    {
        try
        {
        Session::forget('lp_id');		
        $states_data=$this->getStates();
        $countries_data=$this->getCountries();
        
        $countries = countries::all();
        $countries = json_decode(json_encode($countries),true);
        
        $states = ZoneModel::all();
        $states = json_decode(json_encode($states),true);
        
        $cur_path = Route::getFacadeRoot()->current()->uri();
        $cur_path = explode("/",$cur_path);
		
		$breadCrumbs = array('Home'=>url('/'),'Configuration'=>url('/'),'Logistic partners'=>url('/').'/logisticpartners','Add'=>'#');
		parent::Breadcrumbs($breadCrumbs);
        
        return View::make('Lp::addlp',['states_data'=>$states_data,'countries_data'=>$countries_data,'states'=>$states,'countries'=>$countries,'cur_path'=> $cur_path[1]]);
			
            //return View::make('logistipartners/add');
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }    

		
    /**
     * desc : Edit logistic partner
     * @return success
     */

    
    
 public function editAction($lpid)
    {
        try
        {
		Session::forget('lp_id');
		Session::put('lp_id',$lpid);			
			
        $states_data=$this->getStates();
        $countries_data=$this->getCountries();
        
        $countries = countries::all();
        $countries = json_decode(json_encode($countries),true);
        
        $states = ZoneModel::all();
        $states = json_decode(json_encode($states),true);
        
            $whs_count = 0;                                    
            $whs_count = DB::table('lp_warehouses')->where('lp_id',$lpid)->select('lp_id','lp_wh_id','city','lp_wh_name')->count();
            $lp = new LogisticsPartner();
            $logisticPartner = $lp::where('lp_id',$lpid)->first();
             $cur_path = Route::getFacadeRoot()->current()->uri();
            $cur_path = explode("/",$cur_path);
			
			$breadCrumbs = array('Home'=>url('/'),'Configuration'=>url('/'),'Logistic partners'=>url('/').'/logisticpartners','Edit'=>'#');
			parent::Breadcrumbs($breadCrumbs);
        
            return View::make('Lp::addlp',['logisticPartner'=>$logisticPartner,'cur_path'=> $cur_path[1],'states_data'=>$states_data,'countries'=>$countries,'states'=>$states,'countries_data'=>$countries_data,'whs_count'=>$whs_count]);
                //return View::make('logistipartners/edit');
            } catch (\ErrorException $ex) {
                Log::error($ex->getMessage());
                Log::error($ex->getTraceAsString());
            }
    }

    /**
     * desc : Deletes Logistic partner
     * @return void
     */
    
    public function deleteAction(Request $request)
    {
           try
        {
            
            $LpID=$request->LpID;
            $lp = new LogisticsPartner();
            $logisticPartner = $lp::where('lp_id',$LpID)->delete();
            echo 'true';

        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }
		
    /**
     * desc : Saves logistic partner
     * @return integer
     */

	
	public function saveAction(Request $request)
        {
            
            $files = ($request->hasFile('files'))?$request->file('files')->getClientOriginalName():array();
            $lp = new LogisticsPartner();
            
            if($request->hasFile('files'))
            {
                $destinationPath = $_SERVER['DOCUMENT_ROOT'] . '/uploads/logistic_partner/';

                $request->file('files')->move($destinationPath,$files);
            }
            
           
            if(isset($request->lp_id) && $request->lp_id!='')
            {

              $lp = $lp::where('lp_id',$request->lp_id)->first();
			  if($request->hasFile('files'))
			  {
                  $lp::where('lp_id', $request->lp_id)->update([
                    "lp_name"=> $request->lp_name,
                    "lp_legal_name"=> NULL,
                    "description"=> $request->description,
                    "address_1"=> $request->address_1,
                    "address_2"=> $request->address_2,
                    "city"=> $request->city,
                    "state"=> $request->state,
                    "country"=> $request->country,
                    "pincode"=> $request->pincode,
                    "phone"=> $request->phone,
                    "email"=> $request->email,
                    "website"=> $request->website,            
                    "files"=> $files,
                    "full_service"=> $request->full_service,
                    "for_service"=> $request->for_service,
                    "cod_service"=> $request->cod_service]);
              }
			  else{
        		    $lp::where('lp_id', $request->lp_id)->update([
                    "lp_name"=> $request->lp_name,
                    "lp_legal_name"=> NULL,
                    "description"=> $request->description,
                    "address_1"=> $request->address_1,
                    "address_2"=> $request->address_2,
                    "city"=> $request->city,
                    "state"=> $request->state,
                    "country"=> $request->country,
                    "pincode"=> $request->pincode,
                    "phone"=> $request->phone,
                    "email"=> $request->email,
                    "website"=> $request->website,            
                    "full_service"=> $request->full_service,
                    "for_service"=> $request->for_service,
                    "cod_service"=> $request->cod_service]);
			  }
            $logisticId = $request->lp_id;  
            }
            else
            {
                $lp->lp_name = $request->lp_name;
                $lp->lp_legal_name = NULL;
                $lp->description = $request->description;
                $lp->address_1 = $request->address_1;
                $lp->address_2 = $request->address_2;
                $lp->city = $request->city;
                $lp->state= $request->state;
                $lp->country = $request->country;
                $lp->pincode = $request->pincode;
                $lp->phone = $request->phone;
                $lp->email = $request->email;
                $lp->website = $request->website;
                $lp->files = $files;
                $lp->full_service = $request->full_service;
                $lp->for_service = $request->for_service;
                $lp->cod_service = $request->cod_service;
                //$lp->api_username = $request->api_username;            
                //$lp->api_password = Hash::make($request->api_password);
                //$lp->api_apikey = $request->api_apikey;               
            }
            $lp->save();
            
            if(!isset($logisticId))
            {
                $logisticId = $lp->id;
            }
			Session::forget('lp_id');
			Session::put('lp_id',$logisticId);
            return $logisticId;
        }
		
		
    /**
     * desc : Gets logistic partners
     * @return JSON
     */

	
		        public function getLpList(Request $request)
        {

            $page = $request->input('page');			//Page number
            $pageSize = $request->input('pageSize');	//Page size for ajax call
            $skip = $page*$pageSize;

            $lp = new LogisticsPartner();
            $query=$lp::select(['logistics_partners.lp_id as LpID','logistics_partners.lp_name as LpName','logistics_partners.files as LpLogo','logistics_partners.full_service as LpFullService','logistics_partners.cod_service as LpCODService','logistics_partners.for_service as LpForService',DB::raw('count(lp_warehouses.lp_id) as Warehouses'),DB::raw("CONCAT_WS(', ',IF(LENGTH(`logistics_partners`.`address_1`),`logistics_partners`.`address_1`,NULL),IF(LENGTH(`logistics_partners`.`address_2`),`logistics_partners`.`address_2`,NULL),IF(LENGTH(`logistics_partners`.`city`),`logistics_partners`.`city`,NULL),IF(LENGTH(`state`.`master_lookup_name`),`state`.`master_lookup_name`,NULL),IF(LENGTH(`country`.`master_lookup_name`),`country`.`master_lookup_name`,NULL),IF(LENGTH(`logistics_partners`.`pincode`),`logistics_partners`.`pincode`,NULL)) as LpAddress")]);
            
            $query->leftjoin('lp_warehouses','lp_warehouses.lp_id','=','logistics_partners.lp_id');
            $query->leftjoin('master_lookup as state','state.master_lookup_id','=','logistics_partners.state');
            $query->leftjoin('master_lookup as country','country.master_lookup_id','=','logistics_partners.country');
            
            
            if($request->input('$orderby'))				//checking for sorting
            {
                    $order = explode(' ',$request->input('$orderby'));

                    $order_query_field		=	 $order[0];	//on which field sorting need to be done
                    $order_query_type 		=	 $order[1];	//sort type asc or desc

                    $order_by_type = 'desc';

                    if($order_query_type=='asc')
                    {
                            $order_by_type = 'asc';
                    }	
                    
                    if(isset($this->grid_field_db_match[$order_query_field]))	//getting appropriate table field based on grid field
                    {
                        $order_by=$this->grid_field_db_match[$order_query_field];
                            $query->orderBy($order_by,$order_by_type);
                    }
 
            }
            
            
		if($request->input('$filter'))											//checking for filtering
		{
			
			$post_filter_query = explode(' and ',$request->input('$filter'));	//multiple filtering seperated by 'and'


			foreach($post_filter_query as $post_filter_query_sub)				//looping through each filter
			{
				$filter = explode(' ',$post_filter_query_sub);

				$filter_query_field		=	 $filter[0];
				$filter_query_operator 	=	 $filter[1];
				$filter_query_value 	=	 $filter[2];
				
				$filter_query_substr=substr($filter_query_field,0,7);
				
				if($filter_query_substr=='startsw' || $filter_query_substr=='endswit' || $filter_query_substr=='indexof' || $filter_query_substr=='tolower')
				//It's string filter,checking the filter is of type startwith,endswith,contains,doesn't contain,equals,doesn't eual
				{
				
					if($filter_query_substr=='startsw')
					{
						
						$filter_value_array = explode("'",$filter_query_field);		//extracting the input filter value between single quotes ex 'value'
						
						
						$filter_value=$filter_value_array[1].'%';
						
						
						foreach($this->grid_field_db_match as $key=>$value)			 
						{
														
							if(strpos($filter_query_field,'('.$key.')') != 0)		//getting the filter field name
							{
								$query->where($this->grid_field_db_match[$key],'like',$filter_value);
							}
						}
												
					}
				
				
					if($filter_query_substr=='endswit')
					{
						
						$filter_value_array = explode("'",$filter_query_field);		//extracting the input filter value between single quotes ex 'value'
						
						
						$filter_value='%'.$filter_value_array[1];
						
						
						//substr(strpos($info, '-', strpos($info, '-')+1)
						
						foreach($this->grid_field_db_match as $key=>$value)			 
						{
														
							if(strpos($filter_query_field,'('.$key.')') != 0)		//getting the filter field name
							{
								$query->where($this->grid_field_db_match[$key],'like',$filter_value);
							}
						}
												
					}

					
				
				
					if($filter_query_substr=='tolower')
					{

						$filter_value_array = explode("'",$filter_query_value);		//extracting the input filter value between single quotes ex 'value'
						
						$filter_value=$filter_value_array[1];

						if($filter_query_operator=='eq')
						{
							$like='=';
						}
						else
						{
							$like='!=';
						}
					
						
						//substr(strpos($info, '-', strpos($info, '-')+1)
						
						foreach($this->grid_field_db_match as $key=>$value)			 
						{
														
							if(strpos($filter_query_field,'('.$key.')') != 0)		//getting the filter field name
							{
								$query->where($this->grid_field_db_match[$key],$like,$filter_value);
							}
						}
												
					}

					if($filter_query_substr=='indexof')
					{
						
						$filter_value_array = explode("'",$filter_query_field);		//extracting the input filter value between single quotes ex 'value'
						
						$filter_value='%'.$filter_value_array[1].'%';

						if($filter_query_operator=='ge')
						{
							$like='like';
						}
						else
						{
							$like='not like';
						}
					
						
						//substr(strpos($info, '-', strpos($info, '-')+1)
						
						foreach($this->grid_field_db_match as $key=>$value)			 
						{
														
							if(strpos($filter_query_field,'('.$key.')') != 0)		//getting the filter field name
							{
								$query->where($this->grid_field_db_match[$key],$like,$filter_value);
							}
						}
												
					}
					
				}
				else
				{

					switch($filter_query_operator)
					{
						case 'eq' :

							$filter_operator = '=';

						break;

						case 'ne':
							
							$filter_operator = '!=';
							
						break;

						case 'gt' : 

							$filter_operator = '>';
						
						break;
						
						case 'lt' : 

							$filter_operator = '<';
						
						break;

						case 'ge' : 

							$filter_operator = '>=';
						
						break;
						
						case 'le' : 

							$filter_operator = '<=';
						
						break;
					}
					
					
						if(isset($this->grid_field_db_match[$filter_query_field]))	//getting appropriate table field based on grid field
						{
							$filter_field=$this->grid_field_db_match[$filter_query_field];
						}

						$query->where($filter_field,$filter_operator,$filter_query_value);
				
				}

				
			}
			
		}
            
            $query->groupBy('logistics_partners.lp_id');
            
            
            $query->skip($page*$pageSize)->take($pageSize);
            $lplist=$query->get()->all();

            foreach($lplist as $k=>$list)
            {
                $lplist[$k]['LpLogo']='<img src="'.url('/').'/uploads/logistic_partner/'.$lplist[$k]['LpLogo'].'" width="30" height="30"/>';
                $lplist[$k]['LpFullService']=($lplist[$k]['LpFullService']=='true') ? true : false;
                $lplist[$k]['LpForService']=($lplist[$k]['LpForService']=='true') ? true : false;
                $lplist[$k]['LpCODService']=($lplist[$k]['LpCODService']=='true') ? true : false;
                $lplist[$k]['Action']='<a data-toggle="modal" href="logisticpartners/edit/'.$lplist[$k]['LpID'].'"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp;<a class="deleteLogisticPartner" href="'.$lplist[$k]['LpID'].'"> <i class="fa fa-trash-o"></i> </a>';
            }
            
            $row_count  = $lp->count();
//            echo $query->toSql();
            echo json_encode(array('Records'=>$lplist,'TotalRecordsCount'=>$row_count));
            
        }

		
    /**
     * desc : Gets warehouse list based on logistic partner
     * @return JSON
     */

	
        
        public function getWarehouseList(Request $request)
        {

            try{
                
                $page = $request->input('page');			//Page number

                $pageSize = $request->input('pageSize');	//Page size for ajax call
                $skip = $page*$pageSize;

                $path = explode(':',$request->input('path'));
                $LpID = $path[1];

				
                $wh = new lpWarehouses();
				
                $query=$wh::select(['lp_warehouses.lp_wh_id as WarehouseId','lp_warehouses.lp_wh_name as WarehouseName',DB::raw('concat(lp_warehouses.address1,",",lp_warehouses.address2) as WarehouseArea'),'lp_warehouses.city as WarehouseCity','lp_warehouses.email as WarehouseEmail','lp_warehouses.phone_no as WarehousePhone'])->where('lp_warehouses.lp_id','=',$LpID)->get()->all();


                foreach($query as $key=>$value)
                {
                    $query[$key]['Action'] ='<a data-toggle="modal" onclick="editWarehoust('.$query[$key]['WarehouseId'].')"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp;<a class="delete" onclick="deleteWarehoust('.$query[$key]['WarehouseId'].')"> <i class="fa fa-trash-o"></i> </a>';
                }
				
				
                echo json_encode(array('Records'=>$query,'TotalRecordsCount'=>count($query)));
            } catch (\ErrorException $ex) {

                Log::error($ex->getMessage());
                Log::error($ex->getTraceAsString());

            }
            
        }

		
    /**
     * desc : Edits Warehouse
     * @return array
     */

	

        public function editWareHouseAction($wh_id)
        {   
		Session::put('warehouse_id',$wh_id);
		
            $wh = new lpWarehouses();
                $query=$wh->where('lp_wh_id',$wh_id)->get()->all();
				
                return $query;
				
        }
		
    /**
     * desc : Deletes warehouse
     * @return success
     */

        public function deleteWareHouseAction($wh_id)
        {   
		
            $warehouse = new lpWarehouses();
               
				$wh = $warehouse::where('lp_wh_id',$wh_id);
               $wh->delete();
                return '1';
				
        }
		
    /**
     * desc : Gets warehouse list based on the LP Id
     * @return JSON
     */

        public function getLpWarehouseList(Request $request,$LpID)
        {
            try{
                
                
                $page = $request->input('page');			//Page number

                $pageSize = $request->input('pageSize');	//Page size for ajax call
                $skip = $page*$pageSize;

                $wh = new lpWarehouses();
                $query=$wh::select(['lp_warehouses.lp_wh_id as WarehouseId','lp_warehouses.lp_wh_name as WarehouseName',DB::raw("CONCAT_WS(', ',IF(LENGTH(`lp_warehouses`.`address1`),`lp_warehouses`.`address1`,NULL),IF(LENGTH(`lp_warehouses`.`address2`),`lp_warehouses`.`address2`,NULL)) as WarehouseArea"),'lp_warehouses.city as WarehouseCity','lp_warehouses.email as WarehouseEmail','lp_warehouses.phone_no as WarehousePhone']);
                $query->where('lp_warehouses.lp_id','=',$LpID);

                if($request->input('$orderby'))				//checking for sorting
            {
                    $order = explode(' ',$request->input('$orderby'));

                    $order_query_field		=	 $order[0];	//on which field sorting need to be done
                    $order_query_type 		=	 $order[1];	//sort type asc or desc

                    $order_by_type = 'desc';

                    if($order_query_type=='asc')
                    {
                            $order_by_type = 'asc';
                    }	
                    
                    if(isset($this->grid_field_db_match[$order_query_field]))	//getting appropriate table field based on grid field
                    {
                        $order_by=$this->grid_field_db_match[$order_query_field];
                            $query->orderBy($order_by,$order_by_type);
                    }
 
            }
            
            
		if($request->input('$filter'))											//checking for filtering
		{
			
			$post_filter_query = explode(' and ',$request->input('$filter'));	//multiple filtering seperated by 'and'


			foreach($post_filter_query as $post_filter_query_sub)				//looping through each filter
			{
				$filter = explode(' ',$post_filter_query_sub);

				$filter_query_field		=	 $filter[0];
				$filter_query_operator 	=	 $filter[1];
				$filter_query_value 	=	 $filter[2];
				
				$filter_query_substr=substr($filter_query_field,0,7);
				
				if($filter_query_substr=='startsw' || $filter_query_substr=='endswit' || $filter_query_substr=='indexof' || $filter_query_substr=='tolower')
				//It's string filter,checking the filter is of type startwith,endswith,contains,doesn't contain,equals,doesn't eual
				{
				
					if($filter_query_substr=='startsw')
					{
						
						$filter_value_array = explode("'",$filter_query_field);		//extracting the input filter value between single quotes ex 'value'
						
						
						$filter_value=$filter_value_array[1].'%';
						
						
						foreach($this->grid_field_db_match as $key=>$value)			 
						{
														
							if(strpos($filter_query_field,'('.$key.')') != 0)		//getting the filter field name
							{
								$query->where($this->grid_field_db_match[$key],'like',$filter_value);
							}
						}
												
					}
				
				
					if($filter_query_substr=='endswit')
					{
						
						$filter_value_array = explode("'",$filter_query_field);		//extracting the input filter value between single quotes ex 'value'
						
						
						$filter_value='%'.$filter_value_array[1];
						
						
						//substr(strpos($info, '-', strpos($info, '-')+1)
						
						foreach($this->grid_field_db_match as $key=>$value)			 
						{
														
							if(strpos($filter_query_field,'('.$key.')') != 0)		//getting the filter field name
							{
								$query->where($this->grid_field_db_match[$key],'like',$filter_value);
							}
						}
												
					}

					
				
				
					if($filter_query_substr=='tolower')
					{

						$filter_value_array = explode("'",$filter_query_value);		//extracting the input filter value between single quotes ex 'value'
						
						$filter_value=$filter_value_array[1];

						if($filter_query_operator=='eq')
						{
							$like='=';
						}
						else
						{
							$like='!=';
						}
					
						
						//substr(strpos($info, '-', strpos($info, '-')+1)
						
						foreach($this->grid_field_db_match as $key=>$value)			 
						{
														
							if(strpos($filter_query_field,'('.$key.')') != 0)		//getting the filter field name
							{
								$query->where($this->grid_field_db_match[$key],$like,$filter_value);
							}
						}
												
					}

					if($filter_query_substr=='indexof')
					{
						
						$filter_value_array = explode("'",$filter_query_field);		//extracting the input filter value between single quotes ex 'value'
						
						$filter_value='%'.$filter_value_array[1].'%';

						if($filter_query_operator=='ge')
						{
							$like='like';
						}
						else
						{
							$like='not like';
						}
					
						
						//substr(strpos($info, '-', strpos($info, '-')+1)
						
						foreach($this->grid_field_db_match as $key=>$value)			 
						{
														
							if(strpos($filter_query_field,'('.$key.')') != 0)		//getting the filter field name
							{
								$query->where($this->grid_field_db_match[$key],$like,$filter_value);
							}
						}
												
					}
					
				}
				else
				{

					switch($filter_query_operator)
					{
						case 'eq' :

							$filter_operator = '=';

						break;

						case 'ne':
							
							$filter_operator = '!=';
							
						break;

						case 'gt' : 

							$filter_operator = '>';
						
						break;
						
						case 'lt' : 

							$filter_operator = '<';
						
						break;

						case 'ge' : 

							$filter_operator = '>=';
						
						break;
						
						case 'le' : 

							$filter_operator = '<=';
						
						break;
					}
					
					
						if(isset($this->grid_field_db_match[$filter_query_field]))	//getting appropriate table field based on grid field
						{
							$filter_field=$this->grid_field_db_match[$filter_query_field];
						}

						$query->where($filter_field,$filter_operator,$filter_query_value);
				
				}

				
			}
			
		}
                
                $before = $query;
                
                $RowCount = $before->count();
                
                $query->skip($page*$pageSize)->take($pageSize);

                
                 $wh_list=$query->get()->all();
               
                
                
                foreach($wh_list as $key=>$value)
                {
                    $wh_list[$key]['Action'] ='<a data-toggle="modal" onclick="editWarehoust('.$wh_list[$key]['WarehouseId'].')"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp;<a class="delete" onclick="deleteWarehoust('.$wh_list[$key]['WarehouseId'].')"> <i class="fa fa-trash-o"></i> </a>';
                }
                
                
                
                
                
                
                echo json_encode(array('Records'=>$wh_list,'TotalRecordsCount'=>$RowCount));
                
            } catch (\ErrorException $ex) {

                Log::error($ex->getMessage());
                Log::error($ex->getTraceAsString());

            }
        }
		
		
    /**
     * desc : Downloads logistic partners and warehouses information in excel format
     * @return void
     */
    public function downloadExcel($type, $lp_id) {
        ini_set('memory_limit', -1);
        $warehouse = new lpWarehouses();
        $data3 = array(array('WarehouseName(Required)'=>'', 'ContactName(Required)'=>'','Address(Required)'=>'', 'PhoneNumber(Required)'=>'', 'Email(Required)'=>'', 'Country(Required)'=>'', 'State(Required)'=>'', 'City(Required)'=>'', 'Pincode(Required)'=>'', 'Longitude(Required)'=>'', 'Latitude(Required)'=>''));
        $data = $warehouse::where('lp_id', $lp_id)->select('lp_wh_name AS WarehouseName(Required)', 'contact_name AS ContactName(Required)', 'address1 AS Address(Required)', 'phone_no AS PhoneNumber(Required)', 'email AS Email(Required)', 'country AS Country(Required)', 'state AS State(Required)', 'city As City(Required)', 'pincode AS Pincode(Required)', 'longitude AS Longitude(Required)', 'latitude AS Latitude(Required)')->get()->all();
        $data2 = array(array('lp_id'=>'',$lp_id=>''));
        if(empty($data))
        {
            $data = $data3;
        }
        //$data3 = array(array('Required', 'Required', 'Required', 'Required', 'Required', 'Required', 'Required', 'Required', 'Required', 'Required','Required'));

        //$data3 = array(array('Logistic Partner ID'=>'', $lp_id=>''));
        //$data4 = array('Logistic Partner Ware House Code', 'Address', 'Phone Number', 'Email', 'Country', 'State', 'City', 'Pincode', 'Longitude', 'Latitude', 'LandMark');
        
        $states = $this->getStates();
        $countries = $this->getCountries();
        
        
        foreach($data as $key=>$wh)
        {
            foreach($states as $state)
            {                                     
                if($wh['State(Required)'] == $state->id)
                {
                    $data[$key]['State(Required)'] = $state->state_name;
                }              
            }
            
            foreach($countries as $country)
            {                                     
                if($wh['Country(Required)'] == $country->id)
                {
                    $data[$key]['Country(Required)'] = $country->country_name;
                }              
            }
            
        }
        
        
        $sheetName = "warehouse_export_".Carbon::now();
        return Excel::create($sheetName, function($excel) use ($data, $data2) {

                    $excel->sheet('warehouse_export', function($sheet) use ($data, $data2) {
                        $sheet->fromArray($data2);
                        //$sheet->fromArray($data3);
                        $sheet->fromArray($data);
                    });
                })->download($type);
    }

		
    /**
     * desc : Downloads sample excel format which is used to upload logistic partners and warehouses
     * @return void
     */

        
        public function downloadTemplate($type)
	{
                //$data = array(array('Required'=>'', 'Required'=>'', 'Required'=>'', 'Required'=>'', 'Required'=>'', 'Required'=>'', 'Required'=>'', 'Required'=>'', 'Required'=>'', 'Required'=>'', 'Required'=>''));
                $data2 = array(array('WarehouseName(Required)'=>'', 'ContactName(Required)'=>'','Address(Required)'=>'', 'PhoneNumber(Required)'=>'', 'Email(Required)'=>'', 'Country(Required)'=>'', 'State(Required)'=>'', 'City(Required)'=>'', 'Pincode(Required)'=>'', 'Longitude(Required)'=>'', 'Latitude(Required)'=>''));
		
                $sheetName = "warehouse_import_".Carbon::now();
		return Excel::create($sheetName, function($excel) use ($data2) {
		
			$excel->sheet('Warehouse_import', function($sheet) use ($data2)
	        {
	           // $sheet->fromArray($data);
                    $sheet->fromArray($data2);
				
	        });
		})->download($type);
	}
        
		
    /**
     * desc : Imports warehouse information from excel
     * @return string
     */

	
        public function importExcel($lp_id) {
            //if(!isset($lp_id))
            //{
            //    echo "Uploading warehouses is unsuccessful. Please create a Logistic partner"; exit;
            //}
         $save = array();   
        if (Input::hasFile('import_file')) {
            $path = Input::file('import_file')->getRealPath();
            $ext = Input::file('import_file')->getClientOriginalExtension();
            if ($ext != "xls") {
                echo "Invalid Format!";
                exit;
            } else {
                
                $headerRowNumber = 1;
                Config::set('excel.import.startRow', $headerRowNumber);
                $states = $this->getStates();
                $countries = $this->getCountries();
                $data = Excel::load($path, function($reader) {                       
                        })->get()->all();
                $insertedCount = 0;
                
                if (!empty($data) && $data->count()) {
                    foreach ($data as $key => $value) {

                        $warehouse = new lpWarehouses();
                        $whexists = new lpWarehouses();
                        $warehouse->lp_id = $lp_id;
                        $where = ['lp_wh_name'=>$value->warehousenamerequired,'lp_id'=>$lp_id];
                        $whexists = $warehouse->where($where)->first();
                        //echo $value->warehousenamerequired; print_r($whexists->lp_wh_id); die;
                        $error = '';
                        if(preg_match('/^[1-9][0-9]{5}$/', $value->pincoderequired) == 0)
                        {
                          $error = "Pincode Invalid";  
                        }
                        if(preg_match('/^[a-zA-Z ]+$/', $value->contactnamerequired) == 0)
                        {
                          $error = "ContactName Invalid";  
                        }
                        if(preg_match('/^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/', $value->emailrequired) == 0)
                        {
                           $error =  "Email Invalid";
                        }
                        if(preg_match('/^[1-9][0-9]{9,11}$/', $value->phonenumberrequired) == 0)
                        {
                             $error =  "Phone Number Invalid";
                        }
                        if(preg_match('/^[1-9][0-9]{5}$/', $value->pincoderequired) == 0)
                        {
                            $error =  "Pincode Invalid";
                        }
                        if(preg_match('/^[a-zA-Z ]*\z/', $value->cityrequired) == 0)
                        {
                            $error =  "City Invalid";
                        }
                        if(preg_match('/^[a-zA-Z ]*\z/', $value->staterequired) == 0)
                        {
                           $error =  "State Invalid"; 
                        }
                        if(preg_match('/^[a-zA-Z ]*\z/', $value->countryrequired) == 0)
                        {
                            $error =  "Country Invalid";
                        }
                        if($value->latituderequired != '' && (preg_match('/^-?([1-8]?[1-9]|[1-9]0)\.{1}\d{1,6}/', $value->latituderequired) == 0))
                        {
                            $error =  "Latitude Invalid";
                        }
                        if($value->longituderequired != '' && (preg_match('/^-?([1-8]?[1-9]|[1-9]0)\.{1}\d{1,6}/', $value->longituderequired) == 0))
                        {
                            $error =  "Longitude Invalid";
                        }
                        if(isset($whexists) && $whexists->lp_wh_id)
                        {
                           $error =  "Warehouse Name Should Be Unique";
                        }
                        if((isset($whexists) && $whexists->lp_wh_id) || ($value->warehousenamerequired=='') || ($value->contactnamerequired=='') || ($value->addressrequired=='') || ($value->phonenumberrequired=='') || ($value->emailrequired=='') || ($value->countryrequired=='') || ($value->staterequired=='') || ($value->cityrequired=='') || ($value->pincoderequired=='') || (preg_match('/^[a-zA-Z ]+$/', $value->contactnamerequired)==0) || (preg_match('/^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/', $value->emailrequired)==0) || (preg_match('/^[1-9][0-9]{9,11}$/', $value->phonenumberrequired)==0) || (preg_match('/^[1-9][0-9]{5}$/', $value->pincoderequired)==0) || (preg_match('/^[a-zA-Z ]*\z/', $value->cityrequired)==0) || (preg_match('/^[a-zA-Z ]*\z/', $value->staterequired)==0) || (preg_match('/^[a-zA-Z ]*\z/', $value->countryrequired)==0) || (preg_match('/^-?([1-8]?[1-9]|[1-9]0)\.{1}\d{1,6}/', $value->longituderequired)==0 && $value->longituderequired != '') || (preg_match('/^-?([1-8]?[1-9]|[1-9]0)\.{1}\d{1,6}/', $value->latituderequired)==0 && $value->latituderequired != '') )
                        {
                            continue;                            
                        }
                        $warehouse->lp_wh_name = $value->warehousenamerequired;
                        $warehouse->contact_name = $value->contactnamerequired;
                        $warehouse->address1 = $value->addressrequired;
                        $warehouse->phone_no = $value->phonenumberrequired;
                        $warehouse->email = $value->emailrequired;
                        foreach($countries as $country)
                        {
                            if(strtolower($country->country_name) == strtolower($value->countryrequired))
                            {
                                $warehouse->country = $country->id;
                            }
                        }
                        foreach($states as $state)
                        {
                            if(strtolower($state->state_name) == strtolower($value->staterequired))
                            {
                                $warehouse->state = $state->id;
                            }
                        }                        
                        
                        $warehouse->city = $value->cityrequired;
                        $warehouse->pincode = $value->pincoderequired;
                        $warehouse->longitude = $value->longituderequired;
                        $warehouse->latitude = $value->latituderequired;
                        //print_r($warehouse); die;
                        $save[] = $warehouse->save();
                        $insertedCount++;
                    }
                    if (!$save) {
                        echo "Uploading warehouses is unsuccessful. Please check xls"."(".$error.")";
                    } else {
                        echo "Sucessfully inserted " . $insertedCount . " Record(s)";
                    }
                }
            }
        } else {
            echo "Please Select File";
            exit;
        }
        //return back();
    }

    /**
     * desc : Saves logistic partner
     * @return integer
     */

	

    public function saveWarehouseAction(Request $request)
    {
        $warehouse = new lpWarehouses();
        $whLpId = Session('lp_id');		
		$whId = Session('warehouse_id');
                $status = $request->status;
		
		if($whId && $status == "EDIT")
		{
			//$warehouse = $warehouse::where('lp_wh_id',$whId)->first();
			$warehouse::where('lp_wh_id', $whId)->update([
 			'lp_wh_name' => $request->wh_name,
			'contact_name' => $request->wh_cont_name,
			'email' => $request->wh_email,
			'phone_no' => $request->wh_phone,
			'address1' => $request->wh_address1,
			'address2' => $request->wh_address2,
			'pincode' => $request->wh_pincode,
			'city' => $request->wh_city,
			'state' => $request->wh_state,
			'country' => $request->wh_country,
			'latitude' => $request->wh_lat,
			'longitude' => $request->wh_log
			]);
			Session::forget('warehouse_id');
			return "1";
		}
		else{
			
		if($whLpId)
			{//print_r($request->wh_lp_id); die;
			$warehouse->lp_id = $whLpId;	
			$warehouse->lp_wh_name = $request->wh_name;
			$warehouse->contact_name = $request->wh_cont_name;
			$warehouse->email = $request->wh_email;
			$warehouse->phone_no = $request->wh_phone;
			$warehouse->address1 = $request->wh_address1;
			$warehouse->address2 = $request->wh_address2;
			$warehouse->pincode = $request->wh_pincode;
			$warehouse->city = $request->wh_city;
			$warehouse->state = $request->wh_state;
			$warehouse->country = $request->wh_country;
			$warehouse->latitude = $request->wh_lat;
			$warehouse->longitude = $request->wh_log;
			$warehouse->save();
			return "1";
			}
			else
			{
				return "0";
			}  
        }
    }
	
		
    /**
     * desc : Edits warehouse information
     * @return bool
     */

        
	public function warehouseEdit()
	{
		$whId = Session('warehouse_id'); 
		$warehouse = new lpWarehouses();
		if($whId)
		{
			//$warehouse = $warehouse::where('lp_wh_id',$whId)->first();
			$warehouse::where('lp_wh_id', $whId)->update([
 			'lp_wh_name' => $request->wh_name,
			'contact_name' => $request->wh_cont_name,
			'email' => $request->wh_email,
			'phone_no' => $request->wh_phone,
			'address1' => $request->wh_address1,
			'address2' => $request->wh_address2,
			'pincode' => $request->wh_pincode,
			'city' => $request->wh_city,
			'state' => $request->wh_state,
			'country' => $request->wh_country,
			'latitude' => $request->wh_lat,
			'longitude' => $request->wh_log
			]);
			Session::forget('warehouse_id');
			return "1";
		}
	}
        
        public function warehuniq(Request $request, $lp_id)
        {
                        $warehouse = new lpWarehouses();
                        $whexists = new lpWarehouses();
                        $whId = Session('warehouse_id');
                        $warehouse->lp_id = $lp_id;
                        $where = ['lp_wh_name'=>$request->input('wh_name'),'lp_id'=>$lp_id];
                        $whexists = $warehouse->where($where)->first();
                        //echo $request->wh_name; print_r($whexists->lp_wh_id); die;
                        if(empty($whId) && isset($whexists) && $whexists->lp_wh_id)
                        {
                            echo "false";
                            exit;
                        }
                        if(isset($whId) && ($whexists->lp_wh_id != $whId))
                        {
                            echo "false";
                            exit;
                        }
                        else
                        {
                            echo "true";
                        }    
        }
          
        
        function googlepincode(Request $resquest)
        {
        $pincode = $resquest->pincode;    
        $url = "http://maps.googleapis.com/maps/api/geocode/json?address=".$pincode."&region=in";
        $callType = "GET";
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

        return $output;
        }
}
