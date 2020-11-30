<?php

namespace App\Modules\Attributes\Models;

/*
  Filename : AttributeSet.php
  Author : Ebutor
  CreateData : 20-Aug-2016
  Desc : Model for attribute_sets table
 */

use Illuminate\Database\Eloquent\Model;

class AttributeSet extends Model {

    protected $primaryKey 			= 'attribute_set_id';

    public function getAllData($page, $pageSize, $orderby='', $filterBy = '')
    {
    	//print_r($orderby);die;
        //echo "Page: ".$page.", pageSize:".$pageSize.", orderby: ".$orderby.", filterBy: ".$filterBy."\n";
    	$result 					= array();
    	$sql 						=	$this->join('categories', 'categories.category_id', '=', 'attribute_sets.category_id')->join('legal_entities','legal_entities.legal_entity_id','=','attribute_sets.legal_entity_id')->join('attribute_set_mapping','attribute_set_mapping.attribute_set_id','=','attribute_sets.attribute_set_id')->groupby('attribute_set_mapping.attribute_set_id');
    	
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

    		$count 					= $sql->count();
	        $result['count'] 		= $count;
	        $sql 					= $sql->skip($page * $pageSize)->take($pageSize);

	        $result['result'] 		= $sql->get()->all();
	        //print_r($result);
	        

	        
	        return $result;
    }
}