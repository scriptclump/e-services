<?php

namespace App\Modules\Indent\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Session;
use App\Modules\Indent\Models\LegalEntity;
use Log;
use App\Modules\Roles\Models\Role;

class IndentModel extends Model {
    /*
     * getOrderStatus() method is used to get order name with value
     * @param $filter, $rowCount, $offset, $perpage
     * @return Array
     */
    protected $table = "indent";

    public function getOrderIndents($filter = array(),$filterBy = array(), $offset = 0, $perpage = 10, $orderbyarray = array()) {
        try {
            
            $_leModel = new LegalEntity();

            $sup = $_leModel->getSupplierId();
            $fieldArr = array('indent.*', 'warehouse.lp_wh_name as lp_name', 'legal.business_legal_name', 
                DB::raw('(select SUM(qty) from indent_products where indent_id = indent.indent_id) as qty'),
            DB::raw('GetUserName(indent.created_by,2) AS user_name'),
            DB::raw('getMastLookupValue(indent.indent_status) AS status_name'),
             DB::raw('getManfName(products.manufacturer_id) AS manufacturer_id'),
        );
            $query = DB::table('indent as indent')->select($fieldArr);
            $query->join('legalentity_warehouses as warehouse', 'warehouse.le_wh_id', '=', 'indent.le_wh_id');
            $query->leftJoin('legal_entities as legal', 'legal.legal_entity_id', '=', 'indent.legal_entity_id');
            $query->leftJoin('indent_products as iproduct', 'iproduct.indent_id', '=', 'indent.indent_id');
            $query->leftJoin('products as products','iproduct.product_id','=','products.product_id');
            
            $this->_roleModel = new Role();
            $legalEntityId = Session::get('legal_entity_id');
            $Json = json_decode($this->_roleModel->getFilterData(6), 1);
            $filters = json_decode($Json['sbu'], 1);
            $dc_acess_list = isset($filters['118001']) ? $filters['118001'] : 'NULL';
            
            $query->whereIn('indent.le_wh_id', explode(',',$dc_acess_list));
            
            // $query->join('indent_products as product', 'product.indent_id', '=', 'indent.indent_id');
            /*if(count($sup)) {
                $query->whereIn('indent.legal_entity_id',$sup);
            }*/

            if(isset($filter['indent_code']) && !empty($filter['indent_code'])) {
                $query->where('indent.indent_code',$filter['indent_code']);   
            }

            if(isset($filter['indent_status']) && !empty($filter['indent_status'])) {
                $query->where('indent.indent_status',$filter['indent_status']);   
            }

            if(isset($filter['legal_entity_id']) && !empty($filter['legal_entity_id'])) {
                $query->where('indent.legal_entity_id',$filter['legal_entity_id']);   
            }

            if(isset($filter['fdate']) && !empty($filter['fdate'])) {
                $query->where('indent.created_at', '>=', $filter['fdate'].' 00:00:00');   
            }

            if(isset($filter['tdate']) && !empty($filter['tdate'])) {
                $query->where('indent.created_at', '<=', $filter['tdate'].' 23:59:59');   
            }            
            
            if (!empty($orderbyarray)) {
                $orderClause = explode(" ", $orderbyarray);
                $query = $query->orderby($orderClause[0], $orderClause[1]);  //order by query 
            }else
            {
                $query->orderBy('indent.indent_id', 'DESC');
            }
            if (!empty($filterBy)) {
                foreach ($filterBy as $key=>$filterByEach) {
                    if(!is_array($filterByEach)){
                    $filterByEachExplode = explode(' ', $filterByEach);
                    $length = count($filterByEachExplode);
                    $filter_query_value = '';
                    if ($length > 3) {
                        $filter_query_field = $filterByEachExplode[0];
                        $filter_query_operator = $filterByEachExplode[1];
                        for ($i = 2; $i < $length; $i++)
                            $filter_query_value .= $filterByEachExplode[$i] . " ";
                    } else {
                        $filter_query_field = $filterByEachExplode[0];
                        $filter_query_operator = $filterByEachExplode[1];
                        $filter_query_value = $filterByEachExplode[2];
                    }
                }else {
                    $filter_query_field = '';
                    $filter_query_operator = '';
                    $filter_query_value = '';
                }
                    $operator_array = array('=', '!=', '>', '<', '>=', '<=');
                    
                    if ($filter_query_field=='' && $key=='indentDate') {
                        $fdate = '';
                        if (isset($filterBy['indentDate'][2]) && isset($filterBy['indentDate'][1]) && isset($filterBy['indentDate'][0])) {
                            $fdate = $filterBy['indentDate'][2] . '-' . $filterBy['indentDate'][1] . '-' . $filterBy['indentDate'][0];
                        }
                        if ($filterBy['indentDate']['operator'] == '=' && !empty($fdate)) {
                            $query = $query->whereBetween('indent.created_at', [$fdate . ' 00:00:00', $fdate . ' 23:59:59']);
                        } else if (!empty($fdate) && $filterBy['indentDate']['operator'] == '<' || $filterBy['indentDate']['operator'] == '<=') {
                            $query = $query->where('indent.created_at', $filterBy['indentDate']['operator'], $fdate . ' 23:59:59');
                        } else if (!empty($fdate)) {
                            $query = $query->where('indent.created_at', $filterBy['indentDate']['operator'], $fdate . ' 00:00:00');
                        }
                    }

                    if ($filter_query_field == "createdBy") {
                        $query = $query->where(DB::raw("GetUserName(indent.created_by,2)"), $filter_query_operator, trim($filter_query_value));
                    }else if ($filter_query_field == "status_name") {
                        $query = $query->where(DB::raw('getMastLookupValue(indent.indent_status)'), $filter_query_operator, trim($filter_query_value));
                    }else if ($filter_query_field == "indent_type") {
                        $query = $query->whereIn($filter_query_field, [$filter_query_value]);
                    } else if ($filter_query_field == "qty") {
                        $query = $query->where(DB::raw('(select SUM(qty) from indent_products where indent_id = indent.indent_id)'), $filter_query_operator, trim($filter_query_value));
                    } else if ($filter_query_field == "getManfName(products.manufacturer_id)") {
                        $query = $query->where(DB::raw('getManfName(products.manufacturer_id)'), $filter_query_operator, trim($filter_query_value));
                    }else  if($filter_query_field!=''){
                        $query = $query->where($filter_query_field, $filter_query_operator, trim($filter_query_value));
                    }
                }
            }
            
            $query->groupBy('indent.indent_id');
            $count = count($query->get()->all());

            $query->skip($offset)->take($perpage);
            $indents = $query->get()->all();
            $finalArr['data'] = $indents;
            $finalArr['count'] = $count; 
            return $finalArr;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    
    public function getIndentCount($filter = array()) {
        try {
            $_leModel = new LegalEntity();
            //$sup = $_leModel->getSupplierId();
            $fieldArr = array(DB::raw('COUNT(indent.indent_id) as totRow'));
            $query = DB::table('indent as indent')->select($fieldArr);
            $query->join('legal_entities as legal', 'legal.legal_entity_id', '=', 'indent.legal_entity_id');
            
            $this->_roleModel = new Role();
            $legalEntityId = Session::get('legal_entity_id');
            $Json = json_decode($this->_roleModel->getFilterData(6), 1);
            $filters = json_decode($Json['sbu'], 1);
            $dc_acess_list = isset($filters['118001']) ? $filters['118001'] : 'NULL';
            
            $query->whereIn('indent.le_wh_id', explode(',',$dc_acess_list));

            /*if(count($sup)) {
                $query->whereIn('indent.legal_entity_id',$sup);
            }*/

            if(isset($filter['indent_code']) && !empty($filter['indent_code'])) {
                $query->where('indent.indent_code',$filter['indent_code']);   
            }

            if(isset($filter['indent_status']) && !empty($filter['indent_status'])) {
                $query->where('indent.indent_status',$filter['indent_status']);   
            }

            if(isset($filter['legal_entity_id']) && !empty($filter['legal_entity_id'])) {
                $query->where('indent.legal_entity_id',$filter['legal_entity_id']);   
            }

            if(isset($filter['fdate']) && !empty($filter['fdate'])) {
                $query->where('indent.created_at', '>=', $filter['fdate'].' 00:00:00');   
            }

            if(isset($filter['tdate']) && !empty($filter['tdate'])) {
                $query->where('indent.created_at', '<=', $filter['tdate'].' 23:59:59');   
            }

            

            #echo $query->toSql();die;
            $row = $query->first();
            return isset($row->totRow) ? $row->totRow :0;
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    
    /*
     * getWarehouses() method is used to get list of warehouses
     * @param $legalentityId
     * @return Array
     */
    public function getWarehouses($legalentityId) {
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

    /*
     * getSuppliers() method is used to get list of Suppliers
     * @param $legalentityId
     * @return Array
     */
    public function getSuppliers($legalentityId) {
        $fieldArr = array('suppliers.lp_wh_name', 'lewh.le_wh_id');
        $query = DB::table('suppliers')->select($fieldArr);
        $query->where('lewh.legal_entity_id', $legalentityId);
        $warehouses = $query->get()->all();   
        return $warehouses;
    }
    
    public function getIndentDetailById($indentId) {
		$fieldArr = array('indent.*', 'product.*', 'legal.*', 'currency.symbol_left as symbol','inventory.soh','inventory.mbq',
    'inventory.order_qty', 
                    'products.seller_sku',
                    DB::raw('getMastLookupValue(product.pack_type) AS packtype'), 
                    DB::raw('(select PPC.no_of_eaches from product_pack_config as PPC where product_id=product.product_id and PPC.no_of_eaches=product.no_of_eaches order by PPC.no_of_eaches DESC limit 1) as no_of_eaches'), 
                    'product.no_of_eaches as prod_eaches');
        
		$query = DB::table('indent as indent')->select($fieldArr);
		$query->join('indent_products as product', 'product.indent_id', '=', 'indent.indent_id');
		$query->leftJoin('legal_entities as legal', 'legal.legal_entity_id', '=', 'indent.legal_entity_id');
        $query->leftJoin('inventory', function($join){
            $join->on('inventory.product_id', '=', 'product.product_id')->on('inventory.le_wh_id', '=', 'indent.le_wh_id');
        });
        //$query->join('product_pack_config as PPC', 'product.product_id', '=', 'PPC.product_id');
        // $query->join('vw_inventory_report as IVR', 'indent.le_wh_id', '=', 'IVR.le_wh_id');
        $query->join('products', 'products.product_id', '=', 'product.product_id');
      	$query->join('currency', 'currency.currency_id', '=', 'indent.currency_id');
        //$query->whereIn('PPC.level',[16004,16006]); //CFC masetr lookup value
		$query->where('indent.indent_id', $indentId);
		//echo $query->toSql();die;
		return $query->get()->all();
	}
    /*
     * saveIndent() method is used to Save Indent details
     * @param $indentArr
     * @return $indent_id
     */

    public function saveIndent($indentArr) {
        try {
            $indent_id = DB::table('indent')->insertGetId($indentArr);
            return $indent_id;
        } catch (Exception $ex) {
            
        }
    }
    /*
     * getProductByGdsOrderProductId() method is used to Get Product details
     * @param $productId
     * @return Array
     */

    public function getProductByGdsOrderProductId($gds_order_prod_id) {
        try {
            $fieldArr = array('gds_order_products.*');
            $productData = DB::table('gds_order_products')
                    ->select($fieldArr)
                    ->where('gds_order_prod_id',$gds_order_prod_id)
                    ->first();
            return $productData;
        } catch (Exception $ex) {
            
        }
    }
    /*
     * saveIndentProducts() method is used to Save Indent Product details
     * @param $indentArr
     * @return $gds_op_id
     */

    public function saveIndentProducts($indentProducts) {
        try {
            $gds_op_id = DB::table('indent_products')->insert($indentProducts);
            return $gds_op_id;
        } catch (Exception $e) {
           Log::info($e->getMessage() . ' => ' . $e->getTraceAsString()); 
        }
    }
    
    public function updateIndentProduct($gdsOpId, $dataArr) {
        try {
			DB::table('indent_products')->where('gds_op_id', $gdsOpId)->update($dataArr);
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function updateIndent($indentId, $dataArr) {
        try {
            $status = DB::table('indent')->where('indent_id', $indentId)->pluck("indent_status")->all();
            if($status[0] == 70001)
            return DB::table('indent')->where('indent_id', $indentId)->update($dataArr);
            else
                return "indent-closed";
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function getUserEmailByRoleName($roleName) {
        try {
            $query = DB::table('users')->select('users.email_id');
            $query->join('user_roles', 'users.user_id', '=', 'user_roles.user_id');
            $query->join('roles', 'roles.role_id', '=', 'user_roles.role_id');
            $query->where('users.is_active',1);
            return $query->where('roles.name', $roleName)->get()->all();
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    
    public function getSupplierWHId($productId) {
        try {
            $fields = array( 'slwm.le_wh_id', 'slwm.supplier_id');
            $query = DB::table('supplier_le_wh_mapping as slwm')->select($fields);
            $query->where('slwm.product_id', $productId);
           
            return $query->first();
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function getIndentCodeById($indentId) {
        try {
            $fields = array( 'indent.indent_code');
            $query = DB::table('indent as indent')->select($fields);
            $query->where('indent.indent_id', $indentId);
            $row = $query->first();
            return (isset($row->indent_code) ? $row->indent_code : '');
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function getIndentQtyById($indentId) {
        try {
            $fields = array( DB::raw('SUM(product.qty*product.no_of_eaches) as totQty'));
            $query = DB::table('indent_products as product')->select($fields);
            $query->where('product.indent_id', $indentId);
            $row = $query->first();
            return (isset($row->totQty) ? $row->totQty : 0);
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }
    public function getIndentProductQtyById($indentId,$product_id) {
        try {
            $fields = array( DB::raw('SUM(product.qty*product.no_of_eaches) as totQty'));
            $query = DB::table('indent_products as product')->select($fields);
            $query->where('product.product_id', $product_id);
            $query->where('product.indent_id', $indentId);
            $row = $query->first();
            return (isset($row->totQty) ? $row->totQty : 0);
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    public function getUpdateIndent($indentArr)
    {
        try {
            $error_arr = array();
            $error_var = 0;
            $indent_id = $indentArr['indentid'];
            unset($indentArr['indentid']);
            DB::beginTransaction();
            
            foreach ($indentArr as $key => $value) {
                // print_r($value);die;
                $mainKey_explode =  explode("_", $key);
                $sku = $mainKey_explode[1];

                // $getPOQty = DB::table("indent_products")->where("sku", "=", $sku)->where("indent_id", $indent_id)->lists("po_qty");
                 $getPOQty = DB::table("indent_products")->where("sku", "=", $sku)->where("indent_id", $indent_id)->get(array("po_qty", "no_of_eaches"))->all();
                 $getPOQty = json_decode(json_encode($getPOQty), true);

                $PO_Qty = $this->getPoProductQtyByIndentId($indent_id, $sku);
				
				/* checking the POQty for that particular Indent */
				if($PO_Qty > $value*$getPOQty[0]['no_of_eaches'])
                {
				   $error_arr['sku'] = $sku;
                   $error_arr['status_code'] = 2; 
                   DB::rollback();
                   break;
                }

                $sql = DB::table("indent_products")
                                    ->where("sku", "=", $sku)
                                    ->where("indent_id", $indent_id)
                                    ->update(array('qty' => $value));

                                    // echo "<pre>";print_r($sql); 

                $error_arr['status_code'] = 1;
                // if($sql)
                // {
                //     // $error_arr['success'] = 0;
                //     $error_arr['status_code'] = 1;
                // }else{
                //     // $error_arr['error'] = 1;
                //     $error_arr['status_code'] =0;
                    
                //     continue;
                // }
            }

            
            if($error_arr['status_code'] == 1)
            {
                DB::commit();
            }
            return $error_arr;

        } catch (Exception $e) {
            Log::info($e->getMessage() . ' => ' . $e->getTraceAsString());
        }
    }

    //Get Menufaturer name..
    public function manufaturerName($manuid) {
        $manuQuery = DB::table("legal_entities")
                    ->where("legal_entity_id", $manuid)
                    ->pluck("business_legal_name")->all();
        $manuName = json_decode(json_encode($manuQuery), true);
        return $manuName[0];
    }

    /*
     * getpacktype() method is used to get Product Pack type
     * @param $product_id, $no_of_eaches
     * @return value
     */
    public function getpacktype($product_id, $no_of_eaches)
    {
        try {
                $sql = DB::table("product_pack_config as PPC")
                        ->join("master_lookup as ML", "ML.value", "=", "PPC.level")
                        ->where("PPC.product_id", $product_id)
                        ->where("PPC.no_of_eaches", $no_of_eaches)
                        ->pluck('ML.master_lookup_name')->all();

                return isset($sql[0])?$sql[0]:"";
        } catch (\ErrorException $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    public function getPoProductQtyByIndentId($indentId,$sku) 
    {
      $getProdID = DB::table("products")->where("sku", "=", $sku)->pluck("product_id")->all();
      $fields = array(DB::raw('SUM(product.qty*product.no_of_eaches) as totQty'));
      $query = DB::table('po')->select($fields);
      $query->join('po_products as product', 'product.po_id', '=', 'po.po_id');
      $query->where('product.product_id', $getProdID[0]);
      $query->where('po.indent_id', $indentId);
      $row = $query->first();
      return isset($row->totQty) ? (int)$row->totQty : 0;
    }
    public function getIndentOrderData_ByDC($fdate,$tdate,$dcNames,$flag=NUll){
      $query = DB::selectFromWriteConnection(DB::raw("CALL getIndentOrderData_ByDC('".$fdate."','".$tdate."',".$dcNames.",1)")); 
      $query1 = DB::selectFromWriteConnection(DB::raw("CALL getIndentOrderData_ByDC('".$fdate."','".$tdate."',".$dcNames.",2)")); 
      return array(["query" =>$query,"query1"=>$query1]);
    }
    // public function getIndentOrderDataConsolidate($fdate,$tdate){
    //   $query = DB::selectFromWriteConnection(DB::raw("CALL getIndentOrderDataConsolidate('".$fdate."','".$tdate."')")); 
    //   return $query;
    // }
    public function getIndentOrderDataConsolidate($fdate,$tdate) {
        $query = "CALL getIndentOrderDataConsolidate('".$fdate."','".$tdate."')";
        $file_name = 'Consolidate_Indent_Order'.date('d-m-Y-H-i-s').'.csv';
        $filePath = public_path().'/uploads/reports/'.$file_name;
        //$this->exportToExcel($query,$file_name,$filePath);
        $this->exportToCsv($query, $file_name);  

    }

     public function exportToExcel($query,$filename,$filePath){
                $host = env('DB_HOST');
                $port = env('DB_PORT');
                $dbname = env('DB_DATABASE');
                $uname = env('DB_USERNAME');
                $pwd = env('DB_PASSWORD');
                $sqlIssolation = 'SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;';
                $sqlCommit = 'COMMIT';
                $exportCommand = "mysql -h ".$host." -u ".$uname." -p'".$pwd."' ".$dbname." -e \"".$sqlIssolation.$query.';'.$sqlCommit.";\" | sed  's/\\t/\",\"/g;s/^/\"/;s/$/\"/g' > ".$filePath;
                system($exportCommand);        
                header("Content-Type: application/force-download");
                header("Content-Disposition:  attachment; filename=\"" . $filename . "\";" );
                header("Content-Transfer-Encoding:  binary");
                header("Accept-Ranges: bytes");
                header('Content-Length: ' . filesize($filePath));        
                $readFile = file($filePath);
                foreach($readFile as $val){
                    echo $val;
                    
            }           
    }
    public function indentDelete($id){

       try {
            $message = 'Unable to delete data please contact admin'; 

            if(!empty($id))
            {
               $message="You can't delete the Closed Indent.";
               $indentsts=DB::table('indent')
                             ->where('indent_status',70002)
                             ->where('indent_id','=',$id)
                             ->get()->all();
                    if(count($indentsts)==0){
                
                    $deletechildindent=DB::table('indent_products')
                            ->where('indent_id','=',$id)->delete();
        
                 if($deletechildindent){
                     $deleteaction=DB::table('indent')
                                 ->where('indent_id','=',$id)->delete();
                       if($deleteaction){          
                      $message="Indent Deleted Successfully";
                      }else{
                        $message="Indent Not Deleted";
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

    public function exportToCsv($query, $filename) {
        $host = env('READ_DB_HOST');
        $port = env('DB_PORT');
        $dbname = env('DB_DATABASE');
        $uname = env('DB_USERNAME');
        $pwd = env('DB_PASSWORD');
        $filePath = public_path().'/uploads/reports/'.$filename;
        //echo $filePath;die;
        $sqlIssolation = 'SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;';
        $sqlCommit = 'COMMIT';
        $exportCommand = "mysql -h ".$host." -u ".$uname." -p'".$pwd."' ".$dbname." -e \"".$sqlIssolation.$query.';'.$sqlCommit.";\" | sed  's/\\t/\",\"/g;s/^/\"/;s/$/\"/g' > ".$filePath;
        //echo '<pre>'. $exportCommand;die;
        system($exportCommand);
        
        header("Content-Type: application/force-download");
        header("Content-Disposition:  attachment; filename=\"" . $filename . "\";" );
        header("Content-Transfer-Encoding:  binary");
        header("Accept-Ranges: bytes");
        header('Content-Length: ' . filesize($filePath));
        
        $readFile = file($filePath);
        foreach($readFile as $val){
            echo $val;
        }
        exit;
    }

}
