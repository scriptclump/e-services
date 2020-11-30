<?php

return [
    'errorInputData'=>'Error in getting data',
  
    'heading' => [
        'index_page_title' => 'Banners/Pop-ups',
        'sponsors_page_title' => 'Sponsors',
        'add_banners' => 'Add Banners/Sponsors',
        'edit_banners' => 'Edit Banners',
        'update_banners' => 'Update Banners',
        'index_page_sponsor' =>'Sponsors',
    ],

    'grid'=>[
 
           'banner_name'=>'Banner Name',
           'dc_name'=>'Warehouse',
           'hub_name'=> 'Hub',
           'beat_name'=> 'Beat',
           'grid_frequency' => 'Frequency',
           'grid_from_date' =>'From Date',
           'grid_to_date' =>'To Date',
           'grid_action' =>'Action',
           'clickcost' =>'Click Cost',
           'impressioncost' =>'Impression Cost',
           'sponsor_name' => 'Sponsor Name',

    ],

    'form' => [
        "warehouse_label"=>'Warehouse',
        "hub_label"=>'Hub',
        "beat_label"=>'Beat',
        "banner_label"=>'Name',
        "image_label"=>'Image',
        "banner_type"=>'Item',
        "item_list"=>'Item List',
        "type"=>'Type',
        "impression_cost"=>'Impression Cost',
        "click_cost"=>'Click Cost',
        "from_date"=>'From Date',
        "to_date"=>'To Date',
        "banner_sts"=>'Status',
        "sort_order"=>'Sort Order',
        "banner_frequency"=>'Frequency',
    ],

    'banner_form_validate' => [
        'warehouse_name' => 'WareHouse is required.',
        'hub_name' => 'Hub is required.',
        'beat_name' => 'Beat is required.',
        'banner_name'=>'Banner Name is Required',
        'banner_name_string' => 'Banner name accepts only alphabets and numbers.',
        'banner_name_length' => 'Banner name accepts maximum characters of 20.',
        'banner_reg' => 'Only Letters and Numbers are allowed.',
        'banner_type' => 'Banner Item is Required.',
        'banner_list' => 'Item List is Required.',
        'type' => 'Type is Required.',
        'impression_cost' => 'Impression Cost is Required.',
        'click_cost' => 'Clcik Cost is Required.',
        'banner_frequency' => 'Frequency is required.',
        'from_date' => 'From Date is Required.',
        'to_date' => 'To Date is Required.',
        'status' => 'Status is Required.',
        'sort_order'=> 'Sort Order is Required.',
        'impression_decimal'=> 'Only Decimal Numbers are allowed.',
        'clickcostdecimal'=> 'Only Decimal Numbers are allowed.',
        'frequencydecimal'=> 'Only Decimal Numbers are allowed.',

    ],
    'add_banner_form' => [

        'add_banner' => 'Banner successfully created.',
        'add_banner_details' => 'All Banner details successfully created.',
    ],
    'edit_banner_form' => [

        'edit_banner' => 'Banner successfully updated.',
        'edit_banner_details' => 'All Banner details successfully updated.',
    ],

];