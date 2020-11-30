<?php

namespace App\Modules\Product\Models;
use Illuminate\Database\Eloquent\Model;
use Session;
use Utility;
use DB;
use UserActivity;
use App\Central\Repositories\RoleRepo;
use Log;


class MustSkuProduct extends Model {

    public function getMustSKUGridData($request)
    {
         //DB::enableQueryLog();
        $this->_roleRepo = new RoleRepo();
        $this->grid_field_db_match = array(
            'Product_Title' => 'Product_Title',
            'Brand' => 'Brand',
            'Display_Name'=>'display_name',
            'category_name' => 'category_name',
            'SKU' => 'SKU',
            'ManfName' => 'ManfName',
            'status' => 'STATUS'
        );
      
        $page = $request->input('page');   //Page number
        $pageSize = $request->input('pageSize'); //Page size for ajax call
        $skip = $page * $pageSize;

        $query = DB::table('vw_must_skulist')->select('*');

        if ($request->input('$orderby')) {    //checking for sorting
            $order = explode(' ', $request->input('$orderby'));

            $order_query_field = $order[0]; //on which field sorting need to be done
            $order_query_type = $order[1]; //sort type asc or desc

            $order_by_type = 'desc';

            if ($order_query_type == 'asc') {
                $order_by_type = 'asc';
            }

            if (isset($this->grid_field_db_match[$order_query_field])) { //getting appropriate table field based on grid field
                $order_by = $this->grid_field_db_match[$order_query_field];
                $query->orderBy($order_by, $order_by_type);
            }
        }


        if ($request->input('$filter')) {           //checking for filtering
            $post_filter_query = explode(' and ', $request->input('$filter')); //multiple filtering seperated by 'and'


            foreach ($post_filter_query as $post_filter_query_sub) {    //looping through each filter
                $filter = explode(' ', $post_filter_query_sub);
                $length = count($filter);

                $filter_query_field = '';

                if ($length > 3) {
                    for ($i = 0; $i < $length - 2; $i++)
                    $filter_query_field .= $filter[$i] . " ";
                    $filter_query_field = trim($filter_query_field);
                    $filter_query_operator = $filter[$length - 2];
                    $filter_query_value = $filter[$length - 1];
                } else {
                    $filter_query_field = $filter[0];
                    $filter_query_operator = $filter[1];
                    $filter_query_value = $filter[2];
                }

                $filter_query_substr = substr($filter_query_field, 0, 7);

                if ($filter_query_substr == 'startsw' || $filter_query_substr == 'endswit' || $filter_query_substr == 'indexof' || $filter_query_substr == 'tolower') {
                    //It's string filter,checking the filter is of type startwith,endswith,contains,doesn't contain,equals,doesn't eual

                    if ($filter_query_substr == 'startsw') {

                        $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'


                        $filter_value = $filter_value_array[1] . '%';


                        foreach ($this->grid_field_db_match as $key => $value) {

                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                $query->where($this->grid_field_db_match[$key], 'like', $filter_value);
                            }
                        }
                    }


                    if ($filter_query_substr == 'endswit') {

                        $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'


                        $filter_value = '%' . $filter_value_array[1];


                        //substr(strpos($info, '-', strpos($info, '-')+1)

                        foreach ($this->grid_field_db_match as $key => $value) {

                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                $query->where($this->grid_field_db_match[$key], 'like', $filter_value);
                            }
                        }
                    }




                    if ($filter_query_substr == 'tolower') {

                        $filter_value_array = explode("'", $filter_query_value);  //extracting the input filter value between single quotes ex 'value'

                        $filter_value = $filter_value_array[1];

                        if ($filter_query_operator == 'eq') {
                            $like = '=';
                        } else {
                            $like = '!=';
                        }


                        //substr(strpos($info, '-', strpos($info, '-')+1)

                        foreach ($this->grid_field_db_match as $key => $value) {

                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                $query->where($this->grid_field_db_match[$key], $like, $filter_value);
                            }
                        }
                    }

                    if ($filter_query_substr == 'indexof') {

                        $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'

                        $filter_value = '%' . $filter_value_array[1] . '%';

                        if ($filter_query_operator == 'ge') {
                            $like = 'like';
                        } else {
                            $like = 'not like';
                        }


                        //substr(strpos($info, '-', strpos($info, '-')+1)

                        foreach ($this->grid_field_db_match as $key => $value) {

                            if (strpos($filter_query_field, '(' . $key . ')') != 0) {  //getting the filter field name
                                $query->where($this->grid_field_db_match[$key], $like, $filter_value);
                            }
                        }
                    }
                } else {

                    switch ($filter_query_operator) {
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


                    if (isset($this->grid_field_db_match[$filter_query_field])) { //getting appropriate table field based on grid field
                        $filter_field = $this->grid_field_db_match[$filter_query_field];
                    }

                    $query->where($filter_field, $filter_operator, $filter_query_value);
                }
            }
            $query->groupBy('product_id');
        }
        $query->orderBy('product_id','desc');
        $row_count = count($query->get()->all());
       
        $query->skip($skip)->take($pageSize);

        $Manage_Products = $query->get()->all();
        $Manage_Products = json_decode(json_encode($Manage_Products),1);


        foreach ($Manage_Products as $k => $list) {


            if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $Manage_Products[$k]['ProductLogo'])) {
                $Manage_Products[$k]['ProductLogo'] = '/uploads/products/' . $Manage_Products[$k]['ProductLogo'];
            }

            $action = '';
            $status='';
            $delete_product  = $this->_roleRepo->checkPermissionByFeatureCode('PRD003');
             
            if ($delete_product == 1) {
                $action .='<a class="deleteProduct" title="Delete Product" onclick="deleteskuproduct('.$Manage_Products[$k]['Product_ID'].','.$Manage_Products[$k]['le_wh_id'].')";> <i class="fa fa-trash-o"></i> </a>';
            }

            if($Manage_Products[$k]['STATUS']==1)
            {

                    $status .= '<label class="switch" style="align:center;"><input class="switch-input block_users" type="checkbox" name="'.$Manage_Products[$k]['Product_ID'].'" id="status_'.$Manage_Products[$k]['le_wh_id'].'_'.$Manage_Products[$k]['Product_ID'].'" value="'.$Manage_Products[$k]['Product_ID'].'" checked=checked onclick="changeskuproductstatus('.$Manage_Products[$k]['Product_ID'].','.$Manage_Products[$k]['le_wh_id'].')";><span class="switch-label" data-on="Yes"></span><span class="switch-handle"></span></label>';
            }else{

                $status .= '<label class="switch" style="align:center;"><input class="switch-input block_users" type="checkbox" name="'.$Manage_Products[$k]['Product_ID'].'" id="status_'.$Manage_Products[$k]['le_wh_id'].'_'.$Manage_Products[$k]['Product_ID'].'" value="'.$Manage_Products[$k]['Product_ID'].'" onclick="changeskuproductstatus('.$Manage_Products[$k]['Product_ID'].','.$Manage_Products[$k]['le_wh_id'].')";><span class="switch-label" data-off="No"></span><span class="switch-handle"></span></label>';

            }

                        
            $Manage_Products[$k]['Action'] = $action;
            $Manage_Products[$k]['status'] = $status;
        }
    
        return  json_encode(array('Records' => $Manage_Products, 'TotalRecordsCount' => $row_count));

    }


    public function getMustSkus($data)
    {
        try
        {
           $term = $data['term'];
           $warehouse_id = $data['warehouse_id'];
                $products = DB::table('products')
                ->where('products.is_sellable',1)
                ->where('products.cp_enabled',1)
                ->where(function ($query) use($term) {
                    $query->orWhere('products.sku','like', '%'.$term.'%')
                          ->orWhere('products.product_title','like', '%'.$term.'%')
                          ->orWhere('products.upc','like', '%'.$term.'%');
                          })
                ->leftJoin('product_cpenabled_dcfcwise as pcd','products.product_id','=','pcd.product_id')
                ->where('pcd.cp_enabled',1)
                ->where('pcd.is_sellable',1)
                ->where('pcd.le_wh_id',$warehouse_id)
                ->select('products.product_id','products.product_title','products.upc','products.sku','products.pack_size','products.seller_sku','products.mrp')
                ->groupBy('products.product_id')->get()->all();
            $prodAry = array();
            if(count($products)>0){
                foreach($products as $product){
                    $product_name = $product->product_title;
                    $product_id = $product->product_id;
                    $product_title = $product->product_title;
                    $sku = $product->sku;
                    $mrp = ($product->mrp!='')?$product->mrp:0;
                    $prod_arr = array("label" => $product_name, "product_id" => $product_id, "product_title" => $product_title, "sku" => $sku,'mrp'=>'Rs. '.$mrp);
                    array_push($prodAry, $prod_arr);
                }
            }else{
                $prod_arr = array("label" => 'No Result Found','value'=>'');
                array_push($prodAry, $prod_arr);
            }
            echo json_encode($prodAry);
        } catch (\ErrorException $ex) {
            $prodAry=array();
            echo json_encode($prodAry);
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }


    public function addMustSku($data)
    {
      try{
        $date=date('Y-m-d H:i:s');
        $addsku=DB::table('sku_list')->insert(['product_id'=>$data['addproduct_id'],'le_wh_id'=>$data['dcid'],'created_at'=>$date,'created_by'=>Session::get('userId'),'updated_by'=>Session::get('userId'),'updated_at'=>$date]);

        return $addsku;

      } catch (\ErrorException $ex) {

        return 0;
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
        }
    }

    public function checkIfProductExitsInskulist($data)
    {
      try{

        $isproductinskulist=DB::table('sku_list')->select('sku_list_id')->where('product_id',$data['addproduct_id'])->where('le_wh_id',$data['dcid'])->get()->all();
        if(count($isproductinskulist)>0){
          return false;
        }else{
          return true;
        }

      }catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
            return false;
        }
    }

    public function deleteMustSku($pid,$le_wh_id){
        try{

        $isproductinskulist=DB::table('sku_list')->where('product_id',$pid)->where('le_wh_id',$le_wh_id)->delete();
        //$isproductinskulist="delete from sku_list where product_id =".$pid." and le_wh_id =".$le_wh_id;
        //$isproductinskulist=DB::selectFromWriteConnection(DB::raw($isproductinskulist));

        if($isproductinskulist){
          return true;
        }else{
          return false;
        }

      }catch (\ErrorException $ex) {
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());
            return false;
        }

    }

    public function changeMustSkuStatus($pid,$le_wh_id,$sts){
        try{
            $status_array=array();
        $updateproductinskulist=DB::table('sku_list')->where('product_id',$pid)->where('le_wh_id',$le_wh_id)->update(['status'=>$sts,'updated_by'=>Session::get('userId'),'updated_at'=>date('Y-m-d H:i:s')]);
        //$updateproductinskulist="update sku_list set status='".$sts."',updated_by=".Session::get('userId').",updated_at='".date('Y-m-d H:i:s')."' where product_id=".$pid." and le_wh_id=".$le_wh_id;
        //$updateproductinskulist=DB::selectFromWriteConnection(DB::raw($updateproductinskulist));
        $product_name=DB::table('products')->select('product_title')->where('product_id',$pid)->get()->all();
            $status_array['product_name']=$product_name[0]->product_title;
        if($updateproductinskulist){
            $status_array['msg']=1;
          return $status_array;
        }else{
            $status_array['msg']=0;
          return $status_array;
        }

      }catch (\ErrorException $ex) {
            $status_array['msg']=0;
            return $status_array;
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());

        }

    }

    public function getMedia($product_id){

        try{
            $image2 = DB::table('products as p')
            ->select('p.primary_image as image')
            ->where('p.product_id','=',$product_id)
            ->first();
            return $image2;
        }catch (\ErrorException $ex) {
            return '';
            Log::error($ex->getMessage().' '.$ex->getTraceAsString());

        }
    }

}
