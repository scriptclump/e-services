<?php

namespace App\Modules\Indent\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use App\Modules\Roles\Models\Role;

class LegalEntity extends Model {
	
	
	public function getWarehouseById($leWarehouseId) {
        try {
			$fieldArr = array('warehouse.*', 'countries.name as country_name', 
                            'zone.name as state_name', 'zone.code as state_code','legal_entities.business_legal_name','legal_entities.fssai',
                            "warehouse.tin_number as gstin","legal_entities.legal_entity_type_id","legal_entities.fssai as fssai");
			$query = DB::table('legalentity_warehouses as warehouse')->select($fieldArr);
            $query->leftJoin('legal_entities', 'legal_entities.legal_entity_id', '=', 'warehouse.legal_entity_id');
            $query->leftJoin('countries', 'countries.country_id', '=', 'warehouse.country');
            $query->leftJoin('zone', 'zone.zone_id', '=', 'warehouse.state');
			$query->where('warehouse.le_wh_id', $leWarehouseId);			
			return $query->first();
		} 
		catch (Exception $e) {
			Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
    }
   
    public function getLegalEntity($entityId=0,$letype='') {
        try {
			$fieldArr = array('legal2.business_legal_name', 'legal2.legal_entity_id');
			$query = DB::table('legal_entities as legal1')->select($fieldArr);
			$query->leftJoin('legal_entities as legal2','legal1.legal_entity_id','=','legal2.parent_id');
            if($letype!=''){
               $query->leftjoin("users",'users.legal_entity_id','=','legal1.legal_entity_id'); 
            }
			$query->where('legal2.parent_id', $entityId);
			$query->where('legal2.is_approved', 1);
            if($letype!=''){
               $query->where('legal2.legal_entity_type_id',$letype); 
               $query->where('users.is_active',1);
               $query->groupby('legal2.legal_entity_id');
               $query->orderby('legal2.business_legal_name');
            }   			
			$allLegalEntityArr = $query->get()->all();
			
			$legalEntityArr = array();
			if(is_array($allLegalEntityArr)) {
				foreach($allLegalEntityArr as $data){
					$legalEntityArr[$data->legal_entity_id] = $data->business_legal_name;
				}
			}
			
			return $legalEntityArr;
		} 
		catch (Exception $e) {
			Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
    }
    
    /*
	 * getLegalEntityById() method is used to fetch legal entity detail by id
	 * @param $legalEntityId Integer
	 * @return Array
	 */ 
	
	public function getLegalEntityById($legalEntityId) {
		try{
			$fieldArr = array(
							'legal.business_legal_name',
                            'legal.logo',
							'legal.address1',
							'legal.address2',
							'legal.city',
							'legal.logo',
							'legal.pincode',
							'legal.pan_number',
							'legal.tin_number', 
                            'legal.gstin',
                            'legal.fssai',  
                            'legal.legal_entity_id', 
							'countries.name as country_name', 
                            'zone.name as state_name',
                            'zone.name as state',
							'zone.code as state_code'
						);
			
			$query = DB::table('legal_entities as legal')->select($fieldArr);
			$query->leftJoin('countries', 'countries.country_id', '=', 'legal.country');
			$query->leftJoin('zone', 'zone.zone_id', '=', 'legal.state_id');
			$query->where('legal.legal_entity_id', $legalEntityId);
			return $query->first();
		}
		catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
		}
	}
        /*
     * getAllLegalEntities() method is used to fetch legal entity details
     * @param 
     * @return Array
     */

    public function getAllLegalEntities() {
        try {
            $fieldArr = array(
                'legal.legal_entity_id',
                'legal.business_legal_name',
                'legal.address1',
                'legal.address2',
                'legal.city',
                'legal.pincode',
                'legal.pan_number',
                'legal.tin_number',
                'countries.name as country_name',
                'zone.name as state_name'
            );

            $query = DB::table('legal_entities as legal')->select($fieldArr);
            $query->leftJoin('countries', 'countries.country_id', '=', 'legal.country');
            $query->leftJoin('zone', 'zone.zone_id', '=', 'legal.state_id');
            $query->where('legal.business_legal_name','!=' ,'');
            //$query->where('legal.legal_entity_id', $legalEntityId);
            return $query->get()->all();
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    /*
     * getWarehouseByLegalEntityId() method is used to get list of warehouses
     * @param $legalentityId
     * @return Array
     */
    
    public function getWarehouseBySupplierId($supplierId) {
        try{
            $roleObj = new Role();
            $Json = json_decode($roleObj->getFilterData(6), 1);
            $filters = json_decode($Json['sbu'], 1);
            $dc_acess_list = isset($filters['118001']) ? $filters['118001'] : '';
            $fieldArr = array('lewh.lp_wh_name', 'lewh.city', 'lewh.address1', 
            					'lewh.pincode', 'lewh.le_wh_id','lewh.margin','lewh.le_wh_code','le.legal_entity_type_id');
            $query = DB::table('legalentity_warehouses as lewh')->select($fieldArr);
            $query->leftJoin('product_tot as lewhmap','lewhmap.le_wh_id','=','lewh.le_wh_id');
            $query->leftJoin('legal_entities as le','le.legal_entity_id','=','lewh.legal_entity_id');
            //$query->where('le.legal_entity_id', $supplierId);
            $query->where('lewh.dc_type', 118001);
            $query->where('lewh.status', 1);
            $query->whereIn('lewh.le_wh_id', explode(",", $dc_acess_list));
            $query->groupBy('lewh.le_wh_id');
            return $query->get()->all();
        }
        catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }        
    }

     /*
     * getWarehouseByLegalEntityId() method is used to get list of warehouses
     * @param $legalentityId
     * @return Array
     */
    
    public function getWarehouseByLegalEntityId($legalentityId) {
        try{
            $fieldArr = array('lewh.lp_wh_name', 'lewh.city', 'lewh.address1', 'lewh.pincode', 'lewh.le_wh_id');
            $query = DB::table('legalentity_warehouses as lewh')->select($fieldArr);
            $query->leftJoin('product_tot as lewhmap','lewhmap.le_wh_id','=','lewh.le_wh_id');
            $query->leftJoin('legal_entities as le','le.legal_entity_id','=','lewhmap.supplier_id');
            $query->where('le.parent_id', $legalentityId);
            $query->groupBy('lewhmap.le_wh_id');
            return $query->get()->all();
        }
        catch(Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }        
    }

    /*
     * getSuppliersByWarehouseId() method is used to get list of approved Suppliers by warehouse id
     * @param $le_wh_id
     * @return Array
     */
    
    public function getSuppliersByWarehouseId($le_wh_id) {
        try {
            $fieldArr = array('le.business_legal_name','le.address1','le.city', 'le.pincode', 'le.legal_entity_id as supplier_id');
            $query = DB::table('legalentity_warehouses as lewh')->select($fieldArr);
            $query->join('product_tot as lewhmap', 'lewhmap.le_wh_id', '=', 'lewh.le_wh_id');
            $query->leftJoin('legal_entities as le', 'le.legal_entity_id', '=', 'lewhmap.supplier_id');
            $query->where('le.legal_entity_type_id', 1002);
            $query->where('le.is_approved', 1);
            $query->where('lewhmap.le_wh_id', $le_wh_id);
            $query->groupBy('lewhmap.supplier_id');
            //echo $query->toSql();die();
            return $query->get()->all();
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function getLeParentIdByLeId($leId) {
        try{
            
            $leArr = DB::table('legal_entities')->select('parent_id')->where('legal_entity_id',$leId)->first();
            return isset($leArr->parent_id) ? (int)$leArr->parent_id : 0;
        } catch (Exception $ex) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function getUserByLegalEntityId($leId) {
        try{
            
            $userArr = DB::table('users')->select('*')->where('legal_entity_id',$leId)->first();
            return $userArr;
        } catch (Exception $ex) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function getUserById($userId) {
        try{
            
            return DB::table('users')->select('*')->where('user_id',$userId)->first();
        } catch (Exception $ex) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function getSupplierId(){
        try{
            $legalentityId = Session::get('legal_entity_id');
            $leParentId = $this->getLeParentIdByLeId($legalentityId);

            if($leParentId) {
                $legalentityId = $leParentId;
            }

            $suppliers = DB::table('legal_entities')->select('legal_entity_id')
                        ->where('is_approved', 1)
                        ->where('parent_id',$legalentityId)
                        ->get()->all();
           
           $sup = array();
           foreach($suppliers as $supplier){
               $sup[]=$supplier->legal_entity_id;
           }
           return $sup;
        } catch (Exception $ex) {

        }
        
    }

     /*
     * getCompanyAccountByLeId() method is used to company detail by le id
     * @param $leId Numeric
     * @return Array
     *
     * 1001 - Company
     * 1002 - Supplier
     * 1006 - Manufacturer
     */
    
    public function getCompanyAccountByLeId($leId) {
        
        try{
            $fields = array('users.email_id', 'users.mobile_no');

            $query = DB::table('roles')->select($fields);
            $query->join('user_roles', 'roles.role_id', '=', 'user_roles.role_id');
            $query->join('users', 'users.user_id', '=', 'user_roles.user_id');
            $query->join('legal_entities as le', 'le.legal_entity_id', '=', 'users.legal_entity_id');
            $query->where('users.legal_entity_id', $leId);
            //$query->whereIn('le.legal_entity_type_id', array(1001, 1002, 1006));            
            $query->skip(0)->take(1);
            return $query->first();
        } catch (Exception $ex) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function getStateBillingDC($state_id){
        $bill_data = DB::table("legalentity_warehouses")
                    ->select("legal_entity_id","le_wh_id")
                    ->where("state",$state_id)
                    ->where("dc_type",118001)
                    ->where("is_billing",1)
                    ->first();
        return $bill_data;
    }

    public function checkisApob($le_wh_id){
        $check_apob = DB::table("legalentity_warehouses")
                    ->select("legal_entity_id","le_wh_id")
                    ->where("is_apob","1")
                    ->where("le_wh_id",$le_wh_id)
                    ->first();
        return count($check_apob);
    }
    public function getUserAndManagerMobileNo($userId) {
        try{
            
            $userInfo =DB::select( DB::raw("SELECT u1.mobile_no AS ff_mobile_no,u2.mobile_no AS manager_mobile_no,u2.user_id AS user_id_ffm
            FROM users u1 JOIN users u2
            WHERE u1.reporting_manager_id=u2.user_id AND u1.user_id=$userId"));
                return $userInfo;
        } catch (Exception $ex) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    
}
