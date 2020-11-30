<?php



namespace App\Http\Controllers;



use Session;

use View;

use Validator;

use Illuminate\Support\Facades\Input;

use URL;

use Log;

use Redirect;

use App\Http\Controllers\Controller;

use Excel;

use Image;

use Imagine;

use DB;

use App\models\countries\Countries;

use App\models\Marketplace\MP;

use \App\models\Marketplace\MPCategories;

use \App\models\Marketplace\MPServiceType;

use \App\models\Marketplace\Features;

use \App\models\Marketplace\Varients;

use \App\models\MasterLookup\MasterLookup;

use \App\models\MasterLookup\MasterLookupCategories;

use \App\models\Marketplace\MPCharges;

use \App\models\Marketplace\MPAttrMap;

use \App\models\Marketplace\MPCategoryMap;

use \App\models\Marketplace\MPOptionMap;

use \App\models\Categories\Categories;

use \App\models\Categories\Attributes;

use \App\models\Categories\AttributeOptions;

use App\models\currency\Currency;

use App\models\Marketplace\Orderstatus;

use App\models\Marketplace\OrderstatusMapping;

use Central\Repositories\ChannelRepo;



class ChannelController extends Controller {



    public function __construct() {

        $this->ChannelRepoObj = new ChannelRepo;

    }



    public function index() {

        Session::put('userId', 0);

        return View::make('channel.index');

	//return View::make('channel.channelList');

    }



    public function addChannel($channel_id = '') {



        $pageproperties = array('t');

        if ($channel_id != '') {

            $mpinfo = MP::where('mp_id', $channel_id)

                            ->get()->all();

//$mpinfo = (string) $mpinfo;

            Session::put('channel_maxid', $channel_id);

            $pageproperties = array(

                'flag' => 'edit',

                'heading' => trans('cp_headings.cp_edit'),

                'cdata' => $mpinfo

            );

        } else {

            Session::put('channel_maxid', '');

            $pageproperties = array(

                'flag' => 'add',

                'heading' => trans('cp_headings.cp_add'),

                'cdata' => array()

            );

        }



        $countries = new Countries();

        $location = json_decode(json_encode($countries->all()));

        $chargeType = $this->ChannelRepoObj->chargeType();

//Gamya Code

        $checkorderstatus = OrderstatusMapping::where('mp_id', $channel_id)

                ->get()->all();



        $ebutor_cat = DB::table('categories')

                ->get()->all();

//Gamya Code

        $channel_order_stat = DB::table('mp_status_mapping')

                ->where('mp_id', $channel_id)

                ->get()->all();

        $ebutor_master_statusid = DB::table('master_lookup_categories')

                ->where('mas_cat_name', 'Order Status')

                ->pluck('mas_cat_id')->all();

        $ebutor_order_stat = DB::table('master_lookup')

                ->where('mas_cat_id', $ebutor_master_statusid[0])

                ->select('master_lookup_name')

                ->get()->all();

//End Gamya code            

// return View::make('channel.addChannel')->with('ebutor_cat',$ebutor_cat);

        $data = array('channel_id' => $channel_id,

            'location' => $location,

            'chargeType' => $chargeType,

            'ebutor_cat' => $ebutor_cat,

            'channel_order_stat' => $channel_order_stat,

            'ebutor_order_stat' => $ebutor_order_stat

        );

//'serviceTypes'=>$serviceTypes);

        return View::make('channel.addChannel')

                        ->with('data', $data)

                        ->with('page_prop', $pageproperties)

                        ->with('channel_id', $channel_id);

    }



    public function storeChannelData() {

        try {

            $channel_id = Input::get('channel_id');

            $channel_name = Input::get('channel_name');



//print_r(Input::get());exit;

            $channel_types = implode(',', Input::get('channel_type'));

            $key_arr = explode(' ', $channel_name);

            $channel_key = '';

            if (count($key_arr) > 1) {

                foreach ($key_arr as $key) {

                    $channel_key.=substr($key, 0, 1);

                }

            } else {

                $channel_key.=substr($key_arr[0], 0, 2);

            }

            $channel_key = strtoupper($channel_key);

            $is_support = (Input::get('is_support') != '') ? 1 : 0;



            if ($channel_id == '') {

                $image = Input::file('channel_logo');

                $filename = $image->getClientOriginalName();

                $ext_array = explode('.', $filename);

                $extension = end($ext_array);

                $channel_logo = $channel_name . '_logo.' . $extension;

                $destination = 'uploads/mp/';



                if ($image->move($destination, $channel_logo)) {



                    /* upload enable logo */

                    $channel_enable_logo = $channel_name . '_enable_logo.' . $extension;

                    $thumbnail = Image::open($destination . $channel_logo)

                            ->thumbnail(new Imagine\Image\Box(30, 30));

                    $thumbnail->save($destination . $channel_enable_logo);



                    $channel_disable_logo = $channel_name . '_disable_logo.' . $extension;

                    $thumbnail_thumb = Image::open($destination . $channel_logo)

                            ->thumbnail(new Imagine\Image\Box(30, 30));

                    $thumbnail_thumb->effects()->grayscale();

                    $thumbnail_thumb->save($destination . $channel_disable_logo);



                    $channel_data = array(

                        'mp_key' => $channel_key,

                        'mp_logo' => '/' . $destination . $channel_logo,

                        'mp_enable_logo' => '/' . $destination . $channel_enable_logo,

                        'mp_disable_logo' => '/' . $destination . $channel_disable_logo,

                        'mp_name' => $channel_name,

                        'mp_url' => Input::get('channel_url'),

                        'price_url' => Input::get('price_url'),

                        'tnc_url' => Input::get('tnc_url'),

                        'shipping_url' => Input::get('shipping_url'),

                        'mp_type' => $channel_types,

                        'mp_description' => Input::get('channel_description'),

                        'country_code' => Input::get('location'),

                        'is_support' => $is_support

                    );

                    $data = MP::Create($channel_data);

                    if (isset($data->id) && $data->id != '') {

                        Session::put('channel_maxid', $data->id);

//return '{"channel_id":'.$data->id.'}';



                        return '{"message":"Channel Created Succesfully","code": ' . $data->id . '}';

                    } else {

//return '{"error":"channel creation failed"}';

                        return '{"message":"Channel Creation failed","code":"0"}';

                    }

                } else {

                    return '{"message":"Image upload failed" ,"code": "0"}';

//return '{"error":"image upload failed"}';

                }

            } else {

                $channel_namearr = MP::where('mp_id', $channel_id)->pluck('mp_name')->all();

                $channel_name = $channel_namearr[0];

                $image = Input::file('edit_channel_logo');

                $channel_data = array(

//'mp_name' => $channel_name,

                    'mp_url' => Input::get('channel_url'),

                    'price_url' => Input::get('price_url'),

                    'tnc_url' => Input::get('tnc_url'),

                    'shipping_url' => Input::get('shipping_url'),

                    'mp_type' => $channel_types,

                    'mp_description' => Input::get('channel_description'),

                    'country_code' => Input::get('location'),

                    'is_support' => $is_support

                );

                if ($image != '') {

                    $filename = $image->getClientOriginalName();

                    $ext_array = explode('.', $filename);

                    $extension = end($ext_array);

                    $channel_logo = $channel_name . '_logo.' . $extension;

                    $destination = 'uploads/mp/';



                    $image->move($destination, $channel_logo);

                    /* upload enable logo */

                    $channel_enable_logo = $channel_name . '_enable_logo.' . $extension;

                    $thumbnail = Image::open($destination . $channel_logo)

                            ->thumbnail(new Imagine\Image\Box(30, 30));

                    $thumbnail->save($destination . $channel_enable_logo);



                    $channel_disable_logo = $channel_name . '_disable_logo.' . $extension;

                    $thumbnail_thumb = Image::open($destination . $channel_logo)

                            ->thumbnail(new Imagine\Image\Box(30, 30));

                    $thumbnail_thumb->effects()->grayscale();

                    $thumbnail_thumb->save($destination . $channel_disable_logo);



                    $channel_data['mp_logo'] = '/' . $destination . $channel_logo;

                    $channel_data['mp_enable_logo'] = '/' . $destination . $channel_enable_logo;

                    $channel_data['mp_disable_logo'] = '/' . $destination . $channel_disable_logo;

                }

                $data = MP::where('mp_id', $channel_id)->update($channel_data);

                return '{"message":"Channel updated Succesfully","code": ' . $channel_id . '}';

            }

        } catch (\ErrorException $ex) {

            Log::error($ex->getMessage());

        }

    }



    public function checkMPExist($mpname = NULL) {

        $channel_name = strtolower(str_replace(' ', '', Input::get('channel_name')));

        $mp = DB::select("SELECT mp_name FROM mp WHERE LOWER(REPLACE(mp_name,' ',''))=?", array($channel_name));

//print_r($mp);exit;

        $data = json_decode(json_encode($mp, true));

        if (count($data) == 0) {

            return '{"valid":true}';

        } else {

            return '{"valid":false}';

        }

    }



    public function checkMPUrlExist($mpurl = NULL) {

        $clean = array('http', ':', '/', 'www', '.');

        $channel_url = strtolower(str_replace($clean, '', Input::get('channel_url')));

        $mp = DB::select("SELECT mp_name FROM mp WHERE LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(mp_url,' ',''),'http',''),':',''),'/',''),'www',''),'.',''))=?", array($channel_url));

        $data = json_decode(json_encode($mp, true));

        if (count($data) == 0) {

            return '{"valid":true}';

        } else {

            return '{"valid":false}';

        }

    }



    public function checkCatgoryExist() {

        $clean = array(' ');

        $channel_catgory = strtolower(str_replace($clean, '', Input::get('channel_catgory')));

        $channel_id = Input::get('channel_id');

        $catgory = DB::select("SELECT category_name FROM mp_categories WHERE LOWER(REPLACE(category_name,' ',''))=? AND mp_id=?", array($channel_catgory, $channel_id));

        $data = json_decode(json_encode($catgory, true));

        if (count($data) == 0) {

            return '{"valid":true}';

        } else {

            return '{"valid":false}';

        }

    }



    public function checkCatgoryIDExist() {

        $channel_catgory = Input::get('category_ID');

        $channel_id = Input::get('channel_id');

        $catgory_id = DB::select("SELECT mp_category_id FROM mp_categories WHERE mp_category_id=? AND mp_id=?", array($channel_catgory, $channel_id));

        $data = json_decode(json_encode($catgory_id, true));

        if (count($data) == 0) {

            return '{"valid":true}';

        } else {

            return '{"valid":false}';

        }

    }



    public function getAllChannels() {



        $page = (Input::get('page')) ? Input::get('page') : Input::get('$skip');   //Page number

        $pageSize = (Input::get('pageSize')) ? Input::get('pageSize') : Input::get('$top'); //Page size for ajax call

        $skip = $page * $pageSize;

        $all_columns = explode(',', Input::get('$select'));

        $query = MP::select();

        $query->skip($page * $pageSize)->take($pageSize);

        if (Input::input('$orderby')) {    //checking for sorting

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

        }

        $channel_data = $query->get()->all();

        $channel_details = json_decode(json_encode($channel_data), true);

//print_r($channel_details);exit;

        $channel_data = array();

        foreach ($channel_details as $k => $channel) {

            $channel_data[$k] = $channel;

            if ($channel['is_active'] == 1) {

                $channel_data[$k]['is_active'] = '<span style="padding-left:20px;"><i class="ui-icon ui-icon-check "></i></span>';

            } else {

                $channel_data[$k]['is_active'] = '<span style="padding-left:20px;"><i class="ui-icon ui-icon-close "></i></span>';

            }

            $channel_data[$k]['mp_logo'] = '<img style="margin:2px; margin-left: 4px;" width="50"  src="' . $channel['mp_logo'] . '">';

            if ($channel['is_active'] == 1) {

                $channel_data[$k]['actions'] = '<span style="padding-left:20px;" >

		<a href="/Commerceplatform/edit/' . $channel['mp_id'] . '"><i class="fa fa-pencil"></i></span>

		<span style="padding-left:20px;" ><a onclick="deleteChannel(' . $channel['mp_id'] . ')"><i class="fa fa-trash-o"></i></a></span><span style="padding-left:20px;" >

		<a id="channeldeact" title="Deactive" class="btn green-meadow" onclick="channelStatuschange(' . $channel['mp_id'] . ',' . $channel['is_active'] . ')"><i class="fa fa-check"></i></a></span>';

            } else {

                $channel_data[$k]['actions'] = '<span style="padding-left:20px;" >

		<a href="/Commerceplatform/edit/' . $channel['mp_id'] . '"><i class="fa fa-pencil"></i></span>

		<span style="padding-left:20px;" ><a onclick="deleteChannel(' . $channel['mp_id'] . ')"><i class="fa fa-trash-o"></i></a></span><span style="padding-left:20px;" >

		<a title="Active"  onclick="channelStatuschange(' . $channel['mp_id'] . ',' . $channel['is_active'] . ')" class="btn btn-danger" style="color:#fff;"><i class="fa fa-times"></i></a></span>';

            }

        }

//echo '<pre/>';print_r($channel_data);exit;

        $row_count = MP::count();



        $data['TotalRecordsCount'] = $row_count;

        $data['Records'] = $channel_data;

        return json_encode($data);

        exit;



//print_r($finalCustArr);exit;

        return json_encode($finalCustArr);

//  return array(json_encode($finalCustArr), json_encode($FinalCred));

}





/**

 * [deleteChannel Deletes the channel related data from grid]

 * @return [NULL] [Deleted rows]

 */

public function deleteChannel() {

    $channel_id = Input::get('channel_id');

    MPCharges::where('mp_id', '=', $channel_id)->forceDelete();

    $features = Features::where('mp_id',$channel_id)->pluck('feature_id')->all();

    foreach($features as $featureid){

        Varients::where('featureid', '=', $featureid)->forceDelete();

    }

    Features::where('mp_id', '=', $channel_id)->forceDelete();

    MPCategories::where('mp_id', '=', $channel_id)->forceDelete();

    MPCategoryMap::where('mp_id', '=', $channel_id)->forceDelete();

    MPAttrMap::where('mp_id', '=', $channel_id)->forceDelete();

    MPOptionMap::where('mp_id', '=', $channel_id)->forceDelete();

    OrderstatusMapping::where('mp_id', '=', $channel_id)->forceDelete();

    MP::where('mp_id', '=', $channel_id)->forceDelete();

}



public function channelChargesStore() {

        try {

            $channel_id = (Input::get('channel_id') != '' || Input::get('channel_id') != 0) ? Input::get('channel_id') : Session::get('channel_maxid');

            $service_type_id = Input::get("service_type_id");

            if ($channel_id != '') {

                if ($service_type_id != '') {

                    $from_date = date('Y-m-d h:i:s', strtotime(Input::get('start_date')));

                    $to_date = date('Y-m-d h:i:s', strtotime(Input::get('end_date')));

                    $chrgeIDD = Input::get("charge_idd");



                    if ($chrgeIDD == '' || $chrgeIDD == 0) {

                        $mp_key = MP::where('mp_id', $channel_id)

                                ->pluck('mp_key')->all();

                        $getcharge = MPCharges::where('mp_key', $mp_key[0])

                                ->where('mp_id', $channel_id)

                                ->where('service_type_id', Input::get('service_type_id'))

                                ->first();

                        if (count($getcharge) == 0) {

                            $insert = array(

                                'mp_id' => $channel_id,

                                'mp_key' => $mp_key[0],

                                'service_type_id' => Input::get('service_type_id'),

                                'recurring_interval' => Input::get('recurring_interval'),

                                'charges' => Input::get('charges'),

                                'charge_type' => Input::get('charge_type'),

                                'currency_id' => Input::get('currency_id'),

                                'is_recurring' => Input::get('is_recurring'),

                                'charges_from_date' => $from_date,

                                'charges_to_date' => $to_date,

                                'is_active' => Input::get('is_active')

                            );

                            $true = MPCharges::Create($insert);



                            $message = 'Added Successfully';

                        } else {

                            $message = 'Service Type of the Particular Channel Already Exists';

                        }

                    } else {

                        $update = array(

                            'service_type_id' => Input::get('service_type_id'),

                            'recurring_interval' => Input::get('recurring_interval'),

                            'charges' => Input::get('charges'),

                            'charge_type' => Input::get('charge_type'),

                            'currency_id' => Input::get('currency_id'),

                            'is_recurring' => Input::get('is_recurring'),

                            'charges_from_date' => $from_date,

                            'charges_to_date' => $to_date,

                            'is_active' => Input::get('is_active')

                        );

                        MPCharges::where('mp_charges_id', $chrgeIDD)->update($update);

                        $message = 'Updated Successfully';

                    }

                } else {

                    $message = 'please provide service type';

                }

            } else {

                $message = 'ChannelId should not be empty';

            }

        } catch (\ErrorException $ex) {

            Log::error($ex->getMessage());

        }

        return json_encode($message);

    }



    public function channelStatuschange() {

        $channel_id = Input::get('channel_id');

        $status = Input::get('status');

        if ($status == 1) {

            $active = 0;

        } else {

            $active = 1;

        }

        $update = array(

            'is_active' => $active

        );

        $true = MP::where('mp_id', $channel_id)->update($update);

        if ($true) {

            return json_encode($true);

        }

    }

    public function delteChannelCharges() {

//DB::enableQueryLog();

        $id = Input::get('charge_id');

        MPCharges::where('mp_charges_id', '=', $id)->forceDelete();

//dd(DB::getQueryLog());

//	return Redirect::to('channels/index');

    }



    public function getChannelChargesData() {

        $channel_id = (Input::get('channel_id') != '' || Input::get('channel_id') != 0) ? Input::get('channel_id') : Session::get('channel_maxid');

        /* $channelCharges = MPCharges::where('mp_id', '=', $channel_id)

          ->get()->all(); */

//$serviceTypes = $this->ChannelRepoObj->allServiceTypes();

        if ($channel_id != '') {

            $serviceTypes = MPServiceType::get();

//print_r($serviceTypes);exit;

            $CurrencyTypes = Currency::get();

            $lookup_cat = MasterLookupCategories::where('mas_cat_name', 'Recurring Period')->pluck('mas_cat_id')->all();

            $paymentsTypes = MasterLookup::where('mas_cat_id', $lookup_cat[0])->get()->all();

//print_r($paymentsTypes);exit;

            $chargeType = $this->ChannelRepoObj->chargeType();

            $charges = MPCharges::where('mp_id', $channel_id)->get()->all();

//print_r($charges);exit;

            foreach ($charges as $charge) {

//print_r($charge);exit;

                $servicetype = MPServiceType::where('service_type_id', $charge['service_type_id'])->pluck('service_name')->all();

                $charge['service_type_id'] = (isset($servicetype[0])) ? $servicetype[0] : '';

                $chargetype = MasterLookup::where('value', $charge['charge_type'])->pluck('master_lookup_name')->all();

                $charge['charge_type'] = (isset($chargetype[0])) ? $chargetype[0] : '';

                $currency_id = Currency::where('currency_id', $charge['currency_id'])->pluck('code')->all();

                $charge['currency_id'] = (isset($currency_id[0])) ? $currency_id[0] : '';

                $charge['is_recurring'] = ($charge['is_recurring'] == 1) ? 'Yes' : 'No';

                $rec_interval = MasterLookup::where('value', $charge['recurring_interval'])->pluck('master_lookup_name')->all();

                $charge['recurring_interval'] = (isset($rec_interval[0])) ? $rec_interval[0] : '';

            }

            $data = array(

                'serviceTypes' => $serviceTypes,

                'chargeType' => $chargeType,

                'CurrencyTypes' => $CurrencyTypes,

                'paymentsTypes' => $paymentsTypes,

                'charges' => $charges

            );

//'serviceTypes'=>$serviceTypes);

            return View::make('channel.channelChargesList')->with('data', $data);

        } else {

            echo 'ChannelId should not be empty';

        }

    }



    function getChannelCharges() {

        $channel_charge_id = Input::get('charge_id');

        $channel_charge_data = MPCharges::where('mp_charges_id', $channel_charge_id)

                ->first();

//print_r($channel_charge_data);exit;

        return json_encode($channel_charge_data);

    }



    public function categoryImportExcel() {

        try {

            ini_set('memory_limit', -1);

            ini_set('max_execution_time', 1200);

            $channel_id = (Session::get('channel_maxid') != '') ? Session::get('channel_maxid') : Input::get('channel_id');

//$channel_id = 1;

            $message = array();

            $template_type = Input::get('template_type');

            if ($template_type != '') {

                if ($channel_id != '') {

                    if (Input::hasFile('import_file')) {

                        $path = Input::file('import_file')->getRealPath();

                        $data = Excel::load($path, function($reader) {

                                    

                                })->get()->all();

                        $all_data = json_decode(json_encode($data));



                        $instructions = json_encode($all_data[0]); //Instructions Sheet Data

                        $template_data = json_encode($all_data[1]); //Data Sheet

//$features_data = json_encode($all_data[3]); //Features Sheet data

//$variants_data = json_encode($all_data[4]); //Variants Sheet data

//print_r($features_data);exit;

                        $filename = $_FILES['import_file']['name'];

                        if ($template_type == 'Categories') {

                            $message[] = $this->importCategories($channel_id, $template_data, $filename);

                        } else if ($template_type == 'Features') {

                            $message[] = $this->importFeatures($channel_id, $template_data, $filename);

                        } else if ($template_type == 'Variants') {

                            $message[] = $this->importVariants($channel_id, $template_data, $filename);

                        } else {

                            $message[] = 'Wrong Template';

                        }

                    } else {

                        $message[] = 'Please upload file';

                    }

                } else {

                    $message[] = 'Channel Id should not be empty';

                }

            } else {

                $message[] = 'Please Select Template Type';

            }

        } catch (\ErrorException $ex) {

            Log::error($ex->getMessage());

        }

        return json_encode($message);

    }



    public function importCategories($channel_id, $category_data, $filename) {

        try {

            ini_set('memory_limit', -1);

            ini_set('max_execution_time', 1200);

            $message = array();

            $category_data = json_decode($category_data, true);

            $getcat = MPCategories::where('mp_id', $channel_id)->get()->all();

            $getcatcount = count($getcat);

            $status_val = array(1 => 1, 0 => 0, 'Yes' => 1, 'Y' => 1, '\Y' => 1, 'No' => 0, 'N' => 0, '\N' => 0);

            if (count($category_data) > 0) {

                if (isset($category_data[0]['categoryname'])) {

                    $export_count = 0;

                    foreach ($category_data as $category) {

                        if ($category['categoryid'] != '' && $category['categoryname'] != '') {

                            $getcategory = MPCategories::where('mp_category_id', $category['categoryid'])

                                    ->where('mp_id', $channel_id)

                                    ->first();

                            $category_id = json_decode($getcategory);

                            if (count($category_id) == 0) {

                                $approved = ($category['approved'] == '') ? 'N' : strtoupper($category['approved']);

                                $options = ($category['options'] == '') ? 'N' : strtoupper($category['options']);

                                $leaf = ($category['leaf'] == '') ? 'N' : strtoupper($category['leaf']);

                                $is_support_multiple = ($category['is_support_multiple'] == '') ? 'Y' : strtoupper($category['is_support_multiple']);

                                $charge_type = MasterLookup::where('master_lookup_name', $category['chargetype'])

                                        ->pluck('value')->all();

                                $charge_type[0] = (isset($charge_type[0])) ? $charge_type[0] : '';

                                $category['parent'] = (isset($category['parent']) || $category['parent'] != '') ? $category['parent'] : 0;

                                $mp_key = MP::where('mp_id', $channel_id)

                                        ->pluck('mp_key')->all();

                                $insert = array(

                                    'mp_id' => $channel_id,

                                    'mp_key' => $mp_key[0],

                                    'mp_category_id' => $category['categoryid'],

                                    'category_name' => $category['categoryname'],

                                    'parent_category_id' => $category['parent'],

                                    'is_leaf_category' => $leaf,

                                    'is_approved' => $approved,

                                    'options' => $options,

                                    'charge_type' => $charge_type[0],

                                    'mp_commission' => $category['categorycommission'],

                                    'is_support_multiple' => $is_support_multiple

                                );

//    print_r($insert);exit;

                                $true = MPCategories::Create($insert);

                                $export_count++;

                                $message['success'] = $export_count . ' -- categories added successfully';

                            } else {

                                $message['error'][] = 'Category already exist---' . $category['categoryid'];

                            }

                        } else {

                            $message['error'][] = 'Category Id or Name should not be empty';

                        }

                    }

                } else {

                    $message['error'][] = 'Invalid Template';

                }

            } else {

                $message['error'][] = 'No Category data to upload';

            }

        } catch (\ErrorException $ex) {

            Log::error($ex->getMessage());

        }

        return json_encode($message);

    }



    public function importFeatures($channel_id, $features_data) {

        try {

            ini_set('memory_limit', -1);

            ini_set('max_execution_time', 1200);

            $message = array();

            $features_data = json_decode($features_data, true);



//var_dump($features_data);

            $status_val = array(1 => 1, 0 => 0, 'Yes' => 1, 'Y' => 1, '\Y' => 1, 'No' => 0, 'N' => 0, '\N' => 0);



            if (count($features_data) > 0) {



                if (isset($features_data[0]['featurename'])) {

                    $feature_count = 0;

                    foreach ($features_data as $features) {



                        if ($features['featureid'] != '' && $features['featurename'] != '') {

                            $getfeature = Features::where('feature_id', $features['featureid'])

                                    ->where('mp_category_id', $features['categoryid'])

                                    ->where('mp_id', $channel_id)

                                    ->first();

                            $feature_id = json_decode($getfeature);

                            if (count($feature_id) == 0) {

                                $mp_key = MP::where('mp_id', $channel_id)

                                        ->pluck('mp_key')->all();

                                $isrequiredfeature = ($features['isrequiredfeature'] == '') ? 'N' : strtoupper($features['isrequiredfeature']);

                                $IsFilterFeature = ($features['isfilterfeature'] == '') ? 'N' : strtoupper($features['isfilterfeature']);

                                $insert_featuters = array(

                                    'mp_id' => $channel_id,

                                    'mp_key' => $mp_key[0],

                                    'mp_category_id' => $features['categoryid'],

                                    'feature_id' => $features['featureid'],

                                    'feature_name' => $features['featurename'],

                                    'feature_type' => $features['featuretype'],

                                    'isrequiredfeature' => $isrequiredfeature,

                                    'isfilterfeature' => $IsFilterFeature

                                );

                                $true = Features::Create($insert_featuters);



                                $feature_count++;

                                $message['success'] = $feature_count . '---Features added successfully';

                            } else {

                                $message['error'][] = 'Category Feature' . ' ' . "'" . $features['featurename'] . "'" . ' ' . 'already exists';

                            }

                        } else {

                            $message['error'][] = 'Feature Id or Name should not be empty';

                        }

                    }

                } else {

                    $message['error'][] = 'Invalid Template';

                }

            } else {

                echo "No data";

                $message['error'][] = 'No features data to upload';

            }

        } catch (\ErrorException $ex) {

            Log::error($ex->getMessage());

        }

        return json_encode($message);

    }



    public function importVariants($channel_id, $variants_data) {

        try {

            ini_set('memory_limit', -1);

            ini_set('max_execution_time', 1200);

            $message = array();

            $variants_data = json_decode($variants_data, true);

            $status_val = array(1 => 1, 0 => 0, 'Yes' => 1, 'Y' => 1, '\Y' => 1, 'No' => 0, 'N' => 0, '\N' => 0);

            if (count($variants_data) > 0) {

                if (isset($variants_data[0]['variantname'])) {

                    $varient_count = 0;

                    foreach ($variants_data as $variants) {

                        if ($variants['variantid'] != '' && $variants['variantname'] != '') {

                            $getvarient = Varients::where('featureid', $variants['featureid'])

                                    ->where('mp_option_id', $variants['variantid'])

                                    ->where('mp_id', $channel_id)

                                    ->first();

                            $varient_id = json_decode($getvarient);

                            if (count($varient_id) == 0) {

                                $mp_key = MP::where('mp_id', $channel_id)

                                        ->pluck('mp_key')->all();

                                $insert_varients = array(

                                    'mp_id' => $channel_id,

                                    'mp_key' => $mp_key[0],

                                    'featureid' => $variants['featureid'],

                                    'mp_option_id' => $variants['variantid'],

                                    'mp_option_name' => $variants['variantname'],

                                    'description' => $variants['description']

                                );

                                Varients::Create($insert_varients);

                                $varient_count++;

                                $message['success'] = $varient_count . '---Variant added successfully';

                            } else {

                                $message['error'][] = 'Variant' . ' ' . "'" . $variants['variantname'] . "'" . ' ' . ' already exist';

                            }

                        } else {

                            $message['error'][] = 'Variant Id or Name should not be empty';

                        }

                    }

                } else {

                    $message['error'][] = 'Invalid Template';

                }

            } else {

                $message['error'][] = 'No Variants data to upload';

            }

        } catch (\ErrorException $ex) {

            Log::error($ex->getMessage());

        }

        return json_encode($message);

    }



// Start Channel Category Grid------------Created by-Raju A

    public function getChannelCategoriesGrid() {

        try {

            $channel_id = Input::get('channel_id');

//echo '<pre/>';print_r(Input::get());exit;

            $page = (Input::get('page')) ? Input::get('page') : Input::get('$skip');   //Page number

            $pageSize = (Input::get('pageSize')) ? Input::get('pageSize') : Input::get('$top'); //Page size for ajax call

            $skip = $page * $pageSize;

            $all_columns = explode(',', Input::get('$select'));

            $query = MPCategories::where('mp_id', $channel_id);

            $query->skip($page * $pageSize)->take($pageSize);



            if (Input::input('$orderby')) {    //checking for sorting

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

                            $filter_value = substr($filter_value, 1, -1); //@$filter_value_array[1];

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

            }



            $channel_categories = $query->get()->all();

            $channel_categories = json_decode(json_encode($channel_categories));

//$channel_categories = MPCategories::where('mp_id',$channel_id)

//      ->get()->all();        

            $cat_data = array();

            foreach ($channel_categories as $k => $category) {

                $cat_data[$k] = $category;

                $charge_type = $category->charge_type;

                $charge_type_name = MasterLookup::where('value', $charge_type)->pluck('master_lookup_name')->all();

                $attributes_count = Features::where('mp_id', $channel_id)

                                ->where('mp_category_id', $category->mp_category_id)->count();

                $charge_type_name[0] = (isset($charge_type_name[0])) ? $charge_type_name[0] : '';

                $category->charge_type = $charge_type_name[0];

                $cat_data[$k]->attribute_count = $attributes_count;

//print_r($charge_type_name[0]);exit;

                $cat_data[$k]->actions = '<a data-toggle="modal" class="edit_category" role="button" href="#edit_cat" id="' . $category->mp_category_id . '"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp;<a class="delete cat_delete" atrr-catid="' . $category->mp_category_id . '" attr-type="categoryDelete"> <i class="fa fa-trash-o"></i> </a>';

            }

//echo '<pre/>';print_r($cat_data);exit;

            $row_count = MPCategories::where('mp_id', $channel_id)->count();



            $data['TotalRecordsCount'] = $row_count;

            $data['Records'] = $cat_data;

            return json_encode($data);

        } catch (\ErrorException $ex) {

//echo "Error";

            echo $ex->getMessage();

            exit;

            Log::error($ex->getMessage());

        }

    }



// End Channel Category Grid

// Start Channel Attribute Grid------------Created by-Raju A

    public function getCannelAttributesGrid() {

        try {

            $channel_id = Input::get('channel_id');

            $path = explode(':', Input::get('path'));

            $mp_category_id = $path[1];

            $page = (Input::get('page')) ? Input::get('page') : Input::get('$skip');   //Page number

            $pageSize = (Input::get('pageSize')) ? Input::get('pageSize') : Input::get('$top'); //Page size for ajax call

            $skip = $page * $pageSize;

            $all_columns = explode(',', Input::get('$select'));



            $query = Features::where('mp_id', $channel_id)

                    ->where('mp_category_id', $mp_category_id);



            $query->skip($page * $pageSize)->take($pageSize);

            $attributes = $query->get()->all();

            $channel_attributes = json_decode(json_encode($attributes));

            $att_data = array();

            foreach ($channel_attributes as $k => $attribute) {

                $att_data[$k] = $attribute;

                $variants_count = DB::table('mp_attr_options')

                        ->where('mp_id', $channel_id)

                        ->where('featureid', $attribute->feature_id)

                        ->count();

                $att_data[$k]->variant_count = $variants_count;

                $att_data[$k]->actions = '<a data-toggle="modal" id="' . $attribute->feature_id . '"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp;<a class="delete cat_delete" atrr-catid="' . $attribute->feature_id . '" attr-type="attributeDelete"> <i class="fa fa-trash-o"></i> </a>';

            }

            $row_count = Features::where('mp_id', $channel_id)

                    ->where('mp_category_id', $mp_category_id)

                    ->count();

            $data['TotalRecordsCount'] = $row_count;

            $data['Records'] = $att_data;

            return json_encode($data);

            exit;

        } catch (\ErrorException $ex) {

            Log::error($ex->getMessage());

        }

    }



// End Channel Attribute Grid

// Start Channel Varients/Options Grid------------Created by-Raju A

    public function getCannelVariantsGrid() {

        try {

            $channel_id = Input::get('channel_id');

            $path = explode('/', Input::get('path'));



            $page = (Input::get('page')) ? Input::get('page') : Input::get('$skip');   //Page number

            $pageSize = (Input::get('pageSize')) ? Input::get('pageSize') : Input::get('$top'); //Page size for ajax call

            $skip = $page * $pageSize;

            $all_columns = explode(',', Input::get('$select'));



            foreach ($path as $key) {

                $path_values[] = explode(':', $key);

            }

            foreach ($path_values as $value) {

                $keys[$value[0]] = $value[1];

            }

            $feature_id = $keys['feature_id'];



            $query = Varients::where('mp_id', $channel_id)

                    ->where('featureid', $feature_id);



            $query->skip($page * $pageSize)->take($pageSize);

            $variants = $query->get()->all();

            $channel_variants = json_decode(json_encode($variants));

            $variant_data = array();

            foreach ($channel_variants as $k => $variant) {

                $variant_data[$k] = $variant;

                $variant_data[$k]->actions = '<a data-toggle="modal" onclick=catEdit(' . $variant->mp_option_id . ')"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp;<a class="delete cat_delete" atrr-catid="' . $variant->mp_option_id . '" attr-type="variantDelete"> <i class="fa fa-trash-o"></i> </a>';

            }

            $row_count = Varients::where('mp_id', $channel_id)

                    ->where('featureid', $feature_id)

                    ->count();

            $data['TotalRecordsCount'] = $row_count;

            $data['Records'] = $variant_data;

            return json_encode($data);

            exit;

        } catch (\ErrorException $ex) {

            Log::error($ex->getMessage());

        }

    }



// End Channel Varients/Options Grid

// Start Channel Category Mapping Grid------------Created by-Raju A

    public function getChannelCategoriesMapGrid() {

        try {

            $channel_id = Input::get('channel_id');

            $catmap = new MPCategoryMap();

//echo '<pre/>';print_r(Input::get());exit;

            $page = (Input::get('page')) ? Input::get('page') : Input::get('$skip');   //Page number

            $pageSize = (Input::get('pageSize')) ? Input::get('pageSize') : Input::get('$top'); //Page size for ajax call

            $all_columns = explode(',', Input::get('$select'));

            $channel_categories = $catmap->getMapCategory($channel_id, $page, $pageSize);

//print_r($channel_categories);exit;

            $cat_data = array();

            foreach ($channel_categories as $k => $category) {

                $cat_data[$k] = $category;

                $eb_cat_id = $category->category_id;

                $mp_cat_id = $category->mp_category_id;

                $cat_data[$k]->mp_category_name = $category->category_name;

                $cat_data[$k]->eb_category_name = $category->cat_name;

                $attrmap = new MPAttrMap();

                $attributes_count = $attrmap->getMapAttributeCount($channel_id, $eb_cat_id);

                $cat_data[$k]->attribute_count = $attributes_count;

                $cat_data[$k]->actions = '<a data-toggle="modal" role="button" href="#myModal1" id="' . $category->mp_category_id . '"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp;<a class="delete mapcat_delete" atrr-catid="' . $category->mp_category_id . '" attr-type="mapcategoryDelete"> <i class="fa fa-trash-o"></i> </a>';

            }

//print_r($cat_data);exit;

            $row_count = MPCategoryMap::where('mp_id', $channel_id)->count();

//echo '<pre/>';print_r($cat_data);exit;

            $data['TotalRecordsCount'] = $row_count;

            $data['Records'] = $cat_data;

            return json_encode($data);

            exit;

        } catch (\ErrorException $ex) {

            Log::error($ex->getMessage());

        }

    }



// End Channel Category Mapping Grid

// Start Channel Attribute Mapping Grid------------Created by-Raju A

    public function getCannelAttributesMapGrid() {

        try {

            $attrmap = new MPAttrMap();

            $optionmap = new MPOptionMap();



            $channel_id = Input::get('channel_id');

            $path = explode(':', Input::get('path'));

            $mp_category_id = $path[1];

            $page = (Input::get('page')) ? Input::get('page') : Input::get('$skip');   //Page number

            $pageSize = (Input::get('pageSize')) ? Input::get('pageSize') : Input::get('$top'); //Page size for ajax call

            $skip = $page * $pageSize;

            $all_columns = explode(',', Input::get('$select'));

            $channel_attributes = $attrmap->getMapAttributes($channel_id, $mp_category_id, $page, $pageSize);

            $att_data = array();

            foreach ($channel_attributes as $k => $attribute) {

                $att_data[$k] = $attribute;

                $att_data[$k]->mp_attr_name = $attribute->feature_name;

                $att_data[$k]->eb_attr_name = $attribute->name;

                $mp_attr_id = $attribute->mp_att_id;

                $variants_count = $optionmap->getMapOptionsCount($channel_id, $mp_attr_id);

                $att_data[$k]->variant_count = $variants_count;

                $att_data[$k]->actions = '<a data-toggle="modal" id="' . $mp_attr_id . '"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp;<a class="delete cat_delete" atrr-catid="' . $mp_attr_id . '" attr-type="mapattributeDelete"> <i class="fa fa-trash-o"></i> </a>';

            }

            $row_count = $attrmap->getMapAttributeCount($channel_id, $mp_category_id);

            $data['TotalRecordsCount'] = $row_count;

//print_r($att_data);exit;

            $data['Records'] = $att_data;

            return json_encode($data);

            exit;

        } catch (\ErrorException $ex) {

            Log::error($ex->getMessage());

        }

    }



// End Channel Attribute Mapping Grid

// Start Channel Varients/Options Mapping Grid------------Created by-Raju A

    public function getCannelVariantsMapGrid() {

        try {

            $optionmap = new MPOptionMap();

            $channel_id = Input::get('channel_id');

            $path = explode('/', Input::get('path'));



            $page = (Input::get('page')) ? Input::get('page') : Input::get('$skip');   //Page number

            $pageSize = (Input::get('pageSize')) ? Input::get('pageSize') : Input::get('$top'); //Page size for ajax call

            $skip = $page * $pageSize;

            $all_columns = explode(',', Input::get('$select'));



            foreach ($path as $key) {

                $path_values[] = explode(':', $key);

            }

            foreach ($path_values as $value) {

                $keys[$value[0]] = $value[1];

            }

            $feature_id = $keys['mp_att_id'];

            $map_variants = $optionmap->getMapOptions($channel_id, $feature_id, $page, $pageSize);

//$variant_data=array();

            foreach ($map_variants as $k => $variant) {

                $mp_option_id = $variant->mp_option_id;

                $actions = '<a data-toggle="modal" id="' . $mp_option_id . '"> <i class="fa fa-pencil"></i> </a>&nbsp;&nbsp;<a class="delete cat_delete" atrr-catid="' . $mp_option_id . '" attr-type="mapvariantDelete"> <i class="fa fa-trash-o"></i> </a>';

                $variant->actions = $actions;

                $variant_data = array($variant);

            }

            $row_count = $optionmap->getMapOptionsCount($channel_id, $feature_id);

            $data['TotalRecordsCount'] = $row_count;

            $data['Records'] = $variant_data;

            return json_encode($data);

            exit;

        } catch (\ErrorException $ex) {

            Log::error($ex->getMessage());

        }

    }



// End Channel Varients/Options Mapping Grid

    public function getCategorydata() {

        $mp_id = Input::get('channel_id');

        $mp_cat_id = Input::get('mp_cat_id');

        $cat_data = MPCategories::where('mp_id', $mp_id)

                ->where('mp_category_id', $mp_cat_id)

                ->first();

        return json_encode($cat_data);

    }



    public function categoryDelete($cat_id) {

        try {

            $channel_id = Input::get('channel_id');

            $id = MPCategories::where('mp_category_id', $cat_id)->forcedelete();

            return $id;

        } catch (\ErrorException $ex) {

            Log::error($ex->getMessage());

        }

    }



    public function attributeDelete($attr_id) {

        try {

            $channel_id = Input::get('channel_id');

            $id = Features::where('feature_id', $attr_id)->forcedelete();

            return $id;

        } catch (\ErrorException $ex) {

            Log::error($ex->getMessage());

        }

    }



    public function variantDelete($variant_id) {

        try {

            $channel_id = Input::get('channel_id');

            $id = Varients::where('mp_option_id', $variant_id)->forcedelete();

            return $id;

        } catch (\ErrorException $ex) {

            Log::error($ex->getMessage());

        }

    }



    public function mapcategoryDelete($cat_id) {

        try {

            $channel_id = Input::get('channel_id');

            $id = MPCategoryMap::where('mp_category_id', $cat_id)->delete();

            return $id;

        } catch (\ErrorException $ex) {

            Log::error($ex->getMessage());

        }

    }



    public function mapattributeDelete($attr_id) {

        try {

            $channel_id = Input::get('channel_id');

            $id = MPAttrMap::where('mp_att_id', $attr_id)->delete();

            return $id;

        } catch (\ErrorException $ex) {

            Log::error($ex->getMessage());

        }

    }



    public function mapvariantDelete($variant_id) {

        try {

            $channel_id = Input::get('channel_id');

            $id = MPOptionMap::where('mp_option_id', $variant_id)->delete();

            return $id;

        } catch (\ErrorException $ex) {

            Log::error($ex->getMessage());

        }

    }



    public function getTreechannelCategories() {

        ini_set('memory_limit', '-1');

        $channel_id = Input::get('channel_id');

        $categ = DB::Table('mp_categories')

//->join('customer_categories', 'customer_categories.category_id', '=', 'categories.category_id')

//->leftjoin('categories as Ec', 'Ec.category_id', '=', 'mp_categories.ebutor_category_id')

                ->select('category_name as name', 'mp_category_id', 'parent_category_id as parent_id', 'mp_categories.mp_commission', 'mp_categories.charge_type as Channel_ChargeType')

                ->where('mp_categories.mp_id', $channel_id)

                ->where('mp_categories.parent_category_id', 0)

//->take(20)

                ->get()->all();

// code for parent columns

        $tempCategoryIds = array();

        $finalcategoryparent = array();

        $categoryparent = array();

        $category = json_decode(json_encode($categ), true);



        foreach ($category as $catparent) {

# code for child columns...



            $categchild = DB::Table('mp_categories')

//->leftjoin('categories as Ec', 'Ec.category_id', '=', 'mp_categories.ebutor_category_id')

                    ->select('category_name as name', 'mp_category_id', 'parent_category_id as parent_id', 'mp_categories.mp_commission', 'mp_categories.charge_type as Channel_ChargeType')

                    ->where('mp_categories.parent_category_id', $catparent['mp_category_id'])

                    ->where('mp_categories.mp_id', '=', $channel_id)

                    ->get()->all();



            $categoryparent = array();



            if (!empty($categchild)) {

                $finalcategorychild = array();

                $categorychild = array();

                $categorychildencode = json_decode(json_encode($categchild), true);

                foreach ($categorychildencode as $catchild) {



                    $getprodclass = DB::Table('mp_categories')

//->leftjoin('categories as Ec', 'Ec.category_id', '=', 'mp_categories.ebutor_category_id')

                            ->select('category_name as name', 'mp_category_id', 'parent_category_id as parent_id', 'mp_categories.mp_commission', 'mp_categories.charge_type as Channel_ChargeType')

                            ->where('mp_categories.parent_category_id', $catchild['mp_category_id'])

                            ->where('mp_categories.mp_id', '=', $channel_id)

                            ->get()->all();

                    $finalProdClassArr = array();

                    $prod = array();

                    $prodclass_details = json_decode(json_encode($getprodclass), true);

                    foreach ($prodclass_details as $values) {

                        $actions = '';

                        $prod['id'] = $values['mp_category_id'];

                        $prod['pname'] = $values['name'];

                        $prod['Channel_ChargeType'] = $values['Channel_ChargeType'];

                        if ($prod['Channel_ChargeType'] == "34001") {

                            $prod['mp_commission'] = $values['mp_commission'] . "%";

                        } else {

                            $prod['mp_commission'] = $values['mp_commission'];

                        }



                        if (!in_array($values['mp_category_id'], $tempCategoryIds)) {

                            $finalProdClassArr[] = $prod;

                        }

                        $tempCategoryIds[] = $values['mp_category_id'];

                    }



                    $categorychild['id'] = $catchild['mp_category_id'];

                    $categorychild['pname'] = $catchild['name'];

                    $categorychild['mp_commission'] = $catchild['mp_commission'];

                    $categorychild['Channel_ChargeType'] = $catchild['Channel_ChargeType'];

                    if ($categorychild['Channel_ChargeType'] == "34001") {

                        $categorychild['mp_commission'] = $categorychild['mp_commission'] . "%";

                    }



                    $categorychild['children'] = $finalProdClassArr;

                    if (!in_array($catchild['mp_category_id'], $tempCategoryIds)) {

                        $finalcategorychild[] = $categorychild;

                    }

                    $tempCategoryIds[] = $catchild['mp_category_id'];

                }



                $categoryparent['pname'] = $catparent['name'];

                $categoryparent['id'] = $catparent['mp_category_id'];

                $categoryparent['mp_commission'] = $catparent['mp_commission'];

                $categoryparent['Channel_ChargeType'] = $catparent['Channel_ChargeType'];

                if ($categoryparent['Channel_ChargeType'] == "34001") {

                    $categoryparent['mp_commission'] = $categoryparent['mp_commission'] . "%";

                }

                $categoryParentData = '';



                $categoryparent['children'] = $finalcategorychild;

                if (!in_array($catparent['mp_category_id'], $tempCategoryIds)) {

                    $finalcategoryparent[] = $categoryparent;

                }

                $tempCategoryIds[] = $catparent['mp_category_id'];

            } else {

                if (isset($catparent['parent_id']) && $catparent['parent_id'] == 0) {

                    $categoryparent['pname'] = $catparent['name'];

                    $categoryparent['id'] = $catparent['mp_category_id'];

                    $categoryparent['mp_commission'] = $catparent['mp_commission'];

                    $categoryparent['Channel_ChargeType'] = $catparent['Channel_ChargeType'];



                    if ($categoryparent['Channel_ChargeType'] == "34001" && $categoryparent['mp_commission'] != null) {

                        $categoryparent['mp_commission'] = $categoryparent['mp_commission'] . "%";

                    }

                    $categoryParentData = '';

                    if (!in_array($catparent['mp_category_id'], $tempCategoryIds)) {

                        $finalcategoryparent[] = $categoryparent;

                    }

                    $tempCategoryIds[] = $catparent['mp_category_id'];

                }

            }

        }

        return json_encode($finalcategoryparent);

    }



    public function getChannelCategoriesMapping() {

        $term = Input::get('term');

        $channel_id = (Input::get('channel_id') == '') ? Input::get('channel_id') : Session::get('channel_maxid');

        $product_arr = array();

        $getlist = db::table('mp_categories')

                ->select('parent_category_id', 'is_leaf_category', 'category_name', 'mp_category_id', 'mp_id', 'mp_commission', 'charge_type', 'currency_id')

                ->where('category_name', 'like', '%' . $term . '%')

                ->where('parent_category_id', '=', 0)

                ->where('mp_id', $channel_id)

                ->get()->all();

        foreach ($getlist as $get) {

            $category_name = '';

            $get_parent = db::table('mp_categories')

                    ->select('is_leaf_category', 'category_name')

                    ->where('mp_category_id', $get->parent_category_id)

                    ->first();

            if (isset($get_parent->is_leaf_category)) {

                $category_name = $get->category_name . '(' . $get_parent->category_name . ')';

            } else {

                $category_name = $get->category_name;

            }

            $product = array("label" => $category_name, "mp_category_id" => $get->mp_category_id, "mp_commission" => $get->mp_commission, "charge_type" => $get->charge_type, "currency" => $get->currency_id);

            array_push($product_arr, $product);

        }

        echo json_encode($product_arr);

    }



    public function getChannelSubCategories() {

        $term = Input::get('term');

        $channel_id = (Input::get('channel_id') == '') ? Input::get('channel_id') : Session::get('channel_maxid');

        $main_cat_id = Input::get('main_cat_id');

        $product_arr = array();

        $getlist = db::table('mp_categories')

                ->select('parent_category_id', 'is_leaf_category', 'category_name', 'mp_category_id', 'mp_id', 'mp_commission', 'charge_type', 'currency_id')

                ->where('category_name', 'like', '%' . $term . '%')

                ->where('parent_category_id', '=', 0)

                ->where('mp_id', $channel_id)

                ->get()->all();

        foreach ($getlist as $get) {

            $category_name = '';

            $get_parent = db::table('mp_categories')

                    ->select('is_leaf_category', 'category_name')

                    ->where('mp_category_id', $get->parent_category_id)

                    ->first();

            if (isset($get_parent->is_leaf_category)) {

                $category_name = $get->category_name . '(' . $get_parent->category_name . ')';

            } else {

                $category_name = $get->category_name;

            }

            $product = array("label" => $category_name, "mp_category_id" => $get->mp_category_id, "mp_commission" => $get->mp_commission, "charge_type" => $get->charge_type, "currency" => $get->currency_id);

            array_push($product_arr, $product);

        }

        echo json_encode($product_arr);

    }



    public function getEdCategoriesMapping() {

        $term = Input::get('term');

        $product_arr = array();

        $getlist = db::table('categories')

                ->select('name', 'category_id')

                ->where('name', 'like', '%' . $term . '%')

                ->get()->all();

        foreach ($getlist as $get) {

            $product = array("label" => $get->name, "category_id" => $get->category_id);

//$product_arr[]= $get->category_name;

            array_push($product_arr, $product);

        }

        echo json_encode($product_arr);

    }



    public function getTreechannelcategorymapping() {

        ini_set('memory_limit', '-1');

//$channel_id=Input::get('channel_id');

        $mp_categories = DB::table('mp_categories')

                ->get()->all();

//print_r($mp_categories);exit;



        $json1 = json_encode($mp_categories);

        $data1['Records'] = $json1;

//print_r(json_encode($data1));



        $json = json_encode($data1);



//$json=json_encode($json->Records);





        /* $json = '{"Records":[{ "ID": 1, "Name": "Amsterdam", "ProductNumber": "BA-8444", "MakeFlag": false, "FinishedGoodsFlag": false, "Color": null, "SafetyStockLevel": 1000, "ReorderPoint": 750, "StandardCost": 0.0000, "ListPrice": 0.0000, "Size": null, "SizeUnitMeasureCode": null, "WeightUnitMeasureCode": null, "Weight": null, "DaysToManufacture": 0, "ProductLine": null, "Class": null, "Style": null, "ProductSubcategoryID": null, "ProductModelID": null, "SellStartDate": "\/Date(896648400000)\/", "SellEndDate": null, "DiscontinuedDate": null, "rowguid": "694215b7-08f7-4c0d-acb1-d734ba44c0c8", "ModifiedDate": "\/Date(1078992096827)\/" }, 

          { "ID": 993, "Name": "Mountain-500 Black, 52", "ProductNumber": "BK-M18B-52", "MakeFlag": true, "FinishedGoodsFlag": true, "Color": "Black", "SafetyStockLevel": 100, "ReorderPoint": 75, "StandardCost": 294.5797, "ListPrice": 539.9900, "Size": "52", "SizeUnitMeasureCode": "CM ", "WeightUnitMeasureCode": "LB ", "Weight": 28.68, "DaysToManufacture": 4, "ProductLine": "M ", "Class": "L ", "Style": "U ", "ProductSubcategoryID": 1, "ProductModelID": 23, "SellStartDate": "\/Date(1057006800000)\/", "SellEndDate": null, "DiscontinuedDate": null, "rowguid": "69ee3b55-e142-4e4f-aed8-af02978fbe87", "ModifiedDate": "\/Date(1078992096827)\/" },

          { "ID": 994, "Name": "LL Bottom Bracket", "ProductNumber": "BB-7421", "MakeFlag": true, "FinishedGoodsFlag": true, "Color": null, "SafetyStockLevel": 500, "ReorderPoint": 375, "StandardCost": 23.9716, "ListPrice": 53.9900, "Size": null, "SizeUnitMeasureCode": null, "WeightUnitMeasureCode": "G  ", "Weight": 223.00, "DaysToManufacture": 1, "ProductLine": null, "Class": "L ", "Style": null, "ProductSubcategoryID": 5, "ProductModelID": 95, "SellStartDate": "\/Date(1057006800000)\/", "SellEndDate": null, "DiscontinuedDate": null, "rowguid": "fa3c65cd-0a22-47e3-bdf6-53f1dc138c43", "ModifiedDate": "\/Date(1078992096827)\/" },

          { "ID": 995, "Name": "ML Bottom Bracket", "ProductNumber": "BB-8107", "MakeFlag": true, "FinishedGoodsFlag": true, "Color": null, "SafetyStockLevel": 500, "ReorderPoint": 375, "StandardCost": 44.9506, "ListPrice": 101.2400, "Size": null, "SizeUnitMeasureCode": null, "WeightUnitMeasureCode": "G  ", "Weight": 168.00, "DaysToManufacture": 1, "ProductLine": null, "Class": "M ", "Style": null, "ProductSubcategoryID": 5, "ProductModelID": 96, "SellStartDate": "\/Date(1057006800000)\/", "SellEndDate": null, "DiscontinuedDate": null, "rowguid": "71ab847f-d091-42d6-b735-7b0c2d82fc84", "ModifiedDate": "\/Date(1078992096827)\/" },

          { "ID": 996, "Name": "HL Bottom Bracket", "ProductNumber": "BB-9108", "MakeFlag": true, "FinishedGoodsFlag": true, "Color": null, "SafetyStockLevel": 500, "ReorderPoint": 375, "StandardCost": 53.9416, "ListPrice": 121.4900, "Size": null, "SizeUnitMeasureCode": null, "WeightUnitMeasureCode": "G  ", "Weight": 170.00, "DaysToManufacture": 1, "ProductLine": null, "Class": "H ", "Style": null, "ProductSubcategoryID": 5, "ProductModelID": 97, "SellStartDate": "\/Date(1057006800000)\/", "SellEndDate": null, "DiscontinuedDate": null, "rowguid": "230c47c5-08b2-4ce3-b706-69c0bdd62965", "ModifiedDate": "\/Date(1078992096827)\/" },

          { "ID": 997, "Name": "Road-750 Black, 44", "ProductNumber": "BK-R19B-44", "MakeFlag": true, "FinishedGoodsFlag": true, "Color": "Black", "SafetyStockLevel": 100, "ReorderPoint": 75, "StandardCost": 343.6496, "ListPrice": 539.9900, "Size": "44", "SizeUnitMeasureCode": "CM ", "WeightUnitMeasureCode": "LB ", "Weight": 19.77, "DaysToManufacture": 4, "ProductLine": "R ", "Class": "L ", "Style": "U ", "ProductSubcategoryID": 2, "ProductModelID": 31, "SellStartDate": "\/Date(1057006800000)\/", "SellEndDate": null, "DiscontinuedDate": null, "rowguid": "44ce4802-409f-43ab-9b27-ca53421805be", "ModifiedDate": "\/Date(1078992096827)\/" },

          { "ID": 998, "Name": "Road-750 Black, 48", "ProductNumber": "BK-R19B-48", "MakeFlag": true, "FinishedGoodsFlag": true, "Color": "Black", "SafetyStockLevel": 100, "ReorderPoint": 75, "StandardCost": 343.6496, "ListPrice": 539.9900, "Size": "48", "SizeUnitMeasureCode": "CM ", "WeightUnitMeasureCode": "LB ", "Weight": 20.13, "DaysToManufacture": 4, "ProductLine": "R ", "Class": "L ", "Style": "U ", "ProductSubcategoryID": 2, "ProductModelID": 31, "SellStartDate": "\/Date(1057006800000)\/", "SellEndDate": null, "DiscontinuedDate": null, "rowguid": "3de9a212-1d49-40b6-b10a-f564d981dbde", "ModifiedDate": "\/Date(1078992096827)\/" },

          { "ID": 999, "Name": "Road-750 Black, 52", "ProductNumber": "BK-R19B-52", "MakeFlag": true, "FinishedGoodsFlag": true, "Color": "Black", "SafetyStockLevel": 100, "ReorderPoint": 75, "StandardCost": 343.6496, "ListPrice": 539.9900, "Size": "52", "SizeUnitMeasureCode": "CM ", "WeightUnitMeasureCode": "LB ", "Weight": 20.42, "DaysToManufacture": 4, "ProductLine": "R ", "Class": "L ", "Style": "U ", "ProductSubcategoryID": 2, "ProductModelID": 31, "SellStartDate": "\/Date(1057006800000)\/", "SellEndDate": null, "DiscontinuedDate": null, "rowguid": "ae638923-2b67-4679-b90e-abbab17dca31", "ModifiedDate": "\/Date(1078992096827)\/" }

          ]}'; */

        return $json;

        exit;

    }



    public function getchannelcategories_mapping($cid) {



        $term = Input::get('term');

//echo $term;die;

//$channel_id=Input::get('channel_id');



        $product_arr = array();





        $getlist = db::table('mp_categories')

                ->select('parent_category_id', 'is_leaf_category', 'category_name', 'mp_category_id', 'mp_commission', 'charge_type', 'currency_id')

                ->where('category_name', 'like', '%' . $term . '%')

                ->where('mp_id', $cid)

                ->whereNotIn('mp_category_id', function($query) {

                    $query->select(DB::raw('parent_category_id'))

                    ->from('mp_categories');

                })

                ->get()->all();



        foreach ($getlist as $get) {

            $category_name = '';

            $get_parent = db::table('mp_categories')

                    ->select('is_leaf_category', 'category_name')

                    ->where('mp_category_id', $get->parent_category_id)

                    ->where('mp_id', $cid)

                    ->first();

            if (isset($get_parent->is_leaf_category)) {

                $category_name = $get->category_name . '(' . $get_parent->category_name . ')';

            } else {

                $category_name = $get->category_name;

            }

            $product = array("label" => $category_name, "mp_category_id" => $get->mp_category_id, "channel_commission" => $get->mp_commission, "charge_type" => $get->charge_type, "currency" => $get->currency_id);

            array_push($product_arr, $product);

        }

        echo json_encode($product_arr);

    }



    public function getebutorcategories() {



        $term = Input::get('term');

        $product_arr = array();

        $getlist = db::table('categories')

                ->select('parent_id', 'is_product_class', 'cat_name', 'category_id', 'charge_type')

                ->where('cat_name', 'like', '%' . $term . '%')

                ->get()->all();

        foreach ($getlist as $get) {

            $category_name = '';

            $get_parent = db::table('categories')

                    ->select('is_product_class', 'cat_name')

                    ->where('category_id', $get->parent_id)

                    ->first();

            if (isset($get_parent->is_product_class)) {

                $cat_name = $get->cat_name . '(' . $get_parent->cat_name . ')';

            } else {

                $cat_name = $get->cat_name;

            }

            $product = array("label" => $cat_name, "category_id" => $get->category_id, "charge_type" => $get->charge_type);

            array_push($product_arr, $product);

        }

        echo json_encode($product_arr);

    }



    public function getebutorattributes() {

        $data = Input::get();

        $category_id = DB::table('categories')

                ->where('cat_name', $data['category_name'])

                ->pluck('category_id')->all();

//print_r($category_id);die;

        $ebutor_attribute_id = DB::table('attribute_set_mapping as atrmap')

                ->leftjoin('attribute_sets as atrs', 'atrmap.attribute_set_id', '=', 'atrs.attribute_set_id')

                ->where('atrs.category_id', $category_id[0])

                ->select('atrmap.attribute_id')

                ->get()->all();

//print_r($ebutor_attributes);exit;

        foreach ($variable as $key => $value) {

# code...

        }

    }



    public function getchannelattributes($cid) {

//modified By Naresh Pulipati

        $data = Input::get();

        $channel_attributes = DB::table('mp_attributes as mpa')

                ->join('mp_attr_options as mpo', 'mpo.featureid', '=', 'mpa.feature_id')

                ->where('mpo.mp_id', $cid)

                ->where('mpa.mp_category_id', $data['categoryid'])

                ->where('mpa.mp_id', $cid)

                ->select('mpa.mp_category_id', 'mpa.feature_id', 'mpa.feature_name', 'mpo.mp_option_id', 'mpo.mp_option_name')

                ->get()->all(); //, 'mpa.mp_key', 'mpa.mp_id',



        if (count($channel_attributes) == 0) {

            echo "No Attributes available for this Category";

        } else {

            $structuredData = array();

            foreach ($channel_attributes as $ca_key => $ca_val) {

                if ($ca_val->feature_id != '') {

                    $structuredData[$ca_val->mp_category_id][$ca_val->feature_id]['FeatureName'] = $ca_val->feature_name;

                    if ($ca_val->mp_option_id != "") {

                        $structuredData[$ca_val->mp_category_id][$ca_val->feature_id]['options'][$ca_val->mp_option_id] = $ca_val->mp_option_name;

                    }

                }

            }

            $mapping_structure = "<style type='text/css'>";

            $mapping_structure .= ".acc-block{margin:0 30px;}";

            $mapping_structure .= ".acc-main-tab{background:#ccc;width:100%;height:30px;margin:10px 0px}.acc-main-tab td first{padding-left:25px}";

            $mapping_structure .= ".acc-head-tab{width:100%;height:30px} .acc-head-tab span{margin-left:25px;} .acc-head-tab select{margin-right:25px;}";

            $mapping_structure .= ".acc-head-tab td{width:45%} .acc-head-tab td:first-child{width:10%} .panel-heading select{min-width:60%}";

            $mapping_structure .= ".acc-body-tab{width:100%;border:1px solid #ccc} .main tr{background:#ccc;height:30px;} .acc-body-tab tr:first-child td{padding-left:25px}";

            $mapping_structure .= ".acc-body-tab{width:100%;border:1px solid #ccc} .acc-body-tab td{padding:2px;padding-left:25px;} .acc-body-tab  select{min-width:60%}";

            $mapping_structure .= ".outer-acc-body-tab{height:150px;overflow:scroll;border-right:1px solid #ccc;border-bottom:1px solid #ccc}";

            $mapping_structure .= " #accordion3{height:360px;overflow:scroll;} .outer-acc-body{height:250px;overflow:scroll}";

            $mapping_structure .= "</style>";

            $mapping_structure .= '<div class="acc-block">';

            $mapping_structure .= '<table class="acc-main-tab"><tr><td>Channel Attribute</td><td>Ebutor Attribute</td></tr></table>';

            $mapping_structure .= '<div class="panel-group accordion" id="accordion3">';

            $feature_table = "";

            foreach ($structuredData[$data['categoryid']] as $std_key => $std_val) {

                /* Complete Design */

                $mapping_structure .= ' <div class="panel panel-default "> <div class="panel-heading">';

                $mapping_structure .= '<table class="acc-head-tab"><tr><td>';

                $mapping_structure .= '<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_' . $std_key . '" aria-expanded="false">';

                $mapping_structure .= '<span class="fa fa-edit black"></span></a></td>';

                $mapping_structure .= '<td><input type="hidden" class="row_eb_block" id="row_eb_' . $std_key . '">';

                $mapping_structure .= '<h4 class="panel-title">' . $std_val['FeatureName'] . "</h4></td>";

                $mapping_structure .= '<td><select></select></td></tr></table>';

                $mapping_structure .= "</div>"; // . "<select class='hid_eb_feature pull-right' name='hid_eb_feature'></select>

                $mapping_structure .= ' <div id="collapse_' . $std_key . '" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">';

                $mapping_structure .= ' <div class="panel-body">';

                $mapping_structure .= "<table class='acc-body-tab main'>";

                $mapping_structure .= "<tr><td>Channel Value</td>";

                $mapping_structure .= "<input type='hidden' class='hid_feature' id='" . $std_key . "' >";

                $mapping_structure .= "<td>Ebutor Value</td></tr></table>";

                $mapping_structure .= "<div class='outer-acc-body-tab'><table class='acc-body-tab rows'>";

                foreach ($std_val['options'] as $op_key => $op_val) {

                    $tr_id = "rowkey_" . $std_key . "_" . $op_key;

                    $mapping_structure .= "<tr id=" . $tr_id . " style='border-bottom:1px solid #ccc'><td class='fname' id=" . $op_key . " style='border-right:1px solid #ccc;width:50%'>" . $op_val . "</td><td><select></select></td></tr>";

                }





                $mapping_structure .="</table></div>";

                $mapping_structure .= '</div></div></div>';

            }

            $mapping_structure .= "</div></div>";





            echo $mapping_structure;

        }

    }



    public function getparentcategories() {

        $term = Input::get('term');

//echo $term;

        $product_arr = array();

        $channel_id = Input::get('channel_id');

//print_r($channel_id);exit;

        $getlist = MPCategories::select('parent_category_id', 'is_leaf_category', 'category_name', 'mp_category_id', 'mp_commission', 'charge_type', 'currency_id')

                ->where('category_name', 'like', '%' . $term . '%')

                ->where('mp_id', $channel_id)

                ->get()->all();

//print_r($getlist);exit;

        foreach ($getlist as $get) {

            $category_name = '';

            $get_parent = MPCategories::select('is_leaf_category', 'category_name')

                    ->where('mp_category_id', $get->parent_category_id)

                    ->first();

            if (isset($get_parent->is_leaf_category)) {

                $category_name = $get->category_name . '(' . $get_parent->category_name . ')';

            } else {

                $category_name = $get->category_name;

            }

            $product = array("label" => $category_name, "mp_category_id" => $get->mp_category_id, "channel_commission" => $get->mp_commission, "charge_type" => $get->charge_type, "currency" => $get->currency_id);

            array_push($product_arr, $product);

        }

        echo json_encode($product_arr);

    }



    public function addchannelcategories() {

        try {

            $input = Input::get();

            $channel_id = (Session::get('channel_maxid') != '') ? Session::get('channel_maxid') : Input::get('channel_id');



            $parent_id = $input['hidden_parent_id'];

            $parent_leaf = MPCategories::select('is_leaf_category')

                    ->where('parent_category_id', $parent_id)

                    ->get()->all();

            /* if(isset($parent_leaf)){

              $is_leaf=1; */



            $checkcat = MPCategories::where('category_name', $input['channel_catgory'])

                    ->get()->all();

            $cat = json_decode(json_encode($checkcat));

            if (!empty($cat)) {

                $message[] = 'Category already exist';

            } else {

                $mp_key = MP::where('mp_id', $channel_id)

                        ->pluck('mp_key')->all();

                $insert = array('mp_id' => $channel_id,

                    'mp_key' => $mp_key[0],

                    'mp_category_id' => $input['category_ID'],

                    'category_name' => $input['channel_catgory'],

                    'parent_category_id' => $parent_id,

                    //'is_leaf_category' => $is_leaf,

                    'mp_commission' => $input['channel_cat_fee'],

                    'charge_type' => $input['category_chargeType'],

                );

                MPCategories::Create($insert);

                $message[] = 'Category Created Successfully';

            }

        } catch (\ErrorException $ex) {

            $message = $ex->getMessage();

            Log::error($ex->getMessage());

            Log::error($ex->getTraceAsString());

        }

        return $message;

    }



    public function editChannelCategories() {

        try {

            $input = Input::get();

            $channel_id = $input['channel_id'];

            $id = $input['edit_channel_catgory_id'];

            $mp_commission = $input['edit_channel_cat_fee'];

            $charge_type = $input['edit_category_chargeType'];

            $update = array('mp_commission' => $mp_commission,

                'charge_type' => $charge_type);

            MPCategories::where('id', $id)

                    ->update($update);

            $message = 'updated successfully';

        } catch (\ErrorException $ex) {

            $message = $ex->getMessage();

            Log::error($ex->getMessage());

            Log::error($ex->getTraceAsString());

        }

        return $message;

    }



    public function checkUniquevalue() {

        try {

            $data = Input::all();

            $channel_id = (Session::get('channel_maxid') != '') ? Session::get('channel_maxid') : Input::get('channel_id');



            $categoryname = isset($data['channel_catgory']) ? $data['channel_catgory'] : '';

            $result = false;

            $id = MPCategories::where('category_name', $categoryname)->where('mp_id', $channel_id)->pluck('mp_category_id')->all();

            if (count($id) == 0) {

                $result = true;

            }

            return json_encode(array('valid' => $result));

        } catch (\ErrorException $ex) {

            $response['message'] = $ex->getMessage();

            Log::error($ex->getMessage());

            Log::error($ex->getTraceAsString());

        }

    }



    public function checkUniquecategoryid() {

        try {

            $data = Input::all();

            $channel_id = (Session::get('channel_maxid') != '') ? Session::get('channel_maxid') : Input::get('channel_id');



            $categoryid = isset($data['category_ID']) ? $data['category_ID'] : '';

            $result = false;

            $id = MPCategories::where('mp_category_id', $categoryid)->where('mp_id', $channel_id)

                    ->pluck('id')->all();

            if (count($id) == 0) {

                $result = true;

            }

            return json_encode(array('valid' => $result));

        } catch (\ErrorException $ex) {

            $response['message'] = $ex->getMessage();

            Log::error($ex->getMessage());

            Log::error($ex->getTraceAsString());

        }

    }



    public function getebutorCategoryattributes() {

        $data = Input::get();





        $ebutor_attributes = DB::table('attribute_sets as aset')

                ->join('attribute_set_mapping as asm', 'asm.attribute_set_id', '=', 'aset.attribute_set_id')

                ->join('attributes as a', 'a.attribute_id', '=', 'asm.attribute_id')

                ->where('aset.category_id', $data['categoryid'])

                ->select('aset.attribute_set_id', 'asm.attribute_id', 'a.name')

                ->get()->all();

//  echo "<pre>";print_r($ebutor_attributes);exit;







        $attributes = array();

        $attributes['count'] = count($ebutor_attributes);

        foreach ($ebutor_attributes as $eb_key => $eb_att) {



            $attributes['attributes'][$eb_key]['attribute_id'] = $eb_att->attribute_id;

            $attributes['attributes'][$eb_key]['name'] = $eb_att->name;

        }

        echo json_encode($attributes);

    }



    public function addchannelstatus() {
        $data = Input::get();
        $channel_id = Input::get('channel_id');
        $active = $data['active_status'];
        if (!empty($data)) {
            $data = $data;
        } else {
            $data = '';
        }
        $checkebutor = DB::table('master_lookup')
                ->where('master_lookup_name', $data['ebutor_status'])
                ->first();
        $checkebutoro = json_decode(json_encode($checkebutor));
        $checkstat = OrderstatusMapping::where('mp_status', $data['channel_status'])
                ->where('mp_id', $channel_id)
                ->where('status_type', $data['status_type'])
                ->first();
        $stat = json_decode(json_encode($checkstat));

        if (!empty($stat)) {
            return json_encode($stat);
        } else {
            $insert = array('mp_id' => $channel_id,
                'mp_status' => $data['channel_status'],
                'status_type' => $data['status_type'],
                'ebutor_status_id' => $checkebutoro->value,
                'is_active' => 1
            );
            OrderstatusMapping::insert($insert);
        }
    }



    public function getOrderstatusList() {

        $channel_id = Input::get('channel_id');
        $ebutor_master_statusid = DB::table('master_lookup_categories')
                ->where('mas_cat_name', 'Order Status')
                ->first();
        $ebutor_order_stat = DB::table('master_lookup')
                ->where('mas_cat_id', $ebutor_master_statusid->mas_cat_id)
                ->select('master_lookup_name')
                ->get()->all();
        $mapping_dat = OrderstatusMapping::where('mp_id', $channel_id)
                ->get()->all();
        $mapping_data = json_decode(json_encode($mapping_dat));
        $ebutor_stat = array();
        if (!empty($mapping_data)) {
            foreach ($mapping_data as $key => $value) {

                if ($value->mp_status_id != 0) {

                    $ebutor_stat_name = MasterLookup::where('value', $value->ebutor_status_id)
                            ->select('master_lookup_name')
                            ->first();
                    $ebutor_stat_name = json_decode(json_encode($ebutor_stat_name));
                    $status_type = (isset($value->status_type)) ? $value->status_type : '';
                    $mp_status = (isset($value->mp_status)) ? $value->mp_status : '';
                    $eb_status_name = (isset($ebutor_stat_name->master_lookup_name)) ? $ebutor_stat_name->master_lookup_name : '';
                    $mapping_id = (isset($value->mp_status_id)) ? $value->mp_status_id : '';
                    $active_status = (isset($value->active_status)) ? $value->active_status : '';
                    $ebutor_stat[$key]['status_type'] = $status_type;
                    $ebutor_stat[$key]['mp_status_name'] = $mp_status;
                    $ebutor_stat[$key]['eb_status_name'] = $eb_status_name;
                    $ebutor_stat[$key]['mapping_id'] = $mapping_id;
                    $ebutor_stat[$key]['active_status'] = $active_status;
                }

            }
            $data = array('channel_id' => $channel_id,
                'ebutor_order_stat' => $ebutor_order_stat,
                'mapping_data_status' => $ebutor_stat,
            );
        } else {
            $data = array('channel_id' => $channel_id,
                'ebutor_order_stat' => $ebutor_order_stat,
                'mapping_data_status' => '',
            );
        }
        return View::make('channel.tabs.orderStatusList')->with('data', $data);

    }



    public function getmapdetails() {

        $channel_mapping_id = Input::get('stat_id');

        $channel_map_data = OrderstatusMapping::where('mp_status_id', $channel_mapping_id)

                ->first();

        $active = $channel_map_data->active_status;

        if (!empty($channel_mapping_id) && !empty($channel_map_data)) {

            $status = OrderstatusMapping::where('mp_status_id', $channel_map_data->mp_status_id)

                    ->first();

            $ebutor_status = MasterLookup::where('value', $channel_map_data->ebutor_status_id)

                    ->first();

            $status->ebutor_status = $ebutor_status->master_lookup_name;

            $status->active_status = $active;

            $status->id = $channel_mapping_id;

        }

        return json_encode($status);

    }



    public function delChannelstatus() {

        $id = Input::get('id');

        $del_status = OrderstatusMapping::where('mp_status_id', '=', $id)->delete();

        return $del_status;

    }



    public function getupdatedstatus() {

        $data = Input::get();

        $channel_id = Input::get('channel_id');

        $id = Input::get('update_status_id');

        if (count($data) != 0) {

            $active = $data['active_status'];

//            $checkmp = OrderstatusMapping::where('mp_status', $data['channel_status'])->where('mp_id', $channel_id)

//                    ->get()->all();

//            $checkmpo = json_decode(json_encode($checkmp));

//            if (empty($checkmpo)) {

//                $insert = array('mp_id' => $channel_id,

//                    'status_type' => $data['status_type'],

//                    'mp_status' => $data['channel_status']

//                );

//                OrderstatusMapping::Create($insert);

//            }

            $checkebutor = DB::table('master_lookup')

                    ->where('master_lookup_name', $data['ebutor_status'])

                    ->first();

            $checkebutoro = json_decode(json_encode($checkebutor));

            $checkstatus = OrderstatusMapping::where('mp_status', $data['channel_status'])->where('mp_id', $channel_id)

                    ->where('status_type', $data['status_type'])

                    ->first();

            $checkstatuso = json_decode(json_encode($checkstatus));

            if (empty($checkstatuso)) {

                $update_mapstatus = array('ebutor_status_id' => $checkebutoro->value,

                    'mp_status' => $data['channel_status'],

                    'status_type' => $data['status_type'],

                    'active_status' => $active

                );

                $true = OrderstatusMapping::where('mp_id', $channel_id)

                        ->where('mp_status_id', $id)

                        ->update($update_mapstatus);

//                $true = Orderstatus::where('mp_id', $channel_id)

//                        ->where('mp_status_id', $checkstatuso->mp_status_id)

//                        ->update(array('status_type' => $data['status_type']));

                $message = 'Sucessfully Updated';

            } else {

                $message = $data['status_type'] . ' ' . 'Status Already Exists';

            }

            return json_encode($message);

        }

    }



}
