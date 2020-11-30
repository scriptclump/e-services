<?php

namespace App\Modules\Attributes\Models;

/*
  Filename : AttributeSetMapping.php
  Author : Ebutor
  CreateData : 20-Aug-2016
  Desc : Model for attribute set mapping table
 */

use Illuminate\Database\Eloquent\Model;

class AttributeSetMapping extends Model {

    //protected $primaryKey 			= 'attribute_set_id';
    protected $table = 'attribute_set_mapping'; // table name

    public function getDependentData($page, $pageSize, $orderby='', $filterBy = '', $attribute_set_id)
    {
    	// print_r($attribute_set_id);
    	$result 					= array();
        $sql                        =   $this->join('attribute_sets', 'attribute_sets.attribute_set_id', '=', 'attribute_set_mapping.attribute_set_id');
    	$sql 						=	$sql->join('attributes', 'attributes.attribute_id', '=', 'attribute_set_mapping.attribute_id');
        $sql                        =   $sql->join('attributes_groups', 'attributes_groups.attribute_group_id', '=', 'attribute_set_mapping.attribute_group_id');
        
    	if(!empty($orderby))
    	{
    		$orderClause 			= explode(" ", $orderby);
            $sql 					= $sql->orderby($orderClause[0], $orderClause[1]);  //order by query
    	}
        //echo "Model";print_r($filterBy);die;
    	if (!empty($filterBy)) {
            foreach ($filterBy as $filterByEach) {
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

                $operator_array = array('=', '!=', '>', '<', '>=', '<=');
                if (in_array(trim($filter_query_operator), $operator_array)) {
                    $sql = $sql->where($filter_query_field, $filter_query_operator, (int) $filter_query_value);
                } else {
                    $sql = $sql->where($filter_query_field, $filter_query_operator, trim($filter_query_value));
                }
            }
        }

            $sql                        =   $sql->where('attribute_sets.attribute_set_id', '=', $attribute_set_id);
        
            
    		$count 					= $sql->count();
	        $result['count'] 		= $count;
	        $sql 					= $sql->skip($page * $pageSize)->take($pageSize);

	        $result['result'] 		= $sql->get()->all();
	        //print_r($result);
	        
            /*$query = $sql->toSql();
            dd($query);*/
	        
	        return $result;
    }
}