<?php

namespace App\models\Marketplace;

use Illuminate\Database\Eloquent\Model;
use DB;
use Input;
use Session;

class MPCategoryMap extends Model {

    public $timestamps = false;
    protected $table = 'mp_category_mapping';

    //put your code here

    public function getMapCategory($mp_id, $page, $pageSize) {
        $query = DB::table('mp_category_mapping')
                ->select('mp_categories.mp_category_id', 'mp_categories.category_name', 'categories.cat_name', 'mp_category_mapping.category_id')
                ->rightjoin('mp_categories', 'mp_categories.mp_category_id', '=', 'mp_category_mapping.mp_category_id')
                ->rightjoin('categories', 'categories.category_id', '=', 'mp_category_mapping.category_id')
                ->where('mp_categories.mp_id', $mp_id);
        $query->skip($page * $pageSize)->take($pageSize);

      /*  if (Input::input('$orderby')) {    //checking for sorting
            $order = explode(' ', Input::input('$orderby'));
            $order_by = $order[0]; //on which field sorting need to be done
            $order_query_type = $order[1]; //sort type asc or desc
            $order_by_type = ($order_query_type == 'asc') ? 'asc' : 'desc';
            $query->orderBy($order_by, $order_by_type);
        }
        $filter_qry_substr_arr = array('startsw', 'endswit', 'indexof', 'tolower');
        if (Input::input('$filter')) {//checking for filtering
            $post_filter_query = explode(' and ', Input::input('$filter')); //multiple filtering seperated by 'and'
            foreach ($post_filter_query as $post_filter_query_sub) {    //looping through each filter
                $filter = explode(' ', $post_filter_query_sub);
                $filter_query_field = $filter[0];
                $filter_query_operator = $filter[1];
                $filter_value = $filter[2];

                $filter_query_substr = substr($filter_query_field, 0, 7);

                if (in_array($filter_query_substr, $filter_qry_substr_arr)) {
                    //It's string filter,checking the filter is of type startwith,endswith,contains,doesn't contain,equals,doesn't eual
                    $filter_value_array = explode("'", $filter_query_field);  //extracting the input filter value between single quotes ex 'value'                        
                    if ($filter_query_substr == 'indexof') {
                        $filter_value = '%' . $filter_value_array[1] . '%';
                        if ($filter_query_operator == 'ge') {
                            $like = 'like';
                        } else {
                            $like = 'not like';
                        }
                        //substr(strpos($info, '-', strpos($info, '-')+1)
                        foreach ($all_columns as $key => $value) {
                            if (strpos($filter_query_field, '(' . $value . ')') != 0) {  //getting the filter field name
                                $column_name = $value;
                            }
                        }
                    }
                    if ($filter_query_substr == 'startsw') {
                        $filter_value = $filter_value_array[1] . '%';
                        $like = 'like';
                        foreach ($all_columns as $key => $value) {
                            if (strpos($filter_query_field, '(' . $value . ')') != 0) {  //getting the filter field name
                                $column_name = $value;
                            }
                        }
                    }
                    if ($filter_query_substr == 'endswit') {
                        $filter_value = '%' . $filter_value_array[1];
                        $like = 'like';
                        //substr(strpos($info, '-', strpos($info, '-')+1)
                        foreach ($all_columns as $key => $value) {
                            if (strpos($filter_query_field, '(' . $value . ')') != 0) {  //getting the filter field name
                                $column_name = $value;
                            }
                        }
                    }
                    if ($filter_query_substr == 'tolower') {
                        $filter_value = $filter_value_array[1];
                        if ($filter_query_operator == 'eq') {
                            $like = '=';
                        } else {
                            $like = '!=';
                        }
                        //substr(strpos($info, '-', strpos($info, '-')+1)
                        foreach ($all_columns as $key => $value) {
                            if (strpos($filter_query_field, '(' . $value . ')') != 0) {  //getting the filter field name
                                $column_name = $value;
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
                    $column_name = $filter_query_field;
                    $like = $filter_operator;
                }
                $query->where($column_name, $like, $filter_value);
            }
        } */

        $mapcategories = $query->get()->all();
        //print_r($mapcategories);exit;
        return json_decode(json_encode($mapcategories));
    }

    public function getMapCategoryCount($mp_id) {
        $query = DB::table('mp_attribute_map')
                ->rightjoin('mp_attributes', 'mp_attributes.feature_id', '=', 'mp_attribute_map.mp_att_id')
                ->rightjoin('attributes', 'attributes.attribute_id', '=', 'mp_attribute_map.attribute_id')
                ->where('mp_attribute_map.mp_id', $mp_id);
        $attribute_count = $query->count();
        //print_r($variants);exit;
        return json_decode(json_encode($attribute_count));
    }

}
