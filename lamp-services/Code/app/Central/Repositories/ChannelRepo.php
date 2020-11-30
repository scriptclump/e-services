<?php

namespace Central\Repositories;

//use Central\Repositories\AmazonApiRepo;
//namespace Central\MarketplaceWebService; 
//use controllers\AmazondeveloperController; 

use Token;
use User;
use DB;  //Include laravel db class
use Session;

class ChannelRepo {

    public function allServiceTypes() {
        $serviceTypes = DB::Table('mp_service_type as Cst')
                ->select('Cst.service_type_id', 'Cst.service_name')
                ->get()->all();

        return $serviceTypes;
    }

    public function allCurrencyTypes() {
        $CurrencyTypes = DB::Table('currency as Cu')
                ->select('Cu.currency_id', 'Cu.code')
                ->get()->all();

        return $CurrencyTypes;
    }

    public function allpaymentsTypes() {
        $paymentsTypes = DB::Table('master_lookup as Ml')
                ->select('Ml.master_lookup_id', 'Ml.value', 'Ml.master_lookup_name')
                ->where('Ml.mas_cat_id', '=', "35")
                ->get()->all();

        return $paymentsTypes;
    }

    public function allchannelCharges($channel_id) {
        $channelCharges = Db::Table('mp_charges as Cc')
                ->leftJoin('currency as Cu', 'Cu.currency_id', '=', 'Cc.currency_id')
                ->leftJoin('mp_service_type as Cst', 'Cst.service_type_id', '=', 'Cc.service_type_id')
                ->leftJoin('master_lookup as Ml', 'Ml.value', '=', 'Cc.recurring_interval')
                ->select('Cst.service_name', 'Cc.ed_fee as ebutor_fee', 'Cc.charges', 'Cc.charge_type', 'Cu.code', 'Cc.is_recurring', 'Ml.master_lookup_name as recurring_interval', 'Cc.mp_charges_id')
                ->where('Cc.mp_id', '=', $channel_id)
                ->get()->all();
        return $channelCharges;
    }

    public function channelCred($channel_id) {
        $channelCred = DB::Table('mp_configuration as Con')
                ->select('Con.Key_name', 'Con.Key_value', 'Con.mp_configuration_id')
                ->where('Con.mp_id', '=', $channel_id)
                ->get()->all();
        return $channelCred;
    }

    public function keyCred($channel_configuration_id) {
        $keyCred = DB::Table('channel_configuration as Con')
                ->select('Con.Key_name', 'Con.Key_value', 'Con.channel_configuration_id')
                ->where('Con.channel_configuration_id', '=', $channel_configuration_id)
                ->first();


        return $keyCred;
    }

    public function ebutorCategories() {
        $ebutorCategories = DB::Table('categories as Ec')
                ->select('Ec.category_id', 'Ec.cat_name')
                ->get()->all();

        return $ebutorCategories;
    }

    public function getChannelCategories($channel_id) {
        $getChannelCategories = DB::Table('mp_categories as Cc')
                ->select('Cc.id', 'Cc.category_name')
                ->where('Cc.mp_id', '=', $channel_id)
                ->where('Cc.parent_category_id', '=', 0)
                ->get()->all();
          $last = DB::getQueryLog();
          echo "<pre>";print_r(end($last));die; 

        return $getChannelCategories;
    }

    public function location() {

        $location = DB::Table('countries as Cou')
                ->select('Cou.iso_code_3', 'Cou.name')
                ->get()->all();

        return $location;
    }

    public function chargeType() {

        $chargeType = DB::Table('master_lookup as Ml')
                ->join('master_lookup_categories as LC', 'LC.mas_cat_id', '=', 'Ml.mas_cat_id')
                ->select('Ml.master_lookup_id', 'Ml.value', 'Ml.master_lookup_name')
                ->where('LC.mas_cat_name', '=', "Charge Type")
                ->get()->all();

        return $chargeType;
    }

    public function totalCateg($id) {
        $categ = DB::Table('channel_categories')
                //->join('customer_categories', 'customer_categories.category_id', '=', 'categories.category_id')
                ->leftjoin('categories as Ec', 'Ec.category_id', '=', 'channel_categories.ebutor_category_id')
                ->select('channel_categories.category_name as name', 'channel_categories.channel_id as channel_id', 'channel_categories.id as category_id', 'channel_categories.parent_category_id as parent_id', 'Ec.name as EbutorCategory', 'Ec.category_id as ebutor_category_id', 'Ec.ebutor_commission as Ebutor_Commission', 'channel_categories.channel_id', 'channel_categories.channel_commission', 'channel_categories.charge_type as Channel_ChargeType', 'Ec.charge_type as Ebutor_ChargeType')
                //->where('categories.parent_id', 0)
                ->where('channel_categories.channel_id', '=', $id)
                ->get()->all();

        return $categ;
    }

    public function getChildCateg($id) {
        $categchild = DB::Table('channel_categories')
                ->leftjoin('categories as Ec', 'Ec.category_id', '=', 'channel_categories.ebutor_category_id')
                ->select('channel_categories.category_name as name', 'channel_categories.channel_id as channel_id', 'channel_categories.id as category_id', 'channel_categories.parent_category_id as parent_id', 'Ec.name as EbutorCategory', 'Ec.category_id', 'Ec.ebutor_commission as Ebutor_Commission', 'channel_categories.channel_id', 'channel_categories.channel_commission', 'channel_categories.charge_type as Channel_ChargeType', 'Ec.charge_type as Ebutor_ChargeType')
                ->where('channel_categories.parent_category_id', $catparent['category_id'])
                ->where('channel_categories.channel_id', '=', $id)
                ->whereIn('channel_categories.id', $categoryArray)
                ->get()->all();

        return $categchild;
    }

    public function ChildCateg($id) {
        $categchild = DB::Table('channel_categories')
                ->leftjoin('categories as Ec', 'Ec.category_id', '=', 'channel_categories.ebutor_category_id')
                ->select('channel_categories.category_name as name', 'channel_categories.channel_id as channel_id', 'channel_categories.id as category_id', 'channel_categories.parent_category_id as parent_id', 'Ec.name as EbutorCategory', 'Ec.category_id', 'Ec.ebutor_commission as Ebutor_Commission', 'channel_categories.channel_id', 'channel_categories.channel_commission', 'channel_categories.charge_type as Channel_ChargeType', 'Ec.charge_type as Ebutor_ChargeType')
                ->where('channel_categories.parent_category_id', $catparent['category_id'])
                ->where('channel_categories.channel_id', '=', $id)
                ->get()->all();

        return $categchild;
    }

    public function getProdclass($id) {
        $getprodclass = DB::Table('channel_categories')
                ->leftjoin('categories as Ec', 'Ec.category_id', '=', 'channel_categories.ebutor_category_id')
                ->select('channel_categories.category_name as name', 'channel_categories.channel_id as channel_id', 'channel_categories.id as category_id', 'channel_categories.parent_category_id as parent_id', 'Ec.name as EbutorCategory', 'Ec.category_id', 'Ec.ebutor_commission as Ebutor_Commission', 'channel_categories.channel_id', 'channel_categories.channel_commission', 'channel_categories.charge_type as Channel_ChargeType', 'Ec.charge_type as Ebutor_ChargeType')
                ->whereIn('channel_categories.id', $categoryArray)
                ->where('channel_categories.channel_id', '=', $id)
                ->where('channel_categories.parent_category_id', $catchild['category_id'])
                ->get()->all();

        return $getprodclass;
    }

    public function Prodclass($id) {
        $getprodclass = DB::Table('channel_categories')
                ->leftjoin('categories as Ec', 'Ec.category_id', '=', 'channel_categories.ebutor_category_id')
                ->select('channel_categories.category_name as name', 'channel_categories.channel_id as channel_id', 'channel_categories.id as category_id', 'channel_categories.parent_category_id as parent_id', 'Ec.name as EbutorCategory', 'Ec.category_id', 'Ec.ebutor_commission as Ebutor_Commission', 'channel_categories.channel_id', 'channel_categories.channel_commission', 'channel_categories.charge_type as Channel_ChargeType', 'Ec.charge_type as Ebutor_ChargeType')
                ->where('channel_categories.parent_category_id', $catchild['category_id'])
                ->where('channel_categories.channel_id', '=', $id)
                ->get()->all();

        return $getprodclass;
    }

}
